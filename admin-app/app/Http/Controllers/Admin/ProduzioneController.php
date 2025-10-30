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
        $dataInizio = $request->input('data_inizio', date('Y-m-01')); // Default: primo giorno del mese corrente
        $dataFine = $request->input('data_fine', date('Y-m-d')); // Default: oggi
        $commessaFilter = $request->input('commessa'); // Singola commessa
        $sedeFilters = $request->input('sede', []); // Array di sedi
        $macroCampagnaFilters = $request->input('macro_campagna', []); // Array di campagne
        
        // === CALCOLI CALENDARIO (per metriche PAF e Obiettivi) ===
        $annoCorrente = date('Y');
        $meseCorrente = date('m');
        
        // === VERIFICA SE MOSTRARE PAF ===
        // La PAF deve essere visibile SOLO se stiamo filtrando il mese corrente
        // Se l'utente filtra anche un solo giorno del mese precedente, la PAF non deve uscire
        $mostraPaf = false;
        if ($dataInizio && $dataFine) {
            $dataInizioObj = new \DateTime($dataInizio);
            $dataFineObj = new \DateTime($dataFine);
            $primoGiornoMeseCorrente = new \DateTime(date('Y-m-01'));
            $ultimoGiornoMeseCorrente = new \DateTime(date('Y-m-t'));
            
            // Verifica che entrambe le date siano all'interno del mese corrente
            $mostraPaf = ($dataInizioObj >= $primoGiornoMeseCorrente && $dataFineObj <= $ultimoGiornoMeseCorrente);
        }
        
        // Giorni rimanenti (incluso oggi se è un giorno lavorativo) - dal calendario generale
        $giorniLavorativiRimanenti = CalendarioAziendale::giorniLavorativiRimanenti($annoCorrente, $meseCorrente);
        
        // === RECUPERA GIORNI LAVORATI PER SEDE E MACRO_CAMPAGNA DALLA VISTA ===
        // Questa vista contiene i giorni effettivamente lavorati per ogni combinazione sede+campagna
        $giorniLavoratiPerCampagna = collect();
        if ($mostraPaf) {
            $giorniLavoratiPerCampagna = DB::table('view_giorni_paf')
                ->where('anno', $annoCorrente)
                ->where('mese', $meseCorrente)
                ->when(!empty($sedeFilters), fn($q) => $q->whereIn('nome_sede', $sedeFilters))
                ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('macro_campagna', $macroCampagnaFilters))
                ->get()
                ->keyBy(function($item) {
                    return strtoupper($item->nome_sede) . '|' . strtoupper($item->macro_campagna);
                });
        }
        
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
            ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
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
        
        if ($commessaFilter) {
            $query->where('commessa', $commessaFilter);
        }
        
        if (!empty($sedeFilters)) {
            $query->whereIn('nome_sede', $sedeFilters);
        }
        
        if (!empty($macroCampagnaFilters)) {
            $query->whereIn('campagna_id', $macroCampagnaFilters);
        }
        
        // === KPI TOTALI (aggregazione SQL diretta) ===
        $kpiTotali = DB::table('report_produzione_pivot_cache')
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
            ->when(!empty($sedeFilters), fn($q) => $q->whereIn('nome_sede', $sedeFilters))
            ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('campagna_id', $macroCampagnaFilters))
            ->selectRaw('
                SUM(totale_vendite) as prodotto_pda,
                SUM(ok_definitivo) as inserito_pda,
                SUM(ko_definitivo) as ko_pda,
                SUM(backlog) as backlog_pda,
                SUM(ore_lavorate) as ore,
                SUM(totale_kpi) as obiettivo_totale
            ')
            ->first();
        
        // === CALCOLI PAF GLOBALI (NUOVO METODO CON VISTA) ===
        $ore_paf_globale = 0;
        $pezzi_paf_globale = 0;
        $resa_paf_globale = 0;
        
        if ($mostraPaf && $giorniLavoratiPerCampagna->isNotEmpty()) {
            // Media pesata dei giorni lavorati di tutte le campagne coinvolte
            $mediaGiorniLavoratiGlobale = $giorniLavoratiPerCampagna->avg('giorni_lavorati');
            
            if ($mediaGiorniLavoratiGlobale > 0) {
                $giorniTotaliPrevisti = $mediaGiorniLavoratiGlobale + $giorniLavorativiRimanenti;
                
                // PAF globale
                $ore_paf_globale = round(($kpiTotali->ore ?? 0) / $mediaGiorniLavoratiGlobale * $giorniTotaliPrevisti, 2);
                $pezzi_paf_globale = round(($kpiTotali->inserito_pda ?? 0) / $mediaGiorniLavoratiGlobale * $giorniTotaliPrevisti, 2);
                $resa_paf_globale = $ore_paf_globale > 0 ? round($pezzi_paf_globale / $ore_paf_globale, 2) : 0;
            }
        }
        
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
            
            // PAF Mensile (NUOVO CALCOLO)
            'ore_paf' => $ore_paf_globale,
            'pezzi_paf' => $pezzi_paf_globale,
            'resa_paf' => $resa_paf_globale,
            
            // Dati calendario
            'giorni_lavorativi_rimanenti' => $giorniLavorativiRimanenti,
            'mostra_paf' => $mostraPaf, // Flag per sapere se mostrare la PAF nella vista
        ];
        
        // === VISTA DETTAGLIATA: Campagna per Sede ===
        $datiDettagliati = DB::table('report_produzione_pivot_cache')
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
            ->when(!empty($sedeFilters), fn($q) => $q->whereIn('nome_sede', $sedeFilters))
            ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('campagna_id', $macroCampagnaFilters))
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
            ->map(function($clienteGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $giorniLavoratiPerCampagna, $mostraPaf) {
                return $clienteGroup->groupBy('sede')->map(function($sedeGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $giorniLavoratiPerCampagna, $mostraPaf) {
                    return $sedeGroup->groupBy('campagna')->map(function($campagneGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $giorniLavoratiPerCampagna, $mostraPaf) {
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
                        
                        // === CALCOLI PAF MENSILE (NUOVO METODO CON VISTA) ===
                        $ore_paf = 0;
                        $pezzi_paf = 0;
                        $resa_paf = 0;
                        
                        if ($mostraPaf) {
                            // Cerca i giorni lavorati per questa specifica combinazione sede+campagna
                            $chiavePaf = strtoupper($data->sede) . '|' . strtoupper($data->campagna);
                            $infoPaf = $giorniLavoratiPerCampagna->get($chiavePaf);
                            
                            if ($infoPaf && $infoPaf->giorni_lavorati > 0) {
                                // Calcola PAF usando i giorni effettivamente lavorati + giorni rimanenti
                                $giorniTotaliPrevisti = $infoPaf->giorni_lavorati + $giorniLavorativiRimanenti;
                                
                                // PAF = (valori attuali / giorni lavorati) * giorni totali previsti
                                $ore_paf = max(0, round($ore / $infoPaf->giorni_lavorati * $giorniTotaliPrevisti, 2));
                                $pezzi_paf = max(0, round($inseriti / $infoPaf->giorni_lavorati * $giorniTotaliPrevisti, 2));
                                $resa_paf = $ore_paf > 0 ? round($pezzi_paf / $ore_paf, 2) : 0;
                            }
                        }
                        
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
            ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
            ->when(!empty($sedeFilters), fn($q) => $q->whereIn('nome_sede', $sedeFilters))
            ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('campagna_id', $macroCampagnaFilters))
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
            ->map(function($clienteGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $giorniLavoratiPerCampagna, $mostraPaf) {
                return $clienteGroup->groupBy('sede')->map(function($sedeGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $giorniLavoratiPerCampagna, $mostraPaf) {
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
                    
                    // === CALCOLI PAF MENSILE (NUOVO METODO CON VISTA) ===
                    // Per la vista sintetica, sommiamo tutti i giorni lavorati delle campagne di questa sede
                    $ore_paf = 0;
                    $pezzi_paf = 0;
                    $resa_paf = 0;
                    
                    if ($mostraPaf) {
                        // Somma giorni lavorati per tutte le campagne di questa sede
                        $giorniLavoratiTotaliSede = $giorniLavoratiPerCampagna
                            ->filter(function($item) use ($data) {
                                return stripos($item->nome_sede, $data->sede) !== false || stripos($data->sede, $item->nome_sede) !== false;
                            })
                            ->sum('giorni_lavorati');
                        
                        if ($giorniLavoratiTotaliSede > 0) {
                            // Media pesata dei giorni lavorati per la sede
                            $numCampagne = $giorniLavoratiPerCampagna->filter(function($item) use ($data) {
                                return stripos($item->nome_sede, $data->sede) !== false || stripos($data->sede, $item->nome_sede) !== false;
                            })->count();
                            
                            if ($numCampagne > 0) {
                                $mediaGiorniLavorati = $giorniLavoratiTotaliSede / $numCampagne;
                                $giorniTotaliPrevisti = $mediaGiorniLavorati + $giorniLavorativiRimanenti;
                                
                                // PAF = (valori attuali / media giorni lavorati) * giorni totali previsti
                                $ore_paf = max(0, round($ore / $mediaGiorniLavorati * $giorniTotaliPrevisti, 2));
                                $pezzi_paf = max(0, round($inseriti / $mediaGiorniLavorati * $giorniTotaliPrevisti, 2));
                                $resa_paf = $ore_paf > 0 ? round($pezzi_paf / $ore_paf, 2) : 0;
                            }
                        }
                    }
                    
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
        // Lista tutte le commesse disponibili
        $commesse = DB::table('report_produzione_pivot_cache')
            ->distinct()
            ->whereNotNull('commessa')
            ->orderBy('commessa')
            ->pluck('commessa');
        
        // Sedi filtrate per commessa selezionata (se presente)
        $sedi = collect();
        if ($commessaFilter) {
            $sedi = DB::table('report_produzione_pivot_cache')
                ->where('commessa', $commessaFilter)
                ->distinct()
                ->whereNotNull('nome_sede')
                ->where('nome_sede', '!=', '')
                ->orderBy('nome_sede')
                ->pluck('nome_sede');
        }
        
        // Macro campagne filtrate per commessa (se presente)
        $macroCampagne = collect();
        if ($commessaFilter) {
            $macroCampagne = DB::table('report_produzione_pivot_cache')
                ->where('commessa', $commessaFilter)
                ->distinct()
                ->whereNotNull('campagna_id')
                ->where('campagna_id', '!=', '')
                ->orderBy('campagna_id')
                ->pluck('campagna_id');
        }
        
        return view('admin.modules.produzione.cruscotto-produzione', [
            'kpiTotali' => $kpiArray,
            'datiRaggruppati' => $datiDettagliati, // Default
            'datiDettagliati' => $datiDettagliati,
            'datiSintetici' => $datiSintetici,
            'oreRaggruppate' => [], // Già incluse nella cache!
            'commesse' => $commesse,
            'sedi' => $sedi,
            'macroCampagne' => $macroCampagne,
            'dataInizio' => $dataInizio ?? '',
            'dataFine' => $dataFine ?? '',
            'commessaFilter' => $commessaFilter ?? '',
            'sedeFilters' => $sedeFilters ?? [],
            'macroCampagnaFilters' => $macroCampagnaFilters ?? [],
        ]);
    }

    /**
     * API: Ottieni sedi per una specifica commessa
     */
    public function getSedi(Request $request)
    {
        $this->authorize('produzione.view');
        
        $commessa = $request->input('commessa');
        
        if (!$commessa) {
            return response()->json([]);
        }
        
        $sedi = DB::table('report_produzione_pivot_cache')
            ->where('commessa', $commessa)
            ->distinct()
            ->whereNotNull('nome_sede')
            ->where('nome_sede', '!=', '')
            ->orderBy('nome_sede')
            ->pluck('nome_sede');
        
        return response()->json($sedi);
    }

    /**
     * API: Ottieni macro campagne per una specifica commessa e sede
     */
    public function getCampagne(Request $request)
    {
        $this->authorize('produzione.view');
        
        $commessa = $request->input('commessa');
        
        if (!$commessa) {
            return response()->json([]);
        }
        
        $campagne = DB::table('report_produzione_pivot_cache')
            ->where('commessa', $commessa)
            ->distinct()
            ->whereNotNull('campagna_id')
            ->where('campagna_id', '!=', '')
            ->orderBy('campagna_id')
            ->pluck('campagna_id');
        
        return response()->json($campagne);
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
        
        $anno = $request->input('anno', date('Y'));
        $mese = $request->input('mese', date('m'));
        
        $targetMensili = KpiTargetMensile::where('anno', $anno)
            ->where('mese', $mese)
            ->orderBy('commessa')
            ->orderBy('sede_crm')
            ->orderBy('nome_kpi')
            ->paginate(50);
        
        $rendicontoProduzione = KpiRendicontoProduzione::orderBy('commessa')
            ->orderBy('servizio_mandato')
            ->orderBy('nome_kpi')
            ->get();
        
        $targetPerCommessa = $targetMensili->groupBy('commessa');
        $rendicontoPerCommessa = $rendicontoProduzione->groupBy('commessa');
        
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
        
        return view('admin.modules.produzione.kpi-target.index', [
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
     * Aggiorna singolo campo KPI via AJAX
     */
    public function updateKpiField(Request $request, $id)
    {
        $this->authorize('produzione.edit');
        
        $validated = $request->validate([
            'field' => 'required|in:commessa,sede_crm,sede_estesa,macro_campagna,nome_kpi,tipo_kpi,valore_kpi,tipologia_obiettivo',
            'value' => 'required',
        ]);
        
        try {
            $kpi = KpiTargetMensile::findOrFail($id);
            
            // Se il campo è valore_kpi, converti a numero
            if ($validated['field'] === 'valore_kpi') {
                $kpi->valore_kpi = floatval($validated['value']);
            } else {
                $kpi->{$validated['field']} = $validated['value'];
            }
            
            $kpi->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Campo aggiornato con successo',
                'data' => $kpi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mostra form creazione KPI Target
     */
    public function createKpiTarget()
    {
        $this->authorize('produzione.create');
        
        // Lista commesse e sedi disponibili
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
        
        return view('admin.modules.produzione.kpi-target.create', [
            'commesse' => $commesse,
            'sedi' => $sedi,
        ]);
    }
    
    /**
     * Salva nuovo KPI Target
     */
    public function storeKpiTarget(Request $request)
    {
        $this->authorize('produzione.create');
        
        $validated = $request->validate([
            'commessa' => 'required|string|max:100',
            'sede_crm' => 'required|string|max:100',
            'sede_estesa' => 'nullable|string|max:255',
            'macro_campagna' => 'nullable|string|max:255',
            'nome_kpi' => 'required|string|max:100',
            'tipo_kpi' => 'nullable|string|max:50',
            'anno' => 'required|integer|min:2020|max:2030',
            'mese' => 'required|integer|min:1|max:12',
            'valore_kpi' => 'required|numeric|min:0',
            'tipologia_obiettivo' => 'nullable|string|max:50',
            'tipologia_valore_obiettivo' => 'nullable|string|max:50',
            'kpi_variato' => 'nullable|numeric|min:0',
            'data_validita_inizio' => 'nullable|date',
            'data_validita_fine' => 'nullable|date|after_or_equal:data_validita_inizio',
        ], [
            'commessa.required' => 'La commessa è obbligatoria',
            'sede_crm.required' => 'La sede CRM è obbligatoria',
            'nome_kpi.required' => 'Il nome KPI è obbligatorio',
            'anno.required' => 'L\'anno è obbligatorio',
            'mese.required' => 'Il mese è obbligatorio',
            'valore_kpi.required' => 'Il valore KPI è obbligatorio',
            'data_validita_fine.after_or_equal' => 'La data fine deve essere uguale o successiva alla data inizio',
        ]);
        
        KpiTargetMensile::create($validated);
        
        return redirect()
            ->route('admin.produzione.kpi_target', ['anno' => $validated['anno'], 'mese' => sprintf('%02d', $validated['mese'])])
            ->with('success', 'KPI Target creato con successo');
    }
    
    /**
     * Mostra dettaglio KPI Target
     */
    public function showKpiTarget($id)
    {
        $this->authorize('produzione.view');
        
        $kpi = KpiTargetMensile::findOrFail($id);
        
        return view('admin.modules.produzione.kpi-target.show', compact('kpi'));
    }
    
    /**
     * Elimina singolo KPI Target
     */
    public function deleteKpiTarget($id)
    {
        $this->authorize('produzione.delete');
        
        try {
            $kpi = KpiTargetMensile::findOrFail($id);
            $kpi->delete();
            
            return redirect()->back()->with('success', 'KPI eliminato con successo');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }
    
    /**
     * Elimina multipli KPI Target (bulk delete)
     */
    public function bulkDeleteKpiTarget(Request $request)
    {
        $this->authorize('produzione.delete');
        
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:kpi_target_mensile,id',
        ]);
        
        try {
            $count = KpiTargetMensile::whereIn('id', $validated['ids'])->delete();
            
            return redirect()->back()->with('success', "Eliminati {$count} KPI con successo");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }
    
    /**
     * Aggiorna i campi di variazione KPI (kpi_variato, data_validita_inizio, data_validita_fine)
     */
    public function updateKpiVariazione(Request $request, $id)
    {
        $this->authorize('produzione.edit');
        
        $validated = $request->validate([
            'kpi_variato' => 'nullable|numeric|min:0',
            'data_validita_inizio' => 'nullable|date',
            'data_validita_fine' => 'nullable|date|after_or_equal:data_validita_inizio',
        ]);
        
        try {
            $kpi = KpiTargetMensile::findOrFail($id);
            
            // Aggiorna i campi
            $kpi->kpi_variato = $validated['kpi_variato'];
            $kpi->data_validita_inizio = $validated['data_validita_inizio'];
            $kpi->data_validita_fine = $validated['data_validita_fine'];
            
            $kpi->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Variazione KPI aggiornata con successo',
                'data' => $kpi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()
            ], 500);
        }
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
