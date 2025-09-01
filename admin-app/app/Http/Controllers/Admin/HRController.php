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
        
        return view('admin.modules.hr.index', [
            'user' => $user,
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
}
