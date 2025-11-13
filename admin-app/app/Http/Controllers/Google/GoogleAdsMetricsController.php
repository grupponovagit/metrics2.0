<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V22\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V22\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V22\Services\SearchGoogleAdsRequest;
use Google\Ads\GoogleAds\V22\Services\ListAccessibleCustomersRequest;
use Illuminate\Http\Request;

class GoogleAdsMetricsController extends Controller
{
    /**
     * Recupera le metriche delle campagne (ultimi 7 giorni)
     */
    public function getCampaignMetrics(Request $request)
    {
        try {
            // 1. Ottieni il refresh token
            $refreshToken = GoogleAdsOauthController::getRefreshToken();
            
            if (empty($refreshToken)) {
                return response()->json([
                    'errore' => 'Refresh token non trovato',
                    'dettaglio' => 'Devi prima completare il flusso OAuth. Vai su /oauth/google-ads',
                    'link_oauth' => url('/oauth/google-ads')
                ], 401);
            }

            // 2. Costruisci le credenziali OAuth2
            $oAuth2Credential = (new OAuth2TokenBuilder())
                ->withClientId(env('GOOGLE_ADS_CLIENT_ID'))
                ->withClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'))
                ->withRefreshToken($refreshToken)
                ->build();

            // 3. Costruisci il Google Ads Client
            $googleAdsClientBuilder = (new GoogleAdsClientBuilder())
                ->withOAuth2Credential($oAuth2Credential)
                ->withDeveloperToken(env('GOOGLE_ADS_DEVELOPER_TOKEN'));

            // Aggiungi login customer ID se presente
            $loginCustomerId = env('GOOGLE_ADS_LOGIN_CUSTOMER_ID');
            if (!empty($loginCustomerId)) {
                $googleAdsClientBuilder->withLoginCustomerId(\trim(\str_replace(['-', ' '], '', (string) $loginCustomerId)));
            }

            $googleAdsClient = $googleAdsClientBuilder->build();

            // 4. Ottieni il customer ID dell'account operativo
            $customerId = env('GOOGLE_ADS_CUSTOMER_ID');
            if (empty($customerId)) {
                return response()->json([
                    'errore' => 'Customer ID non configurato',
                    'dettaglio' => 'Imposta GOOGLE_ADS_CUSTOMER_ID nel file .env'
                ], 400);
            }

            // Rimuovi trattini e spazi, converti a stringa
            $customerId = trim(str_replace(['-', ' '], '', (string)$customerId));

            // 5. Esegui query GAQL per le metriche delle campagne (ultimi 7 giorni)
            $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
            
            $query = <<<GAQL
                SELECT
                    campaign.id,
                    campaign.name,
                    campaign.status,
                    metrics.impressions,
                    metrics.clicks,
                    metrics.ctr,
                    metrics.average_cpc,
                    metrics.cost_micros,
                    metrics.conversions,
                    metrics.conversions_value,
                    metrics.cost_per_conversion
                FROM campaign
                WHERE segments.date DURING LAST_7_DAYS
                ORDER BY metrics.impressions DESC
GAQL;

            // Esegui search standard con request object per V16
            $searchRequest = new SearchGoogleAdsRequest();
            $searchRequest->setCustomerId($customerId);
            $searchRequest->setQuery($query);
            
            $response = $googleAdsServiceClient->search($searchRequest, []);

            // 6. Processa i risultati
            $campaigns = [];

            foreach ($response->iterateAllElements() as $googleAdsRow) {
                /** @var GoogleAdsRow $googleAdsRow */
                $campaign = $googleAdsRow->getCampaign();
                $metrics = $googleAdsRow->getMetrics();

                $campaigns[] = [
                    'id' => $campaign->getId(),
                    'nome' => $campaign->getName(),
                    'stato' => $campaign->getStatus(),
                    'impressioni' => $metrics->getImpressions(),
                    'click' => $metrics->getClicks(),
                    'ctr' => round($metrics->getCtr() * 100, 2) . '%',
                    'cpc_medio' => number_format($metrics->getAverageCpc() / 1_000_000, 2, ',', '.') . ' €',
                    'costo' => number_format($metrics->getCostMicros() / 1_000_000, 2, ',', '.') . ' €',
                    'conversioni' => round($metrics->getConversions(), 2),
                    'valore_conversioni' => number_format($metrics->getConversionsValue(), 2, ',', '.') . ' €',
                    'costo_per_conversione' => $metrics->getCostPerConversion()
                        ? number_format($metrics->getCostPerConversion() / 1_000_000, 2, ',', '.') . ' €'
                        : 'N/A'
                ];
            }

            return response()->json([
                'successo' => true,
                'periodo' => 'Ultimi 7 giorni',
                'customer_id' => $customerId,
                'numero_campagne' => count($campaigns),
                'campagne' => $campaigns
            ]);

        } catch (\Google\ApiCore\ApiException $e) {
            return response()->json([
                'errore' => 'Errore Google Ads API',
                'dettaglio' => $e->getMessage(),
                'status' => $e->getStatus(),
                'metadata' => $e->getMetadata()
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'errore' => 'Errore durante il recupero delle metriche',
                'dettaglio' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Test endpoint per verificare la configurazione
     */
    public function testConfiguration()
    {
        $config = [
            'client_id' => env('GOOGLE_ADS_CLIENT_ID') ? '✓ Configurato' : '✗ Mancante',
            'client_secret' => env('GOOGLE_ADS_CLIENT_SECRET') ? '✓ Configurato' : '✗ Mancante',
            'redirect_uri' => env('GOOGLE_ADS_REDIRECT_URI', 'Non configurato'),
            'developer_token' => env('GOOGLE_ADS_DEVELOPER_TOKEN') ? '✓ Configurato' : '✗ Mancante',
            'login_customer_id' => env('GOOGLE_ADS_LOGIN_CUSTOMER_ID') ? env('GOOGLE_ADS_LOGIN_CUSTOMER_ID') : 'Non configurato (opzionale)',
            'customer_id' => env('GOOGLE_ADS_CUSTOMER_ID') ? env('GOOGLE_ADS_CUSTOMER_ID') : '✗ Mancante',
            'refresh_token' => GoogleAdsOauthController::getRefreshToken() ? '✓ Presente' : '✗ Mancante',
        ];

        $allConfigured = 
            !str_contains($config['client_id'], '✗') &&
            !str_contains($config['client_secret'], '✗') &&
            !str_contains($config['developer_token'], '✗') &&
            !str_contains($config['customer_id'], '✗');

        return response()->json([
            'stato' => $allConfigured ? 'Configurazione completa' : 'Configurazione incompleta',
            'configurazione' => $config,
            'nota_importante' => '⚠️ Il Developer Token deve essere in modalità "Accesso di Base" o superiore per account di produzione',
            'azioni_necessarie' => $allConfigured ? [] : [
                'Completa le variabili d\'ambiente mancanti nel file .env',
                !str_contains($config['refresh_token'], '✓') ? 'Esegui il flusso OAuth su /oauth/google-ads' : null
            ]
        ]);
    }

    /**
     * Lista tutti gli account accessibili (diagnostica)
     */
    public function listAccessibleAccounts()
    {
        try {
            // 1. Ottieni il refresh token
            $refreshToken = GoogleAdsOauthController::getRefreshToken();
            
            if (empty($refreshToken)) {
                return response()->json([
                    'errore' => 'Refresh token non trovato',
                    'dettaglio' => 'Devi prima completare il flusso OAuth. Vai su /oauth/google-ads',
                    'link_oauth' => url('/oauth/google-ads')
                ], 401);
            }

            // 2. Costruisci le credenziali OAuth2
            $oAuth2Credential = (new OAuth2TokenBuilder())
                ->withClientId(env('GOOGLE_ADS_CLIENT_ID'))
                ->withClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'))
                ->withRefreshToken($refreshToken)
                ->build();

            // 3. Costruisci il Google Ads Client
            $googleAdsClientBuilder = (new GoogleAdsClientBuilder())
                ->withOAuth2Credential($oAuth2Credential)
                ->withDeveloperToken(env('GOOGLE_ADS_DEVELOPER_TOKEN'));

            // Usa il login customer se presente
            $loginCustomerId = env('GOOGLE_ADS_LOGIN_CUSTOMER_ID');
            if (!empty($loginCustomerId)) {
                $loginCustomerId = trim(str_replace(['-', ' '], '', (string)$loginCustomerId));
                $googleAdsClientBuilder->withLoginCustomerId($loginCustomerId);
            }

            $googleAdsClient = $googleAdsClientBuilder->build();

            // 4. Query per listare account accessibili
            $customerServiceClient = $googleAdsClient->getCustomerServiceClient();
            
            $accounts = [];
            
            // Se abbiamo un login customer, usa quello, altrimenti prova il customer_id
            $managerCustomerId = $loginCustomerId ?: trim(str_replace(['-', ' '], '', (string)env('GOOGLE_ADS_CUSTOMER_ID')));
            
            try {
                $listRequest = new ListAccessibleCustomersRequest();
                $customerResourceNames = $customerServiceClient->listAccessibleCustomers($listRequest, []);
                
                foreach ($customerResourceNames->getResourceNames() as $resourceName) {
                    // Estrai l'ID dal resource name (formato: "customers/1234567890")
                    $customerId = str_replace('customers/', '', $resourceName);
                    
                    $accounts[] = [
                        'customer_id' => $customerId,
                        'resource_name' => $resourceName,
                        'accessibile' => true
                    ];
                }
                
                return response()->json([
                    'successo' => true,
                    'messaggio' => 'Lista account accessibili con le tue credenziali',
                    'numero_account' => count($accounts),
                    'account' => $accounts,
                    'suggerimento' => 'Usa uno di questi Customer ID nel tuo .env come GOOGLE_ADS_CUSTOMER_ID'
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'errore' => 'Impossibile listare gli account',
                    'dettaglio' => $e->getMessage(),
                    'suggerimento' => 'Il Developer Token potrebbe essere in modalità "Account di prova". Richiedi accesso di base nel Centro API.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'errore' => 'Errore durante il recupero degli account',
                'dettaglio' => $e->getMessage()
            ], 500);
        }
    }
}

