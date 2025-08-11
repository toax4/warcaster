<?php

namespace App\Console\Commands\ImportWarhammer\AoS;

use App\Jobs\ImportWarhammer\AoS\ImportPivotUnitWeaponJob;
use App\Jobs\ImportWarhammer\AoS\ImportPivotWeaponWeaponAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportWeaponAbilityJob;
use App\Jobs\ImportWarhammer\AoS\ImportWeaponAbilityTranslationJob;
use App\Jobs\ImportWarhammer\AoS\ImportWeaponJob;
use App\Jobs\ImportWarhammer\AoS\ImportWeaponTranslationJob;
use App\Services\Utils\StringTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportWeapons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aos:weapons {--limit=}';

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
        $countJobs = [
            ImportWeaponJob::class => 0,
            ImportWeaponTranslationJob::class => 0,
            ImportWeaponAbilityJob::class => 0,
            ImportWeaponAbilityTranslationJob::class => 0,
            ImportPivotWeaponWeaponAbilityJob::class => 0,
            ImportPivotUnitWeaponJob::class => 0,
        ];

        // Weapons
        $weapons = DB::connection('mysql_aos')
        ->select("SELECT 
                    w.*
                FROM warscroll_weapon w;
        ");
        foreach ($weapons as $weapon) {
            $weapon = (array) $weapon;

            $findWeapon = [
                'slug' => StringTools::slug($weapon['name']),
                'warhammer_id' => $weapon['id'],
            ];

            ImportWeaponJob::dispatch(
                find: $findWeapon,
                data: [
                    'range' => $weapon['range'] ?? null,
                    'attack' => $weapon['attacks'] ?? null,
                    'hit' => $weapon['hit'] ?? null,
                    'wound' => $weapon['wound'] ?? null,
                    'rend' => $weapon['rend'] ?? null,
                    'damage' => $weapon['damage'] ?? null,
                ]
            );
            $countJobs[ImportWeaponJob::class]++;

            ImportWeaponTranslationJob::dispatch(
                weapon: $findWeapon,
                langId: 1,
                data: [
                    'name' => $weapon['name']
                ]
            );
            $countJobs[ImportWeaponTranslationJob::class]++;
        }
        
        // Pivot Unit - Weapon
        $pivotsUnitWeapon = DB::connection('mysql_aos')
        ->select("SELECT w.*,
                    we.id AS weapon_id,
                    we.name AS weapon_name
                FROM warscroll w
                INNER JOIN warscroll_weapon we ON we.warscrollId = w.id;
        ");
        foreach ($pivotsUnitWeapon as $pivotUnitWeapon) {
            $pivotUnitWeapon = (array) $pivotUnitWeapon;

            ImportPivotUnitWeaponJob::dispatch(
                weapon: [
                    'slug' => StringTools::slug($pivotUnitWeapon['weapon_name']),
                    'warhammer_id' => $pivotUnitWeapon['weapon_id'],
                ],
                unit: [
                    'slug' => StringTools::slug($pivotUnitWeapon['name']),
                    'warhammer_id' => $pivotUnitWeapon['id']
                ],
            );
            $countJobs[ImportPivotUnitWeaponJob::class]++;
        }

        // Weapon abilities
        $abilities = DB::connection('mysql_aos')
        ->select("SELECT a.*
                FROM weapon_ability a
        ");
        foreach ($abilities as $ability) {
            $ability = (array) $ability;
            
            $findWeaponAbility = [
                'slug' => StringTools::slug($ability['name']),
                'warhammer_id' => $ability['id']
            ];

            ImportWeaponAbilityJob::dispatch(
                find: $findWeaponAbility,
                data: []
            );
            $countJobs[ImportWeaponAbilityJob::class]++;
    
            ImportWeaponAbilityTranslationJob::dispatch(
                ability: $findWeaponAbility,
                langId: 1,
                data: [
                    'name' => $ability['name'],
                    'lore' => $ability['lore'],
                    'rules' => $ability['rules']
                ]
            );
            $countJobs[ImportWeaponAbilityTranslationJob::class]++;
        }
        
        // Pivot Weapons - Weapon Abilities
        $abilities = DB::connection('mysql_aos')
        ->select("SELECT a.*,
                    wwa.displayOrder,
                    we.id AS weapon_id,
                    we.name AS weapon_name
                FROM weapon_ability a
                INNER JOIN warscroll_weapon_weapon_ability wwa ON wwa.weaponAbilityId = a.id
                INNER JOIN warscroll_weapon we ON we.id = wwa.warscrollWeaponId;
        ");
        foreach ($abilities as $ability) {
            $ability = (array) $ability;
            
            ImportPivotWeaponWeaponAbilityJob::dispatch(
                weaponAbility: [
                    'slug' => StringTools::slug($ability['name']),
                    'warhammer_id' => $ability['id']
                ],
                weapon: [
                    'slug' => StringTools::slug($ability['weapon_name']),
                    'warhammer_id' => $ability['weapon_id']
                ],
                data: [
                    "displayOrder" => $ability["displayOrder"],
                ]
            );
            $countJobs[ImportPivotWeaponWeaponAbilityJob::class]++;
        }


        foreach ($countJobs as $key => $countJob) {
            Log::info("".basename(__FILE__)."::".basename($key)." - $countJob jobs");
        }
    }
}