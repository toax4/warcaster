<?php

use App\Http\Controllers\UnitController;
use App\Http\Resources\AbilityResource;
use App\Http\Resources\UnitFullResource;
use App\Http\Resources\UnitResource;
use App\Http\Resources\WeaponResource;
use App\Jobs\ImportWarhammer\AoS\ImportGlossaryTranslationJob;
use App\Jobs\Rss\ScrapWarhammerShop;
use App\Jobs\Rss\SendTelegramArticle;
use App\Models\Ability;
use App\Models\Faction;
use App\Models\Phase;
use App\Models\Rss\Article;
use App\Models\Rss\ArticleSource;
use App\Models\Unit;
use App\Models\UnitAbility;
use App\Services\Utils\StringTools;
use App\Services\WarhammerAlgoliaService;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('/units')
    ->name('units.')
    ->controller(UnitController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::prefix('/{unit}')
        ->group(function () {
            Route::get('/', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::put('/', 'update')->name('update');
        });
    });

Route::get('/dev', function () {
    return view("admin.index");
});
Route::get("dev/telegram", function () {

});