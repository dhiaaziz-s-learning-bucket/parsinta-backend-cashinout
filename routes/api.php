<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CashController;
use App\Http\Controllers\Auth\MeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Auth::loginUsingId(2);
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
    Route::get('me', [MeController::class, '__invoke']);

    Route::prefix('cash')->group(function () {
        Route::get('/', [CashController::class, 'index']);
        // Route::get('/{id}', 'CashController@show');
        Route::post('/create', [CashController::class, 'store']);
        Route::get('{cash:slug}', [CashController::class, 'show']);
        // Route::put('/{id}', 'CashController@update');
        // Route::delete('/{id}', 'CashController@destroy');
    });
});