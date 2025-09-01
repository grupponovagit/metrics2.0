<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Crea utenti di test per ogni ruolo aziendale
     */
    public function run()
    {
        $password = Hash::make('password123');
        
        // Definizione degli utenti di test per ogni ruolo
        $testUsers = [
            // Livello 1 - Top Management
            'CEO' => [
                'name' => 'Mario Rossi',
                'surname' => 'CEO',
                'email' => 'ceo@novaholding.it',
                'role' => 'CEO'
            ],
            'CFO' => [
                'name' => 'Anna Bianchi',
                'surname' => 'CFO', 
                'email' => 'cfo@novaholding.it',
                'role' => 'CFO'
            ],
            
            // Livello 2
            'LEGALE' => [
                'name' => 'Giuseppe Verdi',
                'surname' => 'Legale',
                'email' => 'legale@novaholding.it',
                'role' => 'LEGALE'
            ],
            
            // Livello 3
            'CONTABILITÀ' => [
                'name' => 'Maria Neri',
                'surname' => 'Contabile',
                'email' => 'contabilita@novaholding.it',
                'role' => 'CONTABILITÀ'
            ],
            
            // Livello 4
            'TESORERIA' => [
                'name' => 'Franco Blu',
                'surname' => 'Tesoriere',
                'email' => 'tesoreria@novaholding.it',
                'role' => 'TESORERIA'
            ],
            
            // Livello 5
            'AMM_PERSONALE' => [
                'name' => 'Laura Gialli',
                'surname' => 'HR Admin',
                'email' => 'hr.admin@novaholding.it',
                'role' => 'AMM_PERSONALE'
            ],
            'AFFARI_GENERALI' => [
                'name' => 'Roberto Viola',
                'surname' => 'Affari Generali',
                'email' => 'affari.generali@novaholding.it',
                'role' => 'AFFARI_GENERALI'
            ],
            
            // Livello 6
            'CTO' => [
                'name' => 'Alessandro Tech',
                'surname' => 'CTO',
                'email' => 'cto@novaholding.it',
                'role' => 'CTO'
            ],
            'CMO' => [
                'name' => 'Valentina Marketing',
                'surname' => 'CMO',
                'email' => 'cmo@novaholding.it',
                'role' => 'CMO'
            ],
            'COMMERCIALE' => [
                'name' => 'Luca Vendite',
                'surname' => 'Commerciale',
                'email' => 'commerciale@novaholding.it',
                'role' => 'COMMERCIALE'
            ],
            'OPERATION' => [
                'name' => 'Stefano Operations',
                'surname' => 'Operations',
                'email' => 'operations@novaholding.it',
                'role' => 'OPERATION'
            ],
            'QUALITÀ' => [
                'name' => 'Elena Quality',
                'surname' => 'Qualità',
                'email' => 'qualita@novaholding.it',
                'role' => 'QUALITÀ'
            ],
            'COGE_REGIA' => [
                'name' => 'Marco Controller',
                'surname' => 'Controllo',
                'email' => 'controllo@novaholding.it',
                'role' => 'COGE_REGIA'
            ],
            
            // Livello 7
            'SVILUPPO' => [
                'name' => 'Davide Developer',
                'surname' => 'Dev',
                'email' => 'dev@novaholding.it',
                'role' => 'SVILUPPO'
            ],
            'WAR_ROOM' => [
                'name' => 'Andrea War',
                'surname' => 'Room',
                'email' => 'warroom@novaholding.it',
                'role' => 'WAR_ROOM'
            ],
            'PM_MANDATO' => [
                'name' => 'Simone Project',
                'surname' => 'Manager',
                'email' => 'pm@novaholding.it',
                'role' => 'PM_MANDATO'
            ],
            'HR_SEL_FORM' => [
                'name' => 'Chiara HR',
                'surname' => 'Selezione',
                'email' => 'hr.selezione@novaholding.it',
                'role' => 'HR_SEL_FORM'
            ],
        ];

        // Aggiungi utenti per ogni location (CCM, TL, OP)
        $locations = ['LAMEZIA', 'RENDE', 'VIBO', 'CASTROVILLARI', 'CATANZARO', 'SAN_PIETRO'];
        $locationRoles = ['CCM', 'TL', 'OP'];
        $locationNames = [
            'LAMEZIA' => 'Lamezia Terme',
            'RENDE' => 'Rende',
            'VIBO' => 'Vibo Valentia',
            'CASTROVILLARI' => 'Castrovillari',
            'CATANZARO' => 'Catanzaro',
            'SAN_PIETRO' => 'San Pietro'
        ];

        foreach ($locations as $location) {
            foreach ($locationRoles as $roleType) {
                $roleName = "{$roleType}_{$location}";
                $locationName = $locationNames[$location];
                
                $testUsers[$roleName] = [
                    'name' => ucfirst(strtolower($roleType)) . ' ' . $locationName,
                    'surname' => $roleType,
                    'email' => strtolower($roleType) . '.' . strtolower($location) . '@novaholding.it',
                    'role' => $roleName
                ];
            }
        }

        // Crea gli utenti
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($testUsers as $userData) {
            // Verifica se l'utente esiste già
            $existingUser = User::where('email', $userData['email'])->first();
            
            if ($existingUser) {
                // Utente esiste, assegna solo il ruolo se non ce l'ha
                if (!$existingUser->hasRole($userData['role'])) {
                    $role = Role::where('name', $userData['role'])->first();
                    if ($role) {
                        $existingUser->assignRole($userData['role']);
                        echo "✓ Ruolo '{$userData['role']}' assegnato all'utente esistente {$existingUser->name}\n";
                    }
                }
                $skippedCount++;
                continue;
            }

            // Crea nuovo utente
            $user = User::create([
                'name' => $userData['name'],
                'surname' => $userData['surname'],
                'email' => $userData['email'],
                'password' => $password,
                'email_verified_at' => now(),
                'phone' => '+39 ' . rand(300, 399) . ' ' . rand(1000000, 9999999),
                'codice_fiscale' => $this->generateFakeCF(),
                'data_nascita' => now()->subYears(rand(25, 55)),
                'luogo_nascita' => 'Cosenza',
            ]);

            // Assegna il ruolo
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($userData['role']);
                echo "✅ Utente '{$user->name}' creato con ruolo '{$userData['role']}' - Email: {$user->email}\n";
                $createdCount++;
            } else {
                echo "⚠️  Ruolo '{$userData['role']}' non trovato per utente {$user->name}\n";
            }
        }

        echo "\n🎉 Seeder completato!\n";
        echo "👥 Utenti creati: {$createdCount}\n";
        echo "⏭️  Utenti esistenti: {$skippedCount}\n";
        echo "🔐 Password per tutti gli utenti: password123\n\n";

        // Mostra alcuni utenti di esempio per il test
        echo "🧪 UTENTI DI TEST PRINCIPALI:\n";
        echo "CEO: ceo@novaholding.it (password123) - Accesso completo\n";
        echo "Contabilità: contabilita@novaholding.it (password123) - Solo Home + Amministrazione\n";
        echo "CCM Lamezia: ccm.lamezia@novaholding.it (password123) - Solo Home + Produzione\n";
        echo "CMO: cmo@novaholding.it (password123) - Solo Home + Marketing\n";
        echo "HR Admin: hr.admin@novaholding.it (password123) - Solo Home + HR\n";
        echo "Legale: legale@novaholding.it (password123) - Solo accesso admin (nessun modulo)\n\n";
    }

    /**
     * Genera un codice fiscale fake per test
     */
    private function generateFakeCF()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        
        $cf = '';
        // 6 lettere
        for ($i = 0; $i < 6; $i++) {
            $cf .= $chars[rand(0, strlen($chars) - 1)];
        }
        // 2 numeri
        for ($i = 0; $i < 2; $i++) {
            $cf .= $numbers[rand(0, strlen($numbers) - 1)];
        }
        // 1 lettera
        $cf .= $chars[rand(0, strlen($chars) - 1)];
        // 2 numeri
        for ($i = 0; $i < 2; $i++) {
            $cf .= $numbers[rand(0, strlen($numbers) - 1)];
        }
        // 1 lettera
        $cf .= $chars[rand(0, strlen($chars) - 1)];
        // 3 caratteri finali
        for ($i = 0; $i < 3; $i++) {
            $cf .= rand(0, 1) ? $chars[rand(0, strlen($chars) - 1)] : $numbers[rand(0, strlen($numbers) - 1)];
        }
        
        return $cf;
    }
}
