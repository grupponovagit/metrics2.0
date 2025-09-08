<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('module.access:home');
    }

    /**
     * Dashboard principale - Modulo Home
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $accessibleModules = ModuleAccessService::getAccessibleModules();
        
        return view('admin.modules.home.index', [
            'user' => $user,
            'modules' => $accessibleModules,
            'userRoles' => $user->getRoleNames(),
            'dashboardData' => $this->getDashboardData()
        ]);
    }

    /**
     * Ottiene i dati del dashboard basati sui permessi dell'utente
     */
    private function getDashboardData()
    {
        /** @var User $user */
        $user = Auth::user();
        $data = [];

        // Dati sempre visibili per il modulo Home
        $data['welcome_message'] = "Benvenuto, {$user->name}!";
        $data['last_login'] = $user->updated_at;
        
        // Statistiche basate sui moduli accessibili
        if (ModuleAccessService::canAccess('hr')) {
            $data['hr_stats'] = [
                'total_employees' => 150, // Esempio
                'new_hires' => 5
            ];
        }

        if (ModuleAccessService::canAccess('produzione')) {
            $data['production_stats'] = [
                'daily_production' => 1250,
                'efficiency' => 92.5
            ];
        }

        if (ModuleAccessService::canAccess('amministrazione')) {
            $data['admin_stats'] = [
                'pending_invoices' => 23,
                'monthly_revenue' => 125000
            ];
        }

        if (ModuleAccessService::canAccess('marketing')) {
            $data['marketing_stats'] = [
                'active_campaigns' => 8,
                'conversion_rate' => 3.2
            ];
        }

        return $data;
    }

    /**
     * Mostra le notifiche dell'utente
     */
    public function notifications()
    {
        /** @var User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(10);
        
        return view('admin.modules.home.notifications', compact('notifications'));
    }

    /**
     * Dashboard Obiettivi - Visualizza e gestisce gli obiettivi aziendali
     */
    public function dashboardObiettivi()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Dati obiettivi - da implementare con logica reale
        $obiettiviData = [
            'obiettivi_mensili' => [],
            'obiettivi_trimestrali' => [],
            'obiettivi_annuali' => [],
            'performance_corrente' => []
        ];
        
        return view('admin.modules.home.dashboard-obiettivi', [
            'user' => $user,
            'obiettivi' => $obiettiviData
        ]);
    }
}
