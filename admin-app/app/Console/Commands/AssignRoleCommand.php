<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign {email} {role} {--remove : Rimuovi il ruolo invece di aggiungerlo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assegna o rimuove un ruolo aziendale a un utente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');
        $remove = $this->option('remove');

        // Trova l'utente
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Utente con email '{$email}' non trovato.");
            return 1;
        }

        // Trova il ruolo
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Ruolo '{$roleName}' non trovato.");
            $this->line('Ruoli disponibili:');
            Role::all()->each(function ($role) {
                $this->line("  - {$role->name}");
            });
            return 1;
        }

        if ($remove) {
            // Rimuovi il ruolo
            if ($user->hasRole($roleName)) {
                $user->removeRole($roleName);
                $this->info("Ruolo '{$roleName}' rimosso dall'utente {$user->name} ({$email})");
            } else {
                $this->warn("L'utente {$user->name} non ha il ruolo '{$roleName}'");
            }
        } else {
            // Assegna il ruolo
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
                $this->info("Ruolo '{$roleName}' assegnato all'utente {$user->name} ({$email})");
            } else {
                $this->warn("L'utente {$user->name} ha già il ruolo '{$roleName}'");
            }
        }

        // Mostra i ruoli attuali dell'utente
        $this->line('');
        $this->line("Ruoli attuali di {$user->name}:");
        $user->roles->each(function ($role) {
            $this->line("  - {$role->name}");
        });

        return 0;
    }
}