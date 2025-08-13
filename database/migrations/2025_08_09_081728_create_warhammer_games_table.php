<?php

use Database\Seeders\WarhammerGameSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warhammer_games', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            // $table->timestamps();
        });

        (new WarhammerGameSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warhammer_games');
    }
};