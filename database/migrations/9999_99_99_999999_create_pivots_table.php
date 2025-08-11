<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pivot_ability_keyword', function (Blueprint $table) {
            $table->unsignedBigInteger('keyword_id');
            $table->unsignedBigInteger('ability_id');

            $table->primary(['keyword_id', 'ability_id']);

            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
        });

        Schema::create('pivot_unit_ability', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('ability_id');

            $table->primary(['unit_id', 'ability_id']);

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
        });

        Schema::create('pivot_unit_weapon', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('weapon_id');

            $table->primary(['unit_id', 'weapon_id']);

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('weapon_id')->references('id')->on('weapons')->onDelete('cascade');
        });

        Schema::create('pivot_weapon_weapon_abilitiy', function (Blueprint $table) {
            $table->unsignedBigInteger('weapon_id');
            $table->unsignedBigInteger('weapon_ability_id');
            $table->integer('displayOrder');

            $table->primary(['weapon_id', 'weapon_ability_id']);

            $table->foreign('weapon_id')->references('id')->on('weapons')->onDelete('cascade');
            $table->foreign('weapon_ability_id')->references('id')->on('weapon_abilities')->onDelete('cascade');
        });

        Schema::create('pivot_battle_formation_ability', function (Blueprint $table) {
            $table->unsignedBigInteger('battle_formation_id');
            $table->unsignedBigInteger('ability_id');

            $table->primary(['battle_formation_id', 'ability_id']);

            $table->foreign('battle_formation_id')->references('id')->on('battle_formations')->onDelete('cascade');
            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
        });

        Schema::create('pivot_lore_ability', function (Blueprint $table) {
            $table->unsignedBigInteger('lore_id');
            $table->unsignedBigInteger('ability_id');

            $table->primary(['lore_id', 'ability_id']);

            $table->foreign('lore_id')->references('id')->on('lores')->onDelete('cascade');
            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
        });

        Schema::create('pivot_lore_faction', function (Blueprint $table) {
            $table->unsignedBigInteger('lore_id');
            $table->unsignedBigInteger('faction_id');

            $table->primary(['lore_id', 'faction_id']);

            $table->foreign('lore_id')->references('id')->on('lores')->onDelete('cascade');
            $table->foreign('faction_id')->references('id')->on('factions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable("pivot_lore_faction")) {
            Schema::table('pivot_lore_faction', function (Blueprint $table) {
                $table->dropForeign(['lore_id']);
                $table->dropForeign(['faction_id']);
            });
            Schema::dropIfExists('pivot_lore_faction');
        }

        if (Schema::hasTable("pivot_lore_ability")) {
            Schema::table('pivot_lore_ability', function (Blueprint $table) {
                $table->dropForeign(['lore_id']);
                $table->dropForeign(['ability_id']);
            });
            Schema::dropIfExists('pivot_lore_ability');
        }

        if (Schema::hasTable('pivot_battle_formation_ability')) {
            Schema::table('pivot_battle_formation_ability', function (Blueprint $table) {
                $table->dropForeign(['battle_formation_id']);
                $table->dropForeign(['ability_id']);
            });
            Schema::dropIfExists('pivot_battle_formation_ability');
        }

        if (Schema::hasTable('pivot_weapon_weapon_abilitiy')) {
            Schema::table('pivot_weapon_weapon_abilitiy', function (Blueprint $table) {
                $table->dropForeign(['weapon_id']);
                $table->dropForeign(['weapon_ability_id']);
            });
            Schema::dropIfExists('pivot_weapon_weapon_abilitiy');
        }

        if (Schema::hasTable('pivot_unit_weapon')) {
            Schema::table('pivot_unit_weapon', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
                $table->dropForeign(['weapon_id']);
            });
            Schema::dropIfExists('pivot_unit_weapon');
        }

        if (Schema::hasTable('pivot_unit_ability')) {
            Schema::table('pivot_unit_ability', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
                $table->dropForeign(['ability_id']);
            });
            Schema::dropIfExists('pivot_unit_ability');
        }
        
        if (Schema::hasTable('pivot_ability_keyword')) {
            Schema::table('pivot_ability_keyword', function (Blueprint $table) {
                $table->dropForeign(['keyword_id']);
                $table->dropForeign(['ability_id']);
            });
            Schema::dropIfExists('pivot_ability_keyword');
        }
    }
};