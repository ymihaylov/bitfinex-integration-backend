<?php

use App\Http\Controllers\ExchangeRateLogController;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::controller(ExchangeRateLogController::class)->group(function () {
    Route::get('/get-last-exchange-rates/{symbol}', 'getLastExchangeRates')
        ->name('api.get_last_exchange_rates');

    Route::post('/subscribe-for-notification', 'subscribeForNotification')
        ->name('api.subscribe_for_notification');
});
