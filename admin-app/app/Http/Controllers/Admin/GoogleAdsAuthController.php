<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Google\Client as GoogleClient;

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
            $refreshToken = $mccAccounts->first()->google_ads_refresh_token;
            $hasToken = $refreshToken ? true : false;
            $tokenExpires = $mccAccounts->first()->google_ads_token_expires_at;
            $developerToken = $mccAccounts->first()->google_ads_developer_token;
            
            // Verifica in tempo reale se il token è ancora valido
            $tokenValid = false;
            $tokenStatus = 'missing';
            
            if ($hasToken) {
                $tokenValid = $this->testToken($refreshToken);
                $tokenStatus = $tokenValid ? 'valid' : 'invalid';
            }
            
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
                'token_valid' => $tokenValid,
                'token_status' => $tokenStatus,
                'token_expires' => $tokenExpires,
                'developer_token' => $developerToken,
                'accounts' => $mccAccounts
            ];
        }
        
        return view('admin.modules.ict.google-ads-auth', [
            'mccGroups' => $mccGroups
        ]);
    }

    /**
     * Testa se un refresh token è ancora valido
     *
     * @param string $refreshToken
     * @return bool
     */
    private function testToken(string $refreshToken): bool
    {
        try {
            $client = new GoogleClient();
            $client->setClientId(env('GOOGLE_ADS_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'));
            
            // Prova a fare il refresh del token
            $token = $client->refreshToken($refreshToken);
            
            // Se arriviamo qui senza eccezioni, il token è valido
            return isset($token['access_token']);
            
        } catch (\Exception $e) {
            // Se c'è un'eccezione, il token è invalido
            return false;
        }
    }
}
