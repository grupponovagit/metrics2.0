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
            
            {{-- RIGA 1: Data Inizio, Data Fine, Commessa --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                {{-- Data Inizio --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-base">
                            <x-ui.icon name="calendar" class="h-5 w-5 inline" />
                            Data Inizio
                        </span>
                    </label>
                    <input type="date" name="data_inizio" value="{{ $dataInizio }}" class="input input-bordered input-md w-full text-base" required>
                </div>
                
                {{-- Data Fine --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-base">
                            <x-ui.icon name="calendar" class="h-5 w-5 inline" />
                            Data Fine
                        </span>
                    </label>
                    <input type="date" name="data_fine" value="{{ $dataFine }}" class="input input-bordered input-md w-full text-base" required>
                </div>
                
                {{-- Commessa --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-base">
                            <x-ui.icon name="briefcase" class="h-5 w-5 inline text-warning" />
                            Commessa
                        </span>
                    </label>
                    <select name="commessa" id="commessaSelect" class="select select-bordered select-warning select-md w-full text-base" required>
                        <option value="">Seleziona commessa</option>
                        @foreach($commesse as $commessa)
                            <option value="{{ $commessa }}" {{ $commessaFilter == $commessa ? 'selected' : '' }}>
                                {{ $commessa }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- RIGA 2: Campagne, Sedi, Pulsanti --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Macro Campagna --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="bullseye" class="h-4 w-4 inline text-success" />
                            Campagne <span class="text-[10px] opacity-60">(Shift+Click)</span>
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllCampagne(true)" class="btn btn-xs btn-success gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllCampagne(false)" class="btn btn-xs btn-outline btn-success gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div id="campagnaContainer" class="border border-base-300 rounded-lg p-2.5 h-[180px] overflow-y-auto bg-base-100">
                        <p class="text-xs text-base-content/50 text-center py-4">Seleziona una commessa</p>
                    </div>
                </div>
                
                {{-- Sede --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="building" class="h-4 w-4 inline text-info" />
                            Sedi <span class="text-[10px] opacity-60">(Shift+Click)</span>
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllSedi(true)" class="btn btn-xs btn-info gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllSedi(false)" class="btn btn-xs btn-outline btn-info gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div id="sedeContainer" class="border border-base-300 rounded-lg p-2.5 h-[180px] overflow-y-auto bg-base-100">
                        <p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>
                    </div>
                </div>
                
                {{-- Pulsanti Azione --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm opacity-0">Azioni</span>
                    </label>
                    <div class="space-y-2.5 pt-1">
                        <button type="submit" class="btn btn-primary btn-md w-full gap-2">
                            <x-ui.icon name="search" class="h-5 w-5" />
                            <span class="text-base">Applica Filtri</span>
                        </button>
                        <a href="{{ route('admin.produzione.cruscotto_produzione') }}" class="btn btn-outline btn-md w-full gap-2">
                            <x-ui.icon name="times" class="h-5 w-5" />
                            <span class="text-base">Reset Filtri</span>
                        </a>
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
            
            {{-- Pulsanti Sintetico/Dettagliato/Giornaliero --}}
            <div class="flex gap-2 items-center">
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
                <button 
                    onclick="switchView('giornaliero')" 
                    id="btn-giornaliero"
                    class="btn btn-sm btn-outline btn-primary"
                >
                    Giornaliero
                </button>
                
                {{-- Separatore --}}
                <div class="divider divider-horizontal mx-2"></div>
                
                {{-- Dropdown per gestire colonne visibili --}}
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-sm btn-outline gap-2">
                        <x-ui.icon name="cog" class="h-4 w-4" />
                        Colonne
                    </label>
                    <div tabindex="0" class="dropdown-content z-[999] menu p-3 shadow-lg bg-base-100 rounded-box w-72 mt-2 border border-base-300">
                        <div class="mb-2 px-2">
                            <p class="font-semibold text-sm mb-2">Colonne Visibili</p>
                            <p class="text-xs text-base-content/60 mb-3">Seleziona le colonne da visualizzare</p>
                        </div>
                        
                        {{-- Checkbox per ogni colonna --}}
                        <div class="space-y-1 max-h-96 overflow-y-auto">
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="prodotto" checked>
                                <span class="text-sm">Prodotto</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="inserito" checked>
                                <span class="text-sm">Inserito</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="ko" checked>
                                <span class="text-sm">KO</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="backlog" checked>
                                <span class="text-sm">BackLog</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="backlog_partner" checked>
                                <span class="text-sm">BackLog Partner</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="ore" checked>
                                <span class="text-sm">Ore</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="resa_prodotto" checked>
                                <span class="text-sm">Resa Prodotto</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="resa_inserito" checked>
                                <span class="text-sm">Resa Inserito</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="resa_oraria" checked>
                                <span class="text-sm">R/H</span>
                            </label>
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" class="checkbox checkbox-xs column-toggle" data-column="paf" checked>
                                <span class="text-sm">PAF</span>
                            </label>
                        </div>
                        
                        <div class="divider my-2"></div>
                        
                        {{-- Pulsanti rapidi --}}
                        <div class="flex gap-2 px-2">
                            <button onclick="toggleAllColumns(true)" class="btn btn-xs btn-success flex-1">
                                Tutte
                            </button>
                            <button onclick="toggleAllColumns(false)" class="btn btn-xs btn-outline btn-success flex-1">
                                Nessuna
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- TABELLA DETTAGLIATA --}}
        <div id="table-dettagliato" class="table-scroll-container max-h-[70vh] hidden" style="overflow-x: auto !important; overflow-y: auto !important;">
            <table class="table table-zebra w-full" style="min-width: 100%; table-layout: auto;">
                <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
                    <tr>
                        <th class="sticky-det-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Commessa</th>
                        <th class="sticky-det-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        <th class="sticky-det-campagna font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Macro Campagna</th>
                        
                        {{-- Prodotto --}}
                        <th class="col-prodotto font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Prodotto</th>
                        
                        {{-- Inserito --}}
                        <th class="col-inserito font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="col-ko font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="col-backlog font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="col-backlog_partner font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" rowspan="2" style="min-width: 120px; width: auto;">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="col-ore font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">Ore</th>
                        
                        {{-- RESA --}}
                        <th class="col-resa_prodotto font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Resa Prod.</th>
                        <th class="col-resa_inserito font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Resa Ins.</th>
                        <th class="col-resa_oraria font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">R/H</th>
                        
                        {{-- OBIETTIVI (3 sottocolonne) --}}
                        <th class="col-paf font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300" colspan="3" style="min-width: 240px;">Obiettivi</th>
                        
                        {{-- PAF MENSILE (3 sottocolonne) --}}
                        <th class="col-paf font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300" colspan="3" style="min-width: 240px;">Paf Mensile</th>
                    </tr>
                    <tr>
                        {{-- Sottocolonne Obiettivi --}}
                        <th class="col-paf font-bold text-xs text-center bg-teal-50 border-r border-base-200" style="min-width: 80px; width: auto;">Mensile</th>
                        <th class="col-paf font-bold text-xs text-center bg-teal-50 border-r border-base-200" style="min-width: 80px; width: auto;">Passo Giorno</th>
                        <th class="col-paf font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300" style="min-width: 80px; width: auto;">Diff. Obj</th>
                        
                        {{-- Sottocolonne PAF --}}
                        <th class="col-paf font-bold text-xs text-center bg-purple-50 border-r border-base-200" style="min-width: 80px; width: auto;">Ore Paf</th>
                        <th class="col-paf font-bold text-xs text-center bg-purple-50 border-r border-base-200" style="min-width: 80px; width: auto;">Pezzi Paf</th>
                        <th class="col-paf font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300" style="min-width: 80px; width: auto;">Resa Paf</th>
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
                                    <td class="col-prodotto text-center text-sm bg-orange-50 border-r-2 border-base-300">{{ number_format($datiCampagna['prodotto_pda']) }}</td>
                                    
                                    {{-- Inserito --}}
                                    <td class="col-inserito text-center text-sm bg-green-50 border-r-2 border-base-300">{{ number_format($datiCampagna['inserito_pda']) }}</td>
                                    
                                    {{-- KO --}}
                                    <td class="col-ko text-center text-sm bg-red-50 border-r-2 border-base-300">{{ number_format($datiCampagna['ko_pda']) }}</td>
                                    
                                    {{-- BackLog --}}
                                    <td class="col-backlog text-center text-sm bg-yellow-50 border-r-2 border-base-300">{{ number_format($datiCampagna['backlog_pda']) }}</td>
                                    
                                    {{-- BackLog Partner --}}
                                    <td class="col-backlog_partner text-center text-sm bg-blue-50 border-r-2 border-base-300">{{ number_format($datiCampagna['backlog_partner_pda']) }}</td>
                                    
                                    {{-- Ore --}}
                                    <td class="col-ore text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">
                                        {{ ($datiCampagna['ore'] ?? 0) > 0 ? number_format($datiCampagna['ore'], 2) : '-' }}
                                    </td>
                                    
                                    {{-- Resa Prodotto --}}
                                    <td class="col-resa_prodotto text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                        {{ $datiCampagna['resa_prodotto'] ?? '-' }}
                                    </td>
                                    
                                    {{-- Resa Inserito --}}
                                    <td class="col-resa_inserito text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                        {{ $datiCampagna['resa_inserito'] ?? '-' }}
                                    </td>
                                    
                                    {{-- R/H --}}
                                    <td class="col-resa_oraria text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                        {{ $datiCampagna['resa_oraria'] ?? '-' }}
                                    </td>
                                    
                                    {{-- OBIETTIVI --}}
                                    <td class="col-paf text-center text-xs bg-teal-50 border-r border-base-200">{{ $datiCampagna['obiettivo_mensile'] ?? 0 }}</td>
                                    <td class="col-paf text-center text-xs bg-teal-50 border-r border-base-200">{{ $datiCampagna['passo_giorno'] ?? 0 }}</td>
                                    <td class="col-paf text-center text-xs bg-teal-50 border-r-2 border-base-300 {{ ($datiCampagna['differenza_obj'] ?? 0) < 0 ? 'text-green-600 font-bold' : 'text-red-600' }}">
                                        {{ $datiCampagna['differenza_obj'] ?? 0 }}
                                    </td>
                                    
                                    {{-- PAF MENSILE --}}
                                    <td class="col-paf text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($datiCampagna['ore_paf'] ?? 0, 2) }}</td>
                                    <td class="col-paf text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($datiCampagna['pezzi_paf'] ?? 0, 0) }}</td>
                                    <td class="col-paf text-center text-xs bg-purple-50 border-r-2 border-base-300">{{ $datiCampagna['resa_paf'] ?? 0 }}</td>
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
                            <td class="col-prodotto text-center text-sm bg-orange-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['prodotto_pda']) }}</td>
                            <td class="col-inserito text-center text-sm bg-green-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['inserito_pda']) }}</td>
                            <td class="col-ko text-center text-sm bg-red-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ko_pda']) }}</td>
                            <td class="col-backlog text-center text-sm bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_pda']) }}</td>
                            <td class="col-backlog_partner text-center text-sm bg-blue-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_partner_pda']) }}</td>
                            <td class="col-ore text-center text-sm bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ore'], 2) }}</td>
                            <td class="col-resa_prodotto text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_prodotto'] }}</td>
                            <td class="col-resa_inserito text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_inserito'] }}</td>
                            <td class="col-resa_oraria text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_oraria'] ?? 0 }}</td>
                            
                            {{-- Obiettivi --}}
                            <td class="col-paf text-center text-xs bg-teal-100 border-r border-slate-200">{{ number_format($totaleCliente['obiettivo_mensile'], 0) }}</td>
                            <td class="col-paf text-center text-xs bg-teal-100 border-r border-slate-200">{{ $passoGiornoCliente }}</td>
                            <td class="col-paf text-center text-xs bg-teal-100 border-r-2 border-slate-300 {{ $diffObjCliente < 0 ? 'text-green-700 font-bold' : 'text-red-700' }}">
                                {{ number_format($diffObjCliente, 0) }}
                            </td>
                            
                            {{-- PAF Mensile --}}
                            <td class="col-paf text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['ore_paf'], 2) }}</td>
                            <td class="col-paf text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['pezzi_paf'], 0) }}</td>
                            <td class="col-paf text-center text-xs bg-purple-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_paf'] }}</td>
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
                            <td class="col-prodotto text-center text-base bg-orange-100 border-r-2 border-slate-300">{{ number_format($totali['prodotto_pda']) }}</td>
                            <td class="col-inserito text-center text-base bg-green-100 border-r-2 border-slate-300">{{ number_format($totali['inserito_pda']) }}</td>
                            <td class="col-ko text-center text-base bg-red-100 border-r-2 border-slate-300">{{ number_format($totali['ko_pda']) }}</td>
                            <td class="col-backlog text-center text-base bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_pda']) }}</td>
                            <td class="col-backlog_partner text-center text-base bg-blue-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_partner_pda']) }}</td>
                            <td class="col-ore text-center text-base bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totali['ore'], 2) }}</td>
                            <td class="col-resa_prodotto text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_prodotto'] }}</td>
                            <td class="col-resa_inserito text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_inserito'] }}</td>
                            <td class="col-resa_oraria text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_oraria'] ?? 0 }}</td>
                            
                            {{-- Obiettivi (al momento a 0) --}}
                            <td class="col-paf text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="col-paf text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="col-paf text-center text-sm bg-teal-100 border-r-2 border-slate-300">0</td>
                            
                            {{-- PAF Mensile --}}
                            <td class="col-paf text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['ore_paf'], 2) }}</td>
                            <td class="col-paf text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['pezzi_paf'], 0) }}</td>
                            <td class="col-paf text-center text-sm bg-purple-100 border-r-2 border-slate-300">{{ $totali['resa_paf'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- TABELLA SINTETICA --}}
        <div id="table-sintetico" class="table-scroll-container max-h-[70vh] w-full" style="overflow-x: auto !important; overflow-y: auto !important;">
            <table class="table table-zebra w-full" style="min-width: 100%; table-layout: auto;">
                <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
                    <tr>
                        <th class="sticky-col-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Commessa</th>
                        <th class="sticky-col-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        
                        {{-- Prodotto --}}
                        <th class="col-prodotto font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Prodotto</th>
                        
                        {{-- Inserito --}}
                        <th class="col-inserito font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="col-ko font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="col-backlog font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="col-backlog_partner font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" rowspan="2" style="min-width: 120px; width: auto;">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="col-ore font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">Ore</th>
                        
                        {{-- RESA --}}
                        <th class="col-resa_prodotto font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Resa Prod.</th>
                        <th class="col-resa_inserito font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Resa Ins.</th>
                        <th class="col-resa_oraria font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">R/H</th>
                        
                        {{-- OBIETTIVI (3 sottocolonne) --}}
                        <th class="col-paf font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300" colspan="3" style="min-width: 240px;">Obiettivi</th>
                        
                        {{-- PAF MENSILE (3 sottocolonne) --}}
                        <th class="col-paf font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300" colspan="3" style="min-width: 240px;">Paf Mensile</th>
                    </tr>
                    <tr>
                        {{-- Sottocolonne Obiettivi --}}
                        <th class="col-paf font-bold text-xs text-center bg-teal-50 border-r border-base-200" style="min-width: 80px; width: auto;">Mensile</th>
                        <th class="col-paf font-bold text-xs text-center bg-teal-50 border-r border-base-200" style="min-width: 80px; width: auto;">Passo Giorno</th>
                        <th class="col-paf font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300" style="min-width: 80px; width: auto;">Diff. Obj</th>
                        
                        {{-- Sottocolonne PAF --}}
                        <th class="col-paf font-bold text-xs text-center bg-purple-50 border-r border-base-200" style="min-width: 80px; width: auto;">Ore Paf</th>
                        <th class="col-paf font-bold text-xs text-center bg-purple-50 border-r border-base-200" style="min-width: 80px; width: auto;">Pezzi Paf</th>
                        <th class="col-paf font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300" style="min-width: 80px; width: auto;">Resa Paf</th>
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
                                <td class="col-prodotto text-center text-sm bg-orange-50 border-r-2 border-base-300">{{ number_format($dati['prodotto_pda']) }}</td>
                                
                                {{-- Inserito --}}
                                <td class="col-inserito text-center text-sm bg-green-50 border-r-2 border-base-300">{{ number_format($dati['inserito_pda']) }}</td>
                                
                                {{-- KO --}}
                                <td class="col-ko text-center text-sm bg-red-50 border-r-2 border-base-300">{{ number_format($dati['ko_pda']) }}</td>
                                
                                {{-- BackLog --}}
                                <td class="col-backlog text-center text-sm bg-yellow-50 border-r-2 border-base-300">{{ number_format($dati['backlog_pda']) }}</td>
                                
                                {{-- BackLog Partner --}}
                                <td class="col-backlog_partner text-center text-sm bg-blue-50 border-r-2 border-base-300">{{ number_format($dati['backlog_partner_pda']) }}</td>
                                
                                {{-- Ore --}}
                                <td class="col-ore text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">
                                    {{ ($dati['ore'] ?? 0) > 0 ? number_format($dati['ore'], 2) : '-' }}
                                </td>
                                
                                {{-- Resa Prodotto --}}
                                <td class="col-resa_prodotto text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                    {{ $dati['resa_prodotto'] ?? '-' }}
                                </td>
                                
                                {{-- Resa Inserito --}}
                                <td class="col-resa_inserito text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                    {{ $dati['resa_inserito'] ?? '-' }}
                                </td>
                                
                                {{-- R/H --}}
                                <td class="col-resa_oraria text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                    {{ $dati['resa_oraria'] ?? '-' }}
                                </td>
                                
                                {{-- OBIETTIVI --}}
                                <td class="col-paf text-center text-xs bg-teal-50 border-r border-base-200">{{ $dati['obiettivo_mensile'] ?? 0 }}</td>
                                <td class="col-paf text-center text-xs bg-teal-50 border-r border-base-200">{{ $dati['passo_giorno'] ?? 0 }}</td>
                                <td class="col-paf text-center text-xs bg-teal-50 border-r-2 border-base-300 {{ ($dati['differenza_obj'] ?? 0) < 0 ? 'text-green-600 font-bold' : 'text-red-600' }}">
                                    {{ $dati['differenza_obj'] ?? 0 }}
                                </td>
                                
                                {{-- PAF MENSILE --}}
                                <td class="col-paf text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($dati['ore_paf'] ?? 0, 2) }}</td>
                                <td class="col-paf text-center text-xs bg-purple-50 border-r border-base-200">{{ number_format($dati['pezzi_paf'] ?? 0, 0) }}</td>
                                <td class="col-paf text-center text-xs bg-purple-50 border-r-2 border-base-300">{{ $dati['resa_paf'] ?? 0 }}</td>
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
                            <td class="col-prodotto text-center text-sm bg-orange-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['prodotto_pda']) }}</td>
                            <td class="col-inserito text-center text-sm bg-green-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['inserito_pda']) }}</td>
                            <td class="col-ko text-center text-sm bg-red-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ko_pda']) }}</td>
                            <td class="col-backlog text-center text-sm bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_pda']) }}</td>
                            <td class="col-backlog_partner text-center text-sm bg-blue-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['backlog_partner_pda']) }}</td>
                            <td class="col-ore text-center text-sm bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totaleCliente['ore'], 2) }}</td>
                            <td class="col-resa_prodotto text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_prodotto'] }}</td>
                            <td class="col-resa_inserito text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_inserito'] }}</td>
                            <td class="col-resa_oraria text-center text-sm bg-indigo-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_oraria'] ?? 0 }}</td>
                            
                            {{-- Obiettivi --}}
                            <td class="col-paf text-center text-xs bg-teal-100 border-r border-slate-200">{{ number_format($totaleCliente['obiettivo_mensile'], 0) }}</td>
                            <td class="col-paf text-center text-xs bg-teal-100 border-r border-slate-200">{{ $passoGiornoCliente }}</td>
                            <td class="col-paf text-center text-xs bg-teal-100 border-r-2 border-slate-300 {{ $diffObjCliente < 0 ? 'text-green-700 font-bold' : 'text-red-700' }}">
                                {{ number_format($diffObjCliente, 0) }}
                            </td>
                            
                            {{-- PAF Mensile --}}
                            <td class="col-paf text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['ore_paf'], 2) }}</td>
                            <td class="col-paf text-center text-xs bg-purple-100 border-r border-slate-200">{{ number_format($totaleCliente['pezzi_paf'], 0) }}</td>
                            <td class="col-paf text-center text-xs bg-purple-100 border-r-2 border-slate-300">{{ $totaleCliente['resa_paf'] }}</td>
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
                            <td class="col-prodotto text-center text-base bg-orange-100 border-r-2 border-slate-300">{{ number_format($totali['prodotto_pda']) }}</td>
                            <td class="col-inserito text-center text-base bg-green-100 border-r-2 border-slate-300">{{ number_format($totali['inserito_pda']) }}</td>
                            <td class="col-ko text-center text-base bg-red-100 border-r-2 border-slate-300">{{ number_format($totali['ko_pda']) }}</td>
                            <td class="col-backlog text-center text-base bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_pda']) }}</td>
                            <td class="col-backlog_partner text-center text-base bg-blue-100 border-r-2 border-slate-300">{{ number_format($totali['backlog_partner_pda']) }}</td>
                            <td class="col-ore text-center text-base bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totali['ore'], 2) }}</td>
                            <td class="col-resa_prodotto text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_prodotto'] }}</td>
                            <td class="col-resa_inserito text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_inserito'] }}</td>
                            <td class="col-resa_oraria text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totali['resa_oraria'] ?? 0 }}</td>
                            
                            {{-- Obiettivi (al momento a 0) --}}
                            <td class="col-paf text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="col-paf text-center text-sm bg-teal-100 border-r border-slate-200">0</td>
                            <td class="col-paf text-center text-sm bg-teal-100 border-r-2 border-slate-300">0</td>
                            
                            {{-- PAF Mensile --}}
                            <td class="col-paf text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['ore_paf'], 2) }}</td>
                            <td class="col-paf text-center text-sm bg-purple-100 border-r border-slate-200">{{ number_format($totali['pezzi_paf'], 0) }}</td>
                            <td class="col-paf text-center text-sm bg-purple-100 border-r-2 border-slate-300">{{ $totali['resa_paf'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- TABELLA GIORNALIERA --}}
        <div id="table-giornaliero" class="table-scroll-container max-h-[70vh] hidden" style="overflow-x: auto !important; overflow-y: auto !important;">
            <table class="table table-zebra w-full" style="min-width: 100%; table-layout: auto;">
                <thead>
                    <tr>
                        {{-- Data --}}
                        <th class="sticky-giorn-data font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2" style="min-width: 100px; width: auto;">Data</th>
                        
                        {{-- Cliente --}}
                        <th class="sticky-giorn-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2" style="min-width: 140px; width: auto;">Commessa</th>
                        
                        {{-- Sede --}}
                        <th class="sticky-giorn-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2" style="min-width: 140px; width: auto;">Sede</th>
                        
                        {{-- Macro Campagna --}}
                        <th class="sticky-giorn-campagna font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2" style="min-width: 160px; width: auto;">Macro Campagna</th>
                        
                        {{-- Prodotto --}}
                        <th class="col-prodotto font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Prodotto</th>
                        
                        {{-- Inserito --}}
                        <th class="col-inserito font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="col-ko font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="col-backlog font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="col-backlog_partner font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" rowspan="2" style="min-width: 120px; width: auto;">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="col-ore font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">Ore</th>
                        
                        {{-- RESA --}}
                        <th class="col-resa_prodotto font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Resa Prod.</th>
                        <th class="col-resa_inserito font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 90px; width: auto;">Resa Ins.</th>
                        <th class="col-resa_oraria font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300" rowspan="2" style="min-width: 70px; width: auto;">R/H</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totaleGiornaliero = [
                            'prodotto_pda' => 0,
                            'inserito_pda' => 0,
                            'ko_pda' => 0,
                            'backlog_pda' => 0,
                            'backlog_partner_pda' => 0,
                            'ore' => 0,
                        ];
                    @endphp
                    
                    @forelse($datiGiornalieri as $data => $clientiData)
                        @foreach($clientiData as $cliente => $sediData)
                            @foreach($sediData as $sede => $campagneData)
                                @foreach($campagneData as $datiGiorno)
                                    @php
                                        $totaleGiornaliero['prodotto_pda'] += $datiGiorno['prodotto_pda'] ?? 0;
                                        $totaleGiornaliero['inserito_pda'] += $datiGiorno['inserito_pda'] ?? 0;
                                        $totaleGiornaliero['ko_pda'] += $datiGiorno['ko_pda'] ?? 0;
                                        $totaleGiornaliero['backlog_pda'] += $datiGiorno['backlog_pda'] ?? 0;
                                        $totaleGiornaliero['backlog_partner_pda'] += $datiGiorno['backlog_partner_pda'] ?? 0;
                                        $totaleGiornaliero['ore'] += $datiGiorno['ore'] ?? 0;
                                    @endphp
                                    
                                    <tr>
                                        {{-- Data --}}
                                        <td class="sticky-giorn-data text-sm font-semibold border-r-2 border-base-300 bg-base-100">
                                            {{ \Carbon\Carbon::parse($datiGiorno['data'])->format('d/m/Y') }}
                                        </td>
                                        
                                        {{-- Cliente --}}
                                        <td class="sticky-giorn-cliente text-sm font-bold border-r-2 border-base-300 bg-base-50">
                                            {{ $datiGiorno['cliente'] }}
                                        </td>
                                        
                                        {{-- Sede --}}
                                        <td class="sticky-giorn-sede text-sm font-semibold border-r-2 border-base-300 bg-base-50">
                                            {{ $datiGiorno['sede'] }}
                                        </td>
                                        
                                        {{-- Campagna --}}
                                        <td class="sticky-giorn-campagna text-sm border-r-2 border-base-300">
                                            {{ $datiGiorno['campagna'] }}
                                        </td>
                                        
                                        {{-- Prodotto --}}
                                        <td class="col-prodotto text-center text-sm bg-orange-50 border-r-2 border-base-300">
                                            {{ number_format($datiGiorno['prodotto_pda']) }}
                                        </td>
                                        
                                        {{-- Inserito --}}
                                        <td class="col-inserito text-center text-sm bg-green-50 border-r-2 border-base-300">
                                            {{ number_format($datiGiorno['inserito_pda']) }}
                                        </td>
                                        
                                        {{-- KO --}}
                                        <td class="col-ko text-center text-sm bg-red-50 border-r-2 border-base-300">
                                            {{ number_format($datiGiorno['ko_pda']) }}
                                        </td>
                                        
                                        {{-- BackLog --}}
                                        <td class="col-backlog text-center text-sm bg-yellow-50 border-r-2 border-base-300">
                                            {{ number_format($datiGiorno['backlog_pda']) }}
                                        </td>
                                        
                                        {{-- BackLog Partner --}}
                                        <td class="col-backlog_partner text-center text-sm bg-blue-50 border-r-2 border-base-300">
                                            {{ number_format($datiGiorno['backlog_partner_pda']) }}
                                        </td>
                                        
                                        {{-- Ore --}}
                                        <td class="col-ore text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">
                                            {{ ($datiGiorno['ore'] ?? 0) > 0 ? number_format($datiGiorno['ore'], 2) : '-' }}
                                        </td>
                                        
                                        {{-- Resa Prodotto --}}
                                        <td class="col-resa_prodotto text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                            {{ $datiGiorno['resa_prodotto'] ?? '-' }}
                                        </td>
                                        
                                        {{-- Resa Inserito --}}
                                        <td class="col-resa_inserito text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                            {{ $datiGiorno['resa_inserito'] ?? '-' }}
                                        </td>
                                        
                                        {{-- R/H --}}
                                        <td class="col-resa_oraria text-center text-sm font-semibold bg-indigo-50 border-r-2 border-base-300">
                                            {{ $datiGiorno['resa_oraria'] ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                        
                        {{-- RIGA TOTALE GENERALE --}}
                        @if(count($datiGiornalieri) > 0)
                            @php
                                // Calcoli resa totali
                                $totaleGiornaliero['resa_prodotto'] = $totaleGiornaliero['ore'] > 0 
                                    ? round($totaleGiornaliero['prodotto_pda'] / $totaleGiornaliero['ore'], 2) 
                                    : 0;
                                $totaleGiornaliero['resa_inserito'] = $totaleGiornaliero['ore'] > 0 
                                    ? round($totaleGiornaliero['inserito_pda'] / $totaleGiornaliero['ore'], 2) 
                                    : 0;
                            @endphp
                            
                            <tr class="bg-slate-100 font-bold border-t-4 border-slate-400">
                                <td colspan="4" class="text-center text-base uppercase tracking-wide py-3 border-r-2 border-slate-300 sticky-giorn-totale">
                                    TOTALE PERIODO
                                </td>
                                
                                {{-- Totali metriche --}}
                                <td class="col-prodotto text-center text-base bg-orange-100 border-r-2 border-slate-300">{{ number_format($totaleGiornaliero['prodotto_pda']) }}</td>
                                <td class="col-inserito text-center text-base bg-green-100 border-r-2 border-slate-300">{{ number_format($totaleGiornaliero['inserito_pda']) }}</td>
                                <td class="col-ko text-center text-base bg-red-100 border-r-2 border-slate-300">{{ number_format($totaleGiornaliero['ko_pda']) }}</td>
                                <td class="col-backlog text-center text-base bg-yellow-100 border-r-2 border-slate-300">{{ number_format($totaleGiornaliero['backlog_pda']) }}</td>
                                <td class="col-backlog_partner text-center text-base bg-blue-100 border-r-2 border-slate-300">{{ number_format($totaleGiornaliero['backlog_partner_pda']) }}</td>
                                <td class="col-ore text-center text-base bg-cyan-100 border-r-2 border-slate-300">{{ number_format($totaleGiornaliero['ore'], 2) }}</td>
                                <td class="col-resa_prodotto text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totaleGiornaliero['resa_prodotto'] }}</td>
                                <td class="col-resa_inserito text-center text-base bg-indigo-100 border-r-2 border-slate-300">{{ $totaleGiornaliero['resa_inserito'] }}</td>
                                <td class="col-resa_oraria text-center text-base bg-indigo-100 border-r-2 border-slate-300">0</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-base-content/50 py-8">
                                Nessun dato disponibile per il periodo selezionato
                            </td>
                        </tr>
                    @endforelse
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
        
        /* ===== STICKY COLUMNS GIORNALIERO ===== */
        
        /* Data (prima colonna) */
        .sticky-giorn-data {
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            width: 120px !important;
            min-width: 120px !important;
            max-width: 120px !important;
            white-space: nowrap !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Cliente/Commessa (seconda colonna) */
        .sticky-giorn-cliente {
            position: sticky !important;
            left: 120px !important;
            z-index: 3 !important;
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Sede (terza colonna) */
        .sticky-giorn-sede {
            position: sticky !important;
            left: 270px !important;
            z-index: 3 !important;
            width: 180px !important;
            min-width: 180px !important;
            max-width: 180px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Macro Campagna (quarta colonna) */
        .sticky-giorn-campagna {
            position: sticky !important;
            left: 450px !important;
            z-index: 3 !important;
            width: 200px !important;
            min-width: 200px !important;
            max-width: 200px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        }

        /* Header sticky con z-index maggiore (giornaliero) */
        #table-giornaliero thead th.sticky-giorn-data,
        #table-giornaliero thead th.sticky-giorn-cliente,
        #table-giornaliero thead th.sticky-giorn-sede,
        #table-giornaliero thead th.sticky-giorn-campagna {
            z-index: 15 !important;
            background-color: #f3f4f6 !important;
        }
        
        /* Tutti gli header della tabella giornaliera devono rimanere fissi quando si scrolla verticalmente */
        #table-giornaliero thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 10 !important;
            background-color: #f3f4f6 !important;
        }
        
        /* Le colonne sticky orizzontali devono avere z-index più alto */
        #table-giornaliero thead th.sticky-giorn-data {
            z-index: 15 !important;
        }
        
        #table-giornaliero thead th.sticky-giorn-cliente {
            z-index: 15 !important;
        }
        
        #table-giornaliero thead th.sticky-giorn-sede {
            z-index: 15 !important;
        }
        
        #table-giornaliero thead th.sticky-giorn-campagna {
            z-index: 15 !important;
        }

        /* Mantieni background su righe alternate per colonne sticky (giornaliero) */
        #table-giornaliero tbody tr:nth-child(odd) td.sticky-giorn-data,
        #table-giornaliero tbody tr:nth-child(odd) td.sticky-giorn-cliente,
        #table-giornaliero tbody tr:nth-child(odd) td.sticky-giorn-sede,
        #table-giornaliero tbody tr:nth-child(odd) td.sticky-giorn-campagna {
            background-color: white !important;
        }

        #table-giornaliero tbody tr:nth-child(even) td.sticky-giorn-data,
        #table-giornaliero tbody tr:nth-child(even) td.sticky-giorn-cliente,
        #table-giornaliero tbody tr:nth-child(even) td.sticky-giorn-sede,
        #table-giornaliero tbody tr:nth-child(even) td.sticky-giorn-campagna {
            background-color: #f9fafb !important;
        }

        /* Background per riga totale (giornaliero) */
        #table-giornaliero tbody tr.bg-slate-100 td.sticky-giorn-data,
        #table-giornaliero tbody tr.bg-slate-100 td.sticky-giorn-cliente,
        #table-giornaliero tbody tr.bg-slate-100 td.sticky-giorn-sede,
        #table-giornaliero tbody tr.bg-slate-100 td.sticky-giorn-campagna {
            background-color: #f1f5f9 !important;
        }

        /* Hover sulle righe (giornaliero) */
        #table-giornaliero tbody tr:hover td.sticky-giorn-data,
        #table-giornaliero tbody tr:hover td.sticky-giorn-cliente,
        #table-giornaliero tbody tr:hover td.sticky-giorn-sede,
        #table-giornaliero tbody tr:hover td.sticky-giorn-campagna {
            background-color: #f0f9ff !important;
        }

        /* Padding consistente (giornaliero) */
        .sticky-giorn-data,
        .sticky-giorn-cliente,
        .sticky-giorn-sede,
        .sticky-giorn-campagna {
            padding: 12px 16px !important;
        }
        
        /* Totale giornaliero sticky (4 colspan) */
        .sticky-giorn-totale {
            position: sticky !important;
            left: 0 !important;
            z-index: 3 !important;
            width: 650px !important;
            min-width: 650px !important;
            max-width: 650px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
            background-color: #f1f5f9 !important;
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
            const tableGiornaliero = document.getElementById('table-giornaliero');
            const btnDettagliato = document.getElementById('btn-dettagliato');
            const btnSintetico = document.getElementById('btn-sintetico');
            const btnGiornaliero = document.getElementById('btn-giornaliero');
            
            // Nascondi tutte le tabelle
            tableDettagliato.classList.add('hidden');
            tableSintetico.classList.add('hidden');
            tableGiornaliero.classList.add('hidden');
            
            // Reset tutti i pulsanti
            btnDettagliato.classList.remove('btn-primary');
            btnDettagliato.classList.add('btn-outline');
            btnSintetico.classList.remove('btn-primary');
            btnSintetico.classList.add('btn-outline');
            btnGiornaliero.classList.remove('btn-primary');
            btnGiornaliero.classList.add('btn-outline');
            
            // Mostra la tabella selezionata e attiva il pulsante corrispondente
            if (view === 'sintetico') {
                tableSintetico.classList.remove('hidden');
                btnSintetico.classList.remove('btn-outline');
                btnSintetico.classList.add('btn-primary');
            } else if (view === 'giornaliero') {
                tableGiornaliero.classList.remove('hidden');
                btnGiornaliero.classList.remove('btn-outline');
                btnGiornaliero.classList.add('btn-primary');
            } else {
                tableDettagliato.classList.remove('hidden');
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

        // Variabili per gestire la selezione con Shift
        let lastCheckedCampagna = null;
        let lastCheckedSede = null;

        // Funzione per gestire shift-click su checkbox o label
        function handleShiftClick(checkboxes, lastChecked, currentCheckbox, event) {
            if (!lastChecked) {
                return currentCheckbox;
            }

            if (event.shiftKey) {
                const start = Array.from(checkboxes).indexOf(lastChecked);
                const end = Array.from(checkboxes).indexOf(currentCheckbox);
                const checkboxesToToggle = Array.from(checkboxes).slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                
                checkboxesToToggle.forEach(checkbox => {
                    checkbox.checked = currentCheckbox.checked;
                });
            }

            return currentCheckbox;
        }
        
        // Funzione per aggiungere event listener a label con checkbox
        function attachLabelClickListeners(containerSelector, checkboxClass, lastCheckedVar) {
            const container = document.querySelector(containerSelector);
            if (!container) return;
            
            const labels = container.querySelectorAll('[data-checkbox-label]');
            labels.forEach(label => {
                const checkbox = label.querySelector(`.${checkboxClass}`);
                if (!checkbox) return;
                
                // Click sulla label (ma non sulla checkbox stessa)
                label.addEventListener('click', function(e) {
                    // Se il click è sulla checkbox, lascia che gestisca lei
                    if (e.target.type === 'checkbox') return;
                    
                    // Altrimenti, previeni il default e gestisci manualmente
                    e.preventDefault();
                    
                    // Toggle checkbox
                    checkbox.checked = !checkbox.checked;
                    
                    // Gestisci shift-click
                    const allCheckboxes = container.querySelectorAll(`.${checkboxClass}`);
                    if (checkboxClass === 'campagna-checkbox') {
                        lastCheckedCampagna = handleShiftClick(allCheckboxes, lastCheckedCampagna, checkbox, e);
                    } else if (checkboxClass === 'sede-checkbox') {
                        lastCheckedSede = handleShiftClick(allCheckboxes, lastCheckedSede, checkbox, e);
                    }
                });
                
                // Click diretto sulla checkbox
                checkbox.addEventListener('click', function(e) {
                    const allCheckboxes = container.querySelectorAll(`.${checkboxClass}`);
                    if (checkboxClass === 'campagna-checkbox') {
                        lastCheckedCampagna = handleShiftClick(allCheckboxes, lastCheckedCampagna, this, e);
                    } else if (checkboxClass === 'sede-checkbox') {
                        lastCheckedSede = handleShiftClick(allCheckboxes, lastCheckedSede, this, e);
                    }
                });
            });
        }
        
        // Funzioni per "Seleziona Tutte" / "Deseleziona Tutte"
        function toggleAllCampagne(selectAll) {
            const checkboxes = document.querySelectorAll('.campagna-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
            // Ricarica sedi dopo aver cambiato le campagne
            loadSedi();
        }
        
        function toggleAllSedi(selectAll) {
            const checkboxes = document.querySelectorAll('.sede-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
        }

        // Funzione per caricare sedi in base a commessa e campagne selezionate
        function loadSedi() {
            const commessa = document.getElementById('commessaSelect').value;
            const selectedCampagne = Array.from(document.querySelectorAll('.campagna-checkbox:checked')).map(cb => cb.value);
            const sedeContainer = document.getElementById('sedeContainer');
            
            console.log('loadSedi chiamato:', { commessa, selectedCampagne });
            
            // Reset dell'ultima checkbox sede selezionata
            lastCheckedSede = null;
            
            if (!commessa || selectedCampagne.length === 0) {
                sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
                return;
            }
            
            // Costruisci URL con parametri
            const params = new URLSearchParams();
            params.append('commessa', commessa);
            selectedCampagne.forEach(c => params.append('campagne[]', c));
            
            console.log('Fetching sedi con URL:', `/admin/produzione/get-sedi?${params.toString()}`);
            
            fetch(`/admin/produzione/get-sedi?${params.toString()}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Sedi ricevute:', data);
                    sedeContainer.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(sede => {
                            const label = document.createElement('label');
                            label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded-md select-sede';
                            label.setAttribute('data-checkbox-label', '');
                            label.innerHTML = `
                                <input type="checkbox" name="sede[]" value="${sede}" 
                                       class="checkbox checkbox-info checkbox-sm sede-checkbox">
                                <span class="text-sm leading-tight">${sede}</span>
                            `;
                            sedeContainer.appendChild(label);
                        });
                        
                        // Aggiungi event listener per shift-click e label click
                        attachLabelClickListeners('#sedeContainer', 'sede-checkbox');
                    } else {
                        sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Nessuna sede disponibile</p>';
                    }
                })
                .catch(error => {
                    console.error('Errore caricamento sedi:', error);
                    sedeContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore caricamento</p>';
                });
        }
        
        // Gestione select Commessa - aggiorna checkbox campagne
        document.getElementById('commessaSelect').addEventListener('change', function() {
            const commessa = this.value;
            const campagnaContainer = document.getElementById('campagnaContainer');
            const sedeContainer = document.getElementById('sedeContainer');
            
            // Reset delle ultime checkbox selezionate
            lastCheckedCampagna = null;
            lastCheckedSede = null;
            
            if (commessa) {
                // Carica campagne per la commessa selezionata via AJAX
                fetch(`/admin/produzione/get-campagne?commessa=${encodeURIComponent(commessa)}`)
                    .then(response => response.json())
                    .then(data => {
                        campagnaContainer.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(campagna => {
                                const label = document.createElement('label');
                                label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded-md select-campagna';
                                label.setAttribute('data-checkbox-label', '');
                                label.innerHTML = `
                                    <input type="checkbox" name="macro_campagna[]" value="${campagna}" 
                                           class="checkbox checkbox-success checkbox-sm campagna-checkbox">
                                    <span class="text-sm leading-tight">${campagna}</span>
                                `;
                                campagnaContainer.appendChild(label);
                            });
                            
                            // Aggiungi event listener per shift-click e label click
                            attachLabelClickListeners('#campagnaContainer', 'campagna-checkbox');
                            
                            // Reset sedi (nessuna campagna selezionata inizialmente)
                            sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
                        } else {
                            campagnaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Nessuna campagna disponibile</p>';
                            sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Errore caricamento campagne:', error);
                        campagnaContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore caricamento</p>';
                    });
            } else {
                // Reset containers
                campagnaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona una commessa</p>';
                sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
            }
        });
        
        // Delega eventi per campagne: quando cambia una checkbox campagna, ricarica le sedi
        document.getElementById('campagnaContainer').addEventListener('click', function(e) {
            // Controlla se è una checkbox o il suo parent label
            const checkbox = e.target.classList.contains('campagna-checkbox') 
                ? e.target 
                : e.target.querySelector('.campagna-checkbox');
            
            if (checkbox || e.target.classList.contains('campagna-checkbox')) {
                console.log('Campagna checkbox cliccata');
                // Usa setTimeout per aspettare che lo stato della checkbox si aggiorni
                setTimeout(() => {
                    loadSedi();
                }, 50);
            }
        });

        // Inizializza shift-click per checkbox già presenti al caricamento pagina
        document.addEventListener('DOMContentLoaded', function() {
            attachLabelClickListeners('#campagnaContainer', 'campagna-checkbox');
            attachLabelClickListeners('#sedeContainer', 'sede-checkbox');
            
            // Se ci sono già campagne selezionate al caricamento, carica le sedi
            const commessaSelect = document.getElementById('commessaSelect');
            if (commessaSelect.value) {
                // Aggiungi listener per cambiamento campagne
                const campagneCheckboxes = document.querySelectorAll('.campagna-checkbox');
                if (campagneCheckboxes.length > 0) {
                    campagneCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', loadSedi);
                    });
                    
                    // Carica sedi iniziali se ci sono campagne già selezionate
                    const selectedCampagne = Array.from(document.querySelectorAll('.campagna-checkbox:checked'));
                    if (selectedCampagne.length > 0) {
                        loadSedi();
                    }
                }
            }
            
            // Inizializza drag-to-scroll per le tabelle
            initDragToScroll();
            
            // Inizializza gestione visibilità colonne
            initColumnToggle();
        });
        
        // Funzione per gestire la visibilità delle colonne
        function initColumnToggle() {
            const checkboxes = document.querySelectorAll('.column-toggle');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const columnName = this.dataset.column;
                    const isVisible = this.checked;
                    
                    toggleColumn(columnName, isVisible);
                });
            });
        }
        
        // Toggle visibilità di una singola colonna
        function toggleColumn(columnName, isVisible) {
            const displayValue = isVisible ? '' : 'none';
            
            // PRIORITÀ 1: Usa le classi col-* (più affidabile per celle con rowspan/colspan)
            const cellsWithClass = document.querySelectorAll(`.col-${columnName}`);
            cellsWithClass.forEach(cell => {
                cell.style.display = displayValue;
            });
            
            // PRIORITÀ 2: Gestisci header con indici precisi per ogni tabella
            const tableHeaderMap = {
                'table-dettagliato': {
                    // Row 1: indici delle celle nella prima riga header (con rowspan=2 le colonne singole)
                    'prodotto': { row1: [3], row2: [] },
                    'inserito': { row1: [4], row2: [] },
                    'ko': { row1: [5], row2: [] },
                    'backlog': { row1: [6], row2: [] },
                    'backlog_partner': { row1: [7], row2: [] },
                    'ore': { row1: [8], row2: [] },
                    'resa_prodotto': { row1: [9], row2: [] },
                    'resa_inserito': { row1: [10], row2: [] },
                    'resa_oraria': { row1: [11], row2: [] },
                    'paf': { row1: [12, 13], row2: [0, 1, 2, 3, 4, 5] }
                },
                'table-sintetico': {
                    'prodotto': { row1: [2], row2: [] },
                    'inserito': { row1: [3], row2: [] },
                    'ko': { row1: [4], row2: [] },
                    'backlog': { row1: [5], row2: [] },
                    'backlog_partner': { row1: [6], row2: [] },
                    'ore': { row1: [7], row2: [] },
                    'resa_prodotto': { row1: [8], row2: [] },
                    'resa_inserito': { row1: [9], row2: [] },
                    'resa_oraria': { row1: [10], row2: [] },
                    'paf': { row1: [11, 12], row2: [0, 1, 2, 3, 4, 5] }
                },
                'table-giornaliero': {
                    'prodotto': { row1: [4], row2: [] },
                    'inserito': { row1: [5], row2: [] },
                    'ko': { row1: [6], row2: [] },
                    'backlog': { row1: [7], row2: [] },
                    'backlog_partner': { row1: [8], row2: [] },
                    'ore': { row1: [9], row2: [] },
                    'resa_prodotto': { row1: [10], row2: [] },
                    'resa_inserito': { row1: [11], row2: [] },
                    'resa_oraria': { row1: [12], row2: [] },
                    'paf': { row1: [], row2: [] }
                }
            };
            
            // Nascondi/mostra gli header
            Object.keys(tableHeaderMap).forEach(tableId => {
                const table = document.getElementById(tableId);
                if (!table) return;
                
                const columnConfig = tableHeaderMap[tableId][columnName];
                if (!columnConfig) return;
                
                const headerRows = table.querySelectorAll('thead tr');
                
                // Prima riga header
                if (headerRows[0] && columnConfig.row1.length > 0) {
                    columnConfig.row1.forEach(colIndex => {
                        const cell = headerRows[0].cells[colIndex];
                        if (cell) cell.style.display = displayValue;
                    });
                }
                
                // Seconda riga header
                if (headerRows[1] && columnConfig.row2.length > 0) {
                    columnConfig.row2.forEach(colIndex => {
                        const cell = headerRows[1].cells[colIndex];
                        if (cell) cell.style.display = displayValue;
                    });
                }
            });
            
            // PRIORITÀ 3: Per le celle del body che NON hanno classi col-*, usa un approccio per testo/bgcolor
            // Questo è un fallback per celle che potrebbero non avere la classe
            const bgColorMap = {
                'prodotto': 'bg-orange-50',
                'inserito': 'bg-green-50',
                'ko': 'bg-red-50',
                'backlog': 'bg-yellow-50',
                'backlog_partner': 'bg-blue-50',
                'ore': 'bg-cyan-50',
                'resa_prodotto': 'bg-indigo-50',
                'resa_inserito': 'bg-indigo-50',
                'resa_oraria': 'bg-indigo-50',
                'paf': ['bg-teal-50', 'bg-teal-100', 'bg-purple-50', 'bg-purple-100']
            };
            
            const bgClasses = bgColorMap[columnName];
            if (bgClasses) {
                const classes = Array.isArray(bgClasses) ? bgClasses : [bgClasses];
                classes.forEach(bgClass => {
                    const cells = document.querySelectorAll(`td.${bgClass}`);
                    cells.forEach(cell => {
                        // Assicurati che non sia una cella sticky
                        if (!cell.classList.contains('sticky-det-cliente') &&
                            !cell.classList.contains('sticky-det-sede') &&
                            !cell.classList.contains('sticky-det-campagna') &&
                            !cell.classList.contains('sticky-det-totale') &&
                            !cell.classList.contains('sticky-col-cliente') &&
                            !cell.classList.contains('sticky-col-sede') &&
                            !cell.classList.contains('sticky-sin-totale') &&
                            !cell.classList.contains('sticky-giorn-data') &&
                            !cell.classList.contains('sticky-giorn-cliente') &&
                            !cell.classList.contains('sticky-giorn-sede') &&
                            !cell.classList.contains('sticky-giorn-campagna') &&
                            !cell.classList.contains('sticky-giorn-totale')) {
                            
                            // Verifica ulteriore per colonne specifiche
                            if (columnName === 'prodotto' && bgClass === 'bg-orange-50') {
                                cell.style.display = displayValue;
                            } else if (columnName === 'inserito' && bgClass === 'bg-green-50') {
                                cell.style.display = displayValue;
                            } else if (columnName === 'ko' && bgClass === 'bg-red-50') {
                                cell.style.display = displayValue;
                            } else if (columnName === 'backlog' && bgClass === 'bg-yellow-50') {
                                cell.style.display = displayValue;
                            } else if (columnName === 'backlog_partner' && bgClass === 'bg-blue-50') {
                                cell.style.display = displayValue;
                            } else if (columnName === 'ore' && bgClass === 'bg-cyan-50') {
                                cell.style.display = displayValue;
                            } else if (columnName === 'resa_prodotto' && bgClass === 'bg-indigo-50' && 
                                       cell.cellIndex >= 9 && cell.cellIndex <= 11) {
                                // Resa prodotto è la prima delle tre rese
                                const prevCells = Array.from(cell.parentElement.cells).slice(0, cell.cellIndex);
                                const indigo50Count = prevCells.filter(c => c.classList.contains('bg-indigo-50')).length;
                                if (indigo50Count === 0) cell.style.display = displayValue;
                            } else if (columnName === 'resa_inserito' && bgClass === 'bg-indigo-50' &&
                                       cell.cellIndex >= 9 && cell.cellIndex <= 11) {
                                // Resa inserito è la seconda delle tre rese
                                const prevCells = Array.from(cell.parentElement.cells).slice(0, cell.cellIndex);
                                const indigo50Count = prevCells.filter(c => c.classList.contains('bg-indigo-50')).length;
                                if (indigo50Count === 1) cell.style.display = displayValue;
                            } else if (columnName === 'resa_oraria' && bgClass === 'bg-indigo-50' &&
                                       cell.cellIndex >= 9 && cell.cellIndex <= 11) {
                                // R/H è la terza delle tre rese
                                const prevCells = Array.from(cell.parentElement.cells).slice(0, cell.cellIndex);
                                const indigo50Count = prevCells.filter(c => c.classList.contains('bg-indigo-50')).length;
                                if (indigo50Count === 2) cell.style.display = displayValue;
                            } else if (columnName === 'paf') {
                                cell.style.display = displayValue;
                            }
                        }
                    });
                });
            }
        }
        
        // Funzione per selezionare/deselezionare tutte le colonne
        function toggleAllColumns(selectAll) {
            const checkboxes = document.querySelectorAll('.column-toggle');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
                const columnName = checkbox.dataset.column;
                toggleColumn(columnName, selectAll);
            });
        }
        
        // Funzione per abilitare drag-to-scroll sulle tabelle (solo orizzontale)
        function initDragToScroll() {
            const scrollContainers = [
                document.getElementById('table-sintetico'),
                document.getElementById('table-dettagliato'),
                document.getElementById('table-giornaliero')
            ];
            
            scrollContainers.forEach(container => {
                if (!container) return;
                
                let isDown = false;
                let startX;
                let scrollLeft;
                
                // Aggiungi cursore pointer quando si passa sopra
                container.style.cursor = 'grab';
                
                container.addEventListener('mousedown', (e) => {
                    // Ignora se si clicca su un link o button
                    if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                        return;
                    }
                    
                    isDown = true;
                    container.style.cursor = 'grabbing';
                    container.style.userSelect = 'none';
                    
                    startX = e.pageX - container.offsetLeft;
                    scrollLeft = container.scrollLeft;
                });
                
                container.addEventListener('mouseleave', () => {
                    isDown = false;
                    container.style.cursor = 'grab';
                    container.style.userSelect = '';
                });
                
                container.addEventListener('mouseup', () => {
                    isDown = false;
                    container.style.cursor = 'grab';
                    container.style.userSelect = '';
                });
                
                container.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    
                    const x = e.pageX - container.offsetLeft;
                    const walkX = (x - startX) * 1.5; // Moltiplicatore per velocità scroll orizzontale
                    
                    container.scrollLeft = scrollLeft - walkX;
                });
            });
        }
    </script>
</x-admin.wrapper>
