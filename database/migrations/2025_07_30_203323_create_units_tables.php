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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('move', 10)->nullable();
            $table->string('save', 10)->nullable();
            $table->string('control', 10)->nullable();
            $table->string('health', 10)->nullable();
            $table->integer('points')->nullable();
            $table->string('bannerImage')->nullable();
            $table->string('rowImage')->nullable();
            $table->string('warhammer_id')->nullable();
        });

        Schema::create('unit_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('name');
            $table->string('subname')->nullable();
            $table->text('lore')->nullable();

            $table->primary(['unit_id', 'lang_id']);

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });

        Schema::create('pivot_unit_keyword', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('keyword_id');

            $table->primary(['unit_id', 'keyword_id']);

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pivot_unit_keyword', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['keyword_id']);
        });
        Schema::dropIfExists('pivot_unit_keyword');

        Schema::table('unit_translations', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('unit_translations');

        Schema::dropIfExists('units');
    }
};
