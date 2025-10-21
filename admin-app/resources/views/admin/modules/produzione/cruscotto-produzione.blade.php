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
            [
                'name' => 'canale',
                'label' => 'Canale',
                'type' => 'select-multiple',
                'multiple' => true,
                'options' => $canali->toArray(),
                'value' => $canaleFilter,
            ],
        ],
    ];
    @endphp
    
    <x-filters-multi 
        :filters="$filterConfig"
        :action="route('admin.produzione.cruscotto_produzione')"
        :showReset="true"
    />

    {{-- TABELLA DETTAGLIATA - Visibile solo se ci sono filtri applicati --}}
    @if(request()->hasAny(['data_inizio', 'data_fine', 'mandato', 'sede', 'canale']))
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
            <table class="table table-zebra w-full" style="min-width: 1500px;">
                <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
                    <tr>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Cliente</th>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Prodotto</th>
                        
                        {{-- Totale --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" colspan="2">Totale</th>
                        
                        {{-- Inserito --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" colspan="2">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" colspan="2">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" colspan="2">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" colspan="2">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2">Ore</th>
                        
                        {{-- N° Opzioni --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-purple-100" rowspan="2">N° Opzioni</th>
                    </tr>
                    <tr>
                        {{-- Prodotto --}}
                        <th class="text-xs text-center bg-orange-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-orange-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- Inserito --}}
                        <th class="text-xs text-center bg-green-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-green-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- KO --}}
                        <th class="text-xs text-center bg-red-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-red-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- BackLog --}}
                        <th class="text-xs text-center bg-yellow-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-yellow-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="text-xs text-center bg-blue-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-blue-50 border-r-2 border-base-300">Valore</th>
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
                                            {{ $mandato }}
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
                                    <td class="text-center text-sm bg-orange-50">{{ number_format($datiCampagna['prodotto_pda']) }}</td>
                                    <td class="text-center text-sm font-semibold bg-orange-50 border-r-2 border-base-300">{{ number_format($datiCampagna['prodotto_valore'], 0) }}</td>
                                    
                                    {{-- Inserito --}}
                                    <td class="text-center text-sm bg-green-50">{{ number_format($datiCampagna['inserito_pda']) }}</td>
                                    <td class="text-center text-sm font-semibold bg-green-50 border-r-2 border-base-300">{{ number_format($datiCampagna['inserito_valore'], 0) }}</td>
                                    
                                    {{-- KO --}}
                                    <td class="text-center text-sm bg-red-50">{{ number_format($datiCampagna['ko_pda']) }}</td>
                                    <td class="text-center text-sm font-semibold bg-red-50 border-r-2 border-base-300">{{ number_format($datiCampagna['ko_valore'], 0) }}</td>
                                    
                                    {{-- BackLog --}}
                                    <td class="text-center text-sm bg-yellow-50">{{ number_format($datiCampagna['backlog_pda']) }}</td>
                                    <td class="text-center text-sm font-semibold bg-yellow-50 border-r-2 border-base-300">{{ number_format($datiCampagna['backlog_valore'], 0) }}</td>
                                    
                                    {{-- BackLog Partner --}}
                                    <td class="text-center text-sm bg-blue-50">{{ number_format($datiCampagna['backlog_partner_pda']) }}</td>
                                    <td class="text-center text-sm font-semibold bg-blue-50 border-r-2 border-base-300">{{ number_format($datiCampagna['backlog_partner_valore'], 0) }}</td>
                                    
                                    {{-- Ore (placeholder - da implementare se necessario) --}}
                                    <td class="text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">-</td>
                                    
                                    {{-- N° Opzioni --}}
                                    <td class="text-center text-sm font-semibold bg-purple-50">
                                        {{ $datiCampagna['num_opzioni'] ?? 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="15" class="text-center py-12">
                                <div>
                                    <h3 class="text-lg font-semibold text-base-content mb-1">Nessun dato disponibile</h3>
                                    <p class="text-sm text-base-content/60">Prova a modificare i filtri per visualizzare i dati</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- TABELLA SINTETICA --}}
        <div id="table-sintetico" class="table-scroll-container max-h-[70vh]" style="overflow-x: auto !important; overflow-y: auto !important;">
            <table class="table table-zebra w-full" style="min-width: 1400px;">
                <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
                    <tr>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Cliente</th>
                        <th class="font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                        
                        {{-- Totale --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300" colspan="2">Totale</th>
                        
                        {{-- Inserito --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300" colspan="2">Inserito</th>
                        
                        {{-- KO --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300" colspan="2">KO</th>
                        
                        {{-- BackLog --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300" colspan="2">BackLog</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300" colspan="2">BackLog Partner</th>
                        
                        {{-- Ore --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300" rowspan="2">Ore</th>
                        
                        {{-- N° Opzioni --}}
                        <th class="font-bold text-sm uppercase tracking-wider text-center bg-purple-100" rowspan="2">N° Opzioni</th>
                    </tr>
                    <tr>
                        {{-- Totale --}}
                        <th class="text-xs text-center bg-orange-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-orange-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- Inserito --}}
                        <th class="text-xs text-center bg-green-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-green-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- KO --}}
                        <th class="text-xs text-center bg-red-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-red-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- BackLog --}}
                        <th class="text-xs text-center bg-yellow-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-yellow-50 border-r-2 border-base-300">Valore</th>
                        
                        {{-- BackLog Partner --}}
                        <th class="text-xs text-center bg-blue-50 border-l border-base-200">Pda</th>
                        <th class="text-xs text-center bg-blue-50 border-r-2 border-base-300">Valore</th>
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
                                        {{ $cliente }}
                                    </td>
                                    @php $firstCliente = false; @endphp
                                @endif
                                
                                {{-- Sede --}}
                                <td class="font-semibold border-r-2 border-base-300 bg-base-100">
                                    {{ $sede }}
                                </td>
                                
                                {{-- Totale --}}
                                <td class="text-center text-sm bg-orange-50">{{ number_format($dati['prodotto_pda']) }}</td>
                                <td class="text-center text-sm font-semibold bg-orange-50 border-r-2 border-base-300">{{ number_format($dati['prodotto_valore'], 0) }}</td>
                                
                                {{-- Inserito --}}
                                <td class="text-center text-sm bg-green-50">{{ number_format($dati['inserito_pda']) }}</td>
                                <td class="text-center text-sm font-semibold bg-green-50 border-r-2 border-base-300">{{ number_format($dati['inserito_valore'], 0) }}</td>
                                
                                {{-- KO --}}
                                <td class="text-center text-sm bg-red-50">{{ number_format($dati['ko_pda']) }}</td>
                                <td class="text-center text-sm font-semibold bg-red-50 border-r-2 border-base-300">{{ number_format($dati['ko_valore'], 0) }}</td>
                                
                                {{-- BackLog --}}
                                <td class="text-center text-sm bg-yellow-50">{{ number_format($dati['backlog_pda']) }}</td>
                                <td class="text-center text-sm font-semibold bg-yellow-50 border-r-2 border-base-300">{{ number_format($dati['backlog_valore'], 0) }}</td>
                                
                                {{-- BackLog Partner --}}
                                <td class="text-center text-sm bg-blue-50">{{ number_format($dati['backlog_partner_pda']) }}</td>
                                <td class="text-center text-sm font-semibold bg-blue-50 border-r-2 border-base-300">{{ number_format($dati['backlog_partner_valore'], 0) }}</td>
                                
                                {{-- Ore --}}
                                <td class="text-center text-sm font-semibold bg-cyan-50 border-r-2 border-base-300">-</td>
                                
                                {{-- N° Opzioni --}}
                                <td class="text-center text-sm font-semibold bg-purple-50">
                                    {{ $dati['num_opzioni'] ?? 0 }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-12">
                                <div>
                                    <h3 class="text-lg font-semibold text-base-content mb-1">Nessun dato disponibile</h3>
                                    <p class="text-sm text-base-content/60">Prova a modificare i filtri per visualizzare i dati</p>
                                </div>
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
