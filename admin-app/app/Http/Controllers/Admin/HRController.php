<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('module.access:hr');
    }

    /**
     * Dashboard HR - Solo per ruoli autorizzati
     */
    public function index()
    {
        $user = Auth::user();
        
        // Mock stats per dashboard HR
        $stats = [
            'total_employees' => 45,
            'new_hires' => 5,
            'absences' => 3,
            'turnover_rate' => 8.5,
        ];
        
        return view('admin.modules.hr.index', [
            'user' => $user,
            'stats' => $stats,
            'canCreate' => ModuleAccessService::canPerform('hr', 'create'),
            'canEdit' => ModuleAccessService::canPerform('hr', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('hr', 'delete'),
            'canViewReports' => ModuleAccessService::canPerform('hr', 'reports'),
        ]);
    }

    /**
     * Lista dipendenti - Solo con permesso view
     */
    public function employees()
    {
        $this->authorize('hr.view');
        
        // Logica per ottenere i dipendenti
        $employees = collect([
            ['id' => 1, 'name' => 'Mario Rossi', 'role' => 'Developer', 'location' => 'LAMEZIA'],
            ['id' => 2, 'name' => 'Luigi Verdi', 'role' => 'Manager', 'location' => 'RENDE'],
        ]);
        
        return view('admin.modules.hr.employees', [
            'employees' => $employees,
            'canCreate' => ModuleAccessService::canPerform('hr', 'create'),
            'canEdit' => ModuleAccessService::canPerform('hr', 'edit'),
            'canDelete' => ModuleAccessService::canPerform('hr', 'delete'),
        ]);
    }

    /**
     * Crea nuovo dipendente - Solo con permesso create
     */
    public function createEmployee()
    {
        $this->authorize('hr.create');
        
        return view('admin.modules.hr.create-employee');
    }

    /**
     * Salva nuovo dipendente
     */
    public function storeEmployee(Request $request)
    {
        $this->authorize('hr.create');
        
        // Validazione e salvataggio
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|string',
        ]);
        
        // Logica di salvataggio...
        
        return redirect()->route('admin.hr.employees')
            ->with('success', 'Dipendente creato con successo');
    }

    /**
     * Report HR - Solo con permesso reports
     */
    public function reports()
    {
        $this->authorize('hr.reports');
        
        $reportData = [
            'total_employees' => 150,
            'new_hires_month' => 5,
            'departures_month' => 2,
            'departments' => [
                'IT' => 45,
                'Amministrazione' => 25,
                'Produzione' => 80
            ]
        ];
        
        return view('admin.modules.hr.reports', compact('reportData'));
    }

    // ===== SOTTOMENU HR =====

    /**
     * Cruscotto Lead Recruit
     */
    public function cruscottoLeadRecruit()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.cruscotto-lead-recruit');
    }

    /**
     * Gara Ore
     */
    public function garaOre()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.gara-ore');
    }

    /**
     * Gara Punti
     */
    public function garaPunti()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.gara-punti');
    }

    /**
     * Formazione
     */
    public function formazione()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.formazione');
    }

    /**
     * Stringhe
     */
    public function stringhe()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.stringhe');
    }

    /**
     * Cruscotto Assenze
     */
    public function cruscottoAssenze()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.cruscotto-assenze');
    }

    /**
     * Gestione Operatori
     */
    public function gestioneOperatori()
    {
        $this->authorize('hr.edit');
        return view('admin.modules.hr.gestione-operatori');
    }

    /**
     * PES
     */
    public function pes()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.pes');
    }

    /**
     * Tabella per Mese
     */
    public function tabellaPerMese()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.tabella-per-mese');
    }

    /**
     * Tabella per Operatore
     */
    public function tabellaPerOperatore()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.tabella-per-operatore');
    }

    /**
     * Archivio Iban Operatori
     */
    public function archivioIbanOperatori()
    {
        $this->authorize('hr.view');
        return view('admin.modules.hr.archivio-iban-operatori');
    }

    /**
     * Import Indeed
     */
    public function importIndeed()
    {
        $this->authorize('hr.create');
        return view('admin.modules.hr.import-indeed');
    }
}
