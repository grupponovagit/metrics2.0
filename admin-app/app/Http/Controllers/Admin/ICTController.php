<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarioAziendale;
use App\Models\KpiTargetMensile;
use App\Models\KpiRendicontoProduzione;
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
            ->get();
        
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
        
        return view('admin.modules.ict.kpi-target', [
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
     * Aggiorna KPI Target
     */
    public function updateKpiTarget(Request $request)
    {
        $this->authorize('ict.edit');
        
        $validated = $request->validate([
            'tabella' => 'required|in:target_mensili,rendiconto_produzione',
            'kpi' => 'required|array',
            'kpi.*' => 'required|integer|min:0',
        ]);
        
        $tabella = $validated['tabella'] === 'target_mensili' 
            ? KpiTargetMensile::class 
            : KpiRendicontoProduzione::class;
        
        foreach ($validated['kpi'] as $id => $valore) {
            $tabella::where('id', $id)->update(['valore_kpi' => $valore]);
        }
        
        return redirect()->back()->with('success', 'KPI aggiornati con successo');
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
}
