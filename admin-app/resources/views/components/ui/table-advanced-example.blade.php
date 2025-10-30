{{-- ESEMPIO DI UTILIZZO DEL COMPONENTE TABLE-ADVANCED --}}
{{-- 
    Questo è un esempio di come refactorizzare la tabella "Sintetico" 
    usando il nuovo componente riutilizzabile 
--}}

<x-ui.table-advanced
    id="table-sintetico"
    title="Dettaglio KPI per Commessa, Sede e Campagna"
    subtitle="Visualizzazione gerarchica delle metriche di produzione"
    :enable-drag-scroll="true"
    :enable-column-toggle="true"
    max-height="70vh"
    :show-period-info="true"
    :period-start="$dataInizio"
    :period-end="$dataFine"
    :total-value="$kpiTotali['prodotto_pda']"
    :sticky-columns="[
        ['key' => 'cliente', 'label' => 'Commessa', 'width' => 200, 'wrap' => 'normal'],
        ['key' => 'sede', 'label' => 'Sede', 'width' => 250, 'wrap' => 'normal'],
    ]"
    :columns="[
        ['key' => 'prodotto', 'label' => 'Prodotto', 'toggleable' => true, 'bgColor' => 'bg-orange-50'],
        ['key' => 'inserito', 'label' => 'Inserito', 'toggleable' => true, 'bgColor' => 'bg-green-50'],
        ['key' => 'ko', 'label' => 'KO', 'toggleable' => true, 'bgColor' => 'bg-red-50'],
        ['key' => 'backlog', 'label' => 'BackLog', 'toggleable' => true, 'bgColor' => 'bg-yellow-50'],
        ['key' => 'backlog_partner', 'label' => 'BackLog Partner', 'toggleable' => true, 'bgColor' => 'bg-blue-50'],
        ['key' => 'ore', 'label' => 'Ore', 'toggleable' => true, 'bgColor' => 'bg-cyan-50'],
        ['key' => 'resa_prodotto', 'label' => 'Resa Prodotto', 'toggleable' => true, 'bgColor' => 'bg-indigo-50'],
        ['key' => 'resa_inserito', 'label' => 'Resa Inserito', 'toggleable' => true, 'bgColor' => 'bg-indigo-50'],
        ['key' => 'resa_oraria', 'label' => 'R/H', 'toggleable' => true, 'bgColor' => 'bg-indigo-50'],
        ['key' => 'paf', 'label' => 'PAF', 'toggleable' => true, 'bgColor' => 'bg-purple-50'],
    ]"
>
    {{-- Slot per pulsanti personalizzati (Sintetico/Dettagliato/Giornaliero) --}}
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
    
    {{-- Slot "table" per il contenuto della tabella --}}
    <x-slot name="table">
        <thead class="bg-base-200 sticky top-0 z-10" style="background-color: #f3f4f6 !important;">
            <tr>
                <th class="sticky-table-sintetico-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Commessa</th>
                <th class="sticky-table-sintetico-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200" rowspan="2">Sede</th>
                
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
                            <td class="sticky-table-sintetico-cliente font-bold border-r-2 border-base-300 bg-base-200/30" rowspan="{{ $clienteRowspan }}">
                                {{ $dati['cliente_originale'] ?? $cliente }}
                            </td>
                            @php $firstCliente = false; @endphp
                        @endif
                        
                        {{-- Sede --}}
                        <td class="sticky-table-sintetico-sede font-semibold border-r-2 border-base-300 bg-base-100">
                            {{ $sede }}
                        </td>
                        
                        {{-- Prodotto --}}
                        <td class="col-prodotto text-center text-sm bg-orange-50 border-r-2 border-base-300">{{ number_format($dati['prodotto_pda']) }}</td>
                        
                        {{-- Inserito --}}
                        <td class="col-inserito text-center text-sm bg-green-50 border-r-2 border-base-300">{{ number_format($dati['inserito_pda']) }}</td>
                        
                        {{-- ... resto delle celle ... --}}
                    </tr>
                @endforeach
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
        </tbody>
    </x-slot>
</x-ui.table-advanced>

{{-- 
    VANTAGGI DI QUESTO APPROCCIO:
    
    1. ✅ **Riutilizzabilità**: Stesso componente per Sintetico, Dettagliato, Giornaliero
    2. ✅ **Configurabilità**: Tutte le opzioni via props
    3. ✅ **Manutenibilità**: Stili e logica centralizzati
    4. ✅ **Sticky Columns**: Configurabili via array
    5. ✅ **Column Toggle**: Gestito automaticamente
    6. ✅ **Drag Scroll**: Attivabile/disattivabile
    7. ✅ **Responsive**: Funziona su mobile/desktop
    8. ✅ **Personalizzabile**: Slot per pulsanti custom e contenuto tabella
    
    PROPS DISPONIBILI:
    - id: ID univoco della tabella
    - title/subtitle: Titolo e sottotitolo
    - stickyColumns: Array di colonne da rendere sticky
    - columns: Array di colonne per il toggle
    - enableDragScroll: Abilita drag-to-scroll (default: true)
    - enableColumnToggle: Abilita toggle colonne (default: true)
    - maxHeight: Altezza massima container (default: 70vh)
    - showPeriodInfo: Mostra info periodo (default: false)
    - periodStart/periodEnd: Date inizio/fine periodo
    - totalValue: Valore totale da mostrare nel footer
    
    SLOT DISPONIBILI:
    - Slot default: Per pulsanti personalizzati (es: Sintetico/Dettagliato)
    - Slot "table": Per il contenuto della tabella (thead + tbody)
--}}

