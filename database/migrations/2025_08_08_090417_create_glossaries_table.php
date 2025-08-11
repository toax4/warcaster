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
        Schema::create('glossaries', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
        });

        Schema::create('glossary_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('glossary_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('name');
            $table->text('content')->nullable();

            $table->primary(['glossary_id', 'lang_id']);

            $table->foreign('glossary_id')->references('id')->on('glossaries')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('glossary_translations', function (Blueprint $table) {
            $table->dropForeign(['glossary_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('glossary_translations');

        Schema::dropIfExists('glossaries');
    }
};