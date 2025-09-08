<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Calendario
     */
    public function calendario()
    {
        $this->authorize('ict.view');
        return view('admin.modules.ict.calendario');
    }

    /**
     * Stato
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
