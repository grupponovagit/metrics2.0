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
        $this->info('🧹 Pulizia cache in corso...');
        $this->newLine();
        
        // Pulisce tutte le cache
        $this->call('optimize:clear');
        $this->newLine();
        
        $this->info('📦 Creazione cache ottimizzate...');
        $this->newLine();
        
        // Cache configurazione
        $this->call('config:cache');
        
        // Cache routes
        $this->call('route:cache');
        
        // Cache views
        $this->call('view:cache');
        
        // Cache blade icons
        if ($this->laravel->has('blade-icons')) {
            $this->call('blade-icons:cache');
        }
        $this->newLine();
        $this->info('✅ Progetto pulito e ottimizzato con successo!');
        $this->newLine();
        
        return Command::SUCCESS;
    }
}
