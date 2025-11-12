<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigurazioneUtmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('marketing.view');
        
        $configurazioni = DB::table('configurazione_campagne_digital')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.modules.marketing.configurazione-utm.index', compact('configurazioni'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('marketing.create');
        
        return view('admin.modules.marketing.configurazione-utm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('marketing.create');
        
        $validated = $request->validate([
            'account_id' => 'nullable|string|max:100',
            'tipo_lavorazione' => 'nullable|string|max:50',
            'utm_campaign' => 'nullable|string|max:100',
            'campagna_id' => 'nullable|string|max:25',
            'list_id' => 'nullable|integer',
        ], [
            'account_id.max' => 'L\'ID Account non può superare i 100 caratteri.',
            'tipo_lavorazione.max' => 'Il Tipo Lavorazione non può superare i 50 caratteri.',
            'utm_campaign.max' => 'L\'UTM Campaign non può superare i 100 caratteri.',
            'campagna_id.max' => 'L\'ID Campagna non può superare i 25 caratteri.',
            'list_id.integer' => 'Il List ID deve essere un numero intero.',
        ]);
        
        try {
            DB::table('configurazione_campagne_digital')->insert([
                'account_id' => $validated['account_id'],
                'tipo_lavorazione' => $validated['tipo_lavorazione'],
                'utm_campaign' => $validated['utm_campaign'],
                'campagna_id' => $validated['campagna_id'],
                'list_id' => $validated['list_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return redirect()
                ->route('admin.marketing.configurazione_utm.index')
                ->with('success', 'Configurazione UTM creata con successo!');
                
        } catch (\Exception $e) {
            Log::error('Errore creazione configurazione UTM', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Errore durante la creazione: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('marketing.edit');
        
        $configurazione = DB::table('configurazione_campagne_digital')
            ->where('id', $id)
            ->first();
        
        if (!$configurazione) {
            return redirect()
                ->route('admin.marketing.configurazione_utm.index')
                ->with('error', 'Configurazione non trovata.');
        }
        
        return view('admin.modules.marketing.configurazione-utm.edit', compact('configurazione'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('marketing.edit');
        
        $validated = $request->validate([
            'account_id' => 'nullable|string|max:100',
            'tipo_lavorazione' => 'nullable|string|max:50',
            'utm_campaign' => 'nullable|string|max:100',
            'campagna_id' => 'nullable|string|max:25',
            'list_id' => 'nullable|integer',
        ], [
            'account_id.max' => 'L\'ID Account non può superare i 100 caratteri.',
            'tipo_lavorazione.max' => 'Il Tipo Lavorazione non può superare i 50 caratteri.',
            'utm_campaign.max' => 'L\'UTM Campaign non può superare i 100 caratteri.',
            'campagna_id.max' => 'L\'ID Campagna non può superare i 25 caratteri.',
            'list_id.integer' => 'Il List ID deve essere un numero intero.',
        ]);
        
        try {
            $updated = DB::table('configurazione_campagne_digital')
                ->where('id', $id)
                ->update([
                    'account_id' => $validated['account_id'],
                    'tipo_lavorazione' => $validated['tipo_lavorazione'],
                    'utm_campaign' => $validated['utm_campaign'],
                    'campagna_id' => $validated['campagna_id'],
                    'list_id' => $validated['list_id'],
                    'updated_at' => now(),
                ]);
            
            if ($updated) {
                return redirect()
                    ->route('admin.marketing.configurazione_utm.index')
                    ->with('success', 'Configurazione UTM aggiornata con successo!');
            }
            
            return back()
                ->withInput()
                ->with('warning', 'Nessuna modifica effettuata.');
                
        } catch (\Exception $e) {
            Log::error('Errore aggiornamento configurazione UTM', [
                'id' => $id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Errore durante l\'aggiornamento: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('marketing.delete');
        
        try {
            $deleted = DB::table('configurazione_campagne_digital')
                ->where('id', $id)
                ->delete();
            
            if ($deleted) {
                return redirect()
                    ->route('admin.marketing.configurazione_utm.index')
                    ->with('success', 'Configurazione UTM eliminata con successo!');
            }
            
            return back()->with('error', 'Configurazione non trovata.');
            
        } catch (\Exception $e) {
            Log::error('Errore eliminazione configurazione UTM', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Errore durante l\'eliminazione: ' . $e->getMessage());
        }
    }
}

