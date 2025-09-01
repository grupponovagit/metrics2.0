<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompleteSystemSeeder extends Seeder
{
    /**
     * Seeder completo per il sistema di ruoli aziendali
     * Esegue tutti i seeder necessari in sequenza
     */
    public function run()
    {
        echo "🚀 Inizializzazione completa del sistema di ruoli aziendali...\n\n";

        // 1. Seeder base del sistema admin
        echo "1️⃣  Esecuzione AdminCoreSeeder...\n";
        $this->call(AdminCoreSeeder::class);
        echo "✅ AdminCoreSeeder completato\n\n";

        // 2. Seeder dei ruoli aziendali
        echo "2️⃣  Esecuzione CompanyRolesSeeder...\n";
        $this->call(CompanyRolesSeeder::class);
        echo "✅ CompanyRolesSeeder completato\n\n";

        // 3. Seeder degli utenti di test
        echo "3️⃣  Esecuzione TestUsersSeeder...\n";
        $this->call(TestUsersSeeder::class);
        echo "✅ TestUsersSeeder completato\n\n";

        echo "🎉 SISTEMA COMPLETAMENTE INIZIALIZZATO! 🎉\n";
        echo "═══════════════════════════════════════════\n\n";

        echo "📋 RIEPILOGO:\n";
        echo "• Ruoli di sistema: creati\n";
        echo "• Ruoli aziendali: 35 ruoli creati\n";
        echo "• Permessi moduli: 33 permessi creati\n";
        echo "• Utenti di test: creati per ogni ruolo\n\n";

        echo "🔐 CREDENZIALI DI ACCESSO:\n";
        echo "Password universale: password123\n\n";

        echo "👥 UTENTI PRINCIPALI PER TEST:\n";
        echo "┌─────────────────────────────────────────────────────────────┐\n";
        echo "│ CEO (Accesso completo)                                      │\n";
        echo "│ Email: ceo@novaholding.it                                   │\n";
        echo "│ Moduli: TUTTI                                               │\n";
        echo "├─────────────────────────────────────────────────────────────┤\n";
        echo "│ Contabilità (Limitato)                                     │\n";
        echo "│ Email: contabilita@novaholding.it                          │\n";
        echo "│ Moduli: Home + Amministrazione                              │\n";
        echo "├─────────────────────────────────────────────────────────────┤\n";
        echo "│ CCM Lamezia (Produzione)                                   │\n";
        echo "│ Email: ccm.lamezia@novaholding.it                          │\n";
        echo "│ Moduli: Home + Produzione (filtrato per LAMEZIA)           │\n";
        echo "├─────────────────────────────────────────────────────────────┤\n";
        echo "│ CMO (Marketing)                                             │\n";
        echo "│ Email: cmo@novaholding.it                                   │\n";
        echo "│ Moduli: Home + Marketing                                    │\n";
        echo "├─────────────────────────────────────────────────────────────┤\n";
        echo "│ Legale (Solo admin)                                        │\n";
        echo "│ Email: legale@novaholding.it                               │\n";
        echo "│ Moduli: Nessuno (solo accesso admin base)                  │\n";
        echo "└─────────────────────────────────────────────────────────────┘\n\n";

        echo "🛠️  COMANDI UTILI:\n";
        echo "• php artisan user:permissions {email}  - Mostra permessi utente\n";
        echo "• php artisan role:assign {email} {ruolo} - Assegna ruolo\n";
        echo "• php artisan role:assign {email} {ruolo} --remove - Rimuovi ruolo\n\n";

        echo "🌐 URL DI ACCESSO:\n";
        echo "• Dashboard Admin: /admin\n";
        echo "• Modulo Home: /admin/home\n";
        echo "• Modulo HR: /admin/hr\n";
        echo "• Modulo Amministrazione: /admin/amministrazione\n";
        echo "• Modulo Produzione: /admin/produzione\n";
        echo "• Modulo Marketing: /admin/marketing\n";
        echo "• Modulo ICT: /admin/ict\n\n";

        echo "✨ Il sistema è pronto per il test!\n";
    }
}
