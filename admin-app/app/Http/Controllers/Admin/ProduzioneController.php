<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiRendicontoProduzione;
use App\Models\KpiTargetMensile;
use App\Models\CalendarioAziendale;
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
        
        // === CALCOLI CALENDARIO (per metriche PAF e Obiettivi) ===
        $annoCorrente = date('Y');
        $meseCorrente = date('m');
        
        // Giorni lavorativi totali del mese
        $giorniLavorabiliPrevisti = CalendarioAziendale::giorniLavorativiMese($annoCorrente, $meseCorrente);
        
        // Giorni già trascorsi (escluso oggi)
        $giorniLavoratiEffettivi = CalendarioAziendale::giorniLavorativiTrascorsi($annoCorrente, $meseCorrente);
        
        // Giorni rimanenti (incluso oggi se è un giorno lavorativo)
        $giorniLavorativiRimanenti = CalendarioAziendale::giorniLavorativiRimanenti($annoCorrente, $meseCorrente);
        
        // === MAPPING SEDI: kpi_target_mensile.sede_crm -> report_produzione_pivot_cache.nome_sede ===
        $mappingSedi = [
            'FRC_FONT' => 'FRANCAVILLA FONTANA',
            'LMZ' => 'LAMEZIA TERME',
            'LCR' => 'LOCRI',
            'TAR' => 'TARANTO',
            'VIGEVANO' => 'VIGEVANO',
            'TOTALE' => 'TOTALE',
            'RND' => 'RND',
            'MARSALA' => 'MARSALA',
            // Aggiungi altri mapping se necessario
        ];
        
        // === RECUPERA OBIETTIVI DA kpi_target_mensile ===
        // IMPORTANTE: Prendiamo SOLO i record con tipologia_obiettivo = 'OBIETTIVO'
        $obiettiviKpi = DB::table('kpi_target_mensile')
            ->where('anno', $annoCorrente)
            ->where('mese', $meseCorrente)
            ->where('tipologia_obiettivo', 'OBIETTIVO') // SOLO quelli con "OBIETTIVO"
            ->when(!empty($mandatoFilter), fn($q) => $q->whereIn('commessa', $mandatoFilter))
            ->get()
            ->groupBy(function($item) use ($mappingSedi) {
                // Usa il mapping per convertire la sede
                $sedeConvertita = $mappingSedi[$item->sede_crm] ?? $item->sede_crm;
                return strtoupper($item->commessa) . '|' . strtoupper($sedeConvertita);
            })
            ->map(function($group) {
                // Somma tutti i KPI per questa commessa+sede (se ci sono più record)
                return $group->sum(function($kpi) {
                    $kpiTemp = new KpiTargetMensile();
                    foreach((array)$kpi as $key => $value) {
                        $kpiTemp->{$key} = $value;
                    }
                    return $kpiTemp->getMediaPonderata();
                });
            });
        
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
                SUM(ore_lavorate) as ore,
                SUM(totale_kpi) as obiettivo_totale
            ')
            ->first();
        
        $kpiArray = [
            'prodotto_pda' => $kpiTotali->prodotto_pda ?? 0,
            'inserito_pda' => $kpiTotali->inserito_pda ?? 0,
            'ko_pda' => $kpiTotali->ko_pda ?? 0,
            'backlog_pda' => $kpiTotali->backlog_pda ?? 0,
            'backlog_partner_pda' => 0, // Non tracciato nella cache
            'ore' => $kpiTotali->ore ?? 0,
            'obiettivo' => $kpiTotali->obiettivo_totale ?? 0,
            
            // Resa
            'resa_prodotto' => ($kpiTotali->ore ?? 0) > 0 ? round(($kpiTotali->prodotto_pda ?? 0) / $kpiTotali->ore, 2) : 0,
            'resa_inserito' => ($kpiTotali->ore ?? 0) > 0 ? round(($kpiTotali->inserito_pda ?? 0) / $kpiTotali->ore, 2) : 0,
            
            // Obiettivi (al momento a 0 come richiesto)
            'obiettivo_mensile' => 0,
            'passo_giorno' => 0,
            'differenza_obj' => 0,
            
            // PAF Mensile
            'ore_paf' => $giorniLavoratiEffettivi > 0 ? round(($kpiTotali->ore ?? 0) / $giorniLavoratiEffettivi * $giorniLavorabiliPrevisti, 2) : 0,
            'pezzi_paf' => $giorniLavoratiEffettivi > 0 ? round(($kpiTotali->inserito_pda ?? 0) / $giorniLavoratiEffettivi * $giorniLavorabiliPrevisti, 2) : 0,
            'resa_paf' => 0, // Calcolato dopo
            
            // Dati calendario
            'giorni_lavorabili_previsti' => $giorniLavorabiliPrevisti,
            'giorni_lavorati_effettivi' => $giorniLavoratiEffettivi,
            'giorni_lavorativi_rimanenti' => $giorniLavorativiRimanenti,
        ];
        
        // Calcola Resa PAF = Pezzi PAF / Ore PAF
        $kpiArray['resa_paf'] = $kpiArray['ore_paf'] > 0 ? round($kpiArray['pezzi_paf'] / $kpiArray['ore_paf'], 2) : 0;
        
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
                SUM(ore_lavorate) as ore,
                SUM(totale_kpi) as obiettivo,
                0 as backlog_partner_pda
            ')
            ->groupBy('commessa', 'campagna_id', 'nome_sede')
            ->orderBy('commessa')
            ->orderBy('nome_sede')
            ->orderBy('campagna_id')
            ->get()
            ->groupBy('cliente')
            ->map(function($clienteGroup) use ($giorniLavorabiliPrevisti, $giorniLavoratiEffettivi, $giorniLavorativiRimanenti, $obiettiviKpi) {
                return $clienteGroup->groupBy('sede')->map(function($sedeGroup) use ($giorniLavorabiliPrevisti, $giorniLavoratiEffettivi, $giorniLavorativiRimanenti, $obiettiviKpi) {
                    return $sedeGroup->groupBy('campagna')->map(function($campagneGroup) use ($giorniLavorabiliPrevisti, $giorniLavoratiEffettivi, $giorniLavorativiRimanenti, $obiettiviKpi) {
                        $data = $campagneGroup->first();
                        $inseriti = (int)$data->inserito_pda;
                        $prodotto = (int)$data->prodotto_pda;
                        $ore = (float)$data->ore;
                        
                        // === RECUPERA OBIETTIVO PER QUESTA COMMESSA/SEDE ===
                        $chiaveObiettivo = strtoupper($data->cliente) . '|' . strtoupper($data->sede);
                        $obiettivoMensile = $obiettiviKpi->get($chiaveObiettivo, 0);
                        
                        // === CALCOLI RESA ===
                        $resa_prodotto = $ore > 0 ? round($prodotto / $ore, 2) : 0;
                        $resa_inserito = $ore > 0 ? round($inseriti / $ore, 2) : 0;
                        
                        // === CALCOLI OBIETTIVI ===
                        // Differenza Obj può essere negativa (è l'unico campo dove ha senso)
                        $differenzaObj = $obiettivoMensile - $inseriti;
                        
                        // Passo Giorno: solo se ci sono giorni rimanenti E c'è ancora da raggiungere l'obiettivo
                        $passoGiorno = 0;
                        if ($giorniLavorativiRimanenti > 0 && $differenzaObj > 0) {
                            $passoGiorno = round($differenzaObj / $giorniLavorativiRimanenti, 2);
                        }
                        
                        // === CALCOLI PAF MENSILE ===
                        // Ore PAF e Pezzi PAF devono essere sempre positivi
                        $ore_paf = $giorniLavoratiEffettivi > 0 ? max(0, round($ore / $giorniLavoratiEffettivi * $giorniLavorabiliPrevisti, 2)) : 0;
                        $pezzi_paf = $giorniLavoratiEffettivi > 0 ? max(0, round($inseriti / $giorniLavoratiEffettivi * $giorniLavorabiliPrevisti, 2)) : 0;
                        $resa_paf = $ore_paf > 0 ? round($pezzi_paf / $ore_paf, 2) : 0;
                        
                        return [
                            'campagna' => $data->campagna,
                            'prodotti_aggiuntivi' => [],
                            'prodotto_pda' => $prodotto,
                            'inserito_pda' => $inseriti,
                            'ko_pda' => (int)$data->ko_pda,
                            'backlog_pda' => (int)$data->backlog_pda,
                            'backlog_partner_pda' => 0,
                            'cliente' => $data->cliente,
                            'cliente_originale' => $data->cliente,
                            'sede' => $data->sede,
                            'ore' => $ore,
                            'obiettivo' => (int)($data->obiettivo ?? 0),
                            
                            // === RESA ===
                            'resa_prodotto' => $resa_prodotto,
                            'resa_inserito' => $resa_inserito,
                            
                            // === OBIETTIVI ===
                            'obiettivo_mensile' => round($obiettivoMensile, 0),
                            'passo_giorno' => $passoGiorno,
                            'differenza_obj' => round($differenzaObj, 0),
                            
                            // === PAF MENSILE ===
                            'ore_paf' => $ore_paf,
                            'pezzi_paf' => $pezzi_paf,
                            'resa_paf' => $resa_paf,
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
                SUM(ore_lavorate) as ore,
                SUM(totale_kpi) as obiettivo
            ')
            ->groupBy('commessa', 'nome_sede')
            ->orderBy('commessa')
            ->orderBy('nome_sede')
            ->get()
            ->groupBy('cliente')
            ->map(function($clienteGroup) use ($giorniLavorabiliPrevisti, $giorniLavoratiEffettivi, $giorniLavorativiRimanenti, $obiettiviKpi) {
                return $clienteGroup->groupBy('sede')->map(function($sedeGroup) use ($giorniLavorabiliPrevisti, $giorniLavoratiEffettivi, $giorniLavorativiRimanenti, $obiettiviKpi) {
                    $data = $sedeGroup->first();
                    $inseriti = (int)$data->inserito_pda;
                    $prodotto = (int)$data->prodotto_pda;
                    $ore = (float)$data->ore;
                    
                    // === RECUPERA OBIETTIVO PER QUESTA COMMESSA/SEDE ===
                    $chiaveObiettivo = strtoupper($data->cliente) . '|' . strtoupper($data->sede);
                    $obiettivoMensile = $obiettiviKpi->get($chiaveObiettivo, 0);
                    
                    // === CALCOLI RESA ===
                    $resa_prodotto = $ore > 0 ? round($prodotto / $ore, 2) : 0;
                    $resa_inserito = $ore > 0 ? round($inseriti / $ore, 2) : 0;
                    
                    // === CALCOLI OBIETTIVI ===
                    // Differenza Obj può essere negativa (è l'unico campo dove ha senso)
                    $differenzaObj = $obiettivoMensile - $inseriti;
                    
                    // Passo Giorno: solo se ci sono giorni rimanenti E c'è ancora da raggiungere l'obiettivo
                    $passoGiorno = 0;
                    if ($giorniLavorativiRimanenti > 0 && $differenzaObj > 0) {
                        $passoGiorno = round($differenzaObj / $giorniLavorativiRimanenti, 2);
                    }
                    
                    // === CALCOLI PAF MENSILE ===
                    // Ore PAF e Pezzi PAF devono essere sempre positivi
                    $ore_paf = $giorniLavoratiEffettivi > 0 ? max(0, round($ore / $giorniLavoratiEffettivi * $giorniLavorabiliPrevisti, 2)) : 0;
                    $pezzi_paf = $giorniLavoratiEffettivi > 0 ? max(0, round($inseriti / $giorniLavoratiEffettivi * $giorniLavorabiliPrevisti, 2)) : 0;
                    $resa_paf = $ore_paf > 0 ? round($pezzi_paf / $ore_paf, 2) : 0;
                    
                    return collect([
                        'totale' => [
                            'campagna' => 'TOTALE SEDE',
                            'prodotti_aggiuntivi' => [],
                            'prodotto_pda' => $prodotto,
                            'inserito_pda' => $inseriti,
                            'ko_pda' => (int)$data->ko_pda,
                            'backlog_pda' => (int)$data->backlog_pda,
                            'backlog_partner_pda' => 0,
                            'cliente' => $data->cliente,
                            'cliente_originale' => $data->cliente,
                            'sede' => $data->sede,
                            'ore' => $ore,
                            'obiettivo' => (int)($data->obiettivo ?? 0),
                            
                            // === RESA ===
                            'resa_prodotto' => $resa_prodotto,
                            'resa_inserito' => $resa_inserito,
                            
                            // === OBIETTIVI ===
                            'obiettivo_mensile' => round($obiettivoMensile, 0),
                            'passo_giorno' => $passoGiorno,
                            'differenza_obj' => round($differenzaObj, 0),
                            
                            // === PAF MENSILE ===
                            'ore_paf' => $ore_paf,
                            'pezzi_paf' => $pezzi_paf,
                            'resa_paf' => $resa_paf,
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
