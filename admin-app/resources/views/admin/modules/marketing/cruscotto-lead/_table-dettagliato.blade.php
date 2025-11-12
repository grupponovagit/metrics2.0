{{-- TABELLA DETTAGLIATO - LEAD MARKETING --}}
<x-ui.table-advanced id="table-dettagliato" :enable-drag-scroll="true" :enable-column-toggle="false" max-height="70vh" class="hidden" :sticky-columns="[
    ['key' => 'ragione_sociale', 'label' => 'Ragione Sociale', 'width' => 180, 'wrap' => 'normal'],
    ['key' => 'provenienza', 'label' => 'Provenienza', 'width' => 150, 'wrap' => 'normal'],
    ['key' => 'campagna', 'label' => 'Campagna', 'width' => 220, 'wrap' => 'normal'],
]"
    :columns="[
        ['key' => 'leads', 'label' => 'Lead', 'toggleable' => true],
        ['key' => 'conversioni', 'label' => 'Conversioni', 'toggleable' => true],
        ['key' => 'economics', 'label' => 'Economics (CPL, CPA, CPC)', 'toggleable' => true],
        ['key' => 'performance', 'label' => 'Performance (ROAS, ROI)', 'toggleable' => true],
        ['key' => 'click', 'label' => 'Click', 'toggleable' => true],
        ['key' => 'ore', 'label' => 'Ore', 'toggleable' => true],
    ]">
    <x-slot name="table">
        <thead class="bg-base-200" style="background-color: #f3f4f6 !important;">
            <tr>
                <th class="sticky-table-dettagliato-ragione_sociale font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important;">
                    Ragione Sociale</th>
                <th class="sticky-table-dettagliato-provenienza font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important;">
                    Provenienza</th>
                <th class="sticky-table-dettagliato-campagna font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important;">
                    Campagna</th>

                {{-- Costo --}}
                <th class="font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 100px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Costo</th>

                {{-- Lead --}}
                <th class="col-leads font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 80px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Lead</th>

                {{-- Conversioni (totali e qualità) --}}
                <th class="col-conversioni font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300"
                    colspan="3"
                    style="min-width: 240px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Conversioni</th>

                {{-- ECONOMICS (CPL, CPA, CPC) --}}
                <th class="col-economics font-bold text-sm uppercase tracking-wider text-center bg-amber-100 border-r-2 border-base-300"
                    colspan="3"
                    style="min-width: 270px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Economics</th>

                {{-- PERFORMANCE (ROAS, ROI) --}}
                <th class="col-performance font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300"
                    colspan="2"
                    style="min-width: 180px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Performance</th>

                {{-- Click --}}
                <th class="col-click font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 80px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Click</th>

                {{-- Ore --}}
                <th class="col-ore font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 80px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Ore</th>

                {{-- Ricavi --}}
                <th class="font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 100px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Ricavi</th>
            </tr>
            <tr style="position: sticky !important; top: 48px !important; z-index: 9 !important;">
                {{-- Sottocolonne Conversioni --}}
                <th class="col-conversioni font-bold text-xs text-center bg-teal-50"
                    style="min-width: 80px; width: auto; border-bottom: 1px solid #e5e7eb !important;">Conv.</th>
                <th class="col-conversioni font-bold text-xs text-center bg-teal-50"
                    style="min-width: 80px; width: auto; border-bottom: 1px solid #e5e7eb !important;">OK Lead</th>
                <th class="col-conversioni font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300"
                    style="min-width: 80px; width: auto; border-bottom: 1px solid #e5e7eb !important;">KO Lead</th>

                {{-- Sottocolonne Economics --}}
                <th class="col-economics font-bold text-xs text-center bg-amber-50"
                    style="min-width: 90px; width: auto; border-bottom: 1px solid #e5e7eb !important;">CPL</th>
                <th class="col-economics font-bold text-xs text-center bg-amber-50"
                    style="min-width: 90px; width: auto; border-bottom: 1px solid #e5e7eb !important;">CPA</th>
                <th class="col-economics font-bold text-xs text-center bg-amber-50 border-r-2 border-base-300"
                    style="min-width: 90px; width: auto; border-bottom: 1px solid #e5e7eb !important;">CPC</th>

                {{-- Sottocolonne Performance --}}
                <th class="col-performance font-bold text-xs text-center bg-purple-50"
                    style="min-width: 90px; width: auto; border-bottom: 1px solid #e5e7eb !important;">ROAS %</th>
                <th class="col-performance font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300"
                    style="min-width: 90px; width: auto; border-bottom: 1px solid #e5e7eb !important;">ROI %</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Raggruppa i dati per Ragione Sociale, Provenienza e Campagna (sommando per range date)
                $grouped = [];
                foreach($datiDettagliati as $dato) {
                    $key = ($dato->ragione_sociale ?? 'N/D') . '|' . ($dato->provenienza ?? 'N/D') . '|' . $dato->utm_campaign;
                    
                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'ragione_sociale' => $dato->ragione_sociale ?? 'N/D',
                            'provenienza' => $dato->provenienza ?? 'N/D',
                            'utm_campaign' => $dato->utm_campaign,
                            'costo' => 0,
                            'leads' => 0,
                            'conv' => 0,
                            'ok_lead' => 0,
                            'ko_lead' => 0,
                            'click' => 0,
                            'ore' => 0,
                            'ricavi' => 0,
                        ];
                    }
                    
                    $grouped[$key]['costo'] += $dato->costo;
                    $grouped[$key]['leads'] += $dato->leads;
                    $grouped[$key]['conv'] += $dato->conv;
                    $grouped[$key]['ok_lead'] += $dato->ok_lead;
                    $grouped[$key]['ko_lead'] += $dato->ko_lead;
                    $grouped[$key]['click'] += $dato->click;
                    $grouped[$key]['ore'] += $dato->ore;
                    $grouped[$key]['ricavi'] += $dato->ricavi;
                }
                
                // Calcola i KPI per ogni gruppo
                $datiAggregati = [];
                foreach($grouped as $data) {
                    $data['cpl'] = $data['leads'] > 0 ? $data['costo'] / $data['leads'] : 0;
                    $data['cpa'] = $data['conv'] > 0 ? $data['costo'] / $data['conv'] : 0;
                    $data['cpc'] = $data['click'] > 0 ? $data['costo'] / $data['click'] : 0;
                    $data['roas'] = $data['costo'] > 0 ? ($data['ricavi'] / $data['costo']) * 100 : 0;
                    $data['roi'] = $data['costo'] > 0 ? (($data['ricavi'] - $data['costo']) / $data['costo']) * 100 : 0;
                    $datiAggregati[] = (object)$data;
                }
                
                // Calcola rowspan per ragione sociale e provenienza
                $ragioneSocialeCount = [];
                $provenienzaCount = [];
                foreach($datiAggregati as $dato) {
                    $rsKey = $dato->ragione_sociale;
                    $provKey = $dato->ragione_sociale . '|' . $dato->provenienza;
                    
                    if (!isset($ragioneSocialeCount[$rsKey])) {
                        $ragioneSocialeCount[$rsKey] = 0;
                    }
                    if (!isset($provenienzaCount[$provKey])) {
                        $provenienzaCount[$provKey] = 0;
                    }
                    $ragioneSocialeCount[$rsKey]++;
                    $provenienzaCount[$provKey]++;
                }
                
                $prevRagioneSociale = null;
                $prevProvenienza = null;
            @endphp
            
            @foreach($datiAggregati as $dato)
            <tr class="hover:bg-base-200/50 transition-colors group">
                {{-- Ragione Sociale (con rowspan) --}}
                @if($prevRagioneSociale !== $dato->ragione_sociale)
                    @php
                        $rowspan = $ragioneSocialeCount[$dato->ragione_sociale];
                        $prevRagioneSociale = $dato->ragione_sociale;
                        $prevProvenienza = null;
                    @endphp
                    <td class="sticky-table-dettagliato-ragione_sociale font-semibold border-r-2 border-base-300 bg-base-100"
                        rowspan="{{ $rowspan }}">
                        {{ $dato->ragione_sociale }}
                    </td>
                @endif

                {{-- Provenienza (con rowspan) --}}
                @if($prevProvenienza !== $dato->provenienza)
                    @php
                        $provKey = $dato->ragione_sociale . '|' . $dato->provenienza;
                        $rowspanProv = $provenienzaCount[$provKey];
                        $prevProvenienza = $dato->provenienza;
                    @endphp
                    <td class="sticky-table-dettagliato-provenienza text-sm border-r-2 border-base-300 bg-base-100"
                        rowspan="{{ $rowspanProv }}">
                        {{ $dato->provenienza }}
                    </td>
                @endif

                {{-- Campagna --}}
                <td class="sticky-table-dettagliato-campagna text-sm border-r-2 border-base-300 bg-base-100">
                    {{ $dato->utm_campaign }}
                </td>

                {{-- Costo --}}
                <td class="text-right">€ {{ number_format($dato->costo, 2, ',', '.') }}</td>

                {{-- Lead --}}
                <td class="col-leads text-center font-semibold">{{ number_format($dato->leads) }}</td>

                {{-- Conversioni totali --}}
                <td class="col-conversioni text-center">{{ number_format($dato->conv) }}</td>
                <td class="col-conversioni text-center text-success">{{ number_format($dato->ok_lead) }}</td>
                <td class="col-conversioni text-center text-error">{{ number_format($dato->ko_lead) }}</td>

                {{-- Economics: CPL, CPA, CPC --}}
                <td class="col-economics text-right">€ {{ number_format($dato->cpl, 2, ',', '.') }}</td>
                <td class="col-economics text-right">€ {{ number_format($dato->cpa, 2, ',', '.') }}</td>
                <td class="col-economics text-right">€ {{ number_format($dato->cpc, 2, ',', '.') }}</td>

                {{-- Performance: ROAS, ROI --}}
                <td class="col-performance text-right font-semibold {{ $dato->roas >= 100 ? 'text-success' : 'text-warning' }}">
                    {{ number_format($dato->roas, 1) }}%
                </td>
                <td class="col-performance text-right font-semibold {{ $dato->roi >= 0 ? 'text-success' : 'text-error' }}">
                    {{ number_format($dato->roi, 1) }}%
                </td>

                {{-- Click --}}
                <td class="col-click text-center">{{ number_format($dato->click) }}</td>

                {{-- Ore --}}
                <td class="col-ore text-center">{{ number_format($dato->ore, 1) }}</td>

                {{-- Ricavi --}}
                <td class="text-right">€ {{ number_format($dato->ricavi, 2, ',', '.') }}</td>
            </tr>
            @endforeach

            {{-- RIGA TOTALE GENERALE (sticky) --}}
            <tr class="totale-generale-sticky bg-info/10 font-bold border-t-2 border-info">
                <td class="sticky-table-dettagliato-ragione_sociale border-r-2 border-base-300" colspan="3">TOTALE GENERALE</td>
                <td class="text-right">€ {{ number_format($totali['costo'], 2, ',', '.') }}</td>
                <td class="col-leads text-center">{{ number_format($totali['leads']) }}</td>
                <td class="col-conversioni text-center">{{ number_format($totali['conv']) }}</td>
                <td class="col-conversioni text-center text-success">{{ number_format($totali['ok_lead']) }}</td>
                <td class="col-conversioni text-center text-error">{{ number_format($totali['ko_lead']) }}</td>
                <td class="col-economics text-right">€ {{ number_format($totali['cpl'], 2, ',', '.') }}</td>
                <td class="col-economics text-right">€ {{ number_format($totali['cpa'], 2, ',', '.') }}</td>
                <td class="col-economics text-right">€ {{ number_format($totali['cpc'], 2, ',', '.') }}</td>
                <td class="col-performance text-right {{ $totali['roas'] >= 100 ? 'text-success' : 'text-warning' }}">
                    {{ number_format($totali['roas'], 1) }}%
                </td>
                <td class="col-performance text-right {{ $totali['roi'] >= 0 ? 'text-success' : 'text-error' }}">
                    {{ number_format($totali['roi'], 1) }}%
                </td>
                <td class="col-click text-center">{{ number_format($totali['click']) }}</td>
                <td class="col-ore text-center">{{ number_format($totali['ore'], 1) }}</td>
                <td class="text-right">€ {{ number_format($totali['ricavi'], 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </x-slot>
</x-ui.table-advanced>
