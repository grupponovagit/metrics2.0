<?php

namespace App\Console\Commands;

use App\Services\GoogleAdsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GoogleAdsImportYesterday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleads:import-yesterday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa le metriche Google Ads del giorno precedente per tutti gli account configurati';

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
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        
        $this->info("🚀 Inizio import dati Google Ads per il giorno: {$yesterday}");
        $this->newLine();

        // Ottieni tutti gli account configurati
        $accounts = $this->googleAdsService->getConfiguredAccounts();

        if (empty($accounts)) {
            $this->error('❌ Nessun account configurato con Google Ads trovato.');
            return Command::FAILURE;
        }

        $this->info("📊 Trovati " . count($accounts) . " account configurati");
        $this->newLine();

        $totalImported = 0;
        $successCount = 0;
        $errorCount = 0;

        foreach ($accounts as $account) {
            $this->line("📍 Processing: {$account->ragione_sociale} ({$account->account_id})");

            try {
                // Recupera le metriche da Google Ads API
                $campaigns = $this->googleAdsService->getCampaignMetricsByDate(
                    $account->account_id,
                    $yesterday
                );

                if (empty($campaigns)) {
                    $this->warn("  ⚠️  Nessuna campagna trovata per questo account");
                    continue;
                }

                // Sincronizza nel database (non è oggi, quindi non sovrascrive)
                $imported = $this->googleAdsService->syncToDatabase(
                    $account->account_id,
                    $campaigns,
                    false // Non è oggi
                );

                $totalImported += $imported;
                $successCount++;

                $this->info("  ✅ Importate {$imported} campagne");

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ❌ Errore: {$e->getMessage()}");
            }

            $this->newLine();
        }

        // Riepilogo finale
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📈 RIEPILOGO IMPORT");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("📅 Data: {$yesterday}");
        $this->line("✅ Account processati con successo: {$successCount}");
        $this->line("❌ Account con errori: {$errorCount}");
        $this->line("📊 Totale record importati: {$totalImported}");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

