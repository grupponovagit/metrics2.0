<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GoogleAdsOauthController extends Controller
{
    /**
     * Redirect all'URL di autorizzazione Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            $client = $this->getGoogleClient();
            
            // Force offline access e consent prompt per ottenere il refresh token
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            
            $authUrl = $client->createAuthUrl();
            
            return redirect($authUrl);
            
        } catch (\Exception $e) {
            return response()->json([
                'errore' => 'Impossibile creare l\'URL di autorizzazione',
                'dettaglio' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback OAuth - gestisce lo scambio code→token
     */
    public function handleCallback(Request $request)
    {
        try {
            // Verifica presenza del code
            if (!$request->has('code')) {
                $error = $request->get('error', 'Nessun codice di autorizzazione ricevuto');
                return response()->json([
                    'errore' => 'Autorizzazione fallita',
                    'dettaglio' => $error
                ], 400);
            }

            $client = $this->getGoogleClient();
            
            // Scambia il code con i token
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            
            // Verifica errori nella risposta
            if (isset($token['error'])) {
                return response()->json([
                    'errore' => 'Errore nello scambio del codice',
                    'dettaglio' => $token['error_description'] ?? $token['error']
                ], 500);
            }

            // Verifica presenza del refresh_token
            if (!isset($token['refresh_token'])) {
                return response()->json([
                    'errore' => 'Nessun refresh_token ricevuto',
                    'dettaglio' => 'Assicurati di aver revocato gli accessi precedenti e riprova. Il refresh_token viene fornito solo al primo consenso.',
                    'suggerimento' => 'Vai su https://myaccount.google.com/permissions e revoca l\'accesso all\'applicazione, poi riprova.'
                ], 400);
            }

            $refreshToken = $token['refresh_token'];

            // Salva il refresh_token nel file locale
            $saved = Storage::put('google_ads_refresh_token.txt', $refreshToken);

            if (!$saved) {
                return response()->json([
                    'errore' => 'Impossibile salvare il refresh_token',
                    'dettaglio' => 'Verifica i permessi della cartella storage/app'
                ], 500);
            }

            // Successo! Redirect alla rotta di test
            return redirect('/google-ads/campaigns')->with('success', 'Autenticazione completata! Refresh token salvato con successo.');

        } catch (\Exception $e) {
            return response()->json([
                'errore' => 'Errore durante il callback OAuth',
                'dettaglio' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea e configura il client Google OAuth
     */
    private function getGoogleClient(): \Google\Client
    {
        $client = new \Google\Client();
        
        $client->setClientId(env('GOOGLE_ADS_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_ADS_REDIRECT_URI'));
        
        // Scope necessario per Google Ads
        $client->addScope('https://www.googleapis.com/auth/adwords');
        
        return $client;
    }

    /**
     * Helper per ottenere il refresh token (da file o env)
     */
    public static function getRefreshToken(): ?string
    {
        // Prova prima dal file
        if (Storage::exists('google_ads_refresh_token.txt')) {
            $token = Storage::get('google_ads_refresh_token.txt');
            if (!empty(trim($token))) {
                return trim($token);
            }
        }

        // Fallback su variabile d'ambiente
        return env('GOOGLE_ADS_REFRESH_TOKEN');
    }
}

