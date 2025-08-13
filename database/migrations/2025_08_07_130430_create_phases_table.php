<?php

use App\Models\Phase;
use App\Models\PhaseTranslation;
use App\Services\Utils\StringTools;
use Database\Seeders\PhaseSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
            $table->integer("displayOrder")->default(999);
            $table->string("hexcolor", 7)->nullable();
        });

        Schema::create('phase_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('name');

            $table->primary(['phase_id', 'lang_id']);

            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');
        });

        Schema::table("abilities", function (Blueprint $table) {
            $table->unsignedBigInteger('phase_id')->after("slug")->nullable();
            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade');
        });

        (new PhaseSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abilities', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
        });
        Schema::table('phase_translations', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
            $table->dropForeign(['lang_id']);
        });
        Schema::dropIfExists('phase_translations');

        Schema::dropIfExists('phases');
    }
};