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
        Schema::create('battle_formations', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
            $table->integer("points")->nullable();
            $table->string('warhammer_id')->nullable()->unique();
        });

        Schema::create('battle_formation_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('battle_formation_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('name');

            $table->primary(['battle_formation_id', 'lang_id']);

            $table->foreign('battle_formation_id')->references('id')->on('battle_formations')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battle_formation_translations', function (Blueprint $table) {
            $table->dropForeign(['battle_formation_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('battle_formation_translations');

        Schema::dropIfExists('battle_formations');
    }
};