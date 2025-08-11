<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::create([ 'label' => "English", 'code' => 'en_US', 'position' => 1 ]);
        Language::create([ 'label' => "Français", 'code' => 'fr_FR', 'position' => 2 ]);
        Language::create([ 'label' => "Spanish", 'code' => 'es_ES' ]);
        Language::create([ 'label' => "German", 'code' => 'de_DE' ]);
        Language::create([ 'label' => "Italian", 'code' => 'it_IT' ]);
    }
}
