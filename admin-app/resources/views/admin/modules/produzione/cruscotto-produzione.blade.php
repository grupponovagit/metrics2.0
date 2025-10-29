<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Cruscotto Produzione') }}</x-slot>
    
    <x-admin.page-header 
        title="Cruscotto Produzione" 
        subtitle="Dashboard produzione con KPI e metriche dettagliate"
        icon="chart-area"
        iconColor="warning"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.produzione.index') }}" class="btn btn-outline btn-warning">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- FILTRI DINAMICI A CASCATA - UI MIGLIORATA --}}
    <x-admin.card tone="light" shadow="lg" padding="lg" class="mb-6">
        <form method="GET" action="{{ route('admin.produzione.cruscotto_produzione') }}" id="filterForm">
            
            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Colonna Sinistra: Date --}}
                <div class="lg:w-1/4 space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-base">
                                <x-ui.icon name="calendar" class="h-4 w-4 inline" />
                                Data Inizio
                            </span>
                        </label>
                        <input type="date" name="data_inizio" value="{{ $dataInizio }}" class="input input-bordered w-full" required>
                        <label class="label">
                            <span class="label-text-alt">Data di inizio periodo</span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-base">
                                <x-ui.icon name="calendar" class="h-4 w-4 inline" />
                                Data Fine
                            </span>
                        </label>
                        <input type="date" name="data_fine" value="{{ $dataFine }}" class="input input-bordered w-full" required>
                        <label class="label">
                            <span class="label-text-alt">Data di fine periodo</span>
                        </label>
                    </div>
                    
                    {{-- Pulsanti azione --}}
                    <div class="divider"></div>
                    <div class="space-y-2">
                        <button type="submit" class="btn btn-primary w-full">
                            <x-ui.icon name="filter" class="h-5 w-5" />
                            Applica Filtri
                        </button>
                        <a href="{{ route('admin.produzione.cruscotto_produzione') }}" class="btn btn-outline w-full">
                            <x-ui.icon name="x" class="h-5 w-5" />
                            Reset Filtri
                        </a>
                    </div>
                </div>
                
                {{-- Colonna Destra: Filtri a cascata --}}
                <div class="lg:w-3/4">
                    <div class="bg-base-200/50 rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                            <x-ui.icon name="funnel" class="h-5 w-5 text-primary" />
                            Filtri Avanzati (Opzionali)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Commessa --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">
                                        <x-ui.icon name="briefcase" class="h-4 w-4 inline text-warning" />
                                        Commessa
                                    </span>
                                </label>
                                <select name="commessa" id="commessaSelect" class="select select-bordered select-warning">
                                    <option value="">Tutte le commesse</option>
                                    @foreach($commesse as $commessa)
                                        <option value="{{ $commessa }}" {{ $commessaFilter == $commessa ? 'selected' : '' }}>
                                            {{ $commessa }}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="label">
                                    <span class="label-text-alt">Seleziona per filtrare</span>
                                </label>
                            </div>
                            
                            {{-- Sede --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">
                                        <x-ui.icon name="building" class="h-4 w-4 inline text-info" />
                                        Sede
                                    </span>
                                    @if(!$commessaFilter)
                                        <span class="badge badge-sm">Richiede Commessa</span>
                                    @endif
                                </label>
                                <select name="sede" id="sedeSelect" class="select select-bordered select-info" {{ !$commessaFilter ? 'disabled' : '' }}>
                                    <option value="">Tutte le sedi</option>
                                    @if($commessaFilter)
                                        @foreach($sedi as $sede)
                                            <option value="{{ $sede }}" {{ $sedeFilter == $sede ? 'selected' : '' }}>
                                                {{ $sede }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <label class="label">
                                    <span class="label-text-alt">Dipende da commessa</span>
                                </label>
                            </div>
                            
                            {{-- Macro Campagna --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">
                                        <x-ui.icon name="bullseye" class="h-4 w-4 inline text-success" />
                                        Macro Campagna
                                    </span>
                                    @if(!$sedeFilter)
                                        <span class="badge badge-sm">Richiede Sede</span>
                                    @endif
                                </label>
                                <select name="macro_campagna" id="macroCampagnaSelect" class="select select-bordered select-success" {{ !$sedeFilter ? 'disabled' : '' }}>
                                    <option value="">Tutte le campagne</option>
                                    @if($sedeFilter)
                                        @foreach($macroCampagne as $campagna)
                                            <option value="{{ $campagna }}" {{ $macroCampagnaFilter == $campagna ? 'selected' : '' }}>
                                                {{ $campagna }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <label class="label">
                                    <span class="label-text-alt">Dipende da sede</span>
                                </label>
                            </div>
                        </div>
                        
                        {{-- Info box --}}
                        @if($commessaFilter || $sedeFilter || $macroCampagnaFilter)
                        <div class="alert alert-info mt-4">
                            <x-ui.icon name="info-circle" class="h-5 w-5" />
                            <div>
                                <div class="font-bold">Filtri attivi:</div>
                                <div class="text-sm">
                                    @if($commessaFilter) <span class="badge badge-warning">{{ $commessaFilter }}</span> @endif
                                    @if($sedeFilter) <span class="badge badge-info">{{ $sedeFilter }}</span> @endif
                                    @if($macroCampagnaFilter) <span class="badge badge-success">{{ $macroCampagnaFilter }}</span> @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </x-admin.card>
    
    <br/>
    {{-- TABELLA DETTAGLIATA - Visibile solo se ci sono filtri applicati --}}
    @if(request()->hasAny(['data_inizio', 'data_fine', 'commessa', 'sede', 'macro_campagna']))
    
    {{-- Alert PAF --}}
    @if(!($kpiTotali['mostra_paf'] ?? false))
    <div class="alert alert-warning mb-4">
        <x-ui.icon name="info-circle" class="h-5 w-5" />
        <div>
            <div class="font-bold">Attenzione</div>
            <div class="text-sm">I dati PAF (Proiezione A Fine mese) sono visibili solo quando si filtra esclusivamente il mese corrente. Le colonne PAF verranno mostrate come zero poiché il periodo filtrato non corrisponde al mese corrente.</div>
        </div>
    </div>
    @endif
    
    <x-admin.card tone="light" shadow="lg" padding="none">
        <div class="p-6 border-b border-base-300 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-base-content">
                    Dettaglio KPI per Commessa, Sede e Campagna
                </h3>
                <p class="text-sm text-base-content/60 mt-1">
                    Visualizzazione gerarchica delle metriche di produzione
                </p>
            </div>
            
            {{-- Pulsanti Sintetico/Dettagliato --}}
            <div class="flex gap-2">
                <button 
                    onclick="switchView('sintetico')" 
                    id="btn-sintetico"
                    class="btn btn-sm btn-primary"
                >
                    Sintetico
                </button>
                <button 
                    onclick="switchView('dettagliato')" 
                    id="btn-dettagliato"
                    class="btn btn-sm btn-outline btn-primary"
                >
                    Dettagliato
                </button>
            </div>
        </div>
        
        {{-- TABELLA DETTAGLIATA --}}
        <div id="table-dettagliato" class="table-scroll-container max-h-[70vh] hidden" style="overflow-x: auto !important; overflow-y: auto !important;">
            <table class="table table-zebra w-full" style="min-width: 2400px;">
                <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
                    <tr>
                        <th class="sticky-det-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Commessa</th>
                        <th class="sticky-det-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        <th class="sticky-det-campagna font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Macro Campagna</th>
                        
                        {{-- Prodotto --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" rowspan="2">Prodotto</th>
                        
                        {{-- Inserito --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" rowspan="2">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" rowspan="2">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" rowspan="2">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" rowspan="2">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2">Ore</th>
                        
                        {{-- RESA --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2">Resa Prod.</th>
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2">Resa Ins.</th>
                        
                        {{-- OBIETTIVI (3 sottocolonne) --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300" colspan="3">Obiettivi</th>
                        
                        {{-- PAF MENSILE (3 sottocolonne) --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300" colspan="3">Paf Mensile</th>
                    </tr>
                    <tr>
                        {{-- Sottocolonne Obiettivi --}}
                        <th class="font-bold text-xs text-center bg-teal-50 border-r border-base-200">Mensile</th>
                        <th class="font-bold text-xs text-center bg-teal-50 border-r border-base-200">Passo Giorno</th>
                        <th class="font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300">Diff. Obj</th>
                        
                        {{-- Sottocolonne PAF --}}
                        <th class="font-bold text-xs text-center bg-purple-50 border-r border-base-200">Ore Paf</th>
                        <th class="font-bold text-xs text-center bg-purple-50 border-r border-base-200">Pezzi Paf</th>
                        <th class="font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300">Resa Paf</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datiRaggruppati as $mandato => $sediData)
                        @php
                            $mandatoRowspan = $sediData->sum(fn($campagneData) => $campagneData->count());
                            $firstMandato = true;
                        @endphp
                        
                        @foreach($sediData as $sede => $campagneData)
                            @php
                                $sedeRowspan = $campagneData->count();
                                $firstSede = true;
                            @endphp
                            
                            @foreach($campagneData as $datiCampagna)
                                <tr>
                                {{-- Cliente --}}
                                @if($firstMandato)
                                    <td class="sticky-det-cliente font-bold border-r-2 border-base-300 bg-base-200/30" rowspan="{{ $mandatoRowspan }}">
                                        {{-- Mostra il nome originale dalla cache (es: TIM_CONSUMER) --}}
                                        {{ collect($campagneData)->first()['cliente_originale'] ?? $mandato }}
                                    </td>
                                    @php $firstMandato = false; @endphp
                                @endif
                                    
                                    {{-- Sede --}}
                                    @if($firstSede)
                                        <td class="sticky-det-sede font-semibold border-r-2 border-base-300 bg-base-100" rowspan="{{ $sedeRowspan }}">
                                            {{ $sede }}
                                        </td>
                                        @php $firstSede = false; @endphp
                                    @endif
                                    
                                    {{-- Campagna/Prodotto --}}
                                    <td class="sticky-det-campagna border-r-2 border-base-300">
                                        <div class="text-sm font-medium">{{ $datiCampagna['campagna'] }}</div>
                                        
                                        {{-- Prodotti Aggiuntivi --}}
                                        @if(!empty($datiCampagna['prodotti_aggiuntivi']))
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach($datiCampagna['prodotti_aggiuntivi'] as $prodAggiuntivo)
                                                    <span class="badge badge-xs badge-primary">{{ $prodAggiuntivo }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    
                                    {{-- Prodotto --}}
                                    <td class="text-center text-sm bg-orange-50 border-r-2 border-base-300">{{ number_format($datiCampagna['prodotto_pda']) }}</td>
                                    
                                    {{-- Inserito --}}
                                    <td class="text-center text-sm bg-green-50 border-r-2 border-base-300">{{ number_format($datiCampagna['inserito_pda']) }}</td>
                                    
                                    {{-- KO --}}
                                    <td class="text-center text-sm bg-red-50 border-r-2 border-base-300">{{ number_format($datiCampagna['ko_pda']) }}</td>
                                    
                                    {{-- BackLog --}}
                                    <td class="text-center text-sm bg-yellow-50 border-r-2 border-base-300">{{ number_format($datiCampagna['backlog_pda']) }}</td>
                                    
                                    {{-- BackLog Partner --}}
                                    <td class="text-center text-sm bg-blue-50 border-r-2 border-base-300">{{ number_format($datiCampagna['backlog_partner_pda']) }}</td>
                                    
                                    {{-- Ore --}}
                                    <td class="text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">
                                        {{ ($datiCampagna['ore'] ?? 0) > 0 ? number_format($datiCampagna['ore'], 2) : '-' }}
                                    </td>
                                    
                                    {{-- Resa Prodotto --}}
                                    <td class="text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                        {{ $datiCampagna['resa_prodotto'] ?? '-' }}
                                    </td>
                                    
                                    {{-- Resa Inserito --}}
                                    <td class="text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                        {{ $datiCampagna['resa_inserito'] ?? '-' }}
                                    </td>
                                    
                                    {{-- OBIETTIVI --}}
                                    <td class="text-center text-xs bg-teal-50 border-r border-base-200">{{ $datiCampagna['obiettivo_mensile'] ?? 0 }}</td>
                                    <td class="text-center text-xs bg-teal-50 border-r border-base-200">{{ $datiCampagna['passo_giorno'] ?? 0 }}</td>
                                    <td class="text-center text-xs bg-teal-50 border-r-2 border-base-300 {{ ($datiCampagna['differenza_obj'] ?? 0) < 0 ? 'text-green-600 font-bold' : 'text-red-600' }}">
                                        {{ $datiCampagna['differenza_obj'] ?? 0 }}
                                    </td>
                                    
                                    {{-- PAF MENSILE --}}
                                    <td class="text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($datiCampagna['ore_paf'] ?? 0, 2) }}</td>
                                    <td class="text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($datiCampagna['pezzi_paf'] ?? 0, 0) }}</td>
                                    <td class="text-center text-xs bg-purple-50 border-r-2 border-base-300">{{ $datiCampagna['resa_paf'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        
                        {{-- RIGA TOTALE PER CLIENTE --}}
                        @php
                            $totaleCliente = [
                                'prodotto_pda' => 0,
                                'inserito_pda' => 0,
                                'ko_pda' => 0,
                                'backlog_pda' => 0,
                                'backlog_partner_pda' => 0,
                                'ore' => 0,
                                'ore_paf' => 0,
                                'pezzi_paf' => 0,
                                'obiettivo_mensile' => 0,
                            ];
                            
                            foreach($sediData as $campagneData) {
                                foreach($campagneData as $dati) {
                                    $totaleCliente['prodotto_pda'] += $dati['prodotto_pda'] ?? 0;
                                    $totaleCliente['inserito_pda'] += $dati['inserito_pda'] ?? 0;
                                    $totaleCliente['ko_pda'] += $dati['ko_pda'] ?? 0;
                                    $totaleCliente['backlog_pda'] += $dati['backlog_pda'] ?? 0;
                                    $totaleCliente['backlog_partner_pda'] += $dati['backlog_partner_pda'] ?? 0;
                                    $totaleCliente['ore'] += $dati['ore'] ?? 0;
                                    $totaleCliente['ore_paf'] += $dati['ore_paf'] ?? 0;
                                    $totaleCliente['pezzi_paf'] += $dati['pezzi_paf'] ?? 0;
                                    $totaleCliente['obiettivo_mensile'] += $dati['obiettivo_mensile'] ?? 0;
                                }
                            }
                            
                            // Calcoli resa
                            $totaleCliente['resa_prodotto'] = $totaleCliente['ore'] > 0 ? round($totaleCliente['prodotto_pda'] / $totaleCliente['ore'], 2) : 0;
                            $totaleCliente['resa_inserito'] = $totaleCliente['ore'] > 0 ? round($totaleCliente['inserito_pda'] / $totaleCliente['ore'], 2) : 0;
                            $totaleCliente['resa_paf'] = $totaleCliente['ore_paf'] > 0 ? round($totaleCliente['pezzi_paf'] / $totaleCliente['ore_paf'], 2) : 0;
                            
                            // Calcoli obiettivi
                            $diffObjCliente = $totaleCliente['obiettivo_mensile'] - $totaleCliente['inserito_pda'];
                            
                            // Passo giorno: solo se ci sono giorni rimanenti E differenza positiva
                            $passoGiornoCliente = 0;
                            if (isset($kpiArray['giorni_lavorativi_rimanenti']) && $kpiArray['giorni_lavorativi_rimanenti'] > 0 && $diffObjCliente > 0) {
                                $passoGiornoCliente = round($diffObjCliente / $kpiArray['giorni_lavorativi_rimanenti'], 2);
                            }
                        @endphp
                        <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                            <td colspan="3" class="sticky-det-totale text-left text-sm font-bold py-2 px-4 border-r-2 border-slate-300">TOTALE {{ $mandato }}</td>
                            <td class="text-center text-sm bg-orange-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['prodotto_pda']) }}</td>
                            <td class="text-center text-sm bg-green-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['inserito_pda']) }}</td>
                            <td class="text-center text-sm bg-red-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ko_pda']) }}</td>
                            <td class="text-center text-sm bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_pda']) }}</td>
                            <td class="text-center text-sm bg-blue-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_partner_pda']) }}</td>
                            <td class="text-center text-sm bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ore'], 2) }}</td>
                            <td class="text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_prodotto'] }}</td>
                            <td class="text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_inserito'] }}</td>
                            
                            {{-- Obiettivi --}}
                            <td class="text-center text-xs bg-teal-100 border-r border-slate-200">{{ number_format($totaleCliente['obiettivo_mensile'], 0) }}</td>
                            <td class="text-center text-xs bg-teal-100 border-r border-slate-200">{{ $passoGiornoCliente }}</td>
                            <td class="text-center text-xs bg-teal-100 border-r-2 border-slate-300 {{ $diffObjCliente < 0 ? 'text-green-700 font-bold' : 'text-red-700' }}">
                                {{ number_format($diffObjCliente, 0) }}
                            </td>
                            
                            {{-- PAF Mensile --}}
                            <td class="text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['ore_paf'], 2) }}</td>
                            <td class="text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['pezzi_paf'], 0) }}</td>
                            <td class="text-center text-xs bg-purple-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_paf'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center py-12">
                                <div>
                                    <h3 class="text-lg font-semibold text-base-content mb-1">Nessun dato disponibile</h3>
                                    <p class="text-sm text-base-content/60">Prova a modificare i filtri per visualizzare i dati</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    
                    {{-- RIGA TOTALE --}}
                    @if(!$datiRaggruppati->isEmpty())
                        @php
                            $totali = [
                                'prodotto_pda' => 0,
                                'inserito_pda' => 0,
                                'ko_pda' => 0,
                                'backlog_pda' => 0,
                                'backlog_partner_pda' => 0,
                                'ore' => 0,
                                'resa_prodotto' => 0,
                                'resa_inserito' => 0,
                                'ore_paf' => 0,
                                'pezzi_paf' => 0,
                                'resa_paf' => 0,
                            ];
                            
                            foreach($datiRaggruppati as $sediData) {
                                foreach($sediData as $campagneData) {
                                    foreach($campagneData as $dati) {
                                        $totali['prodotto_pda'] += $dati['prodotto_pda'] ?? 0;
                                        $totali['inserito_pda'] += $dati['inserito_pda'] ?? 0;
                                        $totali['ko_pda'] += $dati['ko_pda'] ?? 0;
                                        $totali['backlog_pda'] += $dati['backlog_pda'] ?? 0;
                                        $totali['backlog_partner_pda'] += $dati['backlog_partner_pda'] ?? 0;
                                        $totali['ore'] += $dati['ore'] ?? 0;
                                        $totali['ore_paf'] += $dati['ore_paf'] ?? 0;
                                        $totali['pezzi_paf'] += $dati['pezzi_paf'] ?? 0;
                                    }
                                }
                            }
                            
                            // Calcoli resa
                            $totali['resa_prodotto'] = $totali['ore'] > 0 ? round($totali['prodotto_pda'] / $totali['ore'], 2) : 0;
                            $totali['resa_inserito'] = $totali['ore'] > 0 ? round($totali['inserito_pda'] / $totali['ore'], 2) : 0;
                            $totali['resa_paf'] = $totali['ore_paf'] > 0 ? round($totali['pezzi_paf'] / $totali['ore_paf'], 2) : 0;
                        @endphp
                        <tr class="bg-slate-200 font-bold border-t-4 border-slate-400">
                            <td colspan="3" class="sticky-det-totale text-left text-base font-bold py-3 px-4 border-r-2 border-slate-300">TOTALE</td>
                            <td class="text-center text-base bg-orange-100 border-r-2 border-slate-300">{{ number_format($totali['prodotto_pda']) }}</td>
                            <td class="text-center text-base bg-green-100 border-r-2 border-slate-300">{{ number_format($totali['inserito_pda']) }}</td>
                            <td class="text-center text-base bg-red-100 border-r-2 border-slate-300">{{ number_format($totali['ko_pda']) }}</td>
                            <td class="text-center text-base bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_pda']) }}</td>
                            <td class="text-center text-base bg-blue-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_partner_pda']) }}</td>
                            <td class="text-center text-base bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totali['ore'], 2) }}</td>
                            <td class="text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_prodotto'] }}</td>
                            <td class="text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_inserito'] }}</td>
                            
                            {{-- Obiettivi (al momento a 0) --}}
                            <td class="text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="text-center text-sm bg-teal-100 border-r-2 border-slate-300">0</td>
                            
                            {{-- PAF Mensile --}}
                            <td class="text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['ore_paf'], 2) }}</td>
                            <td class="text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['pezzi_paf'], 0) }}</td>
                            <td class="text-center text-sm bg-purple-100 border-r-2 border-slate-300">{{ $totali['resa_paf'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- TABELLA SINTETICA --}}
        <div id="table-sintetico" class="table-scroll-container max-h-[70vh] w-full" style="overflow-x: auto !important; overflow-y: auto !important;">
            <table class="table table-zebra w-full" style="min-width: 2200px;">
                <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
                    <tr>
                        <th class="sticky-col-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Commessa</th>
                        <th class="sticky-col-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        
                        {{-- Prodotto --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" rowspan="2">Prodotto</th>
                        
                        {{-- Inserito --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" rowspan="2">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" rowspan="2">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" rowspan="2">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" rowspan="2">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2">Ore</th>
                        
                        {{-- RESA --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2">Resa Prod.</th>
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2">Resa Ins.</th>
                        
                        {{-- OBIETTIVI (3 sottocolonne) --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300" colspan="3">Obiettivi</th>
                        
                        {{-- PAF MENSILE (3 sottocolonne) --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300" colspan="3">Paf Mensile</th>
                    </tr>
                    <tr>
                        {{-- Sottocolonne Obiettivi --}}
                        <th class="font-bold text-xs text-center bg-teal-50 border-r border-base-200">Mensile</th>
                        <th class="font-bold text-xs text-center bg-teal-50 border-r border-base-200">Passo Giorno</th>
                        <th class="font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300">Diff. Obj</th>
                        
                        {{-- Sottocolonne PAF --}}
                        <th class="font-bold text-xs text-center bg-purple-50 border-r border-base-200">Ore Paf</th>
                        <th class="font-bold text-xs text-center bg-purple-50 border-r border-base-200">Pezzi Paf</th>
                        <th class="font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300">Resa Paf</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datiSintetici as $cliente => $sediData)
                        @php
                            $clienteRowspan = $sediData->count();
                            $firstCliente = true;
                        @endphp
                        
                        @foreach($sediData as $sede => $datiSede)
                            @php
                                $dati = $datiSede['totale'];
                            @endphp
                            <tr>
                                {{-- Cliente --}}
                                @if($firstCliente)
                                    <td class="sticky-col-cliente font-bold border-r-2 border-base-300 bg-base-200/30" rowspan="{{ $clienteRowspan }}">
                                        {{-- Mostra il nome originale dalla cache (es: TIM_CONSUMER) --}}
                                        {{ $dati['cliente_originale'] ?? $cliente }}
                                    </td>
                                    @php $firstCliente = false; @endphp
                                @endif
                                
                                {{-- Sede --}}
                                <td class="sticky-col-sede font-semibold border-r-2 border-base-300 bg-base-100">
                                    {{ $sede }}
                                </td>
                                
                                {{-- Prodotto --}}
                                <td class="text-center text-sm bg-orange-50 border-r-2 border-base-300">{{ number_format($dati['prodotto_pda']) }}</td>
                                
                                {{-- Inserito --}}
                                <td class="text-center text-sm bg-green-50 border-r-2 border-base-300">{{ number_format($dati['inserito_pda']) }}</td>
                                
                                {{-- KO --}}
                                <td class="text-center text-sm bg-red-50 border-r-2 border-base-300">{{ number_format($dati['ko_pda']) }}</td>
                                
                                {{-- BackLog --}}
                                <td class="text-center text-sm bg-yellow-50 border-r-2 border-base-300">{{ number_format($dati['backlog_pda']) }}</td>
                                
                                {{-- BackLog Partner --}}
                                <td class="text-center text-sm bg-blue-50 border-r-2 border-base-300">{{ number_format($dati['backlog_partner_pda']) }}</td>
                                
                                {{-- Ore --}}
                                <td class="text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">
                                    {{ ($dati['ore'] ?? 0) > 0 ? number_format($dati['ore'], 2) : '-' }}
                                </td>
                                
                                {{-- Resa Prodotto --}}
                                <td class="text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                    {{ $dati['resa_prodotto'] ?? '-' }}
                                </td>
                                
                                {{-- Resa Inserito --}}
                                <td class="text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                    {{ $dati['resa_inserito'] ?? '-' }}
                                </td>
                                
                                {{-- OBIETTIVI --}}
                                <td class="text-center text-xs bg-teal-50 border-r border-base-200">{{ $dati['obiettivo_mensile'] ?? 0 }}</td>
                                <td class="text-center text-xs bg-teal-50 border-r border-base-200">{{ $dati['passo_giorno'] ?? 0 }}</td>
                                <td class="text-center text-xs bg-teal-50 border-r-2 border-base-300 {{ ($dati['differenza_obj'] ?? 0) < 0 ? 'text-green-600 font-bold' : 'text-red-600' }}">
                                    {{ $dati['differenza_obj'] ?? 0 }}
                                </td>
                                
                                {{-- PAF MENSILE --}}
                                <td class="text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($dati['ore_paf'] ?? 0, 2) }}</td>
                                <td class="text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($dati['pezzi_paf'] ?? 0, 0) }}</td>
                                <td class="text-center text-xs bg-purple-50 border-r-2 border-base-300">{{ $dati['resa_paf'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                        
                        {{-- RIGA TOTALE PER CLIENTE --}}
                        @php
                            $totaleCliente = [
                                'prodotto_pda' => 0,
                                'inserito_pda' => 0,
                                'ko_pda' => 0,
                                'backlog_pda' => 0,
                                'backlog_partner_pda' => 0,
                                'ore' => 0,
                                'ore_paf' => 0,
                                'pezzi_paf' => 0,
                                'obiettivo_mensile' => 0,
                            ];
                            
                            foreach($sediData as $datiSede) {
                                $dati = $datiSede['totale'];
                                $totaleCliente['prodotto_pda'] += $dati['prodotto_pda'] ?? 0;
                                $totaleCliente['inserito_pda'] += $dati['inserito_pda'] ?? 0;
                                $totaleCliente['ko_pda'] += $dati['ko_pda'] ?? 0;
                                $totaleCliente['backlog_pda'] += $dati['backlog_pda'] ?? 0;
                                $totaleCliente['backlog_partner_pda'] += $dati['backlog_partner_pda'] ?? 0;
                                $totaleCliente['ore'] += $dati['ore'] ?? 0;
                                $totaleCliente['ore_paf'] += $dati['ore_paf'] ?? 0;
                                $totaleCliente['pezzi_paf'] += $dati['pezzi_paf'] ?? 0;
                                $totaleCliente['obiettivo_mensile'] += $dati['obiettivo_mensile'] ?? 0;
                            }
                            
                            // Calcoli resa
                            $totaleCliente['resa_prodotto'] = $totaleCliente['ore'] > 0 ? round($totaleCliente['prodotto_pda'] / $totaleCliente['ore'], 2) : 0;
                            $totaleCliente['resa_inserito'] = $totaleCliente['ore'] > 0 ? round($totaleCliente['inserito_pda'] / $totaleCliente['ore'], 2) : 0;
                            $totaleCliente['resa_paf'] = $totaleCliente['ore_paf'] > 0 ? round($totaleCliente['pezzi_paf'] / $totaleCliente['ore_paf'], 2) : 0;
                            
                            // Calcoli obiettivi
                            $diffObjCliente = $totaleCliente['obiettivo_mensile'] - $totaleCliente['inserito_pda'];
                            
                            // Passo giorno: solo se ci sono giorni rimanenti E differenza positiva
                            $passoGiornoCliente = 0;
                            if (isset($kpiArray['giorni_lavorativi_rimanenti']) && $kpiArray['giorni_lavorativi_rimanenti'] > 0 && $diffObjCliente > 0) {
                                $passoGiornoCliente = round($diffObjCliente / $kpiArray['giorni_lavorativi_rimanenti'], 2);
                            }
                        @endphp
                        <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                            <td colspan="2" class="sticky-totale-commessa text-left text-sm font-bold py-2 px-4 border-r-2 border-slate-300">TOTALE {{ $cliente }}</td>
                            <td class="text-center text-sm bg-orange-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['prodotto_pda']) }}</td>
                            <td class="text-center text-sm bg-green-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['inserito_pda']) }}</td>
                            <td class="text-center text-sm bg-red-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ko_pda']) }}</td>
                            <td class="text-center text-sm bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_pda']) }}</td>
                            <td class="text-center text-sm bg-blue-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_partner_pda']) }}</td>
                            <td class="text-center text-sm bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ore'], 2) }}</td>
                            <td class="text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_prodotto'] }}</td>
                            <td class="text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_inserito'] }}</td>
                            
                            {{-- Obiettivi --}}
                            <td class="text-center text-xs bg-teal-100 border-r border-slate-200">{{ number_format($totaleCliente['obiettivo_mensile'], 0) }}</td>
                            <td class="text-center text-xs bg-teal-100 border-r border-slate-200">{{ $passoGiornoCliente }}</td>
                            <td class="text-center text-xs bg-teal-100 border-r-2 border-slate-300 {{ $diffObjCliente < 0 ? 'text-green-700 font-bold' : 'text-red-700' }}">
                                {{ number_format($diffObjCliente, 0) }}
                            </td>
                            
                            {{-- PAF Mensile --}}
                            <td class="text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['ore_paf'], 2) }}</td>
                            <td class="text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['pezzi_paf'], 0) }}</td>
                            <td class="text-center text-xs bg-purple-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_paf'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center py-12">
                                <div>
                                    <h3 class="text-lg font-semibold text-base-content mb-1">Nessun dato disponibile</h3>
                                    <p class="text-sm text-base-content/60">Prova a modificare i filtri per visualizzare i dati</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    
                    {{-- RIGA TOTALE --}}
                    @if(!$datiSintetici->isEmpty())
                        @php
                            $totali = [
                                'prodotto_pda' => 0,
                                'inserito_pda' => 0,
                                'ko_pda' => 0,
                                'backlog_pda' => 0,
                                'backlog_partner_pda' => 0,
                                'ore' => 0,
                                'resa_prodotto' => 0,
                                'resa_inserito' => 0,
                                'ore_paf' => 0,
                                'pezzi_paf' => 0,
                                'resa_paf' => 0,
                            ];
                            
                            foreach($datiSintetici as $sediData) {
                                foreach($sediData as $datiSede) {
                                    $dati = $datiSede['totale'];
                                    $totali['prodotto_pda'] += $dati['prodotto_pda'] ?? 0;
                                    $totali['inserito_pda'] += $dati['inserito_pda'] ?? 0;
                                    $totali['ko_pda'] += $dati['ko_pda'] ?? 0;
                                    $totali['backlog_pda'] += $dati['backlog_pda'] ?? 0;
                                    $totali['backlog_partner_pda'] += $dati['backlog_partner_pda'] ?? 0;
                                    $totali['ore'] += $dati['ore'] ?? 0;
                                    $totali['ore_paf'] += $dati['ore_paf'] ?? 0;
                                    $totali['pezzi_paf'] += $dati['pezzi_paf'] ?? 0;
                                }
                            }
                            
                            // Calcoli resa
                            $totali['resa_prodotto'] = $totali['ore'] > 0 ? round($totali['prodotto_pda'] / $totali['ore'], 2) : 0;
                            $totali['resa_inserito'] = $totali['ore'] > 0 ? round($totali['inserito_pda'] / $totali['ore'], 2) : 0;
                            $totali['resa_paf'] = $totali['ore_paf'] > 0 ? round($totali['pezzi_paf'] / $totali['ore_paf'], 2) : 0;
                        @endphp
                        <tr class="bg-slate-200 font-bold border-t-4 border-slate-400">
                            <td colspan="2" class="sticky-totale-commessa text-left text-base font-bold py-3 px-4 border-r-2 border-slate-300">TOTALE</td>
                            <td class="text-center text-base bg-orange-100 border-r-2 border-slate-300">{{ number_format($totali['prodotto_pda']) }}</td>
                            <td class="text-center text-base bg-green-100 border-r-2 border-slate-300">{{ number_format($totali['inserito_pda']) }}</td>
                            <td class="text-center text-base bg-red-100 border-r-2 border-slate-300">{{ number_format($totali['ko_pda']) }}</td>
                            <td class="text-center text-base bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_pda']) }}</td>
                            <td class="text-center text-base bg-blue-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_partner_pda']) }}</td>
                            <td class="text-center text-base bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totali['ore'], 2) }}</td>
                            <td class="text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_prodotto'] }}</td>
                            <td class="text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_inserito'] }}</td>
                            
                            {{-- Obiettivi (al momento a 0) --}}
                            <td class="text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="text-center text-sm bg-teal-100 border-r-2 border-slate-300">0</td>
                            
                            {{-- PAF Mensile --}}
                            <td class="text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['ore_paf'], 2) }}</td>
                            <td class="text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['pezzi_paf'], 0) }}</td>
                            <td class="text-center text-sm bg-purple-100 border-r-2 border-slate-300">{{ $totali['resa_paf'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Hint scroll mobile --}}
        <div class="p-2 bg-base-200/30 border-t border-base-300 text-center lg:hidden">
            <span class="text-xs text-base-content/50">
                ← Scorri orizzontalmente per vedere tutte le colonne →
            </span>
        </div>

        {{-- Info periodo --}}
        <div class="p-4 bg-base-200/50 border-t border-base-300">
            <div class="flex items-center justify-between text-sm text-base-content/70">
                <div>
                    @if($dataInizio && $dataFine)
                        <span>Periodo: <strong>{{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }}</strong> - <strong>{{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}</strong></span>
                    @else
                        <span>Periodo: <strong>Tutto</strong></span>
                    @endif
                </div>
                <div>
                    <span>Totale vendite: <strong>{{ number_format($kpiTotali['prodotto_pda']) }}</strong></span>
                </div>
            </div>
        </div>

    </x-admin.card>
    
    {{-- STILI CSS PER COLONNE STICKY NELLA TABELLA SINTETICA --}}
    <style>
        /* Container scroll con ombra sulle colonne sticky */
        .table-scroll-container {
            position: relative;
            width: 100%;
        }

        /* Tabella sintetica con layout fisso */
        #table-sintetico table {
            table-layout: auto !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        /* ===== TABELLA SINTETICA - 2 COLONNE STICKY ===== */

        /* Colonna 1: Commessa - sticky */
        .sticky-col-cliente {
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            background-color: white !important;
            width: 200px !important;
            min-width: 200px !important;
            max-width: 200px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Colonna 2: Sede - sticky */
        .sticky-col-sede {
            position: sticky !important;
            left: 200px !important;
            z-index: 3 !important;
            background-color: white !important;
            width: 250px !important;
            min-width: 250px !important;
            max-width: 250px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Header sticky con z-index maggiore */
        #table-sintetico thead th.sticky-col-cliente,
        #table-sintetico thead th.sticky-col-sede {
            z-index: 5 !important;
            background-color: #f3f4f6 !important;
        }

        /* Totale commessa sticky (2 colspan) */
        .sticky-totale-commessa {
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            width: 450px !important;
            min-width: 450px !important;
            max-width: 450px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Mantieni background su righe alternate per colonne sticky */
        #table-sintetico tbody tr:nth-child(odd) td.sticky-col-cliente,
        #table-sintetico tbody tr:nth-child(odd) td.sticky-col-sede {
            background-color: white !important;
        }

        #table-sintetico tbody tr:nth-child(even) td.sticky-col-cliente,
        #table-sintetico tbody tr:nth-child(even) td.sticky-col-sede {
            background-color: #f9fafb !important;
        }

        /* Background per righe totale */
        #table-sintetico tbody tr.bg-slate-100 td.sticky-col-cliente,
        #table-sintetico tbody tr.bg-slate-100 td.sticky-col-sede,
        #table-sintetico tbody tr.bg-slate-100 td.sticky-totale-commessa {
            background-color: #f1f5f9 !important;
        }

        #table-sintetico tbody tr.bg-slate-200 td.sticky-totale-commessa {
            background-color: #e2e8f0 !important;
        }

        /* Hover sulle righe */
        #table-sintetico tbody tr:hover td.sticky-col-cliente,
        #table-sintetico tbody tr:hover td.sticky-col-sede {
            background-color: #f0f9ff !important;
        }

        /* Padding consistente */
        .sticky-col-cliente,
        .sticky-col-sede {
            padding: 12px 16px !important;
        }

        /* ===== TABELLA DETTAGLIATA - 3 COLONNE STICKY ===== */

        /* Tabella dettagliata con layout fisso */
        #table-dettagliato table {
            table-layout: auto !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        /* Colonna 1: Commessa - sticky (dettagliato) */
        .sticky-det-cliente {
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            background-color: white !important;
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Colonna 2: Sede - sticky (dettagliato) */
        .sticky-det-sede {
            position: sticky !important;
            left: 150px !important;
            z-index: 3 !important;
            background-color: white !important;
            width: 180px !important;
            min-width: 180px !important;
            max-width: 180px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Colonna 3: Macro Campagna - sticky (dettagliato) */
        .sticky-det-campagna {
            position: sticky !important;
            left: 330px !important;
            z-index: 3 !important;
            background-color: white !important;
            width: 200px !important;
            min-width: 200px !important;
            max-width: 200px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Header sticky con z-index maggiore (dettagliato) */
        #table-dettagliato thead th.sticky-det-cliente,
        #table-dettagliato thead th.sticky-det-sede,
        #table-dettagliato thead th.sticky-det-campagna {
            z-index: 5 !important;
            background-color: #f3f4f6 !important;
        }

        /* Totale dettagliato sticky (3 colspan) */
        .sticky-det-totale {
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            width: 530px !important;
            min-width: 530px !important;
            max-width: 530px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Mantieni background su righe alternate per colonne sticky (dettagliato) */
        #table-dettagliato tbody tr:nth-child(odd) td.sticky-det-cliente,
        #table-dettagliato tbody tr:nth-child(odd) td.sticky-det-sede,
        #table-dettagliato tbody tr:nth-child(odd) td.sticky-det-campagna {
            background-color: white !important;
        }

        #table-dettagliato tbody tr:nth-child(even) td.sticky-det-cliente,
        #table-dettagliato tbody tr:nth-child(even) td.sticky-det-sede,
        #table-dettagliato tbody tr:nth-child(even) td.sticky-det-campagna {
            background-color: #f9fafb !important;
        }

        /* Background per righe totale (dettagliato) */
        #table-dettagliato tbody tr.bg-slate-100 td.sticky-det-cliente,
        #table-dettagliato tbody tr.bg-slate-100 td.sticky-det-sede,
        #table-dettagliato tbody tr.bg-slate-100 td.sticky-det-campagna,
        #table-dettagliato tbody tr.bg-slate-100 td.sticky-det-totale {
            background-color: #f1f5f9 !important;
        }

        #table-dettagliato tbody tr.bg-slate-200 td.sticky-det-totale {
            background-color: #e2e8f0 !important;
        }

        /* Hover sulle righe (dettagliato) */
        #table-dettagliato tbody tr:hover td.sticky-det-cliente,
        #table-dettagliato tbody tr:hover td.sticky-det-sede,
        #table-dettagliato tbody tr:hover td.sticky-det-campagna {
            background-color: #f0f9ff !important;
        }

        /* Padding consistente (dettagliato) */
        .sticky-det-cliente,
        .sticky-det-sede,
        .sticky-det-campagna {
            padding: 12px 16px !important;
        }

        /* ===== MIGLIORAMENTI VISIVI PER DISTINGUERE LE RIGHE ===== */

        /* ===== STILI PER HEADER (TH) ===== */

        /* Bordi inferiori per le celle TH (solo orizzontali) */
        #table-sintetico thead th,
        #table-dettagliato thead th {
            border-bottom: 3px solid #94a3b8 !important;
            vertical-align: middle !important;
            padding: 14px 12px !important;
            font-weight: 700 !important;
        }

        /* Bordo inferiore più marcato tra le due righe di header */
        #table-sintetico thead tr:first-child th,
        #table-dettagliato thead tr:first-child th {
            border-bottom: 2px solid #94a3b8 !important;
        }

        #table-sintetico thead tr:last-child th,
        #table-dettagliato thead tr:last-child th {
            border-bottom: 3px solid #64748b !important;
        }

        /* ===== STILI PER BODY (TD) ===== */

        /* Bordi orizzontali marcati tra le righe */
        #table-sintetico tbody tr,
        #table-dettagliato tbody tr {
            border-bottom: 2px solid #d1d5db !important;
        }

        /* Bordi orizzontali per tutte le celle (solo orizzontali) */
        #table-sintetico tbody tr td,
        #table-dettagliato tbody tr td {
            border-bottom: 1px solid #d1d5db !important;
            vertical-align: middle !important;
            padding: 14px 12px !important;
        }

        /* Contrasto maggiore per righe alternate */
        #table-sintetico tbody tr:nth-child(odd),
        #table-dettagliato tbody tr:nth-child(odd) {
            background-color: #ffffff !important;
        }

        #table-sintetico tbody tr:nth-child(even),
        #table-dettagliato tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }

        /* Righe totale con bordi più evidenti */
        #table-sintetico tbody tr.bg-slate-100,
        #table-dettagliato tbody tr.bg-slate-100 {
            border-top: 3px solid #94a3b8 !important;
            border-bottom: 3px solid #94a3b8 !important;
            background-color: #f1f5f9 !important;
        }

        #table-sintetico tbody tr.bg-slate-200,
        #table-dettagliato tbody tr.bg-slate-200 {
            border-top: 4px solid #64748b !important;
            border-bottom: 4px solid #64748b !important;
            background-color: #e2e8f0 !important;
        }

        /* Hover su righe totale */
        #table-sintetico tbody tr.bg-slate-100:hover,
        #table-dettagliato tbody tr.bg-slate-100:hover {
            background-color: #e0e7ef !important;
        }

        #table-sintetico tbody tr.bg-slate-200:hover,
        #table-dettagliato tbody tr.bg-slate-200:hover {
            background-color: #cbd5e1 !important;
        }

        /* Alternanza colori preservata su colonne sticky */
        #table-sintetico tbody tr:nth-child(odd) td.sticky-col-cliente,
        #table-sintetico tbody tr:nth-child(odd) td.sticky-col-sede,
        #table-dettagliato tbody tr:nth-child(odd) td.sticky-det-cliente,
        #table-dettagliato tbody tr:nth-child(odd) td.sticky-det-sede,
        #table-dettagliato tbody tr:nth-child(odd) td.sticky-det-campagna {
            background-color: #ffffff !important;
        }

        #table-sintetico tbody tr:nth-child(even) td.sticky-col-cliente,
        #table-sintetico tbody tr:nth-child(even) td.sticky-col-sede,
        #table-dettagliato tbody tr:nth-child(even) td.sticky-det-cliente,
        #table-dettagliato tbody tr:nth-child(even) td.sticky-det-sede,
        #table-dettagliato tbody tr:nth-child(even) td.sticky-det-campagna {
            background-color: #f8fafc !important;
        }
    </style>

    {{-- Script per switchare tra visualizzazioni --}}
    <script>
        function switchView(view) {
            const tableDettagliato = document.getElementById('table-dettagliato');
            const tableSintetico = document.getElementById('table-sintetico');
            const btnDettagliato = document.getElementById('btn-dettagliato');
            const btnSintetico = document.getElementById('btn-sintetico');
            
            if (view === 'sintetico') {
                tableDettagliato.classList.add('hidden');
                tableSintetico.classList.remove('hidden');
                btnDettagliato.classList.remove('btn-primary');
                btnDettagliato.classList.add('btn-outline', 'btn-primary');
                btnSintetico.classList.remove('btn-outline');
                btnSintetico.classList.add('btn-primary');
            } else {
                tableSintetico.classList.add('hidden');
                tableDettagliato.classList.remove('hidden');
                btnSintetico.classList.remove('btn-primary');
                btnSintetico.classList.add('btn-outline', 'btn-primary');
                btnDettagliato.classList.remove('btn-outline');
                btnDettagliato.classList.add('btn-primary');
            }
        }
    </script>
    
    @else
        {{-- Messaggio iniziale --}}
        <x-admin.card tone="light" shadow="lg">
            <div class="text-center py-16">
                <h2 class="text-2xl font-bold text-base-content mb-4">Applica i filtri per visualizzare i dati</h2>
                <p class="text-base-content/70 text-lg">
                    Seleziona uno o più filtri qui sopra e premi "Applica Filtri" per vedere le metriche di produzione
                </p>
            </div>
        </x-admin.card>
    @endif

    {{-- Script per filtri dinamici a cascata --}}
    <script>
        // Dati per i filtri dinamici
        const filtersData = @json([
            'sedi' => $sediPerCommessa ?? [],
            'campagne' => $campagnePerSede ?? []
        ]);

        // Gestione select Commessa
        document.getElementById('commessaSelect').addEventListener('change', function() {
            const commessa = this.value;
            const sedeSelect = document.getElementById('sedeSelect');
            const campagnaSelect = document.getElementById('macroCampagnaSelect');
            
            // Reset e disabilita select successive
            sedeSelect.innerHTML = '<option value="">-- Tutte le sedi --</option>';
            campagnaSelect.innerHTML = '<option value="">-- Tutte le campagne --</option>';
            campagnaSelect.disabled = true;
            
            if (commessa) {
                // Abilita sede select
                sedeSelect.disabled = false;
                
                // Carica sedi per la commessa selezionata via AJAX
                fetch(`/admin/produzione/get-sedi?commessa=${encodeURIComponent(commessa)}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(sede => {
                            const option = document.createElement('option');
                            option.value = sede;
                            option.textContent = sede;
                            sedeSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Errore caricamento sedi:', error));
            } else {
                sedeSelect.disabled = true;
            }
        });

        // Gestione select Sede
        document.getElementById('sedeSelect').addEventListener('change', function() {
            const commessa = document.getElementById('commessaSelect').value;
            const sede = this.value;
            const campagnaSelect = document.getElementById('macroCampagnaSelect');
            
            // Reset campagna select
            campagnaSelect.innerHTML = '<option value="">-- Tutte le campagne --</option>';
            
            if (sede && commessa) {
                // Abilita campagna select
                campagnaSelect.disabled = false;
                
                // Carica campagne per commessa + sede via AJAX
                fetch(`/admin/produzione/get-campagne?commessa=${encodeURIComponent(commessa)}&sede=${encodeURIComponent(sede)}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(campagna => {
                            const option = document.createElement('option');
                            option.value = campagna;
                            option.textContent = campagna;
                            campagnaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Errore caricamento campagne:', error));
            } else {
                campagnaSelect.disabled = true;
            }
        });
    </script>
</x-admin.wrapper>
