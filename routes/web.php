<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Nova\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/kaspi-settings', [App\Http\Controllers\Nova\ShopController::class, 'index']);
Route::post('/kaspi-settings', [App\Http\Controllers\Nova\ShopController::class, 'store']);


Route::post('/api/save-autosale/{id}', [ProductController::class, 'saveAutosale']);
Route::post('/api/save-weekly-update/{id}', [ProductController::class, 'saveWeeklyUpdate']);
Route::post('/api/save-price-min/{product}', [ProductController::class, 'savePriceMin']);
Route::post('/api/save-price-cost/{product}', [ProductController::class, 'savePriceCost']);
