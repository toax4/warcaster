<?php

use App\Http\Controllers\UnitController;
use App\Http\Resources\AbilityResource;
use App\Http\Resources\UnitFullResource;
use App\Http\Resources\UnitResource;
use App\Http\Resources\WeaponResource;
use App\Jobs\ImportWarhammer\AoS\ImportGlossaryTranslationJob;
use App\Jobs\Rss\ScrapWarhammerShop;
use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Ability;
use App\Models\Faction;
use App\Models\Phase;
use App\Models\PhaseDetail;
use App\Models\PhaseDetailTranslation;
use App\Models\Rss\Article;
use App\Models\Rss\ArticleSource;
use App\Models\Unit;
use App\Models\UnitAbility;
use App\Models\UnitTranslation;
use App\Services\Utils\StringTools;
use App\Services\WarhammerAlgoliaService;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admin.index');
});


Route::prefix('/team')
    ->name('team.')
    ->controller(UnitController::class)
    ->group(function () {
        Route::get('/', function () {
            $phases = Phase::whereBetween("displayOrder", [1, 100])->orderBy("displayOrder", "asc")->get()->map(fn ($phase) => $phase->withTranslation());
            return view("admin.team_index", ["phases" => $phases]);
        })->name("index");
        Route::post('/search', function (Request $request) {
            $hits = UnitTranslation::search($request->input("search"))->take(25)->raw()["results"];
            
            $ids = collect($hits)->pluck('unit_id')->unique()->values();

            $units = Unit::whereIn('id', $ids)->get();

            // Réordonner selon l’ordre des hits
            $order = $ids->flip(); // map id → position
            $units = $units->sortBy(fn ($u) => $order[$u->id])->values();
            // dd($units);

            return UnitFullResource::collection($units);
        })->name("search");
        Route::post('/fetch/units', function (Request $request) {
            $units = Unit::whereIn("id", explode(",", $request->input("ids")))->get();
            // dd($request->all());

            return UnitFullResource::collection($units);
        })->name("fetch.units");
        // Route::post('/fetch/abilities', function (Request $request) {
        //     $units = Unit::whereIn("id", explode(",", $request->input("ids")))->get();
        //     // dd($request->all());

        //     $abilities = [];
        //     foreach ($units as $unit) {
        //         foreach ($unit->abilities as $ability) {
        //             $abilities[] = $ability;
        //         }
        //     }

        //     return AbilityResource::collection($abilities);
        // })->name("fetch.abilities");
    });

Route::prefix('/units')
    ->name('units.')
    ->controller(UnitController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::prefix('/{unit}')
        ->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::put('/', 'update')->name('update');
        });
    });

Route::get('/dev', function () {
    return view("admin.index");
});
Route::get("dev/translate", function () {
    $searchs = [
        "Once Per Battle",
        "Once Per Turn",
        "(Army)",
        "Passive",
        "Deployment Phase",
        "Start of Your Turn",
        // "Start of Enemy Turn",
        "Start of Any Turn",
        "Your Hero Phase",
        // "Enemy Hero Phase",
        "Any Hero Phase",
        "Your Movement Phase",
        // "Enemy Movement Phase",
        "Any Movement Phase",
        "Your Shooting Phase",
        // "Enemy Shooting Phase",
        "Any Shooting Phase",
        "Your Charge Phase",
        // "Enemy Charge Phase",
        "Any Charge Phase",
        "Your Combat Phase",
        // "Enemy Combat Phase",
        "Any Combat Phase",
        "End of Your Turn",
        // "End of Enemy Turn",
        "End of Any Turn",
    ];

    $replaces = [
        "Une Fois par Bataille",
        "Une Fois par Tour",
        "(Armée)",
        "Passif",
        "Phase de Déploiement",
        "Début de Votre Tour",
        // "Début de Enemy Tour",
        "Début de N'importe Quelle Tour",
        "Votre Phase des Héros",
        // "Enemy Phase des Héros",
        "N'importe Quelle Phase des Héros",
        "Votre Phase de Mouvement",
        // "Enemy Phase de Mouvement",
        "N'importe Quelle Phase de Mouvement",
        "Votre Phase de Tir",
        // "Enemy Phase de Tir",
        "N'importe Quelle Phase de Tir",
        "Votre Phase de Charge",
        // "Enemy Phase de Charge",
        "N'importe Quelle Phase de Charge",
        "Votre Phase de Mêlée",
        // "Enemy Phase de Mêlée",
        "N'importe Quelle Phase de Mêlée",
        "Fin de Votre Tour",
        // "Fin of Enemy Tour",
        "Fin of N'importe Quelle Tour",
    ];

    foreach ($searchs as $search) {
        $phases = PhaseDetailTranslation::where("name", "like", "%".$search."%")->get();

        foreach ($phases as $phase) {
            PhaseDetailTranslation::updateOrCreate([
                "phase_detail_id" => $phase->phase_detail_id,
                "lang_id" => 2,
            ], [
                "name" => str_replace($searchs, $replaces, $phase->name),
            ]);
        }
    }
});
