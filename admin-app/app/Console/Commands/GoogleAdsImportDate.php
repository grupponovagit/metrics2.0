<?php

namespace App\Console\Commands;

use App\Services\GoogleAdsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GoogleAdsImportDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleads:import-date 
                            {date : Data da importare in formato YYYY-MM-DD}
                            {--account= : Account ID specifico (opzionale, altrimenti tutti)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa le metriche Google Ads per una data specifica (manuale)';

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
        $dateInput = $this->argument('date');
        $specificAccount = $this->option('account');

        // Valida la data
        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateInput);
            $dateFormatted = $date->format('Y-m-d');
        } catch (\Exception $e) {
            $this->error("❌ Formato data non valido. Usa YYYY-MM-DD (es: 2025-11-12)");
            return Command::FAILURE;
        }

        // Verifica che la data non sia futura
        if ($date->isFuture()) {
            $this->error("❌ Non puoi importare dati per una data futura!");
            return Command::FAILURE;
        }

        $this->info("🚀 Inizio import dati Google Ads per il giorno: {$dateFormatted}");
        $this->newLine();

        // Ottieni gli account da processare
        if ($specificAccount) {
            $accounts = collect([$this->googleAdsService->getConfiguredAccounts()])
                ->flatten(1)
                ->where('account_id', $specificAccount)
                ->values()
                ->all();

            if (empty($accounts)) {
                $this->error("❌ Account {$specificAccount} non trovato o non configurato.");
                return Command::FAILURE;
            }
        } else {
            $accounts = $this->googleAdsService->getConfiguredAccounts();
        }

        if (empty($accounts)) {
            $this->error('❌ Nessun account configurato con Google Ads trovato.');
            return Command::FAILURE;
        }

        $this->info("📊 Account da processare: " . count($accounts));
        $this->newLine();

        $totalImported = 0;
        $successCount = 0;
        $errorCount = 0;

        // Chiedi conferma prima di procedere
        if (!$this->confirm("Vuoi procedere con l'import/aggiornamento dei dati?", true)) {
            $this->warn("⚠️  Operazione annullata dall'utente");
            return Command::FAILURE;
        }

        $this->newLine();

        foreach ($accounts as $account) {
            $this->line("📍 Processing: {$account->ragione_sociale} ({$account->account_id})");

            try {
                // Recupera le metriche da Google Ads API
                $campaigns = $this->googleAdsService->getCampaignMetricsByDate(
                    $account->account_id,
                    $dateFormatted
                );

                if (empty($campaigns)) {
                    $this->warn("  ⚠️  Nessuna campagna trovata per questo account");
                    continue;
                }

                // Determina se è oggi (per la logica di upsert)
                $isToday = $date->isToday();

                // Sincronizza nel database
                $imported = $this->googleAdsService->syncToDatabase(
                    $account->account_id,
                    $campaigns,
                    $isToday
                );

                $totalImported += $imported;
                $successCount++;

                $this->info("  ✅ Importate/aggiornate {$imported} campagne");

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ❌ Errore: {$e->getMessage()}");
            }

            $this->newLine();
        }

        // Riepilogo finale
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📈 RIEPILOGO IMPORT MANUALE");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("📅 Data: {$dateFormatted}");
        $this->line("✅ Account processati con successo: {$successCount}");
        $this->line("❌ Account con errori: {$errorCount}");
        $this->line("📊 Totale record importati/aggiornati: {$totalImported}");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

