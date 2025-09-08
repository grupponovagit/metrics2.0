<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('module.access:marketing');
    }

    /**
     * Dashboard Marketing
     */
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'active_campaigns' => 8,
            'conversion_rate' => 3.2,
            'monthly_leads' => 245,
            'roi' => 125.5
        ];
        
        return view('admin.modules.marketing.index', [
            'user' => $user,
            'stats' => $stats,
            'canCreate' => ModuleAccessService::canPerform('marketing', 'create'),
            'canEdit' => ModuleAccessService::canPerform('marketing', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('marketing', 'delete'),
            'canViewReports' => ModuleAccessService::canPerform('marketing', 'reports'),
        ]);
    }

    /**
     * Gestione Campagne
     */
    public function campaigns()
    {
        $this->authorize('marketing.view');
        
        $campaigns = collect([
            ['id' => 1, 'name' => 'Campagna Estate 2024', 'type' => 'Digital', 'budget' => 15000, 'status' => 'active'],
            ['id' => 2, 'name' => 'Promo Prodotti A', 'type' => 'Email', 'budget' => 5000, 'status' => 'completed'],
            ['id' => 3, 'name' => 'Social Media Q2', 'type' => 'Social', 'budget' => 8000, 'status' => 'draft'],
        ]);
        
        return view('admin.modules.marketing.campaigns', [
            'campaigns' => $campaigns,
            'canCreate' => ModuleAccessService::canPerform('marketing', 'create'),
            'canEdit' => ModuleAccessService::canPerform('marketing', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('marketing', 'delete'),
        ]);
    }

    /**
     * Analisi Lead
     */
    public function leads()
    {
        $this->authorize('marketing.view');
        
        $leads = collect([
            ['id' => 1, 'name' => 'Mario Bianchi', 'email' => 'mario@example.com', 'source' => 'Website', 'status' => 'qualified'],
            ['id' => 2, 'name' => 'Anna Verdi', 'email' => 'anna@example.com', 'source' => 'Social Media', 'status' => 'new'],
            ['id' => 3, 'name' => 'Giuseppe Rossi', 'email' => 'giuseppe@example.com', 'source' => 'Email Campaign', 'status' => 'converted'],
        ]);
        
        return view('admin.modules.marketing.leads', [
            'leads' => $leads,
            'canEdit' => ModuleAccessService::canPerform('marketing', 'edit'),
        ]);
    }

    /**
     * Report Marketing
     */
    public function reports()
    {
        $this->authorize('marketing.reports');
        
        $reportData = [
            'monthly_metrics' => [
                'impressions' => 125000,
                'clicks' => 4200,
                'conversions' => 134,
                'cost_per_click' => 2.35,
                'conversion_rate' => 3.19
            ],
            'campaign_performance' => [
                'Digital' => ['leads' => 89, 'conversions' => 23, 'roi' => 145.2],
                'Email' => ['leads' => 156, 'conversions' => 45, 'roi' => 189.7],
                'Social' => ['leads' => 67, 'conversions' => 12, 'roi' => 98.4],
            ],
            'monthly_trend' => [
                'Jan' => 95, 'Feb' => 108, 'Mar' => 125,
                'Apr' => 132, 'May' => 118, 'Jun' => 145
            ]
        ];
        
        return view('admin.modules.marketing.reports', compact('reportData'));
    }

    /**
     * Crea nuova campagna
     */
    public function createCampaign()
    {
        $this->authorize('marketing.create');
        
        $campaignTypes = ['Digital', 'Email', 'Social', 'Print', 'Radio', 'TV'];
        
        return view('admin.modules.marketing.create-campaign', compact('campaignTypes'));
    }

    /**
     * Salva campagna
     */
    public function storeCampaign(Request $request)
    {
        $this->authorize('marketing.create');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required|string',
        ]);
        
        // Logica di salvataggio...
        
        return redirect()->route('admin.marketing.campaigns')
            ->with('success', 'Campagna creata con successo');
    }

    // ===== SOTTOMENU MARKETING =====

    /**
     * Cruscotto Lead
     */
    public function cruscottoLead()
    {
        $this->authorize('marketing.view');
        return view('admin.modules.marketing.cruscotto-lead');
    }

    /**
     * Costi Invio Messaggi
     */
    public function costiInvioMessaggi()
    {
        $this->authorize('marketing.view');
        return view('admin.modules.marketing.costi-invio-messaggi');
    }

    /**
     * Controllo SMS
     */
    public function controlloSms()
    {
        $this->authorize('marketing.view');
        return view('admin.modules.marketing.controllo-sms');
    }
}
