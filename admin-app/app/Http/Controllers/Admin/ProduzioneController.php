<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiRendicontoProduzione;
use App\Models\KpiTargetMensile;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ProduzioneController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('module.access:produzione');
    }

    /**
     * Dashboard Produzione
     */
    public function index()
    {
        $user = Auth::user();
        
        // Filtra dati in base alla posizione dell'utente se necessario
        $userLocation = $this->getUserLocation($user);
        
        $stats = [
            'daily_production' => 1250,
            'efficiency' => 92.5,
            'quality_score' => 98.2,
            'active_orders' => 45
        ];
        
        return view('admin.modules.produzione.index', [
            'user' => $user,
            'stats' => $stats,
            'userLocation' => $userLocation,
            'canCreate' => ModuleAccessService::canPerform('produzione', 'create'),
            'canEdit' => ModuleAccessService::canPerform('produzione', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('produzione', 'delete'),
            'canViewReports' => ModuleAccessService::canPerform('produzione', 'reports'),
        ]);
    }

    /**
     * Report Produzione
     */
    public function reports()
    {
        $this->authorize('produzione.reports');
        
        $user = Auth::user();
        $userLocation = $this->getUserLocation($user);
        
        $reportData = [
            'monthly_production' => [
                'LAMEZIA' => 15000,
                'RENDE' => 12500,
                'VIBO' => 8900,
                'CASTROVILLARI' => 11200,
                'CATANZARO' => 9800,
                'SAN_PIETRO' => 7600
            ],
            'efficiency_trend' => [
                'LAMEZIA' => 94.2,
                'RENDE' => 91.8,
                'VIBO' => 89.5,
                'CASTROVILLARI' => 92.1,
                'CATANZARO' => 88.7,
                'SAN_PIETRO' => 90.3
            ]
        ];
        
        // Se l'utente è legato a una location, mostra solo quella
        if ($userLocation) {
            $reportData['monthly_production'] = [$userLocation => $reportData['monthly_production'][$userLocation]];
            $reportData['efficiency_trend'] = [$userLocation => $reportData['efficiency_trend'][$userLocation]];
        }
        
        return view('admin.modules.produzione.reports', [
            'reportData' => $reportData,
            'userLocation' => $userLocation
        ]);
    }

    /**
     * Determina la location dell'utente in base al ruolo
     */
    private function getUserLocation($user)
    {
        $roles = $user->getRoleNames();
        
        foreach ($roles as $role) {
            if (str_contains($role, '_LAMEZIA')) return 'LAMEZIA';
            if (str_contains($role, '_RENDE')) return 'RENDE';
            if (str_contains($role, '_VIBO')) return 'VIBO';
            if (str_contains($role, '_CASTROVILLARI')) return 'CASTROVILLARI';
            if (str_contains($role, '_CATANZARO')) return 'CATANZARO';
            if (str_contains($role, '_SAN_PIETRO')) return 'SAN_PIETRO';
        }
        
        return null; // Utente non legato a location specifica
    }

    // ===== SOTTOMENU PRODUZIONE =====

    /**
     * Tabella Obiettivi
     */
    public function tabellaObiettivi()
    {
        $this->authorize('produzione.view');
        return view('admin.modules.produzione.tabella-obiettivi');
    }

    /**
     * Cruscotto Produzione - REFACTORED con tabella pivot pre-aggregata
     */
    public function cruscottoProduzione(Request $request)
    {
        $this->authorize('produzione.view');
        
        // === VALIDAZIONE ===
        if ($request->hasAny(['data_inizio', 'data_fine', 'mandato', 'sede'])) {
            $request->validate([
                'data_inizio' => 'required|date',
                'data_fine' => 'required|date|after_or_equal:data_inizio',
            ], [
                'data_inizio.required' => 'La data di inizio è obbligatoria.',
                'data_inizio.date' => 'La data di inizio deve essere una data valida.',
                'data_fine.required' => 'La data di fine è obbligatoria.',
                'data_fine.date' => 'La data di fine deve essere una data valida.',
                'data_fine.after_or_equal' => 'La data di fine deve essere uguale o successiva alla data di inizio.',
            ]);
        }
        
        // === FILTRI ===
        $dataInizio = $request->input('data_inizio');
        $dataFine = $request->input('data_fine');
        $mandatoFilter = $request->input('mandato', []);
        $sedeFilter = $request->input('sede', []);
        
        // === QUERY PRINCIPALE SU TABELLA PIVOT PRE-AGGREGATA ===
        $query = DB::table('report_produzione_pivot_cache');
        
        // Applica filtri
        if ($dataInizio && $dataFine) {
            $query->whereBetween('data_vendita', [$dataInizio, $dataFine]);
        }
        
        if (!empty($mandatoFilter) && is_array($mandatoFilter)) {
            $query->whereIn('commessa', $mandatoFilter);
        }
        
        // Converti id_sede in nomi_sede per il filtro (include TUTTE le id_sede con stesso nome)
        $nomiSediFiltro = [];
        if (!empty($sedeFilter) && is_array($sedeFilter)) {
            $nomiSediFiltro = DB::table('sedi')
                ->whereIn('id_sede', $sedeFilter)
                ->pluck('nome_sede')
                ->unique() // Rimuove duplicati se più id hanno stesso nome
                ->toArray();
        }
        
        // === KPI TOTALI (aggregazione SQL diretta) ===
        $kpiTotali = DB::table('report_produzione_pivot_cache')
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->when(!empty($mandatoFilter), fn($q) => $q->whereIn('commessa', $mandatoFilter))
            ->when(!empty($nomiSediFiltro), fn($q) => $q->whereIn('nome_sede', $nomiSediFiltro))
            ->selectRaw('
                SUM(totale_vendite) as prodotto_pda,
                SUM(ok_definitivo) as inserito_pda,
                SUM(ko_definitivo) as ko_pda,
                SUM(backlog) as backlog_pda,
                SUM(opzioni_rid) as rid_totale,
                SUM(ore_lavorate) as ore
            ')
            ->first();
        
        // Calcola BOLLETTINI = Inseriti - RID
        $kpiArray = [
            'prodotto_pda' => $kpiTotali->prodotto_pda ?? 0,
            'prodotto_valore' => 0,
            'inserito_pda' => $kpiTotali->inserito_pda ?? 0,
            'inserito_valore' => 0,
            'ko_pda' => $kpiTotali->ko_pda ?? 0,
            'ko_valore' => 0,
            'backlog_pda' => $kpiTotali->backlog_pda ?? 0,
            'backlog_valore' => 0,
            'backlog_partner_pda' => 0, // Non tracciato nella cache
            'backlog_partner_valore' => 0,
            'ore' => $kpiTotali->ore ?? 0,
        ];
        
        // === VISTA DETTAGLIATA: Campagna per Sede ===
        $datiDettagliati = DB::table('report_produzione_pivot_cache')
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->when(!empty($mandatoFilter), fn($q) => $q->whereIn('commessa', $mandatoFilter))
            ->when(!empty($nomiSediFiltro), fn($q) => $q->whereIn('nome_sede', $nomiSediFiltro))
            ->selectRaw('
                commessa as cliente,
                campagna_id as campagna,
                nome_sede as sede,
                SUM(totale_vendite) as prodotto_pda,
                SUM(ok_definitivo) as inserito_pda,
                SUM(ko_definitivo) as ko_pda,
                SUM(backlog) as backlog_pda,
                SUM(opzioni_rid) as count_rid,
                SUM(ore_lavorate) as ore,
                0 as backlog_partner_pda
            ')
            ->groupBy('commessa', 'campagna_id', 'nome_sede')
            ->orderBy('commessa')
            ->orderBy('nome_sede')
            ->orderBy('campagna_id')
            ->get()
            ->groupBy('cliente')
            ->map(function($clienteGroup) {
                return $clienteGroup->groupBy('sede')->map(function($sedeGroup) {
                    return $sedeGroup->groupBy('campagna')->map(function($campagneGroup) {
                        $data = $campagneGroup->first();
                        $inseriti = (int)$data->inserito_pda;
                        $rid = (int)$data->count_rid;
                        $bollettini = max(0, $inseriti - $rid);
                        $prodotto = (int)$data->prodotto_pda;
                        $ore = (float)$data->ore;
                        
                        // === CALCOLI AGGIUNTIVI ===
                        // Resa su Prodotto (PDA) = Prodotto PDA / Ore
                        $resa_prodotto = $ore > 0 ? round($prodotto / $ore, 2) : 0;
                        
                        // Resa su Inserito (PDA) = Inserito PDA / Ore
                        $resa_inserito = $ore > 0 ? round($inseriti / $ore, 2) : 0;
                        
                        // B/B+R % = Bollettini / (Bollettini + RID)
                        $totale_pagamenti = $bollettini + $rid;
                        $boll_rid_pct = $totale_pagamenti > 0 ? round(($bollettini / $totale_pagamenti) * 100, 1) : 0;
                        
                        return [
                            'campagna' => $data->campagna,
                            'prodotti_aggiuntivi' => [],
                            'count_rid' => $rid,
                            'count_boll' => $bollettini,
                            'prodotto_pda' => $prodotto,
                            'prodotto_valore' => 0,
                            'inserito_pda' => $inseriti,
                            'inserito_valore' => 0,
                            'ko_pda' => (int)$data->ko_pda,
                            'ko_valore' => 0,
                            'backlog_pda' => (int)$data->backlog_pda,
                            'backlog_valore' => 0,
                            'backlog_partner_pda' => 0,
                            'backlog_partner_valore' => 0,
                            'cliente' => $data->cliente,
                            'cliente_originale' => $data->cliente,
                            'sede' => $data->sede,
                            'ore' => $ore,
                            
                            // === METRICHE AGGIUNTIVE ===
                            'resa_prodotto_pda' => $resa_prodotto,
                            'resa_inserito_pda' => $resa_inserito,
                            'boll_rid_pct' => $boll_rid_pct,
                            
                            // Metriche non disponibili (dati mancanti)
                            'commodity_luce' => 'N/D',
                            'commodity_gas' => 'N/D',
                            'commodity_dual' => 'N/D',
                            'post_ok' => 'N/D',
                            'post_ko' => 'N/D',
                            'tasso_mortalita' => 'N/D',
                        ];
                    });
                });
            });
        
        // === VISTA SINTETICA: Solo Sede (tutte le campagne aggregate) ===
        $datiSintetici = DB::table('report_produzione_pivot_cache')
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->when(!empty($mandatoFilter), fn($q) => $q->whereIn('commessa', $mandatoFilter))
            ->when(!empty($nomiSediFiltro), fn($q) => $q->whereIn('nome_sede', $nomiSediFiltro))
            ->selectRaw('
                commessa as cliente,
                nome_sede as sede,
                SUM(totale_vendite) as prodotto_pda,
                SUM(ok_definitivo) as inserito_pda,
                SUM(ko_definitivo) as ko_pda,
                SUM(backlog) as backlog_pda,
                SUM(opzioni_rid) as count_rid,
                SUM(ore_lavorate) as ore
            ')
            ->groupBy('commessa', 'nome_sede')
            ->orderBy('commessa')
            ->orderBy('nome_sede')
            ->get()
            ->groupBy('cliente')
            ->map(function($clienteGroup) {
                return $clienteGroup->groupBy('sede')->map(function($sedeGroup) {
                    $data = $sedeGroup->first();
                    $inseriti = (int)$data->inserito_pda;
                    $rid = (int)$data->count_rid;
                    $bollettini = max(0, $inseriti - $rid);
                    $prodotto = (int)$data->prodotto_pda;
                    $ore = (float)$data->ore;
                    
                    // === CALCOLI AGGIUNTIVI ===
                    $resa_prodotto = $ore > 0 ? round($prodotto / $ore, 2) : 0;
                    $resa_inserito = $ore > 0 ? round($inseriti / $ore, 2) : 0;
                    $totale_pagamenti = $bollettini + $rid;
                    $boll_rid_pct = $totale_pagamenti > 0 ? round(($bollettini / $totale_pagamenti) * 100, 1) : 0;
                    
                    return collect([
                        'totale' => [
                            'campagna' => 'TOTALE SEDE',
                            'prodotti_aggiuntivi' => [],
                            'count_rid' => $rid,
                            'count_boll' => $bollettini,
                            'prodotto_pda' => $prodotto,
                            'prodotto_valore' => 0,
                            'inserito_pda' => $inseriti,
                            'inserito_valore' => 0,
                            'ko_pda' => (int)$data->ko_pda,
                            'ko_valore' => 0,
                            'backlog_pda' => (int)$data->backlog_pda,
                            'backlog_valore' => 0,
                            'backlog_partner_pda' => 0,
                            'backlog_partner_valore' => 0,
                            'cliente' => $data->cliente,
                            'cliente_originale' => $data->cliente,
                            'sede' => $data->sede,
                            'ore' => $ore,
                            
                            // === METRICHE AGGIUNTIVE ===
                            'resa_prodotto_pda' => $resa_prodotto,
                            'resa_inserito_pda' => $resa_inserito,
                            'boll_rid_pct' => $boll_rid_pct,
                            
                            // Metriche non disponibili
                            'commodity_luce' => 'N/D',
                            'commodity_gas' => 'N/D',
                            'commodity_dual' => 'N/D',
                            'post_ok' => 'N/D',
                            'post_ko' => 'N/D',
                            'tasso_mortalita' => 'N/D',
                        ]
                    ]);
                });
            });
        
        // === DATI PER FILTRI ===
        $mandati = DB::table('report_produzione_pivot_cache')
            ->distinct()
            ->whereNotNull('commessa')
            ->orderBy('commessa')
            ->pluck('commessa', 'commessa');
        
        // Prendi solo le sedi che hanno dati nella cache
        // e mappa id_sede dalla tabella sedi
        $sediCache = DB::table('report_produzione_pivot_cache')
            ->select('nome_sede')
            ->distinct()
            ->whereNotNull('nome_sede')
            ->where('nome_sede', '!=', '')
            ->orderBy('nome_sede')
            ->pluck('nome_sede');
        
        // Mappa nome_sede -> id_sede dalla tabella sedi
        $sedi = DB::table('sedi')
            ->whereIn('nome_sede', $sediCache)
            ->orderBy('nome_sede')
            ->pluck('nome_sede', 'id_sede')
            ->unique(); // Rimuove duplicati per id diverse ma stesso nome
        
        $canali = []; // Non disponibile nella cache
        
        return view('admin.modules.produzione.cruscotto-produzione', [
            'kpiTotali' => $kpiArray,
            'datiRaggruppati' => $datiDettagliati, // Default
            'datiDettagliati' => $datiDettagliati,
            'datiSintetici' => $datiSintetici,
            'oreRaggruppate' => [], // Già incluse nella cache!
            'mandati' => $mandati,
            'sedi' => $sedi,
            'canali' => $canali,
            'dataInizio' => $dataInizio ?? '',
            'dataFine' => $dataFine ?? '',
            'mandatoFilter' => is_array($mandatoFilter) ? $mandatoFilter : [],
            'sedeFilter' => is_array($sedeFilter) ? $sedeFilter : [],
            'canaleFilter' => [],
        ]);
    }

    /**
     * Cruscotto Operatore
     */
    public function cruscottoOperatore()
    {
        $this->authorize('produzione.view');
        return view('admin.modules.produzione.cruscotto-operatore');
    }

    /**
     * Cruscotto Mensile
     */
    public function cruscottoMensile()
    {
        $this->authorize('produzione.view');
        return view('admin.modules.produzione.cruscotto-mensile');
    }

    /**
     * Input Manuale
     */
    public function inputManuale()
    {
        $this->authorize('produzione.create');
        return view('admin.modules.produzione.input-manuale');
    }

    /**
     * Avanzamento Mensile
     */
    public function avanzamentoMensile()
    {
        $this->authorize('produzione.view');
        return view('admin.modules.produzione.avanzamento-mensile');
    }

    /**
     * KPI Lead Quartili
     */
    public function kpiLeadQuartili()
    {
        $this->authorize('produzione.view');
        return view('admin.modules.produzione.kpi-lead-quartili');
    }

    /**
     * Controllo Stato Lead
     */
    public function controlloStatoLead()
    {
        $this->authorize('produzione.view');
        return view('admin.modules.produzione.controllo-stato-lead');
    }

    /**
     * KPI Target - Gestione Target Mensili e Rendiconto Produzione
     */
    public function kpiTarget(Request $request)
    {
        $this->authorize('produzione.view');
        
        // Filtro mese/anno (default: mese corrente)
        $anno = $request->input('anno', date('Y'));
        $mese = $request->input('mese', date('m'));
        
        // === TARGET MENSILI (Pianificazione) ===
        $targetMensili = KpiTargetMensile::where('anno', $anno)
            ->where('mese', $mese)
            ->orderBy('commessa')
            ->orderBy('sede_crm')
            ->orderBy('nome_kpi')
            ->get();
        
        // === RENDICONTO PRODUZIONE (Consuntivo/Esecuzione) ===
        $rendicontoProduzione = KpiRendicontoProduzione::orderBy('commessa')
            ->orderBy('servizio_mandato')
            ->orderBy('nome_kpi')
            ->get();
        
        // Raggruppa target per commessa
        $targetPerCommessa = $targetMensili->groupBy('commessa');
        
        // Raggruppa rendiconto per commessa
        $rendicontoPerCommessa = $rendicontoProduzione->groupBy('commessa');
        
        // Liste per filtri
        $commesse = DB::table('kpi_target_mensile')
            ->distinct()
            ->pluck('commessa')
            ->sort()
            ->values();
        
        $sedi = DB::table('kpi_target_mensile')
            ->distinct()
            ->pluck('sede_crm')
            ->sort()
            ->values();
        
        return view('admin.modules.produzione.kpi-target', [
            'targetMensili' => $targetMensili,
            'rendicontoProduzione' => $rendicontoProduzione,
            'targetPerCommessa' => $targetPerCommessa,
            'rendicontoPerCommessa' => $rendicontoPerCommessa,
            'anno' => $anno,
            'mese' => $mese,
            'commesse' => $commesse,
            'sedi' => $sedi,
        ]);
    }

    /**
     * Aggiorna valori KPI Target
     */
    public function updateKpiTarget(Request $request)
    {
        $this->authorize('produzione.edit');
        
        try {
            $updates = $request->input('kpi', []);
            $tabella = $request->input('tabella'); // 'target' o 'rendiconto'
            
            foreach ($updates as $id => $valore) {
                if ($tabella === 'target') {
                    KpiTargetMensile::where('id', $id)->update(['valore_kpi' => $valore]);
                } elseif ($tabella === 'rendiconto') {
                    KpiRendicontoProduzione::where('id', $id)->update(['valore_kpi' => $valore]);
                }
            }
            
            return redirect()->back()->with('success', 'KPI aggiornati con successo!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }
}
