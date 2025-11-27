{{-- TABELLA GIORNALIERO (Refactored) --}}
<x-ui.table-advanced id="table-giornaliero" :enable-drag-scroll="true" :enable-column-toggle="false" max-height="70vh" :sticky-columns="[
    ['key' => 'data', 'label' => 'Data', 'width' => 120, 'wrap' => 'nowrap'],
    ['key' => 'cliente', 'label' => 'Commessa', 'width' => 150, 'wrap' => 'normal'],
]"
    :columns="[
        ['key' => 'prodotto', 'label' => 'Prodotto', 'toggleable' => true],
        ['key' => 'inserito', 'label' => 'Inserito', 'toggleable' => true],
        ['key' => 'ko', 'label' => 'KO', 'toggleable' => true],
        ['key' => 'backlog', 'label' => 'BackLog', 'toggleable' => true],
        ['key' => 'backlog_partner', 'label' => 'BackLog Partner', 'toggleable' => true],
        ['key' => 'ore', 'label' => 'Ore', 'toggleable' => true],
        ['key' => 'economics', 'label' => 'Economics (tutti)', 'toggleable' => true],
        ['key' => 'resa', 'label' => 'Resa (tutti)', 'toggleable' => true],
    ]">
    <x-slot name="table">
        <thead class="bg-base-200" style="background-color: #f3f4f6 !important;">
            <tr>
                {{-- Data --}}
                <th class="sticky-table-giornaliero-data font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2"
                    style="min-width: 120px; width: auto; position: sticky !important; top: 0 !important; z-index: 15 !important;">
                    Data</th>

                {{-- Commessa --}}
                <th class="sticky-table-giornaliero-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2"
                    style="min-width: 150px; width: auto; position: sticky !important; top: 0 !important; z-index: 15 !important;">
                    Commessa</th>

                {{-- Prodotto --}}
                <th class="col-prodotto font-bold text-sm uppercase tracking-wider text-center bg-orange-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 90px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Prodotto</th>

                {{-- Inserito --}}
                <th class="col-inserito font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 90px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Inserito</th>

                {{-- KO --}}
                <th class="col-ko font-bold text-sm uppercase tracking-wider text-center bg-red-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 70px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    KO</th>

                {{-- BackLog --}}
                <th class="col-backlog font-bold text-sm uppercase tracking-wider text-center bg-yellow-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 90px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    BackLog</th>

                {{-- BackLog Partner --}}
                <th class="col-backlog_partner font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 120px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    BackLog Partner</th>

                {{-- Ore --}}
                <th class="col-ore font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 70px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Ore</th>

                {{-- ECONOMICS (2 sottocolonne) --}}
                <th class="col-economics font-bold text-sm uppercase tracking-wider text-center bg-amber-100 border-r-2 border-base-300"
                    colspan="2"
                    style="min-width: 190px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Economics</th>

                {{-- RESA (2 sottocolonne) --}}
                <th class="col-resa font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300"
                    colspan="2"
                    style="min-width: 180px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Resa</th>
            </tr>
            <tr style="position: sticky !important; top: 48px !important; z-index: 9 !important;">
                {{-- Sottocolonne Economics --}}
                <th class="col-economics col-fatturato font-bold text-xs text-center bg-amber-50 border-r border-base-200"
                    style="min-width: 100px; width: auto;">Fatturato</th>
                <th class="col-economics col-ricavo_orario font-bold text-xs text-center bg-amber-50 border-r-2 border-base-300"
                    style="min-width: 90px; width: auto;">Ricavo/H</th>
                
                {{-- Sottocolonne Resa --}}
                <th class="col-resa col-resa_prodotto font-bold text-xs text-center bg-indigo-50 border-r border-base-200"
                    style="min-width: 90px; width: auto;">Resa Prod.</th>
                <th class="col-resa col-resa_inserito font-bold text-xs text-center bg-indigo-50 border-r-2 border-base-300"
                    style="min-width: 90px; width: auto;">Resa Ins.</th>
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
                    'fatturato' => 0,
                ];
            @endphp

            @forelse($datiGiornalieri as $datiGiorno)
                @php
                    $totaleGiornaliero['prodotto_pda'] += $datiGiorno['prodotto_pda'] ?? 0;
                    $totaleGiornaliero['inserito_pda'] += $datiGiorno['inserito_pda'] ?? 0;
                    $totaleGiornaliero['ko_pda'] += $datiGiorno['ko_pda'] ?? 0;
                    $totaleGiornaliero['backlog_pda'] += $datiGiorno['backlog_pda'] ?? 0;
                    $totaleGiornaliero['backlog_partner_pda'] += $datiGiorno['backlog_partner_pda'] ?? 0;
                    $totaleGiornaliero['ore'] += $datiGiorno['ore'] ?? 0;
                    $totaleGiornaliero['fatturato'] += $datiGiorno['fatturato'] ?? 0;
                @endphp

                <tr class="{{ isset($datiGiorno['is_bonus']) && $datiGiorno['is_bonus'] ? 'bg-warning/10 border-l-4 border-warning' : '' }}">
                    {{-- Data --}}
                    <td
                        class="sticky-table-giornaliero-data text-sm font-semibold border-r-2 border-base-300 {{ isset($datiGiorno['is_bonus']) && $datiGiorno['is_bonus'] ? 'bg-warning/20' : 'bg-base-100' }}">
                        {{ \Carbon\Carbon::parse($datiGiorno['data'])->format('d/m/Y') }}
                    </td>

                    {{-- Commessa --}}
                    <td
                        class="sticky-table-giornaliero-cliente text-sm border-r-2 border-base-300 {{ isset($datiGiorno['is_bonus']) && $datiGiorno['is_bonus'] ? 'bg-warning/20' : 'bg-base-50 font-bold' }}">
                        @if(isset($datiGiorno['is_bonus']) && $datiGiorno['is_bonus'])
                            <div>
                                <div class="font-bold">{{ $datiGiorno['commessa'] }}</div>
                                <div class="text-xs text-warning/80 font-normal">{{ $datiGiorno['bonus_info'] ?? 'Bonus Globale' }}</div>
                            </div>
                        @else
                            {{ $datiGiorno['commessa'] }}
                        @endif
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
                    <td class="col-ore text-center text-sm bg-cyan-50 border-r-2 border-base-300">
                        {{ ($datiGiorno['ore'] ?? 0) > 0 ? number_format($datiGiorno['ore'], 2) : '-' }}
                    </td>

                    {{-- ECONOMICS --}}
                    <td class="col-economics col-fatturato text-center text-sm border-r border-base-200 {{ isset($datiGiorno['is_bonus']) && $datiGiorno['is_bonus'] ? 'bg-warning/30 font-bold' : 'bg-amber-50' }}">
                        {{ ($datiGiorno['fatturato'] ?? 0) > 0 ? '€ ' . number_format($datiGiorno['fatturato'], 2, ',', '.') : '-' }}
                    </td>
                    <td class="col-economics col-ricavo_orario text-center text-sm border-r-2 border-base-300 {{ isset($datiGiorno['is_bonus']) && $datiGiorno['is_bonus'] ? 'bg-warning/20' : 'bg-amber-50' }}">
                        {{ ($datiGiorno['ricavo_orario'] ?? 0) > 0 ? '€ ' . number_format($datiGiorno['ricavo_orario'], 2, ',', '.') : '-' }}
                    </td>

                    {{-- RESA --}}
                    <td class="col-resa col-resa_prodotto text-center text-sm bg-indigo-50 border-r border-base-200">
                        {{ $datiGiorno['resa_prodotto'] ?? '-' }}
                    </td>
                    <td class="col-resa col-resa_inserito text-center text-sm bg-indigo-50 border-r-2 border-base-300">
                        {{ $datiGiorno['resa_inserito'] ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-8 text-base-content/60">
                        Nessun dato disponibile per il periodo selezionato
                    </td>
                </tr>
            @endforelse

            {{-- RIGA TOTALE GENERALE --}}
            @if (count($datiGiornalieri) > 0)
                @php
                    // Calcoli resa totali
                    $totaleGiornaliero['resa_prodotto'] =
                        $totaleGiornaliero['ore'] > 0
                            ? round($totaleGiornaliero['prodotto_pda'] / $totaleGiornaliero['ore'], 2)
                            : 0;
                    $totaleGiornaliero['resa_inserito'] =
                        $totaleGiornaliero['ore'] > 0
                            ? round($totaleGiornaliero['inserito_pda'] / $totaleGiornaliero['ore'], 2)
                            : 0;
                    $totaleGiornaliero['ricavo_orario'] =
                        $totaleGiornaliero['ore'] > 0
                            ? round($totaleGiornaliero['fatturato'] / $totaleGiornaliero['ore'], 2)
                            : 0;
                @endphp

                <tr class="bg-slate-100 font-bold border-t-4 border-slate-400">
                    <td colspan="2"
                        class="text-center text-base uppercase tracking-wide py-3 border-r-2 border-slate-300 sticky-totale-table-giornaliero">
                        TOTALE PERIODO
                    </td>

                    {{-- Totali metriche --}}
                    <td class="col-prodotto text-center text-base bg-orange-100 border-r-2 border-slate-300">
                        {{ number_format($totaleGiornaliero['prodotto_pda']) }}</td>
                    <td class="col-inserito text-center text-base bg-green-100 border-r-2 border-slate-300">
                        {{ number_format($totaleGiornaliero['inserito_pda']) }}</td>
                    <td class="col-ko text-center text-base bg-red-100 border-r-2 border-slate-300">
                        {{ number_format($totaleGiornaliero['ko_pda']) }}</td>
                    <td class="col-backlog text-center text-base bg-yellow-100 border-r-2 border-slate-300">
                        {{ number_format($totaleGiornaliero['backlog_pda']) }}</td>
                    <td class="col-backlog_partner text-center text-base bg-blue-100 border-r-2 border-slate-300">
                        {{ number_format($totaleGiornaliero['backlog_partner_pda']) }}</td>
                    <td class="col-ore text-center text-base bg-cyan-100 border-r-2 border-slate-300">
                        {{ number_format($totaleGiornaliero['ore'], 2) }}</td>
                    
                    {{-- Economics --}}
                    <td class="col-economics col-fatturato text-center text-base bg-amber-100 border-r border-slate-200">
                        {{ $totaleGiornaliero['fatturato'] > 0 ? '€ ' . number_format($totaleGiornaliero['fatturato'], 2, ',', '.') : '-' }}</td>
                    <td class="col-economics col-ricavo_orario text-center text-base bg-amber-100 border-r-2 border-slate-300">
                        {{ $totaleGiornaliero['ricavo_orario'] > 0 ? '€ ' . number_format($totaleGiornaliero['ricavo_orario'], 2, ',', '.') : '-' }}</td>
                    
                    {{-- Resa --}}
                    <td class="col-resa col-resa_prodotto text-center text-base bg-indigo-100 border-r border-slate-200">
                        {{ $totaleGiornaliero['resa_prodotto'] }}</td>
                    <td class="col-resa col-resa_inserito text-center text-base bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totaleGiornaliero['resa_inserito'] }}</td>
                </tr>
            @endif
        </tbody>
    </x-slot>
</x-ui.table-advanced>
