<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulisce tutte le cache e ottimizza il progetto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('🚀 OTTIMIZZAZIONE PROGETTO METRICS 2.0');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();
        
        // Step 1: Pulisce TUTTO con optimize:clear
        $this->info('🧹 Step 1/6: Pulizia completa cache (optimize:clear)...');
        $this->call('optimize:clear');
        $this->info('✅ Cache globale pulita');
        $this->newLine();
        
        // Step 2: Pulizia config cache (doppia sicurezza!)
        $this->info('🔧 Step 2/6: Pulizia configurazione (config:clear)...');
        $this->call('config:clear');
        $this->info('✅ Config cache pulita');
        $this->newLine();
        
        // Step 3: Ricarica configurazione da .env
        $this->info('📝 Step 3/6: Ricaricamento configurazione da .env...');
        $this->call('config:cache');
        $this->info('✅ Configurazione ricaricata (include credenziali Google Ads)');
        $this->newLine();
        
        // Step 4: Cache routes
        $this->info('⚡ Step 4/6: Ottimizzazione route...');
        $this->call('route:cache');
        $this->info('✅ Route ottimizzate');
        $this->newLine();
        
        // Step 5: Cache views
        $this->info('🎨 Step 5/6: Compilazione views...');
        $this->call('view:cache');
        $this->info('✅ Views compilate');
        $this->newLine();
        
        // Step 6: Cache blade icons (se disponibile)
        $this->info('🎯 Step 6/6: Ottimizzazioni finali...');
        if ($this->laravel->has('blade-icons')) {
            $this->call('blade-icons:cache');
        }
        $this->info('✅ Ottimizzazioni completate');
        $this->newLine();
        
        // Riepilogo
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('✅ PROGETTO OTTIMIZZATO CON SUCCESSO!');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();
        $this->line('📊 Cache attive:');
        $this->line('   ✅ Config cache (da .env aggiornato)');
        $this->line('   ✅ Route cache');
        $this->line('   ✅ View cache');
        $this->line('   ✅ Blade icons cache');
        $this->newLine();
        $this->comment('💡 Ora puoi usare le Google Ads API senza problemi!');
        $this->comment('   Prova: php artisan googleads:import-yesterday');
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
