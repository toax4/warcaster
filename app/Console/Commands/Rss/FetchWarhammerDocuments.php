<?php

namespace App\Console\Commands\Rss;

use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Language;
use App\Models\Rss\Article;
use App\Models\Rss\ArticleSource;
use App\Models\Warhammer\Documents\WarhammerDocument;
use App\Models\Warhammer\Documents\WarhammerDocumentCategory;
use App\Models\Warhammer\Documents\WarhammerDocumentVersion;
use App\Models\Warhammer\WarhammerGame;
use App\Services\TelegramService;
use App\Services\Utils\FileTools;
use App\Services\Utils\StringTools;
use App\Services\WarhammerAlgoliaService;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchWarhammerDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:scrap-warhammer-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $articleSource = ArticleSource::where("slug", "warhammer_documents")->first();

        $searchs = [
            [
                "postData" => [
                    "index"=> "downloads_v2",
                    "searchTerm"=> "",
                    "gameSystem"=> "warhammer-age-of-sigmar",
                    "language"=> "french"
                ],
                "lang" => Language::find(2),
                "game" => WarhammerGame::where("slug", "age_of_sigmar")->first(),
            ],
            [
                "postData" => [
                    "index"=> "downloads_v2",
                    "searchTerm"=> "",
                    "gameSystem"=> "warhammer-age-of-sigmar",
                    "language"=> "english"
                ],
                "lang" => Language::find(1),
                "game" => WarhammerGame::where("slug", "age_of_sigmar")->first(),
            ],
            // [
            //     "postData" => [
            //         "index"=> "downloads_v2",
            //         "searchTerm"=> "",
            //         "gameSystem"=> "warhammer-40000",
            //         "language"=> "french"
            //     ],
            //     "lang" => Language::find(2),
            //     "game" => WarhammerGame::where("slug", "40k")->first(),
            // ],
        ];

        foreach ($searchs as $search) {
            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                "https://www.warhammer-community.com/api/search/downloads/",
                $search["postData"]
            );
    
            foreach (($res->json())["hits"] as $doc) {
                $game = $search["game"];
                $category = WarhammerDocumentCategory::firstOrCreate($doc["id"]["download_categories"][0]);
                $lang = $search["lang"];

                $filePath = "/".$lang->code."/".$game->slug."/".$category->slug."/".$doc["id"]["slug"]."_".DateTime::createFromFormat("d/m/Y", $doc["id"]["last_updated"])->format("Y-m-d").".pdf";

                $file = $this->downloadFile("https://assets.warhammer-community.com/".$doc["id"]["file"], $filePath);
                $checksum = FileTools::getFileChecksum($file);

                $document = WarhammerDocument::updateOrCreate([
                    "warhammer_id" => $doc["objectID"],
                ], [
                    "title" => $doc["id"]["title"],
                    "slug" => $doc["id"]["slug"],
                    "lang_id" => $lang->id,
                    "category_id" => $category->id,
                    "game_id" => $game->id,
                    "created_at" => DateTime::createFromFormat("d/m/Y", $doc["id"]["created_at"]),
                ]);

                $documentVersion = WarhammerDocumentVersion::firstOrNew(['checksum' => $checksum, "document_id" => $document->id]);
                if (!$documentVersion->exists) {
                    // 1. Récupérer le contenu du fichier sur le disque source
                    $content = Storage::disk("temp")->get($filePath);
                    // 2. L’écrire sur le disque destination
                    Storage::disk('warhammer_documents')->put($filePath, $content);
                    // 3. (Optionnel) Supprimer l’original
                    // Storage::disk('temp')->delete($filePath);
                    
                    $documentVersion->remote_file = "https://assets.warhammer-community.com/".$doc["id"]["file"];
                    $documentVersion->local_file = $filePath;
                    $documentVersion->updated_at = DateTime::createFromFormat("d/m/Y", $doc["id"]["last_updated"]);

                    $documentVersion->save();
                }

                $data = [
                    "view" => "rss.telegram.warhammer_document",
                    "title"=> $doc["id"]["title"],
                ];

                if ($document->wasRecentlyCreated) {
                    $data["source_name_plus"] = "Nouveau document";

                    Article::firstOrCreate(
                        [
                            'link' => "https://assets.warhammer-community.com/".$doc["id"]["file"],
                            'source_id' => $articleSource->id,
                            'published_at' => DateTime::createFromFormat("d/m/Y", $doc["id"]["last_updated"]),
                        ],
                        [
                            'title' => $doc["id"]["title"],
                            'image' => "https://assets.warhammer-community.com/downloads-file-images/aos-download-image.jpg",
                            'data' => $data,
                        ]
                    );
                } elseif ($documentVersion->wasRecentlyCreated) {
                    $data["source_name_plus"] = "Document mis à jour";

                    Article::firstOrCreate(
                        [
                            'link' => "https://assets.warhammer-community.com/".$doc["id"]["file"],
                            'source_id' => $articleSource->id,
                            'published_at' => DateTime::createFromFormat("d/m/Y", $doc["id"]["last_updated"]),
                        ],
                        [
                            'title' => $doc["id"]["title"],
                            'image' => "https://assets.warhammer-community.com/downloads-file-images/aos-download-image.jpg",
                            'data' => $data,
                        ]
                    );
                }

                dd($document, $documentVersion);
            }
        }
    }

    private function downloadFile(string $url, $fileName = null): string
    {
        $storage = Storage::disk("temp");
        if ($fileName === null) {
            $fileName = basename($url);
        }

        // $fileName = ltrim($filePath, "/");
        // dump($fileName);

        if ($storage->exists($fileName)) {
            $storage->delete($fileName);
        }

        // 1️⃣ Téléchargement de l'image en local
        $imageContent = file_get_contents($url, false, stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36"
            ]
        ]));
        $storage->put($fileName, $imageContent);

        return Storage::disk("temp")->path($fileName);
    }
}