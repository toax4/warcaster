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
        Schema::create('weapons', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('range', 10)->nullable();
            $table->string('attack', 10)->nullable();
            $table->string('hit', 10)->nullable();
            $table->string('wound', 10)->nullable();
            $table->string('rend', 10)->nullable();
            $table->string('damage', 10)->nullable();
            $table->string('warhammer_id')->nullable();
            // $table->timestamps();
        });

        Schema::create('weapon_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('weapon_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('name');

            $table->primary(['weapon_id', 'lang_id']);

            $table->foreign('weapon_id')->references('id')->on('weapons')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });

        Schema::create('weapon_abilities', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('warhammer_id')->nullable();
        });

        Schema::create('weapon_ability_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('weapon_ability_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('name');
            $table->text('lore')->nullable();
            $table->text('rules')->nullable();

            $table->primary(['weapon_ability_id', 'lang_id']);

            $table->foreign('weapon_ability_id')->references('id')->on('weapon_abilities')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weapon_ability_translations');

        Schema::dropIfExists('weapon_abilities');

        Schema::table('weapon_translations', function (Blueprint $table) {
            $table->dropForeign(['weapon_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('weapon_translations');

        Schema::dropIfExists('weapons');
    }
};