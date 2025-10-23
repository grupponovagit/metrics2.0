<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitKpiTargetMesi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:init-mesi 
                            {--anno-inizio=2024 : Anno di inizio}
                            {--anno-fine=2026 : Anno di fine}
                            {--mese-base=10 : Mese base da cui copiare le combinazioni}
                            {--anno-base=2025 : Anno base da cui copiare le combinazioni}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inizializza i KPI Target per tutti i mesi con valore 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $annoInizio = (int) $this->option('anno-inizio');
        $annoFine = (int) $this->option('anno-fine');
        $meseBase = (int) $this->option('mese-base');
        $annoBase = (int) $this->option('anno-base');
        
        $this->info('=== INIZIALIZZAZIONE KPI TARGET MENSILI ===');
        $this->newLine();
        
        // Prendi le combinazioni base
        $this->info("Caricamento combinazioni base da {$annoBase}-{$meseBase}...");
        $kpiBase = DB::table('kpi_target_mensile')
            ->select('commessa', 'sede_crm', 'sede_estesa', 'nome_kpi')
            ->where('anno', $annoBase)
            ->where('mese', $meseBase)
            ->distinct()
            ->get();
        
        if ($kpiBase->isEmpty()) {
            $this->error('Nessuna combinazione base trovata!');
            return 1;
        }
        
        $this->info("Trovate {$kpiBase->count()} combinazioni uniche");
        $this->newLine();
        
        $recordCreati = 0;
        $recordEsistenti = 0;
        
        $progressBar = $this->output->createProgressBar(($annoFine - $annoInizio + 1) * 12 * $kpiBase->count());
        
        for ($anno = $annoInizio; $anno <= $annoFine; $anno++) {
            for ($mese = 1; $mese <= 12; $mese++) {
                foreach ($kpiBase as $kpi) {
                    // Verifica se esiste già
                    $exists = DB::table('kpi_target_mensile')
                        ->where('commessa', $kpi->commessa)
                        ->where('sede_crm', $kpi->sede_crm)
                        ->where('nome_kpi', $kpi->nome_kpi)
                        ->where('anno', $anno)
                        ->where('mese', $mese)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('kpi_target_mensile')->insert([
                            'commessa' => $kpi->commessa,
                            'sede_crm' => $kpi->sede_crm,
                            'sede_estesa' => $kpi->sede_estesa,
                            'nome_kpi' => $kpi->nome_kpi,
                            'anno' => $anno,
                            'mese' => $mese,
                            'valore_kpi' => 0
                        ]);
                        $recordCreati++;
                    } else {
                        $recordEsistenti++;
                    }
                    
                    $progressBar->advance();
                }
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info('=== RIEPILOGO ===');
        $this->table(
            ['Descrizione', 'Quantità'],
            [
                ['Record creati', $recordCreati],
                ['Record già esistenti', $recordEsistenti],
                ['Totale record in tabella', DB::table('kpi_target_mensile')->count()],
            ]
        );
        
        $this->newLine();
        $this->info('✓ Inizializzazione completata con successo!');
        
        return 0;
    }
}

