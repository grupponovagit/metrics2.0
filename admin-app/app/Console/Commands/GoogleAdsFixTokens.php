<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Google\Client as GoogleClient;

class GoogleAdsFixTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleads:fix-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e fornisce istruzioni per risolvere i token Google Ads scaduti/revocati';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🔧 FIX TOKEN GOOGLE ADS - DIAGNOSI E RISOLUZIONE");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->newLine();

        // 1. Ottieni tutti gli MCC configurati
        $mccGroups = DB::table('account_agenzia')
            ->whereNotNull('google_ads_mcc_id')
            ->whereNotNull('google_ads_refresh_token')
            ->select('google_ads_mcc_id', 'google_ads_refresh_token', 'ragione_sociale', 'account_id')
            ->get()
            ->groupBy('google_ads_mcc_id');

        if ($mccGroups->isEmpty()) {
            $this->error("❌ Nessun account Google Ads configurato trovato.");
            return Command::FAILURE;
        }

        $this->info("📊 Trovati " . $mccGroups->count() . " MCC ID con account configurati");
        $this->newLine();

        $invalidTokens = [];
        $validTokens = [];

        // 2. Testa ogni token
        foreach ($mccGroups as $mccId => $accounts) {
            $firstAccount = $accounts->first();
            $refreshToken = $firstAccount->google_ads_refresh_token;

            $this->line("🔍 Verifico MCC ID: {$mccId}");
            $this->line("   Account collegati: " . $accounts->count());

            // Testa il token
            $isValid = $this->testToken($refreshToken);

            if ($isValid) {
                $this->info("   ✅ Token VALIDO");
                $validTokens[] = $mccId;
            } else {
                $this->error("   ❌ Token SCADUTO/REVOCATO");
                $invalidTokens[] = [
                    'mcc_id' => $mccId,
                    'accounts' => $accounts
                ];
            }

            $this->newLine();
        }

        // 3. Riepilogo
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📈 RIEPILOGO");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("✅ MCC con token validi: " . count($validTokens));
        $this->line("❌ MCC con token invalidi: " . count($invalidTokens));
        $this->newLine();

        // 4. Se ci sono token invalidi, fornisci le istruzioni
        if (!empty($invalidTokens)) {
            $this->warn("⚠️  AZIONE RICHIESTA: I seguenti MCC devono essere ri-autenticati");
            $this->newLine();

            $baseUrl = env('APP_URL', 'http://127.0.0.1:8000');

            foreach ($invalidTokens as $invalid) {
                $mccId = $invalid['mcc_id'];
                $accounts = $invalid['accounts'];

                $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
                $this->line("📌 MCC ID: {$mccId}");
                $this->line("   Account coinvolti:");
                foreach ($accounts as $acc) {
                    $this->line("   - {$acc->ragione_sociale} ({$acc->account_id})");
                }
                $this->newLine();

                // URL per ri-autenticare
                $authUrl = "{$baseUrl}/oauth/google-ads/{$mccId}";

                $this->info("🔗 Per ri-autenticare questo MCC, vai a:");
                $this->line("   {$authUrl}");
                $this->newLine();

                $this->line("📋 OPPURE esegui da terminale:");
                $this->line("   open \"{$authUrl}\"");
                $this->newLine();
            }

            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->warn("⚠️  IMPORTANTE:");
            $this->line("1. Prima di cliccare sui link, revoca l'accesso precedente qui:");
            $this->line("   https://myaccount.google.com/permissions");
            $this->newLine();
            $this->line("2. Cerca l'applicazione relativa a Google Ads e revoca l'accesso");
            $this->newLine();
            $this->line("3. Poi clicca sui link sopra per ri-autenticare");
            $this->newLine();
            $this->line("4. Assicurati di usare l'account Google che ha accesso al MCC specificato");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            return Command::FAILURE;
        }

        $this->info("🎉 Tutti i token Google Ads sono validi!");
        return Command::SUCCESS;
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

