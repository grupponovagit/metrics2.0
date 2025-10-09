<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CompanyRolesSeeder extends Seeder
{
    /**
     * Crea la struttura completa dei ruoli aziendali e permessi per moduli.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Definire i permessi per moduli
        $modulePermissions = [
            // Modulo Home - accesso base per tutti
            'home.access',
            'home.view',
            
            // Modulo HR
            'hr.access',
            'hr.view',
            'hr.create',
            'hr.edit',
            'hr.delete',
            'hr.reports',
            
            // Modulo Amministrazione
            'amministrazione.access',
            'amministrazione.view',
            'amministrazione.create',
            'amministrazione.edit',
            'amministrazione.delete',
            'amministrazione.reports',
            
            // Modulo Produzione
            'produzione.access',
            'produzione.view',
            'produzione.create',
            'produzione.edit',
            'produzione.delete',
            'produzione.reports',
            
            // Modulo Marketing
            'marketing.access',
            'marketing.view',
            'marketing.create',
            'marketing.edit',
            'marketing.delete',
            'marketing.reports',
            
            // Modulo iCT
            'ict.access',
            'ict.view',
            'ict.create',
            'ict.edit',
            'ict.delete',
            'ict.reports',
            'ict.admin',
        ];

        // Creare tutti i permessi per moduli
        foreach ($modulePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assicuriamoci che il ruolo super-admin esista
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Definire la struttura dei ruoli con i loro permessi
        $rolesStructure = [
            // Livello 1 - Top Management (Super Admin)
            'CEO' => [
                'level' => 1.9,
                'description' => 'Chief Executive Officer - Accesso completo',
                'permissions' => 'super-admin',
                'is_super_admin' => true
            ],
            'CFO' => [
                'level' => 1.11,
                'description' => 'Chief Financial Officer - Accesso completo', 
                'permissions' => 'super-admin',
                'is_super_admin' => true
            ],
            
            // Livello 2
            'LEGALE' => [
                'level' => 2.13,
                'description' => 'Ufficio Legale - Accesso completo',
                'permissions' => 'super-admin',
                'is_super_admin' => true
            ],
            
            // Livello 3
            'CONTABILITÀ' => [
                'level' => 3.13,
                'description' => 'Ufficio Contabilità',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'amministrazione.access', 'amministrazione.view', 'amministrazione.create', 'amministrazione.edit', 'amministrazione.reports'
                ]
            ],
            
            // Livello 4
            'TESORERIA' => [
                'level' => 4.13,
                'description' => 'Ufficio Tesoreria',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'amministrazione.access', 'amministrazione.view', 'amministrazione.create', 'amministrazione.edit', 'amministrazione.reports'
                ]
            ],
            
            // Livello 5
            'AMM_PERSONALE' => [
                'level' => 5.13,
                'description' => 'Amministrazione del Personale',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'hr.access', 'hr.view', 'hr.create', 'hr.edit', 'hr.reports'
                ]
            ],
            'AFFARI_GENERALI' => [
                'level' => 5.13,
                'description' => 'Ufficio Affari Generali',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'hr.access', 'hr.view', 'hr.create', 'hr.edit', 'hr.reports'
                ]
            ],
            
            // Livello 6
            'CTO' => [
                'level' => 6.2,
                'description' => 'Chief Technology Officer - Accesso completo',
                'permissions' => 'super-admin',
                'is_super_admin' => true
            ],
            'MARKETING' => [
                'level' => 6.4,
                'description' => 'Chief Marketing Officer',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'marketing.access', 'marketing.view', 'marketing.create', 'marketing.edit', 'marketing.delete', 'marketing.reports'
                ]
            ],
            'COMMERCIALE' => [
                'level' => 6.6,
                'description' => 'Responsabile Commerciale',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'amministrazione.access', 'amministrazione.view', 'amministrazione.create', 'amministrazione.edit',
                    'produzione.access', 'produzione.view', 'produzione.create', 'produzione.edit'
                ]
            ],
            'OPERATION' => [
                'level' => 6.9,
                'description' => 'Responsabile Operations',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'produzione.access', 'produzione.view', 'produzione.create', 'produzione.edit', 'produzione.reports',
                    'marketing.access', 'marketing.view', 'marketing.create', 'marketing.edit'
                ]
            ],
            'QUALITÀ' => [
                'level' => 6.11,
                'description' => 'Responsabile Qualità',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'produzione.access', 'produzione.view', 'produzione.create', 'produzione.edit', 'produzione.reports'
                ]
            ],
            'COGE_REGIA' => [
                'level' => 6.13,
                'description' => 'Controllo di Gestione e Regia',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'amministrazione.access', 'amministrazione.view', 'amministrazione.create', 'amministrazione.edit', 'amministrazione.reports',
                    'marketing.access', 'marketing.view', 'marketing.create', 'marketing.edit', 'marketing.reports',
                    'produzione.access', 'produzione.view', 'produzione.create', 'produzione.edit', 'produzione.reports'
                ]
            ],
            
            // Livello 7
            'IT' => [
                'level' => 7.1,
                'description' => 'Team Sviluppo - Accesso completo',
                'permissions' => 'super-admin',
                'is_super_admin' => true
            ],
            'WAR_ROOM' => [
                'level' => 7.3,
                'description' => 'War Room - Accesso completo',
                'permissions' => 'super-admin',
                'is_super_admin' => true
            ],
            'PM_MANDATO' => [
                'level' => 7.6,
                'description' => 'Project Manager Mandato',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'amministrazione.access', 'amministrazione.view', 'amministrazione.create', 'amministrazione.edit',
                    'produzione.access', 'produzione.view', 'produzione.create', 'produzione.edit'
                ]
            ],
            'HR_SEL_FORM' => [
                'level' => 7.10,
                'description' => 'HR Selezione e Formazione',
                'permissions' => [
                    'admin user',
                    'home.access', 'home.view',
                    'hr.access', 'hr.view', 'hr.create', 'hr.edit', 'hr.reports'
                ]
            ],
        ];

        // Creare ruoli per i centri di costo (CCM, TL, OP)
        $locations = ['LAMEZIA', 'RENDE', 'VIBO', 'CASTROVILLARI', 'CATANZARO', 'SAN_PIETRO'];
        $locationLevels = [
            'CCM' => 8, // Centro di Costo Manager
            'TL' => 9,  // Team Leader
            'OP' => 10  // Operatore
        ];

        foreach ($locations as $location) {
            foreach ($locationLevels as $roleType => $level) {
                $roleName = "{$roleType}_{$location}";
                $rolesStructure[$roleName] = [
                    'level' => $level,
                    'description' => "{$roleType} {$location}",
                    'permissions' => [
                        'admin user',
                        'home.access', 'home.view',
                        'produzione.access', 'produzione.view', 'produzione.create', 'produzione.edit'
                    ]
                ];
            }
        }

        // Creare tutti i ruoli con i loro permessi
        foreach ($rolesStructure as $roleName => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleName
            ]);

            // SEMPRE sincronizza i permessi (anche per ruoli esistenti)
            if ($roleData['permissions'] === 'super-admin') {
                // Per i super admin, rimuovi tutti i permessi vecchi e assegna solo admin user
                // Il Gate::before nel AuthServiceProvider darà accesso completo
                $role->syncPermissions(['admin user']);
            } elseif (is_array($roleData['permissions'])) {
                // Sincronizza i permessi specificati (rimuove vecchi, aggiunge nuovi)
                $validPermissions = [];
                foreach ($roleData['permissions'] as $permission) {
                    if (Permission::where('name', $permission)->exists()) {
                        $validPermissions[] = $permission;
                    }
                }
                $role->syncPermissions($validPermissions);
            }

            echo "✓ Ruolo '{$roleName}' aggiornato (Livello {$roleData['level']})\n";
        }

        echo "\n🎉 Struttura ruoli aziendali creata con successo!\n";
        echo "📊 Totale ruoli creati: " . count($rolesStructure) . "\n";
        echo "🔐 Totale permessi per moduli: " . count($modulePermissions) . "\n";
    }
}
