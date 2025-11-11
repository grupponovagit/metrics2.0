<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\GoogleAdsWebhookController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth:sanctum')->get('/user-details', [UserController::class, 'userDetails']);

Route::middleware('auth:sanctum')->post('/user-logout', [UserController::class, 'logOutUser']);

// ===== GOOGLE ADS WEBHOOK (Script esterno) =====
// Endpoint per ricevere dati da Google Ads Script (NO AUTH)
Route::post('/google-ads/webhook/leads-costi', [GoogleAdsWebhookController::class, 'saveLeadsCosti'])
    ->name('api.google-ads.webhook.leads-costi');

// Endpoint di test
Route::get('/google-ads/webhook/test', [GoogleAdsWebhookController::class, 'test'])
    ->name('api.google-ads.webhook.test');

