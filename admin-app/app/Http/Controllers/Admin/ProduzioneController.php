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
                'commessa' => 'required|string',
            ], [
                'data_inizio.required' => 'La data di inizio è obbligatoria.',
                'data_inizio.date' => 'La data di inizio deve essere una data valida.',
                'data_fine.required' => 'La data di fine è obbligatoria.',
                'data_fine.date' => 'La data di fine deve essere una data valida.',
                'data_fine.after_or_equal' => 'La data di fine deve essere uguale o successiva alla data di inizio.',
                'commessa.required' => 'La selezione della commessa è obbligatoria.',
            ]);
        }
        
        // === FILTRI ===
        $dataInizio = $request->input('data_inizio', date('Y-m-01')); // Default: primo giorno del mese corrente
        $dataFine = $request->input('data_fine', date('Y-m-d')); // Default: oggi
        $commessaFilter = $request->input('commessa'); // Singola commessa
        $sedeFilters = $request->input('sede', []); // Array di sedi
        $macroCampagnaFilters = $request->input('macro_campagna', []); // Array di campagne
        
        // Includi bonus solo se l'utente ha i ruoli autorizzati (CEO, CFO, CTO, superadmin, IT)
        $includiBonus = $request->input('includi_bonus', false) && ($request->user()?->hasRole(['CEO', 'CFO', 'CTO', 'super-admin', 'IT']) ?? false);
        
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
        
        // === RECUPERA DATI PAF DALLA VISTA view_giorni_paf ===
        // Questa vista contiene per ogni sede+campagna:
        // - giorni_lavorati: giorni già lavorati nel mese
        // - tot_giorni_mese: totale giorni lavorativi del mese
        // - giorni_rimanenti: giorni lavorativi rimanenti (REAL-TIME, specifico per campagna)
        $giorniLavoratiPerCampagna = collect();
        if ($mostraPaf) {
            $giorniLavoratiPerCampagna = DB::table('view_giorni_paf')
                ->where('anno', $annoCorrente)
                ->where('mese', $meseCorrente)
                ->when(!empty($sedeFilters), fn($q) => $q->whereIn('nome_sede', $sedeFilters))
                ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('macro_campagna', $macroCampagnaFilters))
                ->get()
                ->keyBy(function($item) {
                    // Chiave: ID_SEDE|MACRO_CAMPAGNA (per match preciso)
                    return strtoupper($item->id_sede) . '|' . strtoupper($item->macro_campagna);
                });
        }
        
        // Giorni rimanenti GENERALI (per obiettivi e calcoli non PAF)
        $giorniLavorativiRimanenti = CalendarioAziendale::giorniLavorativiRimanenti($annoCorrente, $meseCorrente);
        
        // === MAPPING SEDI: RIMOSSO - ORA NELLA CACHE ===
        // Il campo nome_sede in report_produzione_pivot_cache contiene già il nome corretto
        // Non serve più il mapping manuale
        /*
        $mappingSedi = [
            'FRC_FONT' => 'FRANCAVILLA FONTANA',
            'LMZ' => 'LAMEZIA TERME',
            'LCR' => 'LOCRI',
            'TAR' => 'TARANTO',
            'VIGEVANO' => 'VIGEVANO',
            'TOTALE' => 'TOTALE',
            'RND' => 'RND',
            'MARSALA' => 'MARSALA',
        ];
        */
        
        // === RECUPERA OBIETTIVI DA kpi_target_mensile ===
        // IMPORTANTE: Prendiamo SOLO i record con tipologia_obiettivo = 'OBIETTIVO'
        // Ora usa direttamente sede_crm senza mapping e include macro_campagna per matching preciso
        $obiettiviKpiRaw = DB::table('kpi_target_mensile')
            ->where('anno', $annoCorrente)
            ->where('mese', $meseCorrente)
            ->where('tipologia_obiettivo', 'OBIETTIVO')
            ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
            ->get();
        
        // Mappa principale con chiave per sede_crm (id_sede)
        $obiettiviKpi = $obiettiviKpiRaw
            ->keyBy(function($item) {
                return strtoupper($item->commessa) . '|' . strtoupper($item->sede_crm ?? '') . '|' . strtoupper($item->macro_campagna ?? '');
            })
            ->map(function($item) {
                $kpiTemp = new KpiTargetMensile();
                foreach((array)$item as $key => $value) {
                    $kpiTemp->{$key} = $value;
                }
                return $kpiTemp->getMediaPonderata();
            });
        
        // Mappa alternativa con chiave per sede_estesa (nome sede) come fallback
        $obiettiviKpiByNome = $obiettiviKpiRaw
            ->keyBy(function($item) {
                return strtoupper($item->commessa) . '|' . strtoupper($item->sede_estesa ?? '') . '|' . strtoupper($item->macro_campagna ?? '');
            })
            ->map(function($item) {
                $kpiTemp = new KpiTargetMensile();
                foreach((array)$item as $key => $value) {
                    $kpiTemp->{$key} = $value;
                }
                return $kpiTemp->getMediaPonderata();
            });
        
        // === QUERY UNIFICATA OTTIMIZZATA ===
        // Recupera TUTTI i dati necessari in una sola query, poi li elabora in memoria
        // ESCLUDI sempre i bonus dalla query principale (saranno gestiti separatamente se richiesto)
        $queryBase = DB::table('report_produzione_pivot_cache')
            ->where('campagna_id', '!=', 'bonus') // Esclude i bonus dalla query principale
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
            ->when(!empty($sedeFilters), fn($q) => $q->whereIn('report_produzione_pivot_cache.nome_sede', $sedeFilters))
            ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('macro_campagna', $macroCampagnaFilters));
        
        // === KPI TOTALI (aggregazione SQL diretta) ===
        $kpiTotali = (clone $queryBase)
            ->selectRaw('
                SUM(totale_vendite) as prodotto_pda,
                SUM(ok_definitivo) as inserito_pda,
                SUM(ko_definitivo) as ko_pda,
                SUM(backlog) as backlog_pda,
                SUM(backlog_partner) as backlog_partner_pda,
                SUM(ore_lavorate) as ore,
                SUM(totale_kpi) as obiettivo_totale,
                SUM(totale_abbattuto) as fatturato
            ')
            ->first();
        
        // === CALCOLO BONUS (se richiesto) ===
        $bonusTotale = 0;
        $bonusPerSedeECampagna = collect(); // Chiave: sede|campagna => importo
        $bonusGlobali = 0; // Totale bonus globali (senza sede)
        $bonusGlobaliDettaglio = collect(); // Dettaglio bonus globali per visualizzazione
        $bonusQuery = collect(); // Inizializza vuoto
        
        if ($includiBonus && $dataInizio && $dataFine && $commessaFilter) {
            // Recupera i bonus dalla PIVOT CACHE (campagna_id = 'bonus')
            $bonusQuery = DB::table('report_produzione_pivot_cache')
                ->where('campagna_id', 'bonus')
                ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
                ->when($commessaFilter, fn($q) => $q->where('commessa', $commessaFilter))
                ->when(!empty($sedeFilters), fn($q) => $q->whereIn('nome_sede', $sedeFilters))
                ->when(!empty($macroCampagnaFilters), fn($q) => $q->whereIn('macro_campagna', $macroCampagnaFilters))
                ->get();
            
            foreach ($bonusQuery as $bonus) {
                $importo = floatval($bonus->totale_abbattuto);
                
                // Determina se è un bonus GLOBALE o per sede specifica
                if ($bonus->id_sede === 'GLOBAL' || empty($bonus->id_sede)) {
                    // BONUS GLOBALE: conta UNA VOLTA SOLA in totale
                    $bonusGlobali += $importo;
                    $bonusGlobaliDettaglio->push([
                        'macro_campagna' => $bonus->macro_campagna,
                        'importo' => $importo,
                        'data_vendita' => $bonus->data_vendita,
                        'id_sede' => $bonus->id_sede,
                        'nome_sede' => $bonus->nome_sede,
                    ]);
                } else {
                    // BONUS PER SEDE SPECIFICA
                    $chiave = strtoupper($bonus->id_sede) . '|' . strtoupper($bonus->macro_campagna ?? '');
                    if (!$bonusPerSedeECampagna->has($chiave)) {
                        $bonusPerSedeECampagna->put($chiave, 0);
                    }
                    $bonusPerSedeECampagna->put($chiave, $bonusPerSedeECampagna->get($chiave) + $importo);
                }
            }
            
            // Totale bonus = globali + per sede
            $bonusTotale = $bonusGlobali + $bonusPerSedeECampagna->sum();
        }
        
        // === CALCOLI PAF GLOBALI (METODO REAL-TIME CON view_giorni_paf) ===
        $ore_paf_globale = 0;
        $pezzi_paf_globale = 0;
        $resa_paf_globale = 0;
        $fatturato_paf_globale = 0;
        
        if ($mostraPaf && $giorniLavoratiPerCampagna->isNotEmpty()) {
            // Usa la media dei giorni_lavorati e tot_giorni_mese dalla vista
            $mediaGiorniLavorati = $giorniLavoratiPerCampagna->avg('giorni_lavorati');
            $mediaTotGiorniMese = $giorniLavoratiPerCampagna->avg('tot_giorni_mese');
            
            if ($mediaGiorniLavorati > 0 && $mediaTotGiorniMese > 0) {
                // PAF globale basato sul totale giorni mese dalla vista
                $ore_paf_globale = round(($kpiTotali->ore ?? 0) / $mediaGiorniLavorati * $mediaTotGiorniMese, 2);
                $pezzi_paf_globale = round(($kpiTotali->inserito_pda ?? 0) / $mediaGiorniLavorati * $mediaTotGiorniMese, 2);
                $resa_paf_globale = $ore_paf_globale > 0 ? round($pezzi_paf_globale / $ore_paf_globale, 2) : 0;
                
                // PAF Fatturato: proietta SOLO il fatturato operativo, poi aggiunge i bonus FISSI
                $fatturato_operativo_paf = round(($kpiTotali->fatturato ?? 0) / $mediaGiorniLavorati * $mediaTotGiorniMese, 2);
                
                // BONUS: sommati FISSI (NON proiettati), sia bonus per sede che globali
                $fatturato_paf_globale = $fatturato_operativo_paf + $bonusTotale;
            }
        }
        
        $kpiArray = [
            'prodotto_pda' => $kpiTotali->prodotto_pda ?? 0,
            'inserito_pda' => $kpiTotali->inserito_pda ?? 0,
            'ko_pda' => $kpiTotali->ko_pda ?? 0,
            'backlog_pda' => $kpiTotali->backlog_pda ?? 0,
            'backlog_partner_pda' => $kpiTotali->backlog_partner_pda ?? 0,
            'ore' => $kpiTotali->ore ?? 0,
            'obiettivo' => $kpiTotali->obiettivo_totale ?? 0,
            'fatturato' => ($kpiTotali->fatturato ?? 0) + $bonusTotale, // Aggiungi bonus al fatturato
            
            // Resa
            'resa_prodotto' => ($kpiTotali->ore ?? 0) > 0 ? round(($kpiTotali->prodotto_pda ?? 0) / $kpiTotali->ore, 2) : 0,
            'resa_inserito' => ($kpiTotali->ore ?? 0) > 0 ? round(($kpiTotali->inserito_pda ?? 0) / $kpiTotali->ore, 2) : 0,
            'ricavo_orario' => ($kpiTotali->ore ?? 0) > 0 ? round((($kpiTotali->fatturato ?? 0) + $bonusTotale) / $kpiTotali->ore, 2) : 0, // Include bonus
            
            // Obiettivi (al momento a 0 come richiesto)
            'obiettivo_mensile' => 0,
            'passo_giorno' => 0,
            'differenza_obj' => 0,
            
            // PAF Mensile (NUOVO CALCOLO)
            'ore_paf' => $ore_paf_globale,
            'pezzi_paf' => $pezzi_paf_globale,
            'resa_paf' => $resa_paf_globale,
            'fatturato_paf' => $fatturato_paf_globale,
            
            // Dati calendario
            'giorni_lavorativi_rimanenti' => $giorniLavorativiRimanenti,
            'mostra_paf' => $mostraPaf, // Flag per sapere se mostrare la PAF nella vista
            
            // Informazioni bonus e incentivi
            'bonus_globali' => $bonusGlobali,
            'bonus_per_sede' => $bonusPerSedeECampagna->sum(),
            'bonus_totale' => $bonusTotale,
            'bonus_dettaglio' => $bonusGlobaliDettaglio,
        ];
        
        // === VISTA DETTAGLIATA: Campagna per Sede ===
        // LEFT JOIN con sedi per ottenere l'ID sede per matching KPI
        $datiDettagliati = (clone $queryBase)
            ->leftJoin('sedi', function($join) {
                $join->on(DB::raw('UPPER(TRIM(report_produzione_pivot_cache.nome_sede))'), '=', DB::raw('UPPER(TRIM(sedi.nome_sede))'));
            })
            ->selectRaw('
                report_produzione_pivot_cache.commessa as cliente,
                report_produzione_pivot_cache.macro_campagna as campagna,
                report_produzione_pivot_cache.nome_sede as sede,
                sedi.id_sede as sede_id,
                SUM(report_produzione_pivot_cache.totale_vendite) as prodotto_pda,
                SUM(report_produzione_pivot_cache.ok_definitivo) as inserito_pda,
                SUM(report_produzione_pivot_cache.ko_definitivo) as ko_pda,
                SUM(report_produzione_pivot_cache.backlog) as backlog_pda,
                SUM(report_produzione_pivot_cache.backlog_partner) as backlog_partner_pda,
                SUM(report_produzione_pivot_cache.ore_lavorate) as ore,
                SUM(report_produzione_pivot_cache.totale_kpi) as obiettivo,
                SUM(report_produzione_pivot_cache.totale_abbattuto) as fatturato
            ')
            ->groupBy('report_produzione_pivot_cache.commessa', 'report_produzione_pivot_cache.macro_campagna', 'report_produzione_pivot_cache.nome_sede', 'sedi.id_sede')
            ->orderBy('report_produzione_pivot_cache.commessa')
            ->orderBy('report_produzione_pivot_cache.nome_sede')
            ->orderBy('report_produzione_pivot_cache.macro_campagna')
            ->get()
            ->groupBy('cliente')
            ->map(function($clienteGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $obiettiviKpiByNome, $giorniLavoratiPerCampagna, $mostraPaf, $bonusPerSedeECampagna, $bonusGlobali, $bonusGlobaliDettaglio, $includiBonus, $commessaFilter, $bonusQuery) {
                $sediData = $clienteGroup->groupBy('sede')->map(function($sedeGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $obiettiviKpiByNome, $giorniLavoratiPerCampagna, $mostraPaf, $bonusPerSedeECampagna, $bonusGlobali, $bonusGlobaliDettaglio, $includiBonus, $bonusQuery) {
                    $economicsData = $sedeGroup->groupBy('campagna')->map(function($campagneGroup) use ($giorniLavorativiRimanenti, $obiettiviKpi, $obiettiviKpiByNome, $giorniLavoratiPerCampagna, $mostraPaf, $bonusPerSedeECampagna, $bonusGlobali, $bonusGlobaliDettaglio, $includiBonus) {
                        $data = $campagneGroup->first();
                        $inseriti = (int)$data->inserito_pda;
                        $prodotto = (int)$data->prodotto_pda;
                        $ore = (float)$data->ore;
                        $fatturato = (float)($data->fatturato ?? 0);
                        
                        // NON aggiungiamo più i bonus qui - saranno righe separate
                        
                        // === RECUPERA OBIETTIVO PER QUESTA COMMESSA/SEDE_ID/MACRO_CAMPAGNA ===
                        // Prima prova con sede_id, poi con nome sede come fallback
                        $obiettivoMensile = 0;
                        
                        if ($data->sede_id) {
                            // Match per sede_id se disponibile
                            $chiaveObiettivo = strtoupper($data->cliente) . '|' . $data->sede_id . '|' . strtoupper($data->campagna ?? '');
                            $obiettivoMensile = $obiettiviKpi->get($chiaveObiettivo, 0);
                        }
                        
                        // Fallback: se sede_id è null o non ha trovato obiettivi, prova con il nome della sede
                        if ($obiettivoMensile == 0 && $data->sede) {
                            $chiaveObiettivo = strtoupper($data->cliente) . '|' . strtoupper($data->sede) . '|' . strtoupper($data->campagna ?? '');
                            $obiettivoMensile = $obiettiviKpiByNome->get($chiaveObiettivo, 0);
                        }
                        
                        // === CALCOLI RESA ===
                        $resa_prodotto = $ore > 0 ? round($prodotto / $ore, 2) : 0;
                        $resa_inserito = $ore > 0 ? round($inseriti / $ore, 2) : 0;
                        $ricavo_orario = $ore > 0 ? round($fatturato / $ore, 2) : 0;
                        
                        // === CALCOLI OBIETTIVI ===
                        // Differenza Obj può essere negativa (è l'unico campo dove ha senso)
                        $differenzaObj = $obiettivoMensile - $inseriti;
                        
                        // Passo Giorno: solo se ci sono giorni rimanenti E c'è ancora da raggiungere l'obiettivo
                        $passoGiorno = 0;
                        if ($giorniLavorativiRimanenti > 0 && $differenzaObj > 0) {
                            $passoGiorno = round($differenzaObj / $giorniLavorativiRimanenti, 2);
                        }
                        
                        // === CALCOLI PAF MENSILE (METODO REAL-TIME CON view_giorni_paf) ===
                        $ore_paf = 0;
                        $pezzi_paf = 0;
                        $resa_paf = 0;
                        $fatturato_paf = 0;
                        
                        if ($mostraPaf) {
                            // Cerca i dati PAF per questa specifica combinazione id_sede+macro_campagna
                            // PRIMA prova con sede_id (più preciso)
                            $chiavePaf = null;
                            $infoPaf = null;
                            
                            if ($data->sede_id) {
                                $chiavePaf = strtoupper($data->sede_id) . '|' . strtoupper($data->campagna);
                                $infoPaf = $giorniLavoratiPerCampagna->get($chiavePaf);
                            }
                            
                            // FALLBACK: prova con nome_sede se sede_id non ha dato risultati
                            if (!$infoPaf && $data->sede) {
                                $chiavePaf = strtoupper($data->sede) . '|' . strtoupper($data->campagna);
                                $infoPaf = $giorniLavoratiPerCampagna->get($chiavePaf);
                            }
                            
                            if ($infoPaf && $infoPaf->giorni_lavorati > 0 && $infoPaf->tot_giorni_mese > 0) {
                                // USA tot_giorni_mese dalla vista (già include giorni_lavorati + giorni_rimanenti specifici)
                                // Questo è REAL-TIME e specifico per questa campagna!
                                $giorniTotaliMese = floatval($infoPaf->tot_giorni_mese);
                                
                                // PAF = (valori attuali / giorni già lavorati) * totale giorni mese
                                $ore_paf = max(0, round($ore / $infoPaf->giorni_lavorati * $giorniTotaliMese, 2));
                                $pezzi_paf = max(0, round($inseriti / $infoPaf->giorni_lavorati * $giorniTotaliMese, 2));
                                $resa_paf = $ore_paf > 0 ? round($pezzi_paf / $ore_paf, 2) : 0;
                                
                                // FATTURATO PAF: proietta il fatturato operativo
                                $fatturato_paf = max(0, round($fatturato / $infoPaf->giorni_lavorati * $giorniTotaliMese, 2));
                            }
                        }
                        
                        return [
                            'campagna' => $data->campagna,
                            'prodotti_aggiuntivi' => [],
                            'prodotto_pda' => $prodotto,
                            'inserito_pda' => $inseriti,
                            'ko_pda' => (int)$data->ko_pda,
                            'backlog_pda' => (int)$data->backlog_pda,
                            'backlog_partner_pda' => (int)$data->backlog_partner_pda,
                            'cliente' => $data->cliente,
                            'cliente_originale' => $data->cliente,
                            'sede' => $data->sede,
                            'ore' => $ore,
                            'obiettivo' => (int)($data->obiettivo ?? 0),
                            'fatturato' => $fatturato,
                            
                            // === RESA ===
                            'resa_prodotto' => $resa_prodotto,
                            'resa_inserito' => $resa_inserito,
                            'ricavo_orario' => $ricavo_orario,
                            
                            // === OBIETTIVI ===
                            'obiettivo_mensile' => round($obiettivoMensile, 0),
                            'passo_giorno' => $passoGiorno,
                            'differenza_obj' => round($differenzaObj, 0),
                            
                            // === PAF MENSILE ===
                            'ore_paf' => $ore_paf,
                            'pezzi_paf' => $pezzi_paf,
                            'resa_paf' => $resa_paf,
                            'fatturato_paf' => $fatturato_paf,
                        ];
                    });
                    
                    // === AGGIUNGI RIGHE BONUS PER QUESTA SEDE (se presenti) - DALLA PIVOT CACHE ===
                    if ($includiBonus && $bonusQuery->isNotEmpty()) {
                        $sedeId = $sedeGroup->first()->sede_id;
                        $nomeSede = $sedeGroup->first()->sede;
                        
                        // Filtra i bonus per questa sede specifica dalla pivot cache
                        $bonusSede = $bonusQuery
                            ->filter(function($bonus) use ($sedeId, $nomeSede) {
                                // Bonus per questa sede (non globali)
                                $isGlobal = ($bonus->id_sede === 'GLOBAL' || empty($bonus->id_sede));
                                if ($isGlobal) return false;
                                
                                // Match per id_sede o nome_sede
                                return $bonus->id_sede === $sedeId || 
                                       strtoupper($bonus->nome_sede) === strtoupper($nomeSede);
                            })
                            ->groupBy('macro_campagna'); // Raggruppa per macro_campagna
                        
                        // Aggiungi ogni bonus come riga separata allo stesso livello delle campagne
                        foreach ($bonusSede as $macroCampagna => $bonusGroup) {
                            $importoTotale = $bonusGroup->sum('totale_abbattuto');
                            
                            // Usa la chiave con suffisso per distinguere dai dati normali
                            $chiaveBonus = $macroCampagna . ' (Bonus)';
                            
                            $economicsData->put($chiaveBonus, [
                                'campagna' => $macroCampagna . ' (Bonus)',
                                'prodotti_aggiuntivi' => [],
                                'prodotto_pda' => 0,
                                'inserito_pda' => 0,
                                'ko_pda' => 0,
                                'backlog_pda' => 0,
                                'backlog_partner_pda' => 0,
                                'cliente' => $sedeGroup->first()->cliente,
                                'cliente_originale' => $sedeGroup->first()->cliente,
                                'sede' => $sedeGroup->first()->sede,
                                'ore' => 0,
                                'obiettivo' => 0,
                                'fatturato' => $importoTotale,
                                'resa_prodotto' => 0,
                                'resa_inserito' => 0,
                                'ricavo_orario' => 0,
                                'obiettivo_mensile' => 0,
                                'passo_giorno' => 0,
                                'differenza_obj' => 0,
                                'ore_paf' => 0,
                                'pezzi_paf' => 0,
                                'resa_paf' => 0,
                                'fatturato_paf' => $importoTotale, // BONUS FISSO
                                'is_bonus_sede' => true, // Flag per identificare la riga
                            ]);
                        }
                    }
                    
                    return $economicsData;
                });
                
                return $sediData;
            })
            ->map(function($sediData, $cliente) use ($includiBonus, $bonusGlobali, $bonusGlobaliDettaglio, $commessaFilter) {
                // === AGGIUNGI BONUS GLOBALI UNA SOLA VOLTA PER COMMESSA (fuori dal ciclo sedi) ===
                if ($includiBonus && $bonusGlobali > 0 && $cliente === $commessaFilter) {
                    // Crea una riga speciale per mostrare i bonus globali
                    $economicsData = collect();
                    
                    foreach ($bonusGlobaliDettaglio as $bonusDetail) {
                        $economicsData->put($bonusDetail['macro_campagna'] ?? 'BONUS', [
                            'campagna' => $bonusDetail['macro_campagna'] ?? 'BONUS',
                            'prodotti_aggiuntivi' => [],
                            'prodotto_pda' => 0,
                            'inserito_pda' => 0,
                            'ko_pda' => 0,
                            'backlog_pda' => 0,
                            'backlog_partner_pda' => 0,
                            'cliente' => $commessaFilter,
                            'cliente_originale' => $commessaFilter,
                            'sede' => 'Bonus Globali',
                            'ore' => 0,
                            'obiettivo' => 0,
                            'fatturato' => $bonusDetail['importo'], // BONUS FISSO
                            'resa_prodotto' => 0,
                            'resa_inserito' => 0,
                            'resa_oraria' => 0,
                            'ricavo_orario' => 0,
                            'obiettivo_mensile' => 0,
                            'passo_giorno' => 0,
                            'differenza_obj' => 0,
                            'ore_paf' => 0,
                            'pezzi_paf' => 0,
                            'resa_paf' => 0,
                            'fatturato_paf' => $bonusDetail['importo'], // BONUS FISSO (NON proiettato)
                            'is_bonus_globale' => true, // Flag per identificare la riga come bonus globale
                        ]);
                    }
                    
                    $sediData->put('Bonus Globali', $economicsData);
                }
                
                return $sediData;
            });
        
        // === VISTA SINTETICA: Aggregazione dei dati DETTAGLIATI per sede ===
        // IMPORTANTE: Genero il sintetico DAI dati del dettagliato per garantire coerenza!
        $datiSintetici = $datiDettagliati->map(function($sediData, $cliente) {
            return $sediData->map(function($campagneData, $sede) use ($cliente) {
                // Inizializza accumulatori per la sede
                $totali = [
                    'prodotto_pda' => 0,
                    'inserito_pda' => 0,
                    'ko_pda' => 0,
                    'backlog_pda' => 0,
                    'backlog_partner_pda' => 0,
                    'ore' => 0,
                    'fatturato' => 0,
                    'ore_paf' => 0,
                    'pezzi_paf' => 0,
                    'fatturato_paf' => 0,
                    'obiettivo_mensile' => 0,
                ];
                
                $isAnyBonus = false;
                
                // Somma tutti i dati delle campagne di questa sede
                foreach ($campagneData as $campagna => $dati) {
                    // Salta se è un bonus (lo gestiamo separatamente)
                    if (isset($dati['is_bonus_sede']) || isset($dati['is_bonus_globale'])) {
                        $isAnyBonus = true;
                        continue;
                    }
                    
                    $totali['prodotto_pda'] += $dati['prodotto_pda'] ?? 0;
                    $totali['inserito_pda'] += $dati['inserito_pda'] ?? 0;
                    $totali['ko_pda'] += $dati['ko_pda'] ?? 0;
                    $totali['backlog_pda'] += $dati['backlog_pda'] ?? 0;
                    $totali['backlog_partner_pda'] += $dati['backlog_partner_pda'] ?? 0;
                    $totali['ore'] += $dati['ore'] ?? 0;
                    $totali['fatturato'] += $dati['fatturato'] ?? 0;
                    $totali['ore_paf'] += $dati['ore_paf'] ?? 0;
                    $totali['pezzi_paf'] += $dati['pezzi_paf'] ?? 0;
                    $totali['fatturato_paf'] += $dati['fatturato_paf'] ?? 0;
                    $totali['obiettivo_mensile'] += $dati['obiettivo_mensile'] ?? 0;
                }
                
                // Calcola metriche derivate
                $totali['resa_prodotto'] = $totali['ore'] > 0 ? round($totali['prodotto_pda'] / $totali['ore'], 2) : 0;
                $totali['resa_inserito'] = $totali['ore'] > 0 ? round($totali['inserito_pda'] / $totali['ore'], 2) : 0;
                $totali['ricavo_orario'] = $totali['ore'] > 0 ? round($totali['fatturato'] / $totali['ore'], 2) : 0;
                $totali['resa_paf'] = $totali['ore_paf'] > 0 ? round($totali['pezzi_paf'] / $totali['ore_paf'], 2) : 0;
                
                // Calcola obiettivi
                $differenzaObj = $totali['obiettivo_mensile'] - $totali['inserito_pda'];
                $giorniLavorativiRimanenti = CalendarioAziendale::giorniLavorativiRimanenti(date('Y'), date('m'));
                $passoGiorno = 0;
                if ($giorniLavorativiRimanenti > 0 && $differenzaObj > 0) {
                    $passoGiorno = round($differenzaObj / $giorniLavorativiRimanenti, 2);
                }
                
                $risultato = collect([
                    'totale' => [
                        'campagna' => 'TOTALE SEDE',
                        'prodotti_aggiuntivi' => [],
                        'prodotto_pda' => $totali['prodotto_pda'],
                        'inserito_pda' => $totali['inserito_pda'],
                        'ko_pda' => $totali['ko_pda'],
                        'backlog_pda' => $totali['backlog_pda'],
                        'backlog_partner_pda' => $totali['backlog_partner_pda'],
                        'cliente' => $cliente,
                        'cliente_originale' => $cliente,
                        'sede' => $sede,
                        'ore' => $totali['ore'],
                        'obiettivo' => 0,
                        'fatturato' => $totali['fatturato'],
                        'resa_prodotto' => $totali['resa_prodotto'],
                        'resa_inserito' => $totali['resa_inserito'],
                        'ricavo_orario' => $totali['ricavo_orario'],
                        'obiettivo_mensile' => round($totali['obiettivo_mensile'], 0),
                        'passo_giorno' => $passoGiorno,
                        'differenza_obj' => round($differenzaObj, 0),
                        'ore_paf' => round($totali['ore_paf'], 2),
                        'pezzi_paf' => round($totali['pezzi_paf'], 2),
                        'resa_paf' => $totali['resa_paf'],
                        'fatturato_paf' => round($totali['fatturato_paf'], 2),
                    ]
                ]);
                
                // Aggiungi righe bonus se presenti nella sede
                foreach ($campagneData as $campagna => $dati) {
                    if (isset($dati['is_bonus_sede']) && $dati['is_bonus_sede']) {
                        $risultato->put('bonus_sede', $dati);
                    }
                }
                
                return $risultato;
            });
        })
        ->map(function($sediData, $cliente) use ($includiBonus, $bonusGlobali, $commessaFilter) {
            // === AGGIUNGI BONUS GLOBALI UNA SOLA VOLTA PER COMMESSA (fuori dal ciclo sedi) ===
            if ($includiBonus && $bonusGlobali > 0 && $cliente === $commessaFilter) {
                // Crea una riga speciale per mostrare i bonus globali
                $sediData['Bonus Globali'] = collect([
                    'totale' => [
                        'campagna' => 'BONUS GLOBALE',
                        'prodotti_aggiuntivi' => [],
                        'prodotto_pda' => 0,
                        'inserito_pda' => 0,
                        'ko_pda' => 0,
                        'backlog_pda' => 0,
                        'backlog_partner_pda' => 0,
                        'cliente' => $commessaFilter,
                        'cliente_originale' => $commessaFilter,
                        'sede' => 'Bonus Globali',
                        'ore' => 0,
                        'obiettivo' => 0,
                        'fatturato' => $bonusGlobali, // BONUS FISSO
                        'resa_prodotto' => 0,
                        'resa_inserito' => 0,
                        'resa_oraria' => 0,
                        'ricavo_orario' => 0,
                        'obiettivo_mensile' => 0,
                        'passo_giorno' => 0,
                        'differenza_obj' => 0,
                        'ore_paf' => 0,
                        'pezzi_paf' => 0,
                        'resa_paf' => 0,
                        'fatturato_paf' => $bonusGlobali, // BONUS FISSO (NON proiettato)
                        'is_bonus_globale' => true, // Flag per identificare come riga bonus globale
                    ]
                ]);
            }
            
            return $sediData;
        });
        
        // === VISTA GIORNALIERA: Dati per Giorno, Sede e Campagna ===
        $datiGiornalieri = (clone $queryBase)
            ->selectRaw('
                data_vendita,
                commessa,
                SUM(totale_vendite) as prodotto_pda,
                SUM(ok_definitivo) as inserito_pda,
                SUM(ko_definitivo) as ko_pda,
                SUM(backlog) as backlog_pda,
                SUM(backlog_partner) as backlog_partner_pda,
                SUM(ore_lavorate) as ore,
                SUM(totale_abbattuto) as fatturato
            ')
            ->groupBy('data_vendita', 'commessa')
            ->orderBy('data_vendita', 'desc')
            ->orderBy('commessa')
            ->get()
            ->map(function($data) {
                $inseriti = (int)$data->inserito_pda;
                $prodotto = (int)$data->prodotto_pda;
                $ore = (float)$data->ore;
                $fatturato = (float)($data->fatturato ?? 0);
                
                // Calcoli resa
                $resa_prodotto = $ore > 0 ? round($prodotto / $ore, 2) : 0;
                $resa_inserito = $ore > 0 ? round($inseriti / $ore, 2) : 0;
                $ricavo_orario = $ore > 0 ? round($fatturato / $ore, 2) : 0;
                
                return [
                    'data' => $data->data_vendita,
                    'commessa' => $data->commessa,
                    'prodotto_pda' => $prodotto,
                    'inserito_pda' => $inseriti,
                    'ko_pda' => (int)$data->ko_pda,
                    'backlog_pda' => (int)$data->backlog_pda,
                    'backlog_partner_pda' => (int)$data->backlog_partner_pda,
                    'ore' => $ore,
                    'fatturato' => $fatturato,
                    'resa_prodotto' => $resa_prodotto,
                    'resa_inserito' => $resa_inserito,
                    'ricavo_orario' => $ricavo_orario,
                ];
            });
        
        // === AGGIUNGI RIGHE BONUS ALLA VISTA GIORNALIERA (se attivi) - DALLA PIVOT CACHE ===
        if ($includiBonus && $bonusQuery->isNotEmpty()) {
            // Recupera i bonus dalla pivot cache e aggiungili come righe separate
            foreach ($bonusQuery as $bonus) {
                $importo = floatval($bonus->totale_abbattuto);
                $isGlobal = ($bonus->id_sede === 'GLOBAL' || empty($bonus->id_sede));
                
                $datiGiornalieri->push([
                    'data' => $bonus->data_vendita,
                    'commessa' => $isGlobal ? $commessaFilter : ($commessaFilter . ' - ' . $bonus->nome_sede),
                    'prodotto_pda' => 0,
                    'inserito_pda' => 0,
                    'ko_pda' => 0,
                    'backlog_pda' => 0,
                    'backlog_partner_pda' => 0,
                    'ore' => 0,
                    'fatturato' => $importo,
                    'resa_prodotto' => 0,
                    'resa_inserito' => 0,
                    'resa_oraria' => 0,
                    'ricavo_orario' => 0,
                    'is_bonus' => true, // Flag per evidenziare nella vista
                    'is_bonus_sede' => !$isGlobal, // Flag specifico per bonus sede
                    'bonus_info' => ($isGlobal ? "BONUS GLOBALE" : "BONUS SEDE") . ": {$bonus->macro_campagna} (€" . number_format($importo, 2, ',', '.') . ")",
                ]);
            }
            
            // Riordina per data (i bonus potrebbero essere in date diverse)
            $datiGiornalieri = $datiGiornalieri->sortByDesc('data')->values();
        }
        
        // === DATI PER FILTRI ===
        // Lista tutte le commesse disponibili (escludi bonus)
        $commesse = DB::table('report_produzione_pivot_cache')
            ->where('campagna_id', '!=', 'bonus')
            ->distinct()
            ->whereNotNull('commessa')
            ->orderBy('commessa')
            ->pluck('commessa');
        
        // === PRE-CARICA CAMPAGNE E SEDI SE LA COMMESSA È SELEZIONATA ===
        $campagneFiltered = collect();
        $sediFiltered = collect();
        
        if ($commessaFilter) {
            // Carica campagne disponibili per la commessa selezionata (escludi bonus)
            $campagneFiltered = DB::table('report_produzione_pivot_cache')
                ->where('campagna_id', '!=', 'bonus')
                ->where('commessa', $commessaFilter)
                ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
                ->distinct()
                ->whereNotNull('macro_campagna')
                ->where('macro_campagna', '!=', '')
                ->orderBy('macro_campagna')
                ->pluck('macro_campagna');
            
            // Se ci sono campagne selezionate, carica le sedi disponibili (escludi bonus)
            if (!empty($macroCampagnaFilters)) {
                $sediFiltered = DB::table('report_produzione_pivot_cache')
                    ->where('campagna_id', '!=', 'bonus')
                    ->where('commessa', $commessaFilter)
                    ->whereIn('macro_campagna', $macroCampagnaFilters)
                    ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
                    ->where('ore_lavorate', '>', 0)
                    ->distinct()
                    ->whereNotNull('nome_sede')
                    ->where('nome_sede', '!=', '')
                    ->orderBy('nome_sede')
                    ->pluck('nome_sede');
            }
        }
        
        // Sedi filtrate per commessa selezionata (se presente) - mantieni per retrocompatibilità
        $sedi = $sediFiltered;
        
        // Macro campagne filtrate per commessa (se presente) - mantieni per retrocompatibilità
        $macroCampagne = $campagneFiltered;
        
        return view('admin.modules.produzione.cruscotto-produzione.index', [
            'kpiTotali' => $kpiArray,
            'datiRaggruppati' => $datiDettagliati, // Default
            'datiDettagliati' => $datiDettagliati,
            'datiSintetici' => $datiSintetici,
            'datiGiornalieri' => $datiGiornalieri,
            'oreRaggruppate' => [], // Già incluse nella cache!
            'commesse' => $commesse,
            'sedi' => $sedi,
            'macroCampagne' => $macroCampagne,
            // Filtri pre-popolati per caricamento veloce
            'campagneFiltered' => $campagneFiltered,
            'sediFiltered' => $sediFiltered,
            'dataInizio' => $dataInizio ?? '',
            'dataFine' => $dataFine ?? '',
            'commessaFilter' => $commessaFilter ?? '',
            'sedeFilters' => $sedeFilters ?? [],
            'macroCampagnaFilters' => $macroCampagnaFilters ?? [],
            'includiBonus' => $includiBonus,
            // Debug PAF
            'giorniLavoratiPerCampagna' => $giorniLavoratiPerCampagna,
            'mostraPaf' => $mostraPaf,
        ]);
    }

    /**
     * API: Ottieni sedi per una specifica commessa
     * Filtra anche per date e campagne, mostrando solo sedi con lavorazione
     */
    public function getSedi(Request $request)
    {
        $this->authorize('produzione.view');
        
        $commessa = $request->input('commessa');
        $campagne = $request->input('campagne', []);
        $dataInizio = $request->input('data_inizio');
        $dataFine = $request->input('data_fine');
        
        if (!$commessa) {
            return response()->json([]);
        }
        
        $query = DB::table('report_produzione_pivot_cache')
            ->where('campagna_id', '!=', 'bonus') // Escludi bonus
            ->where('commessa', $commessa)
            ->distinct()
            ->whereNotNull('nome_sede')
            ->where('nome_sede', '!=', '')
            // Mostra solo sedi che hanno effettivamente lavorato (ore > 0)
            ->where('ore_lavorate', '>', 0);
        
        // Filtra per campagne se fornite
        if (!empty($campagne)) {
            $query->whereIn('macro_campagna', $campagne);
        }
        
        // Filtra per date se fornite
        if ($dataInizio && $dataFine) {
            $query->whereBetween('data_vendita', [$dataInizio, $dataFine]);
        }
        
        $sedi = $query->orderBy('nome_sede')
            ->pluck('nome_sede');
        
        return response()->json($sedi);
    }

    /**
     * API: Ottieni macro campagne per una specifica commessa e sede
     * Filtra anche per intervallo di date se fornito
     * Mostra SOLO campagne con almeno una sede che ha dati
     */
    public function getCampagne(Request $request)
    {
        $this->authorize('produzione.view');
        
        $commessa = $request->input('commessa');
        $dataInizio = $request->input('data_inizio');
        $dataFine = $request->input('data_fine');
        
        if (!$commessa) {
            return response()->json([]);
        }
        
        $campagne = DB::table('report_produzione_pivot_cache')
            ->where('campagna_id', '!=', 'bonus') // Escludi bonus
            ->where('commessa', $commessa)
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data_vendita', [$dataInizio, $dataFine]))
            ->distinct()
            ->whereNotNull('macro_campagna')
            ->where('macro_campagna', '!=', '')
            // Mostra solo campagne che hanno almeno una sede con dati effettivi
            ->where(function($q) {
                $q->where('totale_vendite', '>', 0)
                  ->orWhere('ok_definitivo', '>', 0)
                  ->orWhere('ko_definitivo', '>', 0)
                  ->orWhere('backlog', '>', 0);
            })
            ->orderBy('macro_campagna')
            ->pluck('macro_campagna');
        
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
        
        // === FILTRI OBBLIGATORI (MULTI-SELEZIONE) ===
        $filterCommessa = $request->input('filter_commessa', []);
        $filterSede = $request->input('filter_sede', []);
        $filterMacroCampagna = $request->input('filter_macro_campagna', []);
        $filterNomeKpi = $request->input('filter_nome_kpi', []);
        $filterTipologiaObiettivo = $request->input('filter_tipologia_obiettivo', []);
        
        // Converti in array se non lo sono già
        $filterCommessa = is_array($filterCommessa) ? $filterCommessa : [$filterCommessa];
        $filterSede = is_array($filterSede) ? $filterSede : [$filterSede];
        $filterMacroCampagna = is_array($filterMacroCampagna) ? $filterMacroCampagna : [$filterMacroCampagna];
        $filterNomeKpi = is_array($filterNomeKpi) ? $filterNomeKpi : [$filterNomeKpi];
        $filterTipologiaObiettivo = is_array($filterTipologiaObiettivo) ? $filterTipologiaObiettivo : [$filterTipologiaObiettivo];
        
        // Rimuovi valori vuoti
        $filterCommessa = array_filter($filterCommessa);
        $filterSede = array_filter($filterSede);
        $filterMacroCampagna = array_filter($filterMacroCampagna);
        $filterNomeKpi = array_filter($filterNomeKpi);
        $filterTipologiaObiettivo = array_filter($filterTipologiaObiettivo);
        
        // Flag per verificare se ci sono filtri applicati
        $hasFiltri = !empty($filterCommessa) || !empty($filterSede) || !empty($filterMacroCampagna) || !empty($filterNomeKpi) || !empty($filterTipologiaObiettivo);
        
        // Inizializza variabili vuote
        $targetMensili = collect();
        $rendicontoProduzione = collect();
        $targetPerCommessa = collect();
        $rendicontoPerCommessa = collect();
        
        // Esegui query SOLO se ci sono filtri applicati
        if ($hasFiltri) {
            $targetMensili = KpiTargetMensile::where('anno', $anno)
                ->where('mese', $mese)
                ->when(!empty($filterCommessa), fn($q) => $q->whereIn('commessa', $filterCommessa))
                ->when(!empty($filterSede), fn($q) => $q->whereIn('sede_crm', $filterSede))
                ->when(!empty($filterMacroCampagna), fn($q) => $q->whereIn('macro_campagna', $filterMacroCampagna))
                ->when(!empty($filterNomeKpi), fn($q) => $q->whereIn('nome_kpi', $filterNomeKpi))
                ->when(!empty($filterTipologiaObiettivo), fn($q) => $q->whereIn('tipologia_obiettivo', $filterTipologiaObiettivo))
                ->orderBy('commessa')
                ->orderBy('sede_crm')
                ->orderBy('nome_kpi')
                ->paginate(50)
                ->appends($request->all());
            
            $rendicontoProduzione = KpiRendicontoProduzione::orderBy('commessa')
                ->orderBy('servizio_mandato')
                ->orderBy('nome_kpi')
                ->get();
            
            $targetPerCommessa = $targetMensili->groupBy('commessa');
            $rendicontoPerCommessa = $rendicontoProduzione->groupBy('commessa');
        }
        
        // === RECUPERA VALORI DISPONIBILI PER I FILTRI ===
        $commesse = DB::table('kpi_target_mensile')
            ->distinct()
            ->pluck('commessa')
            ->sort()
            ->values();
        
        $sedi = DB::table('kpi_target_mensile')
            ->distinct()
            ->pluck('sede_crm')
            ->filter()
            ->sort()
            ->values();
        
        // Recupera tutte le sedi dalla tabella sedi
        $sediSelect = DB::table('sedi')
            ->select('id', 'nome_sede', 'id_sede')
            ->orderBy('nome_sede')
            ->get();
        
        // Recupera macro campagne dalla tabella campagne (esclusi NULL e "non usata")
        $macroCampagne = DB::table('campagne')
            ->select('macro_campagna')
            ->whereNotNull('macro_campagna')
            ->where('macro_campagna', '!=', 'non usata')
            ->distinct()
            ->orderBy('macro_campagna')
            ->pluck('macro_campagna');
        
        // Recupera nomi KPI univoci (per il filtro)
        $nomiKpi = DB::table('kpi_target_mensile')
            ->distinct()
            ->pluck('nome_kpi')
            ->filter()
            ->sort()
            ->values();
        
        // Recupera tipologie obiettivo univoche (per il filtro e la select)
        $tipologieObiettivo = DB::table('kpi_target_mensile')
            ->distinct()
            ->pluck('tipologia_obiettivo')
            ->filter()
            ->sort()
            ->values();
        
        // === PRE-POPOLA FILTRI CONCATENATI SE CI SONO SELEZIONI ===
        $sediFiltered = collect();
        $macroCampagneFiltered = collect();
        $nomiKpiFiltered = collect();
        $tipologieObiettivoFiltered = collect();
        
        if (!empty($filterCommessa)) {
            // Carica sedi disponibili per le commesse selezionate
            $sediFiltered = DB::table('kpi_target_mensile')
                ->whereIn('commessa', $filterCommessa)
                ->where('anno', $anno)
                ->where('mese', $mese)
                ->distinct()
                ->pluck('sede_crm')
                ->filter()
                ->sort()
                ->values();
            
            if (!empty($filterSede)) {
                // Carica macro campagne disponibili per commesse + sedi selezionate
                $macroCampagneFiltered = DB::table('kpi_target_mensile')
                    ->whereIn('commessa', $filterCommessa)
                    ->whereIn('sede_crm', $filterSede)
                    ->where('anno', $anno)
                    ->where('mese', $mese)
                    ->distinct()
                    ->pluck('macro_campagna')
                    ->filter()
                    ->sort()
                    ->values();
                
                if (!empty($filterMacroCampagna)) {
                    // Carica nomi KPI disponibili
                    $nomiKpiFiltered = DB::table('kpi_target_mensile')
                        ->whereIn('commessa', $filterCommessa)
                        ->whereIn('sede_crm', $filterSede)
                        ->whereIn('macro_campagna', $filterMacroCampagna)
                        ->where('anno', $anno)
                        ->where('mese', $mese)
                        ->distinct()
                        ->pluck('nome_kpi')
                        ->filter()
                        ->sort()
                        ->values();
                    
                    if (!empty($filterNomeKpi)) {
                        // Carica tipologie disponibili
                        $tipologieObiettivoFiltered = DB::table('kpi_target_mensile')
                            ->whereIn('commessa', $filterCommessa)
                            ->whereIn('sede_crm', $filterSede)
                            ->whereIn('macro_campagna', $filterMacroCampagna)
                            ->whereIn('nome_kpi', $filterNomeKpi)
                            ->where('anno', $anno)
                            ->where('mese', $mese)
                            ->distinct()
                            ->pluck('tipologia_obiettivo')
                            ->filter()
                            ->sort()
                            ->values();
                    }
                }
            }
        }
        
        return view('admin.modules.produzione.kpi-target.index', [
            'targetMensili' => $targetMensili,
            'rendicontoProduzione' => $rendicontoProduzione,
            'targetPerCommessa' => $targetPerCommessa,
            'rendicontoPerCommessa' => $rendicontoPerCommessa,
            'anno' => $anno,
            'mese' => $mese,
            'commesse' => $commesse,
            'sedi' => $sedi,
            'sediSelect' => $sediSelect,
            'macroCampagne' => $macroCampagne,
            'nomiKpi' => $nomiKpi,
            'tipologieObiettivo' => $tipologieObiettivo,
            // Filtri pre-popolati (per caricamento veloce)
            'sediFiltered' => $sediFiltered,
            'macroCampagneFiltered' => $macroCampagneFiltered,
            'nomiKpiFiltered' => $nomiKpiFiltered,
            'tipologieObiettivoFiltered' => $tipologieObiettivoFiltered,
            // Filtri applicati (per mantenere i valori nel form)
            'filterCommessa' => $filterCommessa,
            'filterSede' => $filterSede,
            'filterMacroCampagna' => $filterMacroCampagna,
            'filterNomeKpi' => $filterNomeKpi,
            'filterTipologiaObiettivo' => $filterTipologiaObiettivo,
            'hasFiltri' => $hasFiltri,
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
            'value' => 'nullable', // Cambiato da 'required' a 'nullable' per permettere valori vuoti
        ]);
        
        try {
            $kpi = KpiTargetMensile::findOrFail($id);
            
            // Se il campo è valore_kpi, converti a numero
            if ($validated['field'] === 'valore_kpi') {
                $kpi->valore_kpi = floatval($validated['value'] ?? 0);
            } else {
                // Permetti valori null o stringhe vuote
                $kpi->{$validated['field']} = $validated['value'] ?? null;
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
        
        // Recupera combinazioni istanza/cliente_committente/macro_campagna dalla tabella campagne
        // e fai JOIN con sedi per ottenere solo le sedi dell'istanza specifica (con id_sede)
        $campagne = DB::table('campagne')
            ->join('sedi', 'campagne.istanza', '=', 'sedi.istanza')
            ->select('campagne.istanza', 'campagne.cliente_committente', 'campagne.macro_campagna', 'sedi.id_sede as sede_id', 'sedi.nome_sede')
            ->whereNotNull('campagne.istanza')
            ->whereNotNull('campagne.cliente_committente')
            ->whereNotNull('campagne.macro_campagna')
            ->whereNotNull('sedi.nome_sede')
            ->where('campagne.macro_campagna', '!=', 'non usata')
            ->where('sedi.nome_sede', '!=', '')
            ->distinct()
            ->orderBy('campagne.istanza')
            ->orderBy('campagne.cliente_committente')
            ->orderBy('campagne.macro_campagna')
            ->orderBy('sedi.nome_sede')
            ->get();
        
        // Raggruppa per istanza -> cliente_committente -> macro_campagne -> sedi (filtrate per istanza)
        $datiGerarchici = $campagne->groupBy('istanza')->map(function($istanzaGroup) {
            return $istanzaGroup->groupBy('cliente_committente')->map(function($commessaGroup) {
                return $commessaGroup->groupBy('macro_campagna')->map(function($macroGroup) {
                    // Restituisce array di oggetti con id e nome_sede
                    return $macroGroup->map(function($item) {
                        return [
                            'id' => $item->sede_id,
                            'nome' => $item->nome_sede
                        ];
                    })->unique('id')->sortBy('nome')->values();
                });
            });
        });
        
        // Recupera istanze univoche
        $istanze = $campagne->pluck('istanza')->unique()->sort()->values();
        
        // Nomi KPI standard (sempre usati, indipendentemente dalla tabella)
        $nomiKpi = collect([
            'LEADS',
            'CONVERSIONI',
            'COSTO',
            'CPL',
            'CPA',
            'RICAVI',
            'ROI',
            'ROAS',
            'VENDITE',
            'CONTATTI UTILI',
            'CONTATTI CHIUSI',
            'FATTURATO',
            'MARGINE'
        ]);
        
        // Tipologie Obiettivo: TARGET, GARE, OBIETTIVO
        $tipologieObiettivo = ['TARGET', 'GARE', 'OBIETTIVO'];
        
        // Tipo KPI: solo RESIDENZIALI e BUSINESS
        $tipiKpi = ['RESIDENZIALI', 'BUSINESS'];
        
        return view('admin.modules.produzione.kpi-target.create', [
            'istanze' => $istanze,
            'datiGerarchici' => $datiGerarchici->toJson(),
            'nomiKpi' => $nomiKpi,
            'tipologieObiettivo' => $tipologieObiettivo,
            'tipiKpi' => $tipiKpi,
        ]);
    }
    
    /**
     * Salva nuovo KPI Target
     */
    public function storeKpiTarget(Request $request)
    {
        $this->authorize('produzione.create');
        
        $validated = $request->validate([
            'istanza' => 'required|string|max:100',
            'commessa' => 'required|string|max:100',
            'sede_crm' => 'required|string|max:50|exists:sedi,id_sede',
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
            'istanza.required' => 'L\'istanza è obbligatoria',
            'commessa.required' => 'La commessa è obbligatoria',
            'sede_crm.required' => 'La sede è obbligatoria',
            'sede_crm.exists' => 'Sede non trovata',
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
     * Mostra form modifica KPI Target
     */
    public function editKpiTarget($id)
    {
        $this->authorize('produzione.edit');
        
        $kpi = KpiTargetMensile::findOrFail($id);
        
        // Recupera dati da tabelle master per filtri dinamici con JOIN alla tabella sedi
        // Istanza, Cliente Committente (commessa), Macro Campagna e Nome Sede dalla combinazione campagne + sedi
        $campagne = DB::table('campagne')
            ->join('sedi', 'campagne.istanza', '=', 'sedi.istanza')
            ->select('campagne.istanza', 'campagne.cliente_committente', 'campagne.macro_campagna', 'sedi.id_sede as sede_id', 'sedi.nome_sede')
            ->whereNotNull('campagne.istanza')
            ->whereNotNull('campagne.cliente_committente')
            ->whereNotNull('campagne.macro_campagna')
            ->whereNotNull('sedi.nome_sede')
            ->where('campagne.macro_campagna', '!=', 'non usata')
            ->where('sedi.nome_sede', '!=', '')
            ->distinct()
            ->orderBy('campagne.istanza')
            ->orderBy('campagne.cliente_committente')
            ->orderBy('campagne.macro_campagna')
            ->orderBy('sedi.nome_sede')
            ->get();
        
        // Raggruppa per istanza -> cliente_committente -> macro_campagne -> sedi (filtrate per istanza)
        $datiGerarchici = $campagne->groupBy('istanza')->map(function($istanzaGroup) {
            return $istanzaGroup->groupBy('cliente_committente')->map(function($commessaGroup) {
                return $commessaGroup->groupBy('macro_campagna')->map(function($macroGroup) {
                    // Restituisce array di oggetti con id e nome_sede
                    return $macroGroup->map(function($item) {
                        return [
                            'id' => $item->sede_id,
                            'nome' => $item->nome_sede
                        ];
                    })->unique('id')->sortBy('nome')->values();
                });
            });
        });
        
        // Recupera istanze univoche
        $istanze = $campagne->pluck('istanza')->unique()->sort()->values();
        
        // Nomi KPI standard (sempre usati, indipendentemente dalla tabella)
        $nomiKpi = collect([
            'LEADS',
            'CONVERSIONI',
            'COSTO',
            'CPL',
            'CPA',
            'RICAVI',
            'ROI',
            'ROAS',
            'VENDITE',
            'CONTATTI UTILI',
            'CONTATTI CHIUSI',
            'FATTURATO',
            'MARGINE'
        ]);
        
        // Tipologie Obiettivo: TARGET, GARE, OBIETTIVO
        $tipologieObiettivo = ['TARGET', 'GARE', 'OBIETTIVO'];
        
        // Tipo KPI: solo RESIDENZIALI e BUSINESS
        $tipiKpi = ['RESIDENZIALI', 'BUSINESS'];
        
        return view('admin.modules.produzione.kpi-target.edit', [
            'kpi' => $kpi,
            'istanze' => $istanze,
            'datiGerarchici' => $datiGerarchici->toJson(),
            'nomiKpi' => $nomiKpi,
            'tipologieObiettivo' => $tipologieObiettivo,
            'tipiKpi' => $tipiKpi,
        ]);
    }
    
    /**
     * Aggiorna KPI Target (form completo)
     */
    public function updateKpiTargetFull(Request $request, $id)
    {
        $this->authorize('produzione.edit');
        
        $kpi = KpiTargetMensile::findOrFail($id);
        
        $validated = $request->validate([
            'istanza' => 'required|string|max:100',
            'commessa' => 'required|string|max:100',
            'sede_crm' => 'required|string|max:50|exists:sedi,id_sede',
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
            'istanza.required' => 'L\'istanza è obbligatoria',
            'commessa.required' => 'La commessa è obbligatoria',
            'sede_crm.required' => 'La sede è obbligatoria',
            'sede_crm.exists' => 'Sede non trovata',
            'nome_kpi.required' => 'Il nome KPI è obbligatorio',
            'anno.required' => 'L\'anno è obbligatorio',
            'mese.required' => 'Il mese è obbligatorio',
            'valore_kpi.required' => 'Il valore KPI è obbligatorio',
            'data_validita_fine.after_or_equal' => 'La data fine deve essere uguale o successiva alla data inizio',
        ]);
        
        $kpi->update($validated);
        
        return redirect()
            ->route('admin.produzione.kpi_target', ['anno' => $validated['anno'], 'mese' => sprintf('%02d', $validated['mese'])])
            ->with('success', 'KPI Target aggiornato con successo');
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
     * Inizializza i target per un nuovo mese copiando la struttura del mese precedente con valori a 0
     */
    public function inizializzaMese(Request $request)
    {
        $this->authorize('produzione.create');
        
        $validated = $request->validate([
            'mese_destinazione' => 'required|integer|min:1|max:12',
            'anno_destinazione' => 'required|integer|min:2020|max:2030',
        ], [
            'mese_destinazione.required' => 'Il mese di destinazione è obbligatorio',
            'anno_destinazione.required' => 'L\'anno di destinazione è obbligatorio',
        ]);
        
        $meseDestinazione = $validated['mese_destinazione'];
        $annoDestinazione = $validated['anno_destinazione'];
        
        // Calcola mese precedente
        $mesePrecedente = $meseDestinazione - 1;
        $annoPrecedente = $annoDestinazione;
        
        if ($mesePrecedente === 0) {
            $mesePrecedente = 12;
            $annoPrecedente = $annoDestinazione - 1;
        }
        
        try {
            DB::beginTransaction();
            
            // Recupera tutti i target del mese precedente
            $targetPrecedenti = KpiTargetMensile::where('anno', $annoPrecedente)
                ->where('mese', $mesePrecedente)
                ->get();
            
            if ($targetPrecedenti->isEmpty()) {
                DB::rollBack();
                return redirect()
                    ->route('admin.produzione.kpi_target', [
                        'anno' => $annoDestinazione, 
                        'mese' => sprintf('%02d', $meseDestinazione)
                    ])
                    ->with('error', "Nessun target trovato per il mese precedente ({$mesePrecedente}/{$annoPrecedente}). Impossibile inizializzare.");
            }
            
            // Elimina eventuali target esistenti per il mese di destinazione
            $targetEliminati = KpiTargetMensile::where('anno', $annoDestinazione)
                ->where('mese', $meseDestinazione)
                ->count();
            
            if ($targetEliminati > 0) {
                KpiTargetMensile::where('anno', $annoDestinazione)
                    ->where('mese', $meseDestinazione)
                    ->delete();
            }
            
            // Copia la struttura con valore_kpi = 0
            $targetCreati = 0;
            foreach ($targetPrecedenti as $targetPrecedente) {
                KpiTargetMensile::create([
                    'commessa' => $targetPrecedente->commessa,
                    'sede_crm' => $targetPrecedente->sede_crm,
                    'sede_estesa' => $targetPrecedente->sede_estesa,
                    'macro_campagna' => $targetPrecedente->macro_campagna,
                    'nome_kpi' => $targetPrecedente->nome_kpi,
                    'tipo_kpi' => $targetPrecedente->tipo_kpi,
                    'tipologia_obiettivo' => $targetPrecedente->tipologia_obiettivo,
                    'tipologia_valore_obiettivo' => $targetPrecedente->tipologia_valore_obiettivo,
                    'anno' => $annoDestinazione,
                    'mese' => $meseDestinazione,
                    'valore_kpi' => 0, // Inizializza a 0
                    'kpi_variato' => null,
                    'data_validita_inizio' => null,
                    'data_validita_fine' => null,
                ]);
                $targetCreati++;
            }
            
            DB::commit();
            
            $messaggioEliminati = $targetEliminati > 0 
                ? " (sostituiti {$targetEliminati} target esistenti)" 
                : "";
            
            return redirect()
                ->route('admin.produzione.kpi_target', [
                    'anno' => $annoDestinazione, 
                    'mese' => sprintf('%02d', $meseDestinazione)
                ])
                ->with('success', "Inizializzazione completata! Creati {$targetCreati} target per {$meseDestinazione}/{$annoDestinazione}{$messaggioEliminati}. Ora puoi modificare manualmente i valori.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Errore durante l\'inizializzazione: ' . $e->getMessage());
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
    
    /**
     * Get Sedi per filtri concatenati KPI Target
     */
    public function getSediKpiTarget(Request $request)
    {
        $commesse = $request->input('commesse', []);
        $anno = $request->input('anno', date('Y'));
        $mese = $request->input('mese', date('m'));
        
        if (empty($commesse)) {
            return response()->json([]);
        }
        
        $sedi = DB::table('kpi_target_mensile')
            ->whereIn('commessa', $commesse)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->distinct()
            ->pluck('sede_crm')
            ->filter()
            ->sort()
            ->values();
        
        return response()->json($sedi);
    }
    
    /**
     * Get Macro Campagne per filtri concatenati KPI Target
     */
    public function getMacroCampagneKpiTarget(Request $request)
    {
        $commesse = $request->input('commesse', []);
        $sedi = $request->input('sedi', []);
        $anno = $request->input('anno', date('Y'));
        $mese = $request->input('mese', date('m'));
        
        if (empty($commesse) || empty($sedi)) {
            return response()->json([]);
        }
        
        $macroCampagne = DB::table('kpi_target_mensile')
            ->whereIn('commessa', $commesse)
            ->whereIn('sede_crm', $sedi)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->distinct()
            ->pluck('macro_campagna')
            ->filter()
            ->sort()
            ->values();
        
        return response()->json($macroCampagne);
    }
    
    /**
     * Get Nomi KPI per filtri concatenati KPI Target
     */
    public function getNomiKpiTarget(Request $request)
    {
        $commesse = $request->input('commesse', []);
        $sedi = $request->input('sedi', []);
        $macroCampagne = $request->input('macro_campagne', []);
        $anno = $request->input('anno', date('Y'));
        $mese = $request->input('mese', date('m'));
        
        if (empty($commesse) || empty($sedi) || empty($macroCampagne)) {
            return response()->json([]);
        }
        
        $nomiKpi = DB::table('kpi_target_mensile')
            ->whereIn('commessa', $commesse)
            ->whereIn('sede_crm', $sedi)
            ->whereIn('macro_campagna', $macroCampagne)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->distinct()
            ->pluck('nome_kpi')
            ->filter()
            ->sort()
            ->values();
        
        return response()->json($nomiKpi);
    }
    
    /**
     * Get Tipologie Obiettivo per filtri concatenati KPI Target
     */
    public function getTipologieObiettivoKpiTarget(Request $request)
    {
        $commesse = $request->input('commesse', []);
        $sedi = $request->input('sedi', []);
        $macroCampagne = $request->input('macro_campagne', []);
        $nomiKpi = $request->input('nomi_kpi', []);
        $anno = $request->input('anno', date('Y'));
        $mese = $request->input('mese', date('m'));
        
        if (empty($commesse) || empty($sedi) || empty($macroCampagne) || empty($nomiKpi)) {
            return response()->json([]);
        }
        
        $tipologie = DB::table('kpi_target_mensile')
            ->whereIn('commessa', $commesse)
            ->whereIn('sede_crm', $sedi)
            ->whereIn('macro_campagna', $macroCampagne)
            ->whereIn('nome_kpi', $nomiKpi)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->distinct()
            ->pluck('tipologia_obiettivo')
            ->filter()
            ->sort()
            ->values();
        
        return response()->json($tipologie);
    }
}
