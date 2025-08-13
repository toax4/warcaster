<?php

namespace Database\Seeders;

use App\Models\Rss\ArticleSource;
use Illuminate\Database\Seeder;

class ArticleSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ArticleSource::firstOrCreate([
            "name" => "Warhammer - NEWS Fr",
        ]);
        ArticleSource::firstOrCreate([
            "name" => "Warhammer Shop",
        ]);
        ArticleSource::firstOrCreate([
            "name" => "Warhammer Documents",
        ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Nouveautés Black Library",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Nouveautés Age Of Sigmar",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Nouveautés 40K",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Nouveautés",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Précommandes Black Library",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Précommandes Age Of Sigmar",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Précommandes 40K",
        // ]);
        // ArticleSource::create([
        //     "name" => "Warhammer - Précommandes",
        // ]);
    }
}