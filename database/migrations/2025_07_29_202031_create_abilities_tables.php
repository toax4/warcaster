<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
            $table->integer("cp_cost")->nullable();
            $table->integer("points")->nullable();
            $table->string('warhammer_id', 36)->nullable();

            $table->unique(['slug', 'warhammer_id']);
        });

        Schema::create('ability_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('ability_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('name');
            $table->text('lore')->nullable();
            $table->text('declare')->nullable();
            $table->text('effect')->nullable();

            $table->primary(['ability_id', 'lang_id']);

            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ability_translations', function (Blueprint $table) {
            $table->dropForeign(['ability_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('ability_translations');

        Schema::dropIfExists('abilities');
    }
};