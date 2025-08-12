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
        Schema::create('lores', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
            $table->integer("points")->nullable();
            $table->string('warhammer_id')->nullable();

            $table->unique(['slug', 'warhammer_id']);
        });

        Schema::create('lore_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('lore_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('name');

            $table->primary(['lore_id', 'lang_id']);

            $table->foreign('lore_id')->references('id')->on('lores')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lore_translations', function (Blueprint $table) {
            $table->dropForeign(['lore_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('lore_translations');

        Schema::dropIfExists('lores');
    }
};