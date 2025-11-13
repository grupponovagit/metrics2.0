<?php

namespace App\Console\Commands;

use App\Services\GoogleAdsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GoogleAdsUpdateToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleads:update-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggiorna le metriche Google Ads del giorno corrente (sovrascrive i dati esistenti)';

    protected $googleAdsService;

    public function __construct(GoogleAdsService $googleAdsService)
    {
        parent::__construct();
        $this->googleAdsService = $googleAdsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $this->info("🔄 Inizio aggiornamento dati Google Ads per OGGI: {$today}");
        $this->newLine();

        // Ottieni tutti gli account configurati
        $accounts = $this->googleAdsService->getConfiguredAccounts();

        if (empty($accounts)) {
            $this->error('❌ Nessun account configurato con Google Ads trovato.');
            return Command::FAILURE;
        }

        $this->info("📊 Trovati " . count($accounts) . " account configurati");
        $this->newLine();

        $totalUpdated = 0;
        $successCount = 0;
        $errorCount = 0;

        foreach ($accounts as $account) {
            $this->line("📍 Processing: {$account->ragione_sociale} ({$account->account_id})");

            try {
                // Recupera le metriche AGGIORNATE da Google Ads API
                $campaigns = $this->googleAdsService->getCampaignMetricsByDate(
                    $account->account_id,
                    $today
                );

                if (empty($campaigns)) {
                    $this->warn("  ⚠️  Nessuna campagna attiva oggi per questo account");
                    continue;
                }

                // Sincronizza nel database (SOVRASCRIVE i dati di oggi)
                $updated = $this->googleAdsService->syncToDatabase(
                    $account->account_id,
                    $campaigns,
                    true // È oggi, quindi usa updateOrInsert
                );

                $totalUpdated += $updated;
                $successCount++;

                $this->info("  ✅ Aggiornate {$updated} campagne");

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ❌ Errore: {$e->getMessage()}");
            }

            $this->newLine();
        }

        // Riepilogo finale
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🔄 RIEPILOGO AGGIORNAMENTO OGGI");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("📅 Data: {$today}");
        $this->line("⏰ Ora esecuzione: " . now()->format('H:i:s'));
        $this->line("✅ Account processati con successo: {$successCount}");
        $this->line("❌ Account con errori: {$errorCount}");
        $this->line("📊 Totale record aggiornati: {$totalUpdated}");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

