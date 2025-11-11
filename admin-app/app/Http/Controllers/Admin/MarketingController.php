<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleAccessService;
use App\Models\ProspettoMensile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
    public function cruscottoLead(Request $request)
    {
        $this->authorize('marketing.view');
        
        // Parametri di filtro
        $ragioneSociale = $request->input('ragione_sociale', []);
        $provenienza = $request->input('provenienza', []);
        $campagne = $request->input('utm_campaign', []);
        $dataInizio = $request->input('data_inizio', now()->startOfMonth()->toDateString());
        $dataFine = $request->input('data_fine', now()->endOfMonth()->toDateString());
        
        // Query base
        $query = DB::table('report_digital_leads')
            ->whereBetween('data', [$dataInizio, $dataFine]);
        
        // Applica filtri
        if (!empty($ragioneSociale) && is_array($ragioneSociale)) {
            $query->whereIn('ragione_sociale', $ragioneSociale);
        }
        
        if (!empty($provenienza) && is_array($provenienza)) {
            $query->whereIn('provenienza', $provenienza);
        }
        
        if (!empty($campagne) && is_array($campagne)) {
            $query->whereIn('utm_campaign', $campagne);
        }
        
        // Ottieni dati aggregati
        $datiDettagliati = $query->get();
        
        // Calcola totali
        $totali = [
            'costo' => $datiDettagliati->sum('costo'),
            'leads' => $datiDettagliati->sum('leads'),
            'conv' => $datiDettagliati->sum('conv'),
            'ok_lead' => $datiDettagliati->sum('ok_lead'),
            'ko_lead' => $datiDettagliati->sum('ko_lead'),
            'click' => $datiDettagliati->sum('click'),
            'ore' => $datiDettagliati->sum('ore'),
            'ricavi' => $datiDettagliati->sum('ricavi'),
        ];
        
        // Calcola KPI
        $totali['cpl'] = $totali['leads'] > 0 ? $totali['costo'] / $totali['leads'] : 0;
        $totali['cpa'] = $totali['conv'] > 0 ? $totali['costo'] / $totali['conv'] : 0;
        $totali['cpc'] = $totali['click'] > 0 ? $totali['costo'] / $totali['click'] : 0;
        $totali['roas'] = $totali['costo'] > 0 ? ($totali['ricavi'] / $totali['costo']) * 100 : 0;
        $totali['roi'] = $totali['costo'] > 0 ? (($totali['ricavi'] - $totali['costo']) / $totali['costo']) * 100 : 0;
        
        // Ottieni opzioni per i filtri (distinct values)
        $opzioniRagioneSociale = DB::table('report_digital_leads')
            ->select('ragione_sociale')
            ->distinct()
            ->whereNotNull('ragione_sociale')
            ->orderBy('ragione_sociale')
            ->pluck('ragione_sociale');
        
        $opzioniProvenienza = DB::table('report_digital_leads')
            ->select('provenienza')
            ->distinct()
            ->whereNotNull('provenienza')
            ->orderBy('provenienza')
            ->pluck('provenienza');
        
        // Campagne filtrate dinamicamente in base a ragione sociale e provenienza
        $queryCampagne = DB::table('report_digital_leads')
            ->select('utm_campaign')
            ->distinct()
            ->whereNotNull('utm_campaign');
        
        if (!empty($ragioneSociale) && is_array($ragioneSociale)) {
            $queryCampagne->whereIn('ragione_sociale', $ragioneSociale);
        }
        
        if (!empty($provenienza) && is_array($provenienza)) {
            $queryCampagne->whereIn('provenienza', $provenienza);
        }
        
        $opzioniCampagne = $queryCampagne->orderBy('utm_campaign')->pluck('utm_campaign');
        
        return view('admin.modules.marketing.cruscotto-lead.index', [
            'datiDettagliati' => $datiDettagliati,
            'totali' => $totali,
            'opzioniRagioneSociale' => $opzioniRagioneSociale,
            'opzioniProvenienza' => $opzioniProvenienza,
            'opzioniCampagne' => $opzioniCampagne,
            'filtri' => [
                'ragione_sociale' => $ragioneSociale,
                'provenienza' => $provenienza,
                'utm_campaign' => $campagne,
                'data_inizio' => $dataInizio,
                'data_fine' => $dataFine,
            ]
        ]);
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

    // ===== PROSPETTO MENSILE =====

    /**
     * Lista Prospetti Mensili
     */
    public function prospettoMensile()
    {
        $this->authorize('marketing.view');
        
        $prospetti = ProspettoMensile::orderBy('anno', 'desc')
            ->orderBy('mese', 'desc')
            ->get();
        
        return view('admin.modules.marketing.prospetto-mensile.index', compact('prospetti'));
    }

    /**
     * Crea nuovo prospetto mensile
     */
    public function prospettoMensileCreate()
    {
        $this->authorize('marketing.create');
        
        // Ottieni mesi e anni disponibili
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        
        $anni = range(date('Y') - 1, date('Y') + 2);
        
        return view('admin.modules.marketing.prospetto-mensile.create', compact('mesi', 'anni'));
    }

    /**
     * Salva nuovo prospetto mensile
     */
    public function prospettoMensileStore(Request $request)
    {
        $this->authorize('marketing.create');
        
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:prospetto_mensiles,nome',
            'mese' => 'required|integer|min:1|max:12',
            'anno' => 'required|integer|min:2020|max:2100',
            'giorni_lavorativi' => 'required|integer|min:1|max:31',
            'descrizione' => 'nullable|string',
            'dati_json' => 'required|json',
        ], [
            'nome.required' => 'Il nome del prospetto è obbligatorio',
            'nome.unique' => 'Esiste già un prospetto con questo nome',
            'mese.required' => 'Il mese è obbligatorio',
            'anno.required' => 'L\'anno è obbligatorio',
            'giorni_lavorativi.required' => 'I giorni lavorativi sono obbligatori',
            'giorni_lavorativi.min' => 'I giorni lavorativi devono essere almeno 1',
            'giorni_lavorativi.max' => 'I giorni lavorativi non possono superare 31',
            'dati_json.required' => 'I dati JSON sono obbligatori',
            'dati_json.json' => 'I dati JSON non sono validi',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $datiAccounts = json_decode($request->dati_json, true);
        
        // Validazione struttura JSON
        if (!isset($datiAccounts['accounts']) || !is_array($datiAccounts['accounts'])) {
            return redirect()->back()
                ->withErrors(['dati_json' => 'La struttura JSON non è valida. Deve contenere un array "accounts".'])
                ->withInput();
        }
        
        $prospetto = ProspettoMensile::create([
            'nome' => $request->nome,
            'mese' => $request->mese,
            'anno' => $request->anno,
            'giorni_lavorativi' => $request->giorni_lavorativi,
            'descrizione' => $request->descrizione,
            'dati_accounts' => $datiAccounts,
            'attivo' => true,
        ]);
        
        return redirect()->route('admin.marketing.prospetto_mensile.view', $prospetto->id)
            ->with('success', 'Prospetto mensile creato con successo!');
    }

    /**
     * Visualizza prospetto mensile
     */
    public function prospettoMensileView($id)
    {
        $this->authorize('marketing.view');
        
        $prospetto = ProspettoMensile::findOrFail($id);
        
        return view('admin.modules.marketing.prospetto-mensile.view', compact('prospetto'));
    }

    /**
     * Modifica prospetto mensile
     */
    public function prospettoMensileEdit($id)
    {
        $this->authorize('marketing.edit');
        
        $prospetto = ProspettoMensile::findOrFail($id);
        
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        
        $anni = range(date('Y') - 1, date('Y') + 2);
        
        return view('admin.modules.marketing.prospetto-mensile.edit', compact('prospetto', 'mesi', 'anni'));
    }

    /**
     * Aggiorna prospetto mensile
     */
    public function prospettoMensileUpdate(Request $request, $id)
    {
        $this->authorize('marketing.edit');
        
        $prospetto = ProspettoMensile::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:prospetto_mensiles,nome,' . $id,
            'mese' => 'required|integer|min:1|max:12',
            'anno' => 'required|integer|min:2020|max:2100',
            'giorni_lavorativi' => 'required|integer|min:1|max:31',
            'descrizione' => 'nullable|string',
            'dati_json' => 'required|json',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $datiAccounts = json_decode($request->dati_json, true);
        
        if (!isset($datiAccounts['accounts']) || !is_array($datiAccounts['accounts'])) {
            return redirect()->back()
                ->withErrors(['dati_json' => 'La struttura JSON non è valida.'])
                ->withInput();
        }
        
        $prospetto->update([
            'nome' => $request->nome,
            'mese' => $request->mese,
            'anno' => $request->anno,
            'giorni_lavorativi' => $request->giorni_lavorativi,
            'descrizione' => $request->descrizione,
            'dati_accounts' => $datiAccounts,
        ]);
        
        return redirect()->route('admin.marketing.prospetto_mensile.view', $prospetto->id)
            ->with('success', 'Prospetto mensile aggiornato con successo!');
    }

    /**
     * Elimina prospetto mensile
     */
    public function prospettoMensileDestroy($id)
    {
        $this->authorize('marketing.delete');
        
        $prospetto = ProspettoMensile::findOrFail($id);
        $prospetto->delete();
        
        return redirect()->route('admin.marketing.prospetto_mensile.index')
            ->with('success', 'Prospetto mensile eliminato con successo!');
    }

    /**
     * Toggle stato attivo
     */
    public function prospettoMensileToggleAttivo($id)
    {
        $this->authorize('marketing.edit');
        
        $prospetto = ProspettoMensile::findOrFail($id);
        $prospetto->attivo = !$prospetto->attivo;
        $prospetto->save();
        
        return redirect()->back()
            ->with('success', 'Stato prospetto aggiornato con successo!');
    }
}
