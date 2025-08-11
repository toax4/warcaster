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
        Schema::create('factions', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('factionHeaderImage')->nullable();
            $table->string('rosterHeaderImage')->nullable();
            $table->string('selectFactionImage')->nullable();
            $table->string('rosterFactionImage')->nullable();
            $table->string('moreInfoImage')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('warhammer_id');

            $table->foreign('parent_id')->references('id')->on('factions');
        });

        Schema::create('faction_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('faction_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('name');
            $table->text('lore')->nullable();

            $table->primary(['faction_id', 'lang_id']);

            $table->foreign('faction_id')->references('id')->on('factions')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });

        Schema::create('pivot_unit_faction', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('faction_id');

            $table->primary(['unit_id', 'faction_id']);

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('faction_id')->references('id')->on('factions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pivot_unit_faction', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['faction_id']);
        });
        Schema::dropIfExists('pivot_unit_faction');

        Schema::table('faction_translations', function (Blueprint $table) {
            $table->dropForeign(['faction_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('faction_translations');

        Schema::dropIfExists('factions');
    }
};