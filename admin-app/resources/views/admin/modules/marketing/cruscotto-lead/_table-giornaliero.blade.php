{{-- TABELLA GIORNALIERO - LEAD MARKETING (Una riga per Data + Campagna) --}}
<x-ui.table-advanced id="table-giornaliero" :enable-drag-scroll="true" :enable-column-toggle="false" max-height="calc(100vh - 280px)" class="hidden" :sticky-columns="[
    ['key' => 'data', 'label' => 'Data', 'width' => 120, 'wrap' => 'normal'],
    ['key' => 'campagna', 'label' => 'Campagna', 'width' => 220, 'wrap' => 'normal'],
]"
    :columns="[
        ['key' => 'leads', 'label' => 'Lead', 'toggleable' => true],
        ['key' => 'click', 'label' => 'Click', 'toggleable' => true],
        ['key' => 'ore', 'label' => 'Ore', 'toggleable' => true],
        ['key' => 'conversioni', 'label' => 'Conversioni', 'toggleable' => true],
        ['key' => 'economics', 'label' => 'Economics (CPL, CPA, CPC)', 'toggleable' => true],
        ['key' => 'performance', 'label' => 'Performance (ROAS, ROI)', 'toggleable' => true],
    ]">
    <x-slot name="table">
        <thead class="bg-base-200" style="background-color: #f3f4f6 !important;">
            <tr>
                <th class="sticky-table-giornaliero-data font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important; border-bottom: 1px solid #e5e7eb !important;">
                    Data</th>
                <th class="sticky-table-giornaliero-campagna font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important; border-bottom: 1px solid #e5e7eb !important;">
                    Campagna</th>

                <th class="col-costo font-bold text-sm uppercase tracking-wider text-center bg-blue-100 border-r-2 border-base-300"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 100px; border-bottom: 1px solid #e5e7eb !important;">
                    Costo</th>

                <th class="col-leads font-bold text-sm uppercase tracking-wider text-center bg-green-100 border-r-2 border-base-300"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 80px; border-bottom: 1px solid #e5e7eb !important;">
                    Lead</th>

                <th class="col-click font-bold text-sm uppercase tracking-wider text-center bg-cyan-100 border-r-2 border-base-300"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 80px; border-bottom: 1px solid #e5e7eb !important;">
                    Click</th>

                <th class="col-ore font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 80px; border-bottom: 1px solid #e5e7eb !important;">
                    Ore</th>

                <th class="col-ricavi font-bold text-sm uppercase tracking-wider text-center bg-emerald-100 border-r-2 border-base-300"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 100px; border-bottom: 1px solid #e5e7eb !important;">
                    Ricavi</th>

                <th class="col-conversioni font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300"
                    colspan="3" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 240px;">
                    Conversioni</th>

                <th class="col-economics font-bold text-sm uppercase tracking-wider text-center bg-amber-100 border-r-2 border-base-300"
                    colspan="3" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 270px;">
                    Economics</th>

                <th class="col-performance font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300"
                    colspan="2" style="position: sticky !important; top: 0 !important; z-index: 10 !important; min-width: 180px;">
                    Performance</th>
            </tr>
            <tr style="position: sticky !important; top: 48px !important; z-index: 9 !important;">
                <th class="col-conversioni font-bold text-xs text-center bg-teal-50" style="min-width: 80px; border-bottom: 1px solid #e5e7eb !important;">Conv.</th>
                <th class="col-conversioni font-bold text-xs text-center bg-teal-50" style="min-width: 80px; border-bottom: 1px solid #e5e7eb !important;">OK Lead</th>
                <th class="col-conversioni font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300" style="min-width: 80px; border-bottom: 1px solid #e5e7eb !important;">KO Lead</th>
                
                <th class="col-economics font-bold text-xs text-center bg-amber-50" style="min-width: 90px; border-bottom: 1px solid #e5e7eb !important;">CPL</th>
                <th class="col-economics font-bold text-xs text-center bg-amber-50" style="min-width: 90px; border-bottom: 1px solid #e5e7eb !important;">CPA</th>
                <th class="col-economics font-bold text-xs text-center bg-amber-50 border-r-2 border-base-300" style="min-width: 90px; border-bottom: 1px solid #e5e7eb !important;">CPC</th>
                
                <th class="col-performance font-bold text-xs text-center bg-purple-50" style="min-width: 90px; border-bottom: 1px solid #e5e7eb !important;">ROAS %</th>
                <th class="col-performance font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300" style="min-width: 90px; border-bottom: 1px solid #e5e7eb !important;">ROI %</th>
            </tr>
        </thead>
        <tbody>
            @php
                // NON aggrega, mostra una riga per ogni combinazione Data + Campagna
                $datiGiornalieri = [];
                foreach($datiDettagliati as $dato) {
                    $datiGiornalieri[] = (object)[
                        'data' => $dato->data,
                        'utm_campaign' => $dato->utm_campaign,
                        'costo' => $dato->costo,
                        'leads' => $dato->leads,
                        'conv' => $dato->conv,
                        'ok_lead' => $dato->ok_lead,
                        'ko_lead' => $dato->ko_lead,
                        'click' => $dato->click,
                        'ore' => $dato->ore,
                        'ricavi' => $dato->ricavi,
                        'cpl' => $dato->cpl,
                        'cpa' => $dato->cpa,
                        'cpc' => $dato->cpc,
                        'roas' => $dato->roas,
                        'roi' => $dato->roi,
                    ];
                }
                
                // Ordina per data decrescente
                usort($datiGiornalieri, function($a, $b) {
                    return strcmp($b->data, $a->data);
                });
                
                $prevData = null;
                $dataCount = [];
                foreach($datiGiornalieri as $dato) {
                    if (!isset($dataCount[$dato->data])) {
                        $dataCount[$dato->data] = 0;
                    }
                    $dataCount[$dato->data]++;
                }
            @endphp
            
            @if(count($datiGiornalieri) === 0)
                <tr>
                    <td colspan="15" class="text-center py-8 text-gray-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-lg font-semibold">Nessun dato disponibile</p>
                            <p class="text-sm">Prova a modificare i filtri di selezione</p>
                        </div>
                    </td>
                </tr>
            @endif
            
            @foreach($datiGiornalieri as $dato)
            <tr class="hover:bg-base-200/50 transition-colors">
                {{-- Data (con rowspan se stessa data) --}}
                @if($prevData !== $dato->data)
                    @php
                        $rowspan = $dataCount[$dato->data];
                        $prevData = $dato->data;
                    @endphp
                    <td class="sticky-table-giornaliero-data font-semibold border-r-2 border-base-300 bg-base-100"
                        rowspan="{{ $rowspan }}">
                        {{ \Carbon\Carbon::parse($dato->data)->format('d/m/Y') }}
                    </td>
                @endif

                {{-- Campagna --}}
                <td class="sticky-table-giornaliero-campagna text-sm border-r-2 border-base-300 bg-base-100">
                    {{ $dato->utm_campaign }}
                </td>

                {{-- Costo --}}
                <td class="col-costo text-right">€ {{ number_format($dato->costo, 2, ',', '.') }}</td>

                {{-- Lead --}}
                <td class="col-leads text-center font-semibold">{{ number_format($dato->leads) }}</td>

                {{-- Click --}}
                <td class="col-click text-center">{{ number_format($dato->click) }}</td>

                {{-- Ore --}}
                <td class="col-ore text-center">{{ number_format($dato->ore, 1) }}</td>

                {{-- Ricavi --}}
                <td class="col-ricavi text-right">€ {{ number_format($dato->ricavi, 2, ',', '.') }}</td>

                {{-- Conversioni --}}
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
            </tr>
            @endforeach

            {{-- RIGA TOTALE --}}
            <tr class="totale-generale-sticky bg-info/10 font-bold border-t-2 border-info">
                <td class="sticky-table-giornaliero-data border-r-2 border-base-300" colspan="2">TOTALE GENERALE</td>
                <td class="col-costo text-right">€ {{ number_format($totali['costo'], 2, ',', '.') }}</td>
                <td class="col-leads text-center">{{ number_format($totali['leads']) }}</td>
                <td class="col-click text-center">{{ number_format($totali['click']) }}</td>
                <td class="col-ore text-center">{{ number_format($totali['ore'], 1) }}</td>
                <td class="col-ricavi text-right">€ {{ number_format($totali['ricavi'], 2, ',', '.') }}</td>
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
            </tr>
        </tbody>
    </x-slot>
</x-ui.table-advanced>
