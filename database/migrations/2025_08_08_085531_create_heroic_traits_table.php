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
        Schema::create('heroic_traits', function (Blueprint $table) {
            $table->unsignedBigInteger('ability_id');
            $table->unsignedBigInteger('faction_id');

            $table->primary(['ability_id', 'faction_id']);

            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
            $table->foreign('faction_id')->references('id')->on('factions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heroic_traits', function (Blueprint $table) {
            $table->dropForeign(['ability_id']);
            $table->dropForeign(['faction_id']);
        });
        Schema::dropIfExists('heroic_traits');
    }
};