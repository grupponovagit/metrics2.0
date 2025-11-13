<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GoogleAdsDiagnostic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googleads:diagnostic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostica configurazione Google Ads per tutti gli account';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("🔍 DIAGNOSTICA CONFIGURAZIONE GOOGLE ADS");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->newLine();

        // Raggruppa account per MCC
        $accounts = DB::table('account_agenzia')
            ->whereNotNull('google_ads_developer_token')
            ->select(
                'account_id',
                'ragione_sociale',
                'google_ads_mcc_id',
                'google_ads_developer_token',
                'google_ads_refresh_token',
                'google_ads_token_expires_at'
            )
            ->orderBy('google_ads_mcc_id')
            ->get();

        if ($accounts->isEmpty()) {
            $this->error("❌ Nessun account con configurazione Google Ads trovato");
            return Command::FAILURE;
        }

        $groupedByMcc = $accounts->groupBy('google_ads_mcc_id');

        foreach ($groupedByMcc as $mccId => $mccAccounts) {
            $this->line("📊 <fg=cyan>MCC ID: {$mccId}</>");
            $this->line("   Account collegati: " . count($mccAccounts));
            $this->newLine();

            foreach ($mccAccounts as $account) {
                $hasToken = !empty($account->google_ads_refresh_token);
                $hasDevToken = !empty($account->google_ads_developer_token);
                
                $status = '❌';
                $statusText = 'NON CONFIGURATO';
                
                if ($hasToken && $hasDevToken) {
                    $status = '✅';
                    $statusText = 'PRONTO';
                } elseif ($hasDevToken && !$hasToken) {
                    $status = '⚠️ ';
                    $statusText = 'MANCA OAUTH';
                }

                $this->line("   {$status} <fg=white>{$account->ragione_sociale}</> ({$account->account_id})");
                $this->line("      Developer Token: " . ($hasDevToken ? '✅ Presente' : '❌ Mancante'));
                $this->line("      Refresh Token: " . ($hasToken ? '✅ Presente' : '❌ Mancante'));
                
                if ($hasToken && $account->google_ads_token_expires_at) {
                    $expiresAt = \Carbon\Carbon::parse($account->google_ads_token_expires_at);
                    $daysLeft = now()->diffInDays($expiresAt, false);
                    
                    if ($daysLeft < 0) {
                        $this->line("      Scadenza: <fg=red>❌ SCADUTO il {$expiresAt->format('d/m/Y')}</>");
                    } elseif ($daysLeft < 30) {
                        $this->line("      Scadenza: <fg=yellow>⚠️  {$expiresAt->format('d/m/Y')} (tra {$daysLeft} giorni)</>");
                    } else {
                        $this->line("      Scadenza: <fg=green>✅ {$expiresAt->format('d/m/Y')} (tra {$daysLeft} giorni)</>");
                    }
                }
                
                $this->line("      Status: <fg=" . ($statusText === 'PRONTO' ? 'green' : ($statusText === 'MANCA OAUTH' ? 'yellow' : 'red')) . ">{$statusText}</>");
                $this->newLine();
            }

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();
        }

        // Riepilogo finale
        $totalAccounts = $accounts->count();
        $readyAccounts = $accounts->filter(fn($a) => !empty($a->google_ads_refresh_token) && !empty($a->google_ads_developer_token))->count();
        $missingOAuth = $accounts->filter(fn($a) => empty($a->google_ads_refresh_token) && !empty($a->google_ads_developer_token))->count();

        $this->info("📈 RIEPILOGO");
        $this->line("   Totale account: {$totalAccounts}");
        $this->line("   ✅ Pronti per import: {$readyAccounts}");
        $this->line("   ⚠️  Mancano OAuth: {$missingOAuth}");
        $this->newLine();

        if ($missingOAuth > 0) {
            $this->warn("⚠️  Alcuni account necessitano autenticazione OAuth");
            $this->line("   Vai su: " . url('/admin/ict/google-ads-api'));
        }

        return Command::SUCCESS;
    }
}

