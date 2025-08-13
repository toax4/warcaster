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
        Language::create([ 'label' => "English", 'code' => 'en-US', 'position' => 1 ]);
        Language::create([ 'label' => "Français", 'code' => 'fr-FR', 'position' => 2 ]);
        Language::create([ 'label' => "Spanish", 'code' => 'es-ES' ]);
        Language::create([ 'label' => "German", 'code' => 'de-DE' ]);
        Language::create([ 'label' => "Italian", 'code' => 'it-IT' ]);
    }
}
