<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ModuleAccessService;
use Illuminate\Console\Command;

class ShowUserPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:permissions {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostra i permessi e moduli accessibili per un utente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Trova l'utente
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Utente con email '{$email}' non trovato.");
            return 1;
        }

        $this->info("=== PERMESSI UTENTE ===");
        $this->line("Nome: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line('');

        // Mostra ruoli
        $this->line("🎭 RUOLI:");
        if ($user->roles->count() > 0) {
            $user->roles->each(function ($role) {
                $this->line("  ✓ {$role->name}");
            });
        } else {
            $this->line("  ❌ Nessun ruolo assegnato");
        }
        $this->line('');

        // Simula l'utente per testare i permessi
        /** @phpstan-ignore-next-line */
        auth()->login($user);

        // Mostra moduli accessibili
        $this->line("📋 MODULI ACCESSIBILI:");
        $accessibleModules = ModuleAccessService::getAccessibleModules();
        
        if (count($accessibleModules) > 0) {
            foreach ($accessibleModules as $module) {
                $this->line("  ✅ {$module['name']} ({$module['key']})");
                $this->line("     URL: {$module['url']}");
                $this->line("     Permessi: " . implode(', ', $module['permissions']));
                $this->line('');
            }
        } else {
            $this->line("  ❌ Nessun modulo accessibile");
        }

        // Mostra permessi specifici per modulo
        $this->line("🔐 PERMESSI DETTAGLIATI:");
        $modules = ['home', 'hr', 'amministrazione', 'produzione', 'marketing', 'ict'];
        
        foreach ($modules as $module) {
            $canAccess = ModuleAccessService::canAccess($module);
            $status = $canAccess ? '✅' : '❌';
            $this->line("  {$status} Modulo: " . strtoupper($module));
            
            if ($canAccess) {
                $permissions = ModuleAccessService::getModulePermissions($module);
                if (count($permissions) > 0) {
                    $this->line("     Azioni: " . implode(', ', $permissions));
                } else {
                    $this->line("     Azioni: Solo accesso base");
                }
            }
        }

        // Verifica se è super admin
        $this->line('');
        /** @var User $user */
        $isSuperAdmin = $user->hasRole('super-admin') || 
                       $user->hasAnyRole(['CEO', 'CFO', 'CTO', 'SVILUPPO', 'WAR_ROOM']);
        
        if ($isSuperAdmin) {
            $this->info("👑 SUPER ADMIN - Accesso completo a tutto il sistema");
        }

        // Logout dell'utente simulato
        /** @phpstan-ignore-next-line */
        auth()->logout();

        return 0;
    }
}