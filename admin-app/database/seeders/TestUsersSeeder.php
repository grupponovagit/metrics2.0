<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Popola utenti SOLO da seed_real_users.json (o SEED_USERS_URL)
     */
    public function run()
    {
        $password = Hash::make('password123');

        // 1) Carica utenti reali
        $realUsers = [];
        $jsonPath = storage_path('app/seed_real_users.json');

        if (file_exists($jsonPath)) {
            $content = file_get_contents($jsonPath);
            $realUsers = json_decode($content, true) ?: [];
        } elseif (env('SEED_USERS_URL')) {
            try {
                $response = Http::timeout(5)->get(env('SEED_USERS_URL'));
                if ($response->successful()) {
                    $realUsers = $response->json() ?: [];
                }
            } catch (\Throwable $e) {
                // ignora
            }
        }

        if (empty($realUsers)) {
            echo "⚠️  Nessun utente reale trovato. Assicurati di avere storage/app/seed_real_users.json o SEED_USERS_URL.\n";
            return;
        }

        $createdCount = 0;
        $skippedCount = 0;

        /**
         * Il JSON può essere:
         * - array indicizzato di utenti: [{name, surname, email, role}, ...]
         * - oggetto mappato per ruolo: {"CEO": {name, surname, email, role}, ...}
         */
        $list = $this->normalizeInputArray($realUsers);

        foreach ($list as $u) {
            // 2) Sanitize & inferenze
            $email = trim((string)($u['email'] ?? ''));
            if ($email === '') {
                echo "⚠️  Record senza email, saltato.\n";
                $skippedCount++;
                continue;
            }

            $roleRaw = strtoupper(trim((string)($u['role'] ?? '')));
            if ($roleRaw === '') {
                // prova a inferire dal cognome/qualifica
                $roleRaw = $this->inferRoleFromData($u);
            }
            $targetRole = $this->normalizeRoleName($roleRaw);

            // Se è CCM/TL/OP "generico", prova a dedurre la sede da email
            if (preg_match('/^(CCM|TL|OP)$/', $targetRole)) {
                $maybe = $this->inferLocationRoleFromEmail($targetRole, $email);
                if ($maybe) $targetRole = $maybe;
            }

            // 3) Se esiste, assegna ruolo mancante; altrimenti crea
            $existing = User::where('email', $email)->first();
            if ($existing) {
                if (!$existing->hasRole($targetRole)) {
                    $role = Role::where('name', $targetRole)->first();
                    if ($role) {
                        $existing->assignRole($targetRole);
                        echo "✓ Ruolo '{$targetRole}' assegnato a utente esistente {$email}\n";
                    } else {
                        echo "⚠️  Ruolo '{$targetRole}' non trovato per {$email}\n";
                    }
                }
                $skippedCount++;
                continue;
            }

            // Crea utente
            $user = User::create([
                'name'             => trim((string)($u['name'] ?? 'N/D')),
                'surname'          => trim((string)($u['surname'] ?? 'N/D')),
                'email'            => $email,
                'password'         => $password,
                'email_verified_at'=> now(),
                'phone'            => '+39 ' . rand(300, 399) . ' ' . rand(1000000, 9999999),
                'codice_fiscale'   => $this->generateFakeCF(),
                'data_nascita'     => now()->subYears(rand(25, 55)),
                'luogo_nascita'    => 'Cosenza',
            ]);

            $role = Role::where('name', $targetRole)->first();
            if ($role) {
                $user->assignRole($targetRole);
                echo "✅ Utente '{$email}' creato con ruolo '{$targetRole}'\n";
                $createdCount++;
            } else {
                echo "⚠️  Utente '{$email}' creato ma ruolo '{$targetRole}' non trovato\n";
            }
        }

        echo "\n🎉 Seeder completato!\n";
        echo "👥 Utenti creati: {$createdCount}\n";
        echo "⏭️  Utenti esistenti: {$skippedCount}\n";
        echo "🔐 Password per tutti: password123\n";
    }

    /** Accetta sia array indicizzati che oggetti mappati per ruolo */
    private function normalizeInputArray($realUsers): array
    {
        // Se è già una lista di utenti
        if (isset($realUsers[0]) || empty($realUsers)) return $realUsers;

        // Altrimenti è un oggetto {roleKey: {...}}
        $out = [];
        foreach ($realUsers as $roleKey => $u) {
            if (is_array($u)) {
                // se manca 'role', inserisci la chiave
                if (empty($u['role'])) $u['role'] = $roleKey;
                $out[] = $u;
            }
        }
        return $out;
    }

    /** Inferisce un ruolo di base dal surname/qualifica o dall'email */
    private function inferRoleFromData(array $u): string
    {
        $surname = strtolower((string)($u['surname'] ?? ''));
        $email   = strtolower((string)($u['email'] ?? ''));

        $map = [
            'ceo' => 'CEO',
            'cfo' => 'CFO',
            'legale' => 'LEGALE',
            'contabile' => 'CONTABILITÀ',
            'tesoriere' => 'TESORERIA',
            'hr admin' => 'AMM_PERSONALE',
            'affari generali' => 'AFFARI_GENERALI',
            'cto' => 'CTO',
            'cmo' => 'MARKETING',
            'marketing' => 'MARKETING',
            'commerciale' => 'COMMERCIALE',
            'operations' => 'OPERATION',
            'qualità' => 'QUALITÀ',
            'qualita' => 'QUALITÀ',
            'controllo' => 'COGE_REGIA',
            'sviluppo' => 'IT',
            'ict' => 'IT',
            'it' => 'IT',
            'war room' => 'WAR_ROOM',
            'pm mandato' => 'PM_MANDATO',
            'hr selezione' => 'HR_SEL_FORM',
            'hr selezione e formazione' => 'HR_SEL_FORM',
            'ccm' => 'CCM',
            'tl' => 'TL',
            'op' => 'OP',
        ];

        foreach ($map as $needle => $role) {
            if ($needle !== '' && str_contains($surname, $needle)) {
                if (in_array($role, ['CCM','TL','OP'])) {
                    return $this->inferLocationRoleFromEmail($role, $email) ?? $role;
                }
                return $role;
            }
        }

        // fallback leggero via email (es. cmo@ → MARKETING)
        if (preg_match('/\b(cmo)\b/i', $email)) return 'MARKETING';
        if (preg_match('/\b(cto)\b/i', $email)) return 'CTO';
        if (preg_match('/\b(ccm|tl|op)\b/i', $email, $m)) return strtoupper($m[1]);

        return 'CEO';
    }

    /** Tenta di dedurre la location dal dominio/email */
    private function inferLocationRoleFromEmail(string $baseRole, string $email): ?string
    {
        $locs = [
            'lamezia' => 'LAMEZIA',
            'rende' => 'RENDE',
            'vibo' => 'VIBO',
            'castrovillari' => 'CASTROVILLARI',
            'catanzaro' => 'CATANZARO',
            'san_pietro' => 'SAN_PIETRO',
            'sanpietro' => 'SAN_PIETRO',
        ];
        $email = strtolower($email);
        foreach ($locs as $needle => $loc) {
            if (str_contains($email, $needle)) {
                return "{$baseRole}_{$loc}";
            }
        }
        return null;
    }

    /** Normalizza alias/ridenominazioni ai nomi dei ruoli esistenti */
    private function normalizeRoleName(string $roleName): string
    {
        $roleName = strtoupper($roleName);
        $map = [
            'CMO' => 'MARKETING',
            'SVILUPPO' => 'IT',
            'ICT' => 'IT',
            'OPERATIONS' => 'OPERATION',
            'QUALITA' => 'QUALITÀ',
            'HR ADMIN' => 'AMM_PERSONALE',
            'AFFARI GENERALI' => 'AFFARI_GENERALI',
            'WAR ROOM' => 'WAR_ROOM',
            'PM MANDATO' => 'PM_MANDATO',
            'HR SELEZIONE' => 'HR_SEL_FORM',
        ];
        return $map[$roleName] ?? $roleName;
    }

    /** Codice fiscale fake per test */
    private function generateFakeCF(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $cf = '';
        for ($i=0;$i<6;$i++) $cf .= $chars[random_int(0,25)];
        for ($i=0;$i<2;$i++) $cf .= $numbers[random_int(0,9)];
        $cf .= $chars[random_int(0,25)];
        for ($i=0;$i<2;$i++) $cf .= $numbers[random_int(0,9)];
        $cf .= $chars[random_int(0,25)];
        for ($i=0;$i<3;$i++) $cf .= (random_int(0,1) ? $chars[random_int(0,25)] : $numbers[random_int(0,9)]);
        return $cf;
    }
}