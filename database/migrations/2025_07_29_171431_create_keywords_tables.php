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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('warhammer_id')->nullable();
        });

        Schema::create('keyword_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('keyword_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('label');

            $table->primary(['keyword_id', 'lang_id']);

            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keyword_translations', function (Blueprint $table) {
            $table->dropForeign(['keyword_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('keyword_translations');

        Schema::dropIfExists('keywords');
    }
};