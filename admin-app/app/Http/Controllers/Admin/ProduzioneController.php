<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campagna;
use App\Models\OreLavorate;
use App\Models\Sede;
use App\Models\Vendita;
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
     * Ordini di Produzione
     */
    public function orders()
    {
        $this->authorize('produzione.view');
        
        $user = Auth::user();
        $userLocation = $this->getUserLocation($user);
        
        // Filtra ordini per location se l'utente è legato a una specifica sede
        $orders = collect([
            ['id' => 1, 'code' => 'ORD-001', 'product' => 'Prodotto A', 'quantity' => 100, 'location' => 'LAMEZIA', 'status' => 'in_progress'],
            ['id' => 2, 'code' => 'ORD-002', 'product' => 'Prodotto B', 'quantity' => 75, 'location' => 'RENDE', 'status' => 'completed'],
            ['id' => 3, 'code' => 'ORD-003', 'product' => 'Prodotto C', 'quantity' => 150, 'location' => 'LAMEZIA', 'status' => 'pending'],
        ]);
        
        // Se l'utente è legato a una location specifica, filtra
        if ($userLocation) {
            $orders = $orders->where('location', $userLocation);
        }
        
        return view('admin.modules.produzione.orders', [
            'orders' => $orders,
            'userLocation' => $userLocation,
            'canCreate' => ModuleAccessService::canPerform('produzione', 'create'),
            'canEdit' => ModuleAccessService::canPerform('produzione', 'edit'),
        ]);
    }

    /**
     * Controllo Qualità
     */
    public function quality()
    {
        $this->authorize('produzione.view');
        
        $user = Auth::user();
        $userLocation = $this->getUserLocation($user);
        
        $qualityData = [
            'daily_checks' => 25,
            'passed' => 24,
            'failed' => 1,
            'quality_score' => 96.0,
            'defect_rate' => 4.0
        ];
        
        return view('admin.modules.produzione.quality', [
            'qualityData' => $qualityData,
            'userLocation' => $userLocation,
            'canEdit' => ModuleAccessService::canPerform('produzione', 'edit'),
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
     * Crea nuovo ordine di produzione
     */
    public function createOrder()
    {
        $this->authorize('produzione.create');
        
        $locations = ['LAMEZIA', 'RENDE', 'VIBO', 'CASTROVILLARI', 'CATANZARO', 'SAN_PIETRO'];
        
        return view('admin.modules.produzione.create-order', compact('locations'));
    }

    /**
     * Salva ordine di produzione
     */
    public function storeOrder(Request $request)
    {
        $this->authorize('produzione.create');
        
        $request->validate([
            'product' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'location' => 'required|string',
            'deadline' => 'required|date|after:today',
        ]);
        
        // Logica di salvataggio...
        
        return redirect()->route('admin.produzione.orders')
            ->with('success', 'Ordine di produzione creato con successo');
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
     * Cruscotto Produzione
     */
    public function cruscottoProduzione(Request $request)
    {
        $this->authorize('produzione.view');
        
        // === VALIDAZIONE ===
        // Se ci sono filtri applicati, valida che le date siano presenti
        if ($request->hasAny(['data_inizio', 'data_fine', 'mandato', 'sede', 'canale'])) {
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
        
        // === FILTRI (con supporto multi-select) ===
        $dataInizio = $request->input('data_inizio');
        $dataFine = $request->input('data_fine');
        $mandatoFilter = $request->input('mandato', []); // Array
        $sedeFilter = $request->input('sede', []); // Array
        $canaleFilter = $request->input('canale', []); // Array
        
        // === QUERY OTTIMIZZATA CON VISTA MySQL ===
        $query = DB::table('view_cruscotto_produzione_test1');
        
        // Filtra per date
        if ($dataInizio && $dataFine) {
            $query->whereBetween('data_vendita', [$dataInizio, $dataFine]);
        }
        
        // Applica filtri multipli
        if (!empty($mandatoFilter) && is_array($mandatoFilter)) {
            $query->whereIn('cliente_committente', $mandatoFilter);
        }
        
        if (!empty($sedeFilter) && is_array($sedeFilter)) {
            // Converti gli ID sede in nomi sede per la vista
            $nomiSedi = Sede::whereIn('id_sede', $sedeFilter)->pluck('nome_sede')->toArray();
            if (!empty($nomiSedi)) {
                $query->whereIn('nome_sede', $nomiSedi);
            }
        }
        
        // Nota: Il filtro canale non è disponibile nella vista
        // Se necessario, aggiungerlo alla vista MySQL
        
        $vendite = $query->get();
        
        // === QUERY ORE LAVORATE ===
        $oreQuery = OreLavorate::query();
        
        if ($dataInizio && $dataFine) {
            $oreQuery->whereBetween('data', [$dataInizio, $dataFine]);
        }
        
        if (!empty($sedeFilter) && is_array($sedeFilter)) {
            $oreQuery->whereIn('id_sede', $sedeFilter);
        }
        
        if (!empty($mandatoFilter) && is_array($mandatoFilter)) {
            $oreQuery->whereHas('campagna', function($q) use ($mandatoFilter) {
                $q->whereIn('cliente_committente', $mandatoFilter);
            });
        }
        
        $oreLavorate = $oreQuery->sum('tempo_lavorato');
        
        // === CALCOLO KPI TOTALI (conta solo ID_VENDITA distinti) ===
        $kpiTotali = [
            // Conta solo ID vendita distinti (non le righe duplicate)
            'prodotto_pda' => $vendite->pluck('id_vendita')->unique()->count(),
            'prodotto_valore' => 0, // TODO: Aggiungere campo peso alla vista
            
            // Inserito: conta ID vendita distinti con esito OK
            'inserito_pda' => $vendite->whereIn('esito_vendita', ['OK Definitivo', 'OK Controllo Dati', 'OK RECUPERO CONTROLLO DATI'])
                ->pluck('id_vendita')->unique()->count(),
            'inserito_valore' => 0,
            
            // KO: conta ID vendita distinti con esito KO
            'ko_pda' => $vendite->whereIn('esito_vendita', ['KO Definitivo', 'KO Controllo Dati', 'KO RECUPERO CONTROLLO DATI'])
                ->pluck('id_vendita')->unique()->count(),
            'ko_valore' => 0,
            
            // BackLog: conta ID vendita distinti in BOZZA/recupero
            'backlog_pda' => $vendite->whereIn('esito_vendita', ['BOZZA', 'In recupero', 'Acquisito'])
                ->pluck('id_vendita')->unique()->count(),
            'backlog_valore' => 0,
            
            // BackLog Partner: conta ID vendita distinti PENDING
            'backlog_partner_pda' => $vendite->where('esito_vendita', 'PENDING')
                ->pluck('id_vendita')->unique()->count(),
            'backlog_partner_valore' => 0,
            
            'ore' => $oreLavorate,
        ];
        
        // === RAGGRUPPAMENTO PER CLIENTE > SEDE > ID_VENDITA ===
        // Raggruppa per ID vendita per gestire vendite con più prodotti
        $venditePerIdVendita = $vendite->groupBy('id_vendita')->map(function($righeVendita) {
            $base = $righeVendita->first();
            $prodotti = $righeVendita->pluck('nome_prodotto')->filter()->unique()->values();
            $opzioni = $righeVendita->pluck('nome_opzione')->filter()->unique()->values();
            
            return [
                'id_vendita' => $base->id_vendita,
                'cliente_committente' => $base->cliente_committente,
                'nome_sede' => $base->nome_sede,
                'esito_vendita' => $base->esito_vendita,
                'prodotto_principale' => $prodotti->first() ?: 'N/D',
                'prodotti_aggiuntivi' => $prodotti->slice(1)->toArray(),
                'opzioni' => $opzioni->toArray(),
                'num_opzioni' => $opzioni->count(),
                'data_vendita' => $base->data_vendita,
            ];
        });
        
        // === RAGGRUPPAMENTO DETTAGLIATO: Cliente > Sede > Prodotto ===
        $datiDettagliati = $venditePerIdVendita->groupBy('cliente_committente')->map(function($venditeCliente, $cliente) {
            return $venditeCliente->groupBy('nome_sede')->map(function($venditeSede, $sede) {
                return $venditeSede->groupBy('prodotto_principale')->map(function($venditeGruppo, $prodotto) {
                    $idVenditeUniche = $venditeGruppo->pluck('id_vendita')->unique();
                    
                    return [
                        'campagna' => $prodotto,
                        'prodotti_aggiuntivi' => $venditeGruppo->flatMap(fn($v) => $v['prodotti_aggiuntivi'])->unique()->values()->toArray(),
                        'num_opzioni' => $venditeGruppo->sum('num_opzioni'),
                        'prodotto_pda' => $idVenditeUniche->count(),
                        'prodotto_valore' => 0,
                        'inserito_pda' => $venditeGruppo->whereIn('esito_vendita', ['OK Definitivo', 'OK Controllo Dati', 'OK RECUPERO CONTROLLO DATI'])
                            ->pluck('id_vendita')->unique()->count(),
                        'inserito_valore' => 0,
                        'ko_pda' => $venditeGruppo->whereIn('esito_vendita', ['KO Definitivo', 'KO Controllo Dati', 'KO RECUPERO CONTROLLO DATI'])
                            ->pluck('id_vendita')->unique()->count(),
                        'ko_valore' => 0,
                        'backlog_pda' => $venditeGruppo->whereIn('esito_vendita', ['BOZZA', 'In recupero', 'Acquisito'])
                            ->pluck('id_vendita')->unique()->count(),
                        'backlog_valore' => 0,
                        'backlog_partner_pda' => $venditeGruppo->where('esito_vendita', 'PENDING')
                            ->pluck('id_vendita')->unique()->count(),
                        'backlog_partner_valore' => 0,
                    ];
                });
            });
        });
        
        // === RAGGRUPPAMENTO SINTETICO: Cliente > Sede (somma tutti i prodotti) ===
        $datiSintetici = $venditePerIdVendita->groupBy('cliente_committente')->map(function($venditeCliente, $cliente) {
            return $venditeCliente->groupBy('nome_sede')->map(function($venditeSede, $sede) {
                $idVenditeUniche = $venditeSede->pluck('id_vendita')->unique();
                
                return [
                    'campagna' => 'TOTALE SEDE',
                    'prodotti_aggiuntivi' => [],
                    'num_opzioni' => $venditeSede->sum('num_opzioni'),
                    'prodotto_pda' => $idVenditeUniche->count(),
                    'prodotto_valore' => 0,
                    'inserito_pda' => $venditeSede->whereIn('esito_vendita', ['OK Definitivo', 'OK Controllo Dati', 'OK RECUPERO CONTROLLO DATI'])
                        ->pluck('id_vendita')->unique()->count(),
                    'inserito_valore' => 0,
                    'ko_pda' => $venditeSede->whereIn('esito_vendita', ['KO Definitivo', 'KO Controllo Dati', 'KO RECUPERO CONTROLLO DATI'])
                        ->pluck('id_vendita')->unique()->count(),
                    'ko_valore' => 0,
                    'backlog_pda' => $venditeSede->whereIn('esito_vendita', ['BOZZA', 'In recupero', 'Acquisito'])
                        ->pluck('id_vendita')->unique()->count(),
                    'backlog_valore' => 0,
                    'backlog_partner_pda' => $venditeSede->where('esito_vendita', 'PENDING')
                        ->pluck('id_vendita')->unique()->count(),
                    'backlog_partner_valore' => 0,
                ];
            })->map(function($totale) {
                // Ritorna come collection con un solo elemento per mantenere la struttura
                return collect(['totale' => $totale]);
            });
        });
        
        $datiRaggruppati = $datiDettagliati;
        
        // === DATI PER FILTRI ===
        $mandati = Campagna::distinct()->pluck('cliente_committente', 'cliente_committente')->filter();
        $sedi = Sede::pluck('nome_sede', 'id_sede');
        $canali = Campagna::distinct()->pluck('canale', 'canale')->filter();
        
        // Calcola ore per sede/campagna se necessario
        $orePerGruppo = OreLavorate::query()
            ->when($dataInizio && $dataFine, fn($q) => $q->whereBetween('data', [$dataInizio, $dataFine]))
            ->when(!empty($sedeFilter) && is_array($sedeFilter), fn($q) => $q->whereIn('id_sede', $sedeFilter))
            ->when(!empty($mandatoFilter) && is_array($mandatoFilter), function($q) use ($mandatoFilter) {
                $q->whereHas('campagna', function($q2) use ($mandatoFilter) {
                    $q2->whereIn('cliente_committente', $mandatoFilter);
                });
            })
            ->selectRaw('id_sede, id_campagna, SUM(tempo_lavorato) as totale_ore')
            ->groupBy('id_sede', 'id_campagna')
            ->get()
            ->groupBy('id_sede')
            ->map(fn($g) => $g->groupBy('id_campagna'));
        
        return view('admin.modules.produzione.cruscotto-produzione', [
            'kpiTotali' => $kpiTotali,
            'datiRaggruppati' => $datiRaggruppati,
            'datiDettagliati' => $datiDettagliati,
            'datiSintetici' => $datiSintetici,
            'orePerGruppo' => $orePerGruppo,
            'mandati' => $mandati,
            'sedi' => $sedi,
            'canali' => $canali,
            'dataInizio' => $dataInizio ?? '',
            'dataFine' => $dataFine ?? '',
            'mandatoFilter' => is_array($mandatoFilter) ? $mandatoFilter : [],
            'sedeFilter' => is_array($sedeFilter) ? $sedeFilter : [],
            'canaleFilter' => is_array($canaleFilter) ? $canaleFilter : [],
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
}
