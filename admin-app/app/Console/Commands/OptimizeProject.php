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
        
        // Step 1: Pulisce tutte le cache esistenti
        $this->info('🧹 Step 1/5: Pulizia cache esistente...');
        $this->call('optimize:clear');
        $this->info('✅ Cache pulita');
        $this->newLine();
        
        // Step 2: Ricarica configurazione da .env (IMPORTANTE!)
        $this->info('🔧 Step 2/5: Ricaricamento configurazione da .env...');
        $this->call('config:clear');
        $this->call('config:cache');
        $this->info('✅ Configurazione ricaricata (include credenziali Google Ads)');
        $this->newLine();
        
        // Step 3: Cache routes
        $this->info('⚡ Step 3/5: Ottimizzazione route...');
        $this->call('route:cache');
        $this->info('✅ Route ottimizzate');
        $this->newLine();
        
        // Step 4: Cache views
        $this->info('🎨 Step 4/5: Compilazione views...');
        $this->call('view:cache');
        $this->info('✅ Views compilate');
        $this->newLine();
        
        // Step 5: Cache blade icons (se disponibile)
        $this->info('🎯 Step 5/5: Ottimizzazioni finali...');
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
        $this->comment('💡 Se modifichi il .env, riesegui: php artisan project:optimize');
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
