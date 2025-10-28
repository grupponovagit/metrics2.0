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
    
    {{-- FILTRI CON SELECT MULTIPLE --}}
    @php
    $filterConfig = [
        'dates' => [
            [
                'name' => 'data_inizio',
                'label' => 'Data Inizio',
                'type' => 'date',
                'value' => $dataInizio,
            ],
            [
                'name' => 'data_fine',
                'label' => 'Data Fine',
                'type' => 'date',
                'value' => $dataFine,
            ],
        ],
        'selects' => [
            [
                'name' => 'mandato',
                'label' => 'Cliente',
                'type' => 'select-multiple',
                'multiple' => true,
                'options' => $mandati->toArray(),
                'value' => $mandatoFilter,
            ],
            [
                'name' => 'sede',
                'label' => 'Sede',
                'type' => 'select-multiple',
                'multiple' => true,
                'options' => $sedi->toArray(),
                'value' => $sedeFilter,
            ],
        ],
    ];
    @endphp
    
    <x-filters-multi 
        :filters="$filterConfig"
        :action="route('admin.produzione.cruscotto_produzione')"
        :showReset="true"
    />
    <br/>
    {{-- TABELLA DETTAGLIATA - Visibile solo se ci sono filtri applicati --}}
    @if(request()->hasAny(['data_inizio', 'data_fine', 'mandato', 'sede']))
    <x-admin.card tone="light" shadow="lg" padding="none">
        <div class="p-6 border-b border-base-300 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-base-content">
                    Dettaglio KPI per Cliente, Sede e Campagna
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
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Cliente</th>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Macro Campagna</th>
                        
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
                                <tr class="hover:bg-base-200/50 transition-colors">
                                {{-- Cliente --}}
                                @if($firstMandato)
                                    <td class="font-bold border-r-2 border-base-300 bg-base-200/30" rowspan="{{ $mandatoRowspan }}">
                                        {{-- Mostra il nome originale dalla cache (es: TIM_CONSUMER) --}}
                                        {{ collect($campagneData)->first()['cliente_originale'] ?? $mandato }}
                                    </td>
                                    @php $firstMandato = false; @endphp
                                @endif
                                    
                                    {{-- Sede --}}
                                    @if($firstSede)
                                        <td class="font-semibold border-r-2 border-base-300 bg-base-100" rowspan="{{ $sedeRowspan }}">
                                            {{ $sede }}
                                        </td>
                                        @php $firstSede = false; @endphp
                                    @endif
                                    
                                    {{-- Campagna/Prodotto --}}
                                    <td class="border-r-2 border-base-300">
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
                            <td colspan="3" class="text-left text-sm font-bold py-2 px-4 border-r-2 border-slate-300">TOTALE {{ $mandato }}</td>
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
                            <td colspan="3" class="text-left text-base font-bold py-3 px-4 border-r-2 border-slate-300">TOTALE</td>
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
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Cliente</th>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        
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
                            <tr class="hover:bg-base-200/50 transition-colors">
                                {{-- Cliente --}}
                                @if($firstCliente)
                                    <td class="font-bold border-r-2 border-base-300 bg-base-200/30" rowspan="{{ $clienteRowspan }}">
                                        {{-- Mostra il nome originale dalla cache (es: TIM_CONSUMER) --}}
                                        {{ $dati['cliente_originale'] ?? $cliente }}
                                    </td>
                                    @php $firstCliente = false; @endphp
                                @endif
                                
                                {{-- Sede --}}
                                <td class="font-semibold border-r-2 border-base-300 bg-base-100">
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
                            <td colspan="2" class="text-left text-sm font-bold py-2 px-4 border-r-2 border-slate-300">TOTALE {{ $cliente }}</td>
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
                            <td colspan="2" class="text-left text-base font-bold py-3 px-4 border-r-2 border-slate-300">TOTALE</td>
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

</x-admin.wrapper>
