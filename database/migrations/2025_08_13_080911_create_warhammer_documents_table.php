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
        if (!Schema::hasTable("warhammer_document_category_translations")) {
            Schema::create('warhammer_document_category_translations', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable("warhammer_document_categories")) {
            Schema::create('warhammer_document_categories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
            });
        }

        if (!Schema::hasTable("warhammer_documents")) {
            Schema::create('warhammer_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('game_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('lang_id')->nullable();
                $table->string("title");
                $table->string("slug")->nullable();
                $table->date("created_at")->nullable();
                $table->string("warhammer_id", 100)->unique();

                $table->foreign('game_id')->references('id')->on('warhammer_games')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('warhammer_document_categories')->onDelete('cascade');
                $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable("warhammer_document_versions")) {
            Schema::create('warhammer_document_versions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->string("remote_file")->nullable();
                $table->string("local_file")->nullable();
                $table->string("checksum", 100)->nullable();
                $table->date("updated_at")->nullable();

                $table->unique(["document_id", "checksum"]);
                        
                $table->foreign('document_id')->references('id')->on('warhammer_documents')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('warhammer_documents', function (Blueprint $table) {
        //     $table->dropForeign(['ability_id']);
        //     $table->dropForeign(['lang_id']);
        // });

        // Schema::dropIfExists('warhammer_documents');
    }
};
