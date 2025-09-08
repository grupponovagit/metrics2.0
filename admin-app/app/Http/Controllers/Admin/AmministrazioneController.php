<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AmministrazioneController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('module.access:amministrazione');
    }

    /**
     * Dashboard Amministrazione
     */
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'pending_invoices' => 23,
            'monthly_revenue' => 125000,
            'expenses_month' => 85000,
            'profit_margin' => 32.0
        ];
        
        return view('admin.modules.amministrazione.index', [
            'user' => $user,
            'stats' => $stats,
            'canCreate' => ModuleAccessService::canPerform('amministrazione', 'create'),
            'canEdit' => ModuleAccessService::canPerform('amministrazione', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('amministrazione', 'delete'),
            'canViewReports' => ModuleAccessService::canPerform('amministrazione', 'reports'),
        ]);
    }

    /**
     * Gestione Fatture
     */
    public function invoices()
    {
        $this->authorize('amministrazione.view');
        
        $invoices = collect([
            ['id' => 1, 'number' => 'FAT-2024-001', 'client' => 'Cliente A', 'amount' => 5000, 'status' => 'paid'],
            ['id' => 2, 'number' => 'FAT-2024-002', 'client' => 'Cliente B', 'amount' => 3200, 'status' => 'pending'],
        ]);
        
        return view('admin.modules.amministrazione.invoices', [
            'invoices' => $invoices,
            'canCreate' => ModuleAccessService::canPerform('amministrazione', 'create'),
            'canEdit' => ModuleAccessService::canPerform('amministrazione', 'edit'),
        ]);
    }

    /**
     * Gestione Budget
     */
    public function budget()
    {
        $this->authorize('amministrazione.view');
        
        $budgetData = [
            'annual_budget' => 1500000,
            'spent_ytd' => 850000,
            'remaining' => 650000,
            'departments' => [
                'IT' => ['budget' => 200000, 'spent' => 120000],
                'HR' => ['budget' => 150000, 'spent' => 95000],
                'Marketing' => ['budget' => 300000, 'spent' => 180000],
                'Produzione' => ['budget' => 850000, 'spent' => 455000],
            ]
        ];
        
        return view('admin.modules.amministrazione.budget', compact('budgetData'));
    }

    /**
     * Report Amministrativi
     */
    public function reports()
    {
        $this->authorize('amministrazione.reports');
        
        $reportData = [
            'monthly_summary' => [
                'revenue' => 125000,
                'expenses' => 85000,
                'profit' => 40000,
                'margin' => 32.0
            ],
            'yearly_trend' => [
                'Jan' => 95000, 'Feb' => 108000, 'Mar' => 125000,
                'Apr' => 132000, 'May' => 118000, 'Jun' => 145000
            ]
        ];
        
        return view('admin.modules.amministrazione.reports', compact('reportData'));
    }

    /**
     * Crea nuova fattura
     */
    public function createInvoice()
    {
        $this->authorize('amministrazione.create');
        
        return view('admin.modules.amministrazione.create-invoice');
    }

    /**
     * Salva fattura
     */
    public function storeInvoice(Request $request)
    {
        $this->authorize('amministrazione.create');
        
        $request->validate([
            'client' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);
        
        // Logica di salvataggio...
        
        return redirect()->route('admin.amministrazione.invoices')
            ->with('success', 'Fattura creata con successo');
    }

    // ===== SOTTOMENU AMMINISTRAZIONE =====

    /**
     * PDA Media
     */
    public function pdaMedia()
    {
        $this->authorize('amministrazione.view');
        return view('admin.modules.amministrazione.pda-media');
    }

    /**
     * Costi Stipendi
     */
    public function costiStipendi()
    {
        $this->authorize('amministrazione.view');
        return view('admin.modules.amministrazione.costi-stipendi');
    }

    /**
     * Costi Generali
     */
    public function costiGenerali()
    {
        $this->authorize('amministrazione.view');
        return view('admin.modules.amministrazione.costi-generali');
    }

    /**
     * Inviti a Fatturare
     */
    public function invitiAFatturare()
    {
        $this->authorize('amministrazione.create');
        return view('admin.modules.amministrazione.inviti-a-fatturare');
    }

    /**
     * Lettere Canvass
     */
    public function lettereCanvass()
    {
        $this->authorize('amministrazione.view');
        return view('admin.modules.amministrazione.lettere-canvass');
    }
}
