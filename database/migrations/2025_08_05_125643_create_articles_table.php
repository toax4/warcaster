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
        Schema::create('article_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
        });
        
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->string('title');
            $table->string('link');
            $table->string('image')->nullable();
            $table->boolean('sended')->default(false);
            $table->json("data");
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['source_id', 'link']);

            $table->foreign('source_id')->references('id')->on('article_sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
        });
        Schema::dropIfExists('articles');

        Schema::dropIfExists('article_sources');
    }
};