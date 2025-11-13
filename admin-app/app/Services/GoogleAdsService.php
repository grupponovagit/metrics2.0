<?php

namespace App\Services;

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V22\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V22\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V22\Services\SearchGoogleAdsRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleAdsService
{
    /**
     * Recupera le metriche giornaliere per campagna da Google Ads API
     * 
     * @param string $accountId Google Ads Account ID (es: "966-937-4086")
     * @param string $date Data in formato Y-m-d
     * @return array
     */
    public function getCampaignMetricsByDate(string $accountId, string $date): array
    {
        try {
            // 1. Ottieni il refresh token per questo account
            $account = DB::table('account_agenzia')
                ->where('account_id', $accountId)
                ->first();

            if (!$account) {
                throw new \Exception("Account {$accountId} non trovato nel database");
            }

            if (empty($account->google_ads_refresh_token)) {
                throw new \Exception("Refresh token mancante per account {$accountId}. Esegui autenticazione OAuth.");
            }

            // 2. Costruisci le credenziali OAuth2
            $oAuth2Credential = (new OAuth2TokenBuilder())
                ->withClientId(env('GOOGLE_ADS_CLIENT_ID'))
                ->withClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'))
                ->withRefreshToken($account->google_ads_refresh_token)
                ->build();

            // 3. Costruisci il Google Ads Client
            $googleAdsClientBuilder = (new GoogleAdsClientBuilder())
                ->withOAuth2Credential($oAuth2Credential)
                ->withDeveloperToken($account->google_ads_developer_token);

            // Aggiungi login customer ID (MCC) se presente
            if (!empty($account->google_ads_mcc_id)) {
                $mccId = trim(str_replace(['-', ' '], '', (string) $account->google_ads_mcc_id));
                $googleAdsClientBuilder->withLoginCustomerId($mccId);
            }

            $googleAdsClient = $googleAdsClientBuilder->build();

            // 4. Rimuovi trattini dall'account ID per la query
            $cleanAccountId = trim(str_replace(['-', ' '], '', $accountId));

            // 5. Query GAQL per metriche giornaliere per campagna
            $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
            
            // STEP 1: Ottieni tutte le campagne attive (solo ENABLED, non in pausa)
            $campaignQuery = <<<GAQL
                SELECT
                    campaign.id,
                    campaign.name,
                    campaign.status
                FROM campaign
                WHERE campaign.status = 'ENABLED'
                ORDER BY campaign.name ASC
GAQL;

            $campaignRequest = new SearchGoogleAdsRequest();
            $campaignRequest->setCustomerId($cleanAccountId);
            $campaignRequest->setQuery($campaignQuery);
            
            $campaignResponse = $googleAdsServiceClient->search($campaignRequest, []);
            
            // STEP 2: Per ogni campagna, recupera le metriche del giorno specifico
            $campaigns = [];
            
            foreach ($campaignResponse->iterateAllElements() as $campaignRow) {
                $campaign = $campaignRow->getCampaign();
                $campaignId = $campaign->getId();
                $campaignName = $campaign->getName();
                
                // Query per le metriche di questa specifica campagna in questo giorno
                $metricsQuery = <<<GAQL
                    SELECT
                        campaign.name,
                        segments.date,
                        metrics.cost_micros,
                        metrics.clicks,
                        metrics.conversions
                    FROM campaign
                    WHERE campaign.id = {$campaignId}
                        AND segments.date = '{$date}'
GAQL;

                $metricsRequest = new SearchGoogleAdsRequest();
                $metricsRequest->setCustomerId($cleanAccountId);
                $metricsRequest->setQuery($metricsQuery);
                
                try {
                    $metricsResponse = $googleAdsServiceClient->search($metricsRequest, []);
                    
                    $hasData = false;
                    foreach ($metricsResponse->iterateAllElements() as $metricsRow) {
                        $metrics = $metricsRow->getMetrics();
                        $segments = $metricsRow->getSegments();
                        
                        $cost = $metrics->getCostMicros() / 1_000_000;
                        $clicks = $metrics->getClicks();
                        $conversions = $metrics->getConversions();
                        
                        $campaigns[] = [
                            'utm_campaign' => $campaignName,
                            'data' => $segments->getDate(),
                            'importo_speso' => round($cost, 2),
                            'clicks' => $clicks,
                            'conversioni' => round($conversions, 2),
                        ];
                        
                        $hasData = true;
                        break; // Una campagna = un record al giorno
                    }
                    
                    // Se non ci sono dati, inserisci comunque con valori a 0
                    if (!$hasData) {
                        $campaigns[] = [
                            'utm_campaign' => $campaignName,
                            'data' => $date,
                            'importo_speso' => 0.00,
                            'clicks' => 0,
                            'conversioni' => 0.00,
                        ];
                    }
                    
                } catch (\Exception $e) {
                    // Se errore nella query metriche, inserisci a 0
                    $campaigns[] = [
                        'utm_campaign' => $campaignName,
                        'data' => $date,
                        'importo_speso' => 0.00,
                        'clicks' => 0,
                        'conversioni' => 0.00,
                    ];
                }
            }

            Log::info("Google Ads API: recuperate {count} campagne per account {$accountId} data {$date}", [
                'count' => count($campaigns),
                'account_id' => $accountId,
                'date' => $date
            ]);

            return $campaigns;

        } catch (\Google\ApiCore\ApiException $e) {
            Log::error("Google Ads API Error per account {$accountId}", [
                'message' => $e->getMessage(),
                'status' => $e->getStatus(),
                'metadata' => $e->getMetadata(),
                'date' => $date
            ]);
            throw new \Exception("Errore Google Ads API: {$e->getMessage()}");
            
        } catch (\Exception $e) {
            Log::error("Errore recupero metriche Google Ads per account {$accountId}", [
                'message' => $e->getMessage(),
                'date' => $date
            ]);
            throw $e;
        }
    }

    /**
     * Sincronizza i dati nel database leads_costi_digital
     * 
     * @param string $accountId
     * @param array $campaigns
     * @param bool $isToday Se true, usa updateOrInsert per sovrascrivere i dati di oggi
     * @return int Numero di record inseriti/aggiornati
     */
    public function syncToDatabase(string $accountId, array $campaigns, bool $isToday = false): int
    {
        $count = 0;

        foreach ($campaigns as $campaign) {
            $data = [
                'id_account' => $accountId,
                'data' => $campaign['data'],
                'utm_campaign' => $campaign['utm_campaign'],
                'importo_speso' => $campaign['importo_speso'],
                'clicks' => $campaign['clicks'],
                'conversioni' => $campaign['conversioni'],
            ];

            if ($isToday) {
                // Per il giorno corrente, aggiorna sempre (sovrascrive)
                DB::table('leads_costi_digital')
                    ->updateOrInsert(
                        [
                            'id_account' => $accountId,
                            'data' => $campaign['data'],
                            'utm_campaign' => $campaign['utm_campaign'],
                        ],
                        array_merge($data, [
                            'updated_at' => now()
                        ])
                    );
            } else {
                // Per date passate, inserisci solo se non esiste già
                $exists = DB::table('leads_costi_digital')
                    ->where('id_account', $accountId)
                    ->where('data', $campaign['data'])
                    ->where('utm_campaign', $campaign['utm_campaign'])
                    ->exists();

                if (!$exists) {
                    DB::table('leads_costi_digital')->insert(array_merge($data, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                } else {
                    // Se esiste già, aggiorna comunque (utile per correzioni)
                    DB::table('leads_costi_digital')
                        ->where('id_account', $accountId)
                        ->where('data', $campaign['data'])
                        ->where('utm_campaign', $campaign['utm_campaign'])
                        ->update(array_merge($data, [
                            'updated_at' => now()
                        ]));
                }
            }

            $count++;
        }

        return $count;
    }

    /**
     * Ottieni tutti gli account configurati con Google Ads
     * 
     * @return array
     */
    public function getConfiguredAccounts(): array
    {
        return DB::table('account_agenzia')
            ->whereNotNull('google_ads_refresh_token')
            ->whereNotNull('google_ads_developer_token')
            ->select('account_id', 'ragione_sociale', 'google_ads_mcc_id')
            ->get()
            ->toArray();
    }
}

