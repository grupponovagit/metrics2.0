<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarioAziendale;
use App\Models\EsitoConversione;
use App\Models\EsitoVenditaConversione;
use App\Models\KpiTargetMensile;
use App\Models\KpiRendicontoProduzione;
use App\Models\MantenimentoBonusIncentivo;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ICTController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('module.access:ict');
    }

    /**
     * Dashboard ICT - Solo per ruoli tecnici
     */
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'servers_online' => 15,
            'servers_total' => 16,
            'uptime' => 99.8,
            'active_users' => 145,
            'pending_tickets' => 8
        ];
        
        return view('admin.modules.ict.index', [
            'user' => $user,
            'stats' => $stats,
            'canCreate' => ModuleAccessService::canPerform('ict', 'create'),
            'canEdit' => ModuleAccessService::canPerform('ict', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('ict', 'delete'),
            'canAdmin' => ModuleAccessService::canPerform('ict', 'admin'),
        ]);
    }

    /**
     * Gestione Sistema
     */
    public function system()
    {
        $this->authorize('ict.view');
        
        $systemInfo = [
            'servers' => [
                ['name' => 'WEB-01', 'status' => 'online', 'cpu' => 45, 'memory' => 68, 'disk' => 72],
                ['name' => 'DB-01', 'status' => 'online', 'cpu' => 32, 'memory' => 54, 'disk' => 45],
                ['name' => 'APP-01', 'status' => 'maintenance', 'cpu' => 0, 'memory' => 0, 'disk' => 89],
            ],
            'services' => [
                ['name' => 'Web Server', 'status' => 'running', 'uptime' => '15 giorni'],
                ['name' => 'Database', 'status' => 'running', 'uptime' => '30 giorni'],
                ['name' => 'Email Service', 'status' => 'running', 'uptime' => '7 giorni'],
            ]
        ];
        
        return view('admin.modules.ict.system', [
            'systemInfo' => $systemInfo,
            'canAdmin' => ModuleAccessService::canPerform('ict', 'admin'),
        ]);
    }

    /**
     * Gestione Utenti - Solo admin ICT
     */
    public function users()
    {
        $this->authorize('ict.admin');
        
        $users = collect([
            ['id' => 1, 'name' => 'Mario Rossi', 'email' => 'mario@company.com', 'role' => 'Developer', 'last_login' => '2024-01-15'],
            ['id' => 2, 'name' => 'Anna Bianchi', 'email' => 'anna@company.com', 'role' => 'Manager', 'last_login' => '2024-01-14'],
        ]);
        
        return view('admin.modules.ict.users', [
            'users' => $users,
            'canCreate' => ModuleAccessService::canPerform('ict', 'create'),
            'canEdit' => ModuleAccessService::canPerform('ict', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('ict', 'delete'),
        ]);
    }

    /**
     * Ticket Support
     */
    public function tickets()
    {
        $this->authorize('ict.view');
        
        $tickets = collect([
            ['id' => 1, 'title' => 'Problema accesso email', 'user' => 'Mario Rossi', 'priority' => 'high', 'status' => 'open'],
            ['id' => 2, 'title' => 'Richiesta nuovo software', 'user' => 'Anna Verdi', 'priority' => 'medium', 'status' => 'in_progress'],
            ['id' => 3, 'title' => 'Aggiornamento sistema', 'user' => 'Giuseppe Blu', 'priority' => 'low', 'status' => 'resolved'],
        ]);
        
        return view('admin.modules.ict.tickets', [
            'tickets' => $tickets,
            'canEdit' => ModuleAccessService::canPerform('ict', 'edit'),
        ]);
    }

    /**
     * Backup e Sicurezza
     */
    public function security()
    {
        $this->authorize('ict.admin');
        
        $securityData = [
            'last_backup' => '2024-01-15 02:00:00',
            'backup_status' => 'success',
            'failed_login_attempts' => 3,
            'active_sessions' => 45,
            'security_alerts' => [
                ['type' => 'warning', 'message' => 'Tentativo accesso non autorizzato da IP 192.168.1.100'],
                ['type' => 'info', 'message' => 'Backup completato con successo'],
            ]
        ];
        
        return view('admin.modules.ict.security', compact('securityData'));
    }

    /**
     * Report ICT
     */
    public function reports()
    {
        $this->authorize('ict.reports');
        
        $reportData = [
            'system_performance' => [
                'avg_cpu' => 35.2,
                'avg_memory' => 62.8,
                'avg_disk' => 58.5,
                'uptime_percentage' => 99.8
            ],
            'user_activity' => [
                'daily_logins' => 145,
                'peak_concurrent_users' => 89,
                'avg_session_duration' => 245 // minuti
            ],
            'monthly_tickets' => [
                'Jan' => 25, 'Feb' => 18, 'Mar' => 22,
                'Apr' => 31, 'May' => 19, 'Jun' => 15
            ]
        ];
        
        return view('admin.modules.ict.reports', compact('reportData'));
    }

    // ===== SOTTOMENU ICT =====

    /**
     * Calendario Aziendale - Gestione giorni lavorativi e festività
     */
    public function calendario(Request $request)
    {
        $this->authorize('ict.view');
        
        $anno = $request->input('anno', date('Y'));
        $mandatoFiltro = $request->input('mandato', null);
        
        // Statistiche anno per giorni generali
        $giorniAnno = CalendarioAziendale::giorniAnno($anno);
        
        $totaleDays = $giorniAnno->count();
        $giorniLavorativiFull = $giorniAnno->where('tipo_giorno', 'lavorativo')->count();
        $sabati = $giorniAnno->where('tipo_giorno', 'sabato')->count();
        $domeniche = $giorniAnno->where('tipo_giorno', 'domenica')->count();
        $festivita = $giorniAnno->where('tipo_giorno', 'festivo')->count();
        $giorniLavorativiTotali = $giorniAnno->sum('peso_giornata');
        
        // Eccezioni per mandato (se selezionato)
        $eccezioniMandato = [];
        if ($mandatoFiltro) {
            $eccezioniMandato = CalendarioAziendale::eccezioniMandato($mandatoFiltro, $anno);
        }
        
        // Lista mandati disponibili
        $mandati = CalendarioAziendale::getMandatiConEccezioni();
        
        // Festività anno
        $festivitaAnno = CalendarioAziendale::festivitaAnno($anno);
        
        // Se mese corrente, calcola statistiche
        $meseCorrente = null;
        if ($anno == date('Y')) {
            $mese = date('m');
            $meseCorrente = [
                'mese' => $mese,
                'nome_mese' => date('F', mktime(0, 0, 0, $mese, 1)),
                'giorni_lavorativi' => CalendarioAziendale::giorniLavorativiMese($anno, $mese),
                'giorni_rimanenti' => CalendarioAziendale::giorniLavorativiRimanenti($anno, $mese),
                'giorni_trascorsi' => CalendarioAziendale::giorniLavorativiTrascorsi($anno, $mese),
                'percentuale_trascorsa' => CalendarioAziendale::percentualeMeseTrascorsa($anno, $mese),
            ];
        }
        
        return view('admin.modules.ict.calendario', [
            'anno' => $anno,
            'mandatoFiltro' => $mandatoFiltro,
            'giorniAnno' => $giorniAnno,
            'totaleDays' => $totaleDays,
            'giorniLavorativiFull' => $giorniLavorativiFull,
            'sabati' => $sabati,
            'domeniche' => $domeniche,
            'festivita' => $festivita,
            'giorniLavorativiTotali' => $giorniLavorativiTotali,
            'eccezioniMandato' => $eccezioniMandato,
            'mandati' => $mandati,
            'festivitaAnno' => $festivitaAnno,
            'meseCorrente' => $meseCorrente,
        ]);
    }
    
    /**
     * Aggiungi eccezione per mandato/fornitore
     */
    public function addEccezioneMandato(Request $request)
    {
        $this->authorize('ict.edit');
        
        $validated = $request->validate([
            'data' => 'required|date',
            'mandato' => 'required|string|max:100',
            'descrizione' => 'required|string|max:255',
            'peso_giornata' => 'required|numeric|min:0|max:1',
        ]);
        
        $data = $validated['data'];
        $mandato = strtoupper($validated['mandato']);
        
        CalendarioAziendale::create([
            'data' => $data,
            'anno' => date('Y', strtotime($data)),
            'mese' => date('m', strtotime($data)),
            'giorno' => date('d', strtotime($data)),
            'giorno_settimana' => date('N', strtotime($data)),
            'tipo_giorno' => 'eccezione',
            'peso_giornata' => $validated['peso_giornata'],
            'descrizione' => $validated['descrizione'],
            'mandato' => $mandato,
            'is_ricorrente' => false,
        ]);
        
        return redirect()->back()->with('success', "Eccezione per {$mandato} aggiunta con successo");
    }
    
    /**
     * Aggiorna giornata calendario
     */
    public function updateCalendario(Request $request)
    {
        $this->authorize('ict.edit');
        
        $validated = $request->validate([
            'id' => 'required|exists:calendario_aziendale,id',
            'tipo_giorno' => 'required|in:lavorativo,festivo,sabato,domenica,eccezione',
            'peso_giornata' => 'required|numeric|min:0|max:1',
            'descrizione' => 'nullable|string|max:255',
        ]);
        
        $giorno = CalendarioAziendale::findOrFail($validated['id']);
        $giorno->update($validated);
        
        return redirect()->back()->with('success', 'Giornata aggiornata con successo');
    }
    
    /**
     * Aggiungi festività/eccezione
     */
    public function addFestivo(Request $request)
    {
        $this->authorize('ict.edit');
        
        $validated = $request->validate([
            'data' => 'required|date',
            'descrizione' => 'required|string|max:255',
            'is_ricorrente' => 'boolean',
        ]);
        
        $data = $validated['data'];
        
        CalendarioAziendale::updateOrCreate(
            ['data' => $data],
            [
                'anno' => date('Y', strtotime($data)),
                'mese' => date('m', strtotime($data)),
                'giorno' => date('d', strtotime($data)),
                'giorno_settimana' => date('N', strtotime($data)),
                'tipo_giorno' => 'festivo',
                'peso_giornata' => 0.00,
                'descrizione' => $validated['descrizione'],
                'is_ricorrente' => $validated['is_ricorrente'] ?? false,
            ]
        );
        
        return redirect()->back()->with('success', 'Festività aggiunta con successo');
    }
    
    /**
     * Elimina giorno (ripristina a default)
     */
    public function deleteGiorno($id)
    {
        $this->authorize('ict.edit');
        
        $giorno = CalendarioAziendale::findOrFail($id);
        
        // Non eliminare, ma ripristina a default
        $giornoSettimana = $giorno->giorno_settimana;
        
        $giorno->update([
            'tipo_giorno' => $giornoSettimana == 1 ? 'domenica' : ($giornoSettimana == 7 ? 'sabato' : 'lavorativo'),
            'peso_giornata' => $giornoSettimana == 1 ? 0.00 : ($giornoSettimana == 7 ? 0.50 : 1.00),
            'descrizione' => $giornoSettimana == 1 ? 'Domenica' : ($giornoSettimana == 7 ? 'Sabato' : null),
            'is_ricorrente' => false,
        ]);
        
        return redirect()->back()->with('success', 'Giorno ripristinato ai valori default');
    }
    
    /**
     * KPI Target (spostato da Produzione)
     */
    public function kpiTarget(Request $request)
    {
        $this->authorize('ict.view');
        
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
        
        return view('admin.modules.ict.kpi-target.index', [
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
        $this->authorize('ict.edit');
        
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
     * Aggiorna KPI Target
     */
    public function updateKpiTarget(Request $request)
    {
        $this->authorize('ict.edit');
        
        $validated = $request->validate([
            'tabella' => 'required|in:target_mensili,rendiconto_produzione',
            'kpi' => 'required|array',
            'kpi.*' => 'required|numeric|min:0',
        ]);
        
        try {
            $tabella = $validated['tabella'] === 'target_mensili' 
                ? KpiTargetMensile::class 
                : KpiRendicontoProduzione::class;
            
            foreach ($validated['kpi'] as $id => $valore) {
                $record = $tabella::find($id);
                if ($record) {
                    $record->valore_kpi = $valore;
                    $record->save();
                }
            }
            
            return redirect()->back()->with('success', 'KPI aggiornati con successo');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostra form creazione KPI Target
     */
    public function createKpiTarget()
    {
        $this->authorize('ict.create');
        
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
        
        return view('admin.modules.ict.kpi-target.create', [
            'commesse' => $commesse,
            'sedi' => $sedi,
        ]);
    }
    
    /**
     * Salva nuovo KPI Target
     */
    public function storeKpiTarget(Request $request)
    {
        $this->authorize('ict.create');
        
        $validated = $request->validate([
            'commessa' => 'required|string|max:100',
            'sede_crm' => 'required|string|max:100',
            'sede_estesa' => 'nullable|string|max:255',
            'nome_kpi' => 'required|string|max:100',
            'anno' => 'required|integer|min:2020|max:2030',
            'mese' => 'required|integer|min:1|max:12',
            'valore_kpi' => 'required|numeric|min:0',
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
            ->route('admin.ict.kpi_target', ['anno' => $validated['anno'], 'mese' => sprintf('%02d', $validated['mese'])])
            ->with('success', 'KPI Target creato con successo');
    }
    
    /**
     * Mostra dettaglio KPI Target
     */
    public function showKpiTarget($id)
    {
        $this->authorize('ict.view');
        
        $kpi = KpiTargetMensile::findOrFail($id);
        
        return view('admin.modules.ict.kpi-target.show', compact('kpi'));
    }
    
    /**
     * Elimina singolo KPI Target
     */
    public function deleteKpiTarget($id)
    {
        $this->authorize('ict.delete');
        
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
        $this->authorize('ict.delete');
        
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
        $this->authorize('ict.edit');
        
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
     * Calendario
     */
    public function stato()
    {
        $this->authorize('ict.view');
        return view('admin.modules.ict.stato');
    }

    /**
     * Categoria UTM Campagna
     */
    public function categoriaUtmCampagna()
    {
        $this->authorize('ict.edit');
        return view('admin.modules.ict.categoria-utm-campagna');
    }

    /**
     * Aggiorna Mandati
     */
    public function aggiornaMandati()
    {
        $this->authorize('ict.edit');
        return view('admin.modules.ict.aggiorna-mandati');
    }

    // ===================================================================
    // GESTIONE CONVERSIONE ESITI
    // ===================================================================

    /**
     * Lista esiti conversione
     */
    public function esitiConversione(Request $request)
    {
        $this->authorize('ict.view');

        // Filtro per commessa
        $commessaSelezionata = $request->get('commessa', '');
        
        // Ottieni tutte le commesse disponibili per il filtro
        $commesse = EsitoConversione::getCommesse();

        // Query base
        $query = EsitoConversione::query();

        // Applica filtro se selezionato
        if ($commessaSelezionata) {
            $query->where('commessa', $commessaSelezionata);
        }

        // Ordina e pagina
        $esiti = $query->orderBy('commessa')
            ->orderBy('esito_globale')
            ->orderBy('esito_originale')
            ->paginate(50)
            ->appends(['commessa' => $commessaSelezionata]);

        // Statistiche
        $stats = [
            'totale_conversioni' => EsitoConversione::count(),
            'totale_commesse' => EsitoConversione::select('commessa')->distinct()->count(),
            'per_commessa' => EsitoConversione::conteggioPerCommessa(),
        ];

        return view('admin.modules.ict.esiti-conversione.index', [
            'esiti' => $esiti,
            'commesse' => $commesse,
            'commessaSelezionata' => $commessaSelezionata,
            'stats' => $stats,
            'esitiGlobali' => EsitoConversione::ESITI_GLOBALI,
        ]);
    }

    /**
     * Form creazione esito
     */
    public function createEsitoConversione()
    {
        $this->authorize('ict.create');

        // Ottieni commesse esistenti + possibilità di aggiungerne nuove
        $commesseEsistenti = EsitoConversione::getCommesse();

        return view('admin.modules.ict.esiti-conversione.create', [
            'commesseEsistenti' => $commesseEsistenti,
            'esitiGlobali' => EsitoConversione::ESITI_GLOBALI,
        ]);
    }

    /**
     * Salva nuovo esito
     */
    public function storeEsitoConversione(Request $request)
    {
        $this->authorize('ict.create');

        $validated = $request->validate([
            'commessa' => 'required|string|max:100',
            'esito_originale' => 'required|string|max:255',
            'esito_globale' => 'required|in:' . implode(',', array_keys(EsitoConversione::ESITI_GLOBALI)),
            'note' => 'nullable|string|max:1000',
        ], [
            'commessa.required' => 'La commessa è obbligatoria',
            'esito_originale.required' => 'L\'esito originale è obbligatorio',
            'esito_globale.required' => 'L\'esito globale è obbligatorio',
            'esito_globale.in' => 'Esito globale non valido',
        ]);

        try {
            // Normalizza i valori
            $validated['commessa'] = strtoupper(trim($validated['commessa']));
            $validated['esito_originale'] = trim($validated['esito_originale']);

            EsitoConversione::create($validated);

            return redirect()
                ->route('admin.ict.esiti_conversione.index')
                ->with('success', 'Conversione esito creata con successo');

        } catch (\Illuminate\Database\QueryException $e) {
            // Gestisci duplicati (UNIQUE constraint)
            if ($e->errorInfo[1] == 1062) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Questa conversione esiste già per la commessa selezionata');
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante il salvataggio: ' . $e->getMessage());
        }
    }

    /**
     * Form modifica esito
     */
    public function editEsitoConversione($id)
    {
        $this->authorize('ict.edit');

        $esito = EsitoConversione::findOrFail($id);
        $commesseEsistenti = EsitoConversione::getCommesse();

        return view('admin.modules.ict.esiti-conversione.edit', [
            'esito' => $esito,
            'commesseEsistenti' => $commesseEsistenti,
            'esitiGlobali' => EsitoConversione::ESITI_GLOBALI,
        ]);
    }

    /**
     * Aggiorna esito
     */
    public function updateEsitoConversione(Request $request, $id)
    {
        $this->authorize('ict.edit');

        $esito = EsitoConversione::findOrFail($id);

        $validated = $request->validate([
            'commessa' => 'required|string|max:100',
            'esito_originale' => 'required|string|max:255',
            'esito_globale' => 'required|in:' . implode(',', array_keys(EsitoConversione::ESITI_GLOBALI)),
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            // Normalizza i valori
            $validated['commessa'] = strtoupper(trim($validated['commessa']));
            $validated['esito_originale'] = trim($validated['esito_originale']);

            $esito->update($validated);

            return redirect()
                ->route('admin.ict.esiti_conversione.index')
                ->with('success', 'Conversione esito aggiornata con successo');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Questa conversione esiste già per la commessa selezionata');
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina esito
     */
    public function destroyEsitoConversione($id)
    {
        $this->authorize('ict.delete');

        try {
            $esito = EsitoConversione::findOrFail($id);
            $esito->delete();

            return redirect()
                ->route('admin.ict.esiti_conversione.index')
                ->with('success', 'Conversione esito eliminata con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Elimina esiti multipli
     */
    public function bulkDeleteEsitoConversione(Request $request)
    {
        $this->authorize('ict.delete');

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:esiti_conversione,id',
        ]);

        try {
            $deleted = EsitoConversione::whereIn('id', $validated['ids'])->delete();

            return redirect()
                ->route('admin.ict.esiti_conversione.index')
                ->with('success', "Eliminate {$deleted} conversioni esito");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    // ===================================================================
    // GESTIONE CONVERSIONE ESITI VENDITA
    // ===================================================================

    /**
     * Lista esiti vendita conversione
     */
    public function esitiVenditaConversione(Request $request)
    {
        $this->authorize('ict.view');

        // Filtro per esito globale
        $esitoGlobaleSelezionato = $request->get('esito_globale', '');

        // Query base
        $query = EsitoVenditaConversione::query();

        // Applica filtro se selezionato
        if ($esitoGlobaleSelezionato) {
            $query->where('esito_globale', $esitoGlobaleSelezionato);
        }

        // Ordina e pagina
        $esiti = $query->orderBy('esito_globale')
            ->orderBy('esito_originale')
            ->paginate(50)
            ->appends(['esito_globale' => $esitoGlobaleSelezionato]);

        // Statistiche
        $stats = [
            'totale_conversioni' => EsitoVenditaConversione::count(),
            'per_tipo' => EsitoVenditaConversione::conteggioPerTipo(),
        ];

        return view('admin.modules.ict.esiti-vendita-conversione.index', [
            'esiti' => $esiti,
            'esitoGlobaleSelezionato' => $esitoGlobaleSelezionato,
            'stats' => $stats,
            'esitiGlobali' => EsitoVenditaConversione::ESITI_GLOBALI,
        ]);
    }

    /**
     * Form creazione esito vendita
     */
    public function createEsitoVenditaConversione()
    {
        $this->authorize('ict.create');

        return view('admin.modules.ict.esiti-vendita-conversione.create', [
            'esitiGlobali' => EsitoVenditaConversione::ESITI_GLOBALI,
        ]);
    }

    /**
     * Salva nuovo esito vendita
     */
    public function storeEsitoVenditaConversione(Request $request)
    {
        $this->authorize('ict.create');

        $validated = $request->validate([
            'esito_originale' => 'required|string|max:255',
            'esito_globale' => 'required|in:' . implode(',', array_keys(EsitoVenditaConversione::ESITI_GLOBALI)),
            'note' => 'nullable|string|max:1000',
        ], [
            'esito_originale.required' => 'L\'esito originale è obbligatorio',
            'esito_globale.required' => 'L\'esito globale è obbligatorio',
            'esito_globale.in' => 'Esito globale non valido',
        ]);

        try {
            // Mantieni il case esatto come viene inserito
            $validated['esito_originale'] = trim($validated['esito_originale']);

            EsitoVenditaConversione::create($validated);

            return redirect()
                ->route('admin.ict.esiti_vendita_conversione.index')
                ->with('success', 'Conversione esito vendita creata con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante il salvataggio: ' . $e->getMessage());
        }
    }

    /**
     * Form modifica esito vendita
     */
    public function editEsitoVenditaConversione($id)
    {
        $this->authorize('ict.edit');

        $esito = EsitoVenditaConversione::findOrFail($id);

        return view('admin.modules.ict.esiti-vendita-conversione.edit', [
            'esito' => $esito,
            'esitiGlobali' => EsitoVenditaConversione::ESITI_GLOBALI,
        ]);
    }

    /**
     * Aggiorna esito vendita
     */
    public function updateEsitoVenditaConversione(Request $request, $id)
    {
        $this->authorize('ict.edit');

        $esito = EsitoVenditaConversione::findOrFail($id);

        $validated = $request->validate([
            'esito_originale' => 'required|string|max:255',
            'esito_globale' => 'required|in:' . implode(',', array_keys(EsitoVenditaConversione::ESITI_GLOBALI)),
            'note' => 'nullable|string|max:1000',
        ], [
            'esito_originale.required' => 'L\'esito originale è obbligatorio',
            'esito_globale.required' => 'L\'esito globale è obbligatorio',
            'esito_globale.in' => 'Esito globale non valido',
        ]);

        try {
            // Mantieni il case esatto come viene inserito
            $validated['esito_originale'] = trim($validated['esito_originale']);

            $esito->update($validated);

            return redirect()
                ->route('admin.ict.esiti_vendita_conversione.index')
                ->with('success', 'Conversione esito vendita aggiornata con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina esito vendita
     */
    public function destroyEsitoVenditaConversione($id)
    {
        $this->authorize('ict.delete');

        try {
            $esito = EsitoVenditaConversione::findOrFail($id);
            $esito->delete();

            return redirect()
                ->route('admin.ict.esiti_vendita_conversione.index')
                ->with('success', 'Conversione esito vendita eliminata con successo');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    /**
     * Elimina esiti vendita multipli
     */
    public function bulkDeleteEsitoVenditaConversione(Request $request)
    {
        $this->authorize('ict.delete');

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:esiti_vendita_conversione,id',
        ]);

        try {
            $deleted = EsitoVenditaConversione::whereIn('id', $validated['ids'])->delete();

            return redirect()
                ->route('admin.ict.esiti_vendita_conversione.index')
                ->with('success', "Eliminate {$deleted} conversioni esito vendita");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }

    // =====================================================
    // MANTENIMENTI BONUS INCENTIVI
    // =====================================================

    /**
     * Lista mantenimenti bonus incentivi
     */
    public function mantenimentiBonusIncentivi(Request $request)
    {
        $this->authorize('ict.view');
        
        $query = MantenimentoBonusIncentivo::query();
        
        // Filtri
        if ($request->filled('istanza')) {
            $query->where('istanza', $request->istanza);
        }
        
        if ($request->filled('commessa')) {
            $query->where('commessa', $request->commessa);
        }
        
        if ($request->filled('tipologia_ripartizione')) {
            $query->where('tipologia_ripartizione', $request->tipologia_ripartizione);
        }
        
        $mantenimenti = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Opzioni per filtri
        $istanze = MantenimentoBonusIncentivo::select('istanza')
            ->distinct()
            ->whereNotNull('istanza')
            ->orderBy('istanza')
            ->pluck('istanza');
        
        $commesse = MantenimentoBonusIncentivo::select('commessa')
            ->distinct()
            ->whereNotNull('commessa')
            ->orderBy('commessa')
            ->pluck('commessa');
        
        return view('admin.modules.ict.mantenimenti-bonus-incentivi.index', compact(
            'mantenimenti',
            'istanze',
            'commesse'
        ));
    }

    /**
     * Form creazione mantenimento
     */
    public function createMantenimentoBonusIncentivo()
    {
        $this->authorize('ict.create');
        
        return view('admin.modules.ict.mantenimenti-bonus-incentivi.create');
    }

    /**
     * Salva nuovo mantenimento
     */
    public function storeMantenimentoBonusIncentivo(Request $request)
    {
        $this->authorize('ict.create');
        
        $validated = $request->validate([
            'istanza' => 'nullable|string|max:255',
            'commessa' => 'nullable|string|max:255',
            'macro_campagna' => 'nullable|string|max:255',
            'tipologia_ripartizione' => 'nullable|in:Fissa,Pezzi,Fatturato,Ore,ContattiUtili,ContattiChiusi',
            'sedi_ripartizione' => 'nullable|string|max:500',
            'liste_ripartizione' => 'nullable|string|max:500',
            'extra_bonus' => 'nullable|numeric|min:0',
            'valido_dal' => 'nullable|date',
        ]);
        
        try {
            MantenimentoBonusIncentivo::create($validated);
            
            return redirect()
                ->route('admin.ict.mantenimenti_bonus_incentivi.index')
                ->with('success', 'Mantenimento bonus/incentivo creato con successo');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante la creazione: ' . $e->getMessage());
        }
    }

    /**
     * Form modifica mantenimento
     */
    public function editMantenimentoBonusIncentivo($id)
    {
        $this->authorize('ict.edit');
        
        $mantenimento = MantenimentoBonusIncentivo::findOrFail($id);
        
        return view('admin.modules.ict.mantenimenti-bonus-incentivi.edit', compact('mantenimento'));
    }

    /**
     * Aggiorna mantenimento
     */
    public function updateMantenimentoBonusIncentivo(Request $request, $id)
    {
        $this->authorize('ict.edit');
        
        $validated = $request->validate([
            'istanza' => 'nullable|string|max:255',
            'commessa' => 'nullable|string|max:255',
            'macro_campagna' => 'nullable|string|max:255',
            'tipologia_ripartizione' => 'nullable|in:Fissa,Pezzi,Fatturato,Ore,ContattiUtili,ContattiChiusi',
            'sedi_ripartizione' => 'nullable|string|max:500',
            'liste_ripartizione' => 'nullable|string|max:500',
            'extra_bonus' => 'nullable|numeric|min:0',
            'valido_dal' => 'nullable|date',
        ]);
        
        try {
            $mantenimento = MantenimentoBonusIncentivo::findOrFail($id);
            $mantenimento->update($validated);
            
            return redirect()
                ->route('admin.ict.mantenimenti_bonus_incentivi.index')
                ->with('success', 'Mantenimento bonus/incentivo aggiornato con successo');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina mantenimento
     */
    public function destroyMantenimentoBonusIncentivo($id)
    {
        $this->authorize('ict.delete');
        
        try {
            $mantenimento = MantenimentoBonusIncentivo::findOrFail($id);
            $mantenimento->delete();
            
            return redirect()
                ->route('admin.ict.mantenimenti_bonus_incentivi.index')
                ->with('success', 'Mantenimento bonus/incentivo eliminato con successo');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }
}

