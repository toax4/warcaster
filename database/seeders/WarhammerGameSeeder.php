<?php

namespace Database\Seeders;

use App\Models\Warhammer\WarhammerGame;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarhammerGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WarhammerGame::firstOrCreate([
            "name" => "40k",
        ]);
        WarhammerGame::firstOrCreate([
            "name" => "Age of Sigmar",
        ]);
        WarhammerGame::firstOrCreate([
            "name" => "The old World",
        ]);
    }
}