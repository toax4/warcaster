<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\PhaseTranslation;
use App\Services\Utils\StringTools;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phases = [
            ["name" => "Start of Turn", "position" => 1, "hexcolor" => "#1a1917"],
            ["name" => "Hero Phase", "position" => 2, "hexcolor" => "#af962d"],
            ["name" => "Movement Phase", "position" => 3, "hexcolor" => "#868584"],
            ["name" => "Shooting Phase", "position" => 4, "hexcolor" => "#004d64"],
            ["name" => "Charge Phase", "position" => 5, "hexcolor" => "#ba5c19"],
            ["name" => "Combat Phase", "position" => 6, "hexcolor" => "#861218"],
            ["name" => "End of Turn", "position" => 7, "hexcolor" => "#5d3270"],
            ["name" => "Passive", "position" => 0, "hexcolor" => "#000000"],
        ];
        foreach ($phases as $value) {
            $phase = Phase::create([
                "slug" => StringTools::slug($value["name"]),
                "displayOrder" => $value["position"],
                "hexcolor" => $value["hexcolor"],
            ]);

            PhaseTranslation::create([
                "phase_id" => $phase->id,
                "lang_id" => 1,
                "name" => $value["name"],
            ]);
        }
    }
}