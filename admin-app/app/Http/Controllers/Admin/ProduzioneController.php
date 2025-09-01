<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
