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
        Schema::create('phase_details', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
        });

        Schema::create('phase_detail_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('phase_detail_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('name');

            $table->primary(['phase_detail_id', 'lang_id']);

            $table->foreign('phase_detail_id')->references('id')->on('phase_details')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phase_detail_translations', function (Blueprint $table) {
            $table->dropForeign(['phase_detail_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('phase_detail_translations');
        
        Schema::dropIfExists('phase_details');
    }
};