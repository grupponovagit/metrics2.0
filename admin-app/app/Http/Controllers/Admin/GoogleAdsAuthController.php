<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoogleAdsAuthController extends Controller
{
    /**
     * Mostra la pagina di gestione autenticazioni Google Ads
     */
    public function index()
    {
        // Recupera tutti gli account Google Ads dal database
        $accounts = DB::table('account_agenzia')
            ->where('provenienza', 'Google ADS')
            ->orderBy('google_ads_mcc_id')
            ->select(
                'id',
                'ragione_sociale',
                'account_id',
                'google_ads_mcc_id',
                'google_ads_refresh_token',
                'google_ads_token_expires_at',
                'google_ads_developer_token'
            )
            ->get();
        
        // Raggruppa per MCC
        $accountsByMcc = $accounts->groupBy('google_ads_mcc_id');
        
        // Prepara i dati per MCC
        $mccGroups = [];
        foreach ($accountsByMcc as $mccId => $mccAccounts) {
            $hasToken = $mccAccounts->first()->google_ads_refresh_token ? true : false;
            $tokenExpires = $mccAccounts->first()->google_ads_token_expires_at;
            $developerToken = $mccAccounts->first()->google_ads_developer_token;
            
            // Determina l'email associata (basato su quale account)
            $email = 'Non specificata';
            $firstAccount = $mccAccounts->first();
            if (str_contains(strtolower($firstAccount->ragione_sociale), 'meglioquesto')) {
                $email = 'marketing.digital@meglioquesto.it';
            } elseif (str_contains(strtolower($firstAccount->ragione_sociale), 'gt')) {
                $email = 'pasquale.rizzo@novaholding.it';
            } else {
                $email = 'pasquale.rizzo@novaholding.it';
            }
            
            $mccGroups[] = [
                'mcc_id' => $mccId,
                'email' => $email,
                'has_token' => $hasToken,
                'token_expires' => $tokenExpires,
                'developer_token' => $developerToken,
                'accounts' => $mccAccounts
            ];
        }
        
        return view('admin.modules.ict.google-ads-auth', [
            'mccGroups' => $mccGroups
        ]);
    }
}
