<?php

namespace App\Console\Commands\Dev;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MakeViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:make-views';

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
        // keywords_with_translations
        $this->createView(
            viewName:"keywords_with_translations",
            select:["t.id", "t.warhammer_id", "t.slug"],
            table: "keywords",
            joinTemplate: "LEFT JOIN keyword_translations CODE ON CODE.keyword_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['label'],
            order:"t.slug asc"
        );

        // abilities_with_translations
        $this->createView(
            viewName:"abilities_with_translations",
            select:["t.id", "t.slug"],
            table: "abilities",
            joinTemplate: "LEFT JOIN ability_translations CODE ON CODE.ability_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name','lore','declare','effect'],
            order:"t.slug asc"
        );

        // units_with_translations
        $this->createView(
            viewName:"units_with_translations",
            select:["t.id", "t.slug"],
            table: "units",
            joinTemplate: "LEFT JOIN unit_translations CODE ON CODE.unit_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ["name", "subname", "lore"],
            order:"t.slug asc"
        );

        // factions_with_translations
        $this->createView(
            viewName:"factions_with_translations",
            select:["t.id", "t.slug"],
            table: "factions",
            joinTemplate: "LEFT JOIN faction_translations CODE ON CODE.faction_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ["name", "lore"],
            order:"t.slug asc"
        );

        // weapons_with_translations
        $this->createView(
            viewName:"weapons_with_translations",
            select:["t.id", "t.slug"],
            table: "weapons",
            joinTemplate: "LEFT JOIN weapon_translations CODE ON CODE.weapon_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name'],
            order:"t.slug asc"
        );

        // weapon_abilities_with_translations
        $this->createView(
            viewName:"weapon_abilities_with_translations",
            select:["t.id", "t.slug"],
            table: "weapons",
            joinTemplate: "LEFT JOIN weapon_ability_translations CODE ON CODE.weapon_ability_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ["name", "lore", "rules"],
            order:"t.slug asc"
        );

        // battle_formation_with_translations
        $this->createView(
            viewName:"battle_formation_with_translations",
            select:["t.id", "t.slug"],
            table: "battle_formations",
            joinTemplate: "LEFT JOIN battle_formation_translations CODE ON CODE.battle_formation_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name'],
            order:"t.slug asc"
        );

        // glossary_with_translations
        $this->createView(
            viewName:"glossary_with_translations",
            select:["t.id", "t.slug"],
            table: "glossaries",
            joinTemplate: "LEFT JOIN glossary_translations CODE ON CODE.glossary_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name', 'content'],
            order:"t.slug asc"
        );
        
        // lore_with_translations
        $this->createView(
            viewName:"lore_with_translations",
            select:["t.id", "t.slug"],
            table: "lores",
            joinTemplate: "LEFT JOIN lore_translations CODE ON CODE.lore_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name'],
            order:"t.slug asc"
        );

        // phase_with_translations
        $this->createView(
            viewName:"phase_with_translations",
            select:["t.id", "t.slug"],
            table: "phases",
            joinTemplate: "LEFT JOIN phase_translations CODE ON CODE.phase_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name'],
            order:"t.displayOrder asc"
        );
        
        // phase_detail_with_translations
        $this->createView(
            viewName:"phase_detail_with_translations",
            select:["t.id", "t.slug"],
            table: "phase_details",
            joinTemplate: "LEFT JOIN phase_detail_translations CODE ON CODE.phase_detail_id = t.id AND CODE.lang_id = (SELECT id FROM languages WHERE code = 'CODE')",
            fields: ['name'],
            order:"t.slug asc"
        );
    }


    private function deleteView($viewName)
    {
        DB::statement("DROP VIEW IF EXISTS $viewName");
    }

    private function createView($viewName, $select, $table, $joinTemplate, $fields, $order = null)
    {
        $this->deleteView($viewName);

        $langs = DB::table('languages')->orderBy("position", "asc")->orderBy("code", "asc")->pluck('code', 'id');
        $tables = [$table." t"];

        foreach ($langs as $id => $code) {
            $tables[] = str_replace("CODE", $code, $joinTemplate);
            
            foreach ($fields as $field) {
                $select[] = "{$code}.{$field} AS {$code}_{$field}";
            }
        }

        // dump("CREATE OR REPLACE VIEW $viewName AS SELECT ".implode(",", $select)." FROM {$join}".($order ? "ORDER BY $order" : "")."");
        DB::statement("
            CREATE OR REPLACE VIEW $viewName AS
            SELECT ".implode(", ", $select)."
            FROM ".implode(" ", $tables)."
            ".($order ? "ORDER BY $order" : "")."
        ");
    }
}