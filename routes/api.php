<?php

use App\Http\Controllers\Api\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/units')
    ->name('units.')
    ->controller(UnitController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::prefix('/{unit}')
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/', 'update')->name('update');

            Route::get("/weapons", "weapons")->name('weapons');
            Route::get("/abilities", "abilities")->name('abilities');
        });
    });