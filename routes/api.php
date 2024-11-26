<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\ZohoController;
use App\Http\Controllers\ZohoAuthController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('zoho')->group(function () {
    Route::get('auth', [ZohoController::class, 'redirectToZoho']);
    Route::get('callback', [ZohoController::class, 'handleZohoCallback']);
    Route::get('chart-of-accounts', [ZohoController::class, 'getChartOfAccounts']);
    Route::post('sync-accounts', [ZohoController::class, 'syncChartOfAccounts']);
    Route::get('sync-contacts', [ZohoController::class,'syncContacts']);
    Route::get('contacts', [ZohoController::class,'getContacts']);
    Route::get('sync-receipts', [ZohoController::class,'syncReceipts']);
    Route::get('receipts', [ZohoController::class,'getReceipts']);
});
