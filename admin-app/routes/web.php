<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Google\GoogleAdsOauthController;
use App\Http\Controllers\Google\GoogleAdsMetricsController;

Route::get('/', function () {
    return view('welcome');
});

// Redirect diretto all'admin per utenti autenticati
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== GOOGLE ADS API - OAuth Flow =====
// Pagina gestione autenticazioni
Route::get('/google-ads/auth-manager', function () {
    return view('google-ads-auth');
})->name('google-ads.auth-manager');

// Step 1: Redirect a Google per autorizzazione (con MCC ID opzionale)
Route::get('/oauth/google-ads/{mccId?}', [GoogleAdsOauthController::class, 'redirectToGoogle'])
    ->name('google-ads.oauth.redirect');

// Step 2: Callback OAuth - gestisce code→token
Route::get('/oauth/google-ads/callback', [GoogleAdsOauthController::class, 'handleCallback'])
    ->name('google-ads.oauth.callback');

// ===== GOOGLE ADS API - Metriche =====
// Ottieni metriche campagne (ultimi 7 giorni)
Route::get('/google-ads/campaigns', [GoogleAdsMetricsController::class, 'getCampaignMetrics'])
    ->name('google-ads.campaigns');

// Test configurazione
Route::get('/google-ads/test-config', [GoogleAdsMetricsController::class, 'testConfiguration'])
    ->name('google-ads.test-config');

// Lista account accessibili (diagnostica)
Route::get('/google-ads/test-accounts', [GoogleAdsMetricsController::class, 'listAccessibleAccounts'])
    ->name('google-ads.test-accounts');

require __DIR__ . '/auth.php';
