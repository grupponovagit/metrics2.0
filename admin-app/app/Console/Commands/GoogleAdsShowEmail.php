<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;

class GoogleAdsShowEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleads:show-email {mcc_id? : MCC ID specifico (opzionale)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostra quale email è associata ai token OAuth di Google Ads';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mccIdFilter = $this->argument('mcc_id');

        $this->info("🔍 RECUPERO EMAIL OAUTH GOOGLE ADS");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->newLine();

        // Ottieni gli account configurati
        $query = DB::table('account_agenzia')
            ->whereNotNull('google_ads_refresh_token');

        if ($mccIdFilter) {
            $query->where('google_ads_mcc_id', $mccIdFilter);
        }

        $accounts = $query->select(
            'google_ads_mcc_id',
            'account_id',
            'ragione_sociale',
            'google_ads_refresh_token'
        )
        ->get();

        if ($accounts->isEmpty()) {
            $this->error("❌ Nessun account trovato con refresh token");
            return Command::FAILURE;
        }

        // Raggruppa per MCC
        $groupedByMcc = $accounts->groupBy('google_ads_mcc_id');

        foreach ($groupedByMcc as $mccId => $mccAccounts) {
            $this->line("📊 <fg=cyan>MCC ID: {$mccId}</>");
            $this->newLine();

            // Prendi il primo account per ottenere il refresh token (è uguale per tutti)
            $firstAccount = $mccAccounts->first();

            try {
                // Ottieni un access token dal refresh token
                $oAuth2Credential = (new OAuth2TokenBuilder())
                    ->withClientId(env('GOOGLE_ADS_CLIENT_ID'))
                    ->withClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'))
                    ->withRefreshToken($firstAccount->google_ads_refresh_token)
                    ->build();

                $accessToken = $oAuth2Credential->fetchAuthToken();

                if (isset($accessToken['access_token'])) {
                    // Usa Google Client per ottenere info utente
                    $client = new \Google\Client();
                    $client->setAccessToken($accessToken['access_token']);
                    
                    try {
                        $oauth2 = new \Google\Service\Oauth2($client);
                        $userInfo = $oauth2->userinfo->get();
                        
                        $this->line("   ✅ <fg=green>EMAIL AUTENTICATA: {$userInfo->email}</>");
                        $this->line("   📧 Nome: " . ($userInfo->name ?? 'N/A'));
                        $this->line("   🔐 Email verificata: " . ($userInfo->verifiedEmail ? 'Sì' : 'No'));
                        
                        $this->newLine();
                        $this->line("   📋 <fg=yellow>AZIONE NECESSARIA:</>");
                        $this->line("   └─ Assicurati che <fg=white>{$userInfo->email}</> abbia accesso AMMINISTRATORE");
                        $this->line("      agli account Google Ads:");
                        
                        foreach ($mccAccounts as $acc) {
                            $this->line("      • {$acc->ragione_sociale} ({$acc->account_id})");
                        }
                        
                    } catch (\Exception $e) {
                        $this->warn("   ⚠️  Impossibile recuperare email dall'access token");
                        $this->line("      Errore: " . $e->getMessage());
                    }
                } else {
                    $this->error("   ❌ Impossibile ottenere access token");
                }

            } catch (\Exception $e) {
                $this->error("   ❌ Errore nel recupero del token: " . $e->getMessage());
            }

            $this->newLine();
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();
        }

        $this->info("💡 ISTRUZIONI PER DARE ACCESSO:");
        $this->line("   1. Vai su https://ads.google.com");
        $this->line("   2. Seleziona l'account Google Ads");
        $this->line("   3. Vai in: Strumenti e impostazioni > Accesso e sicurezza > Utenti");
        $this->line("   4. Clicca su '+' per invitare un utente");
        $this->line("   5. Inserisci l'email mostrata sopra");
        $this->line("   6. Assegna il ruolo 'Amministratore'");
        $this->line("   7. Invia l'invito");
        $this->newLine();

        return Command::SUCCESS;
    }
}

