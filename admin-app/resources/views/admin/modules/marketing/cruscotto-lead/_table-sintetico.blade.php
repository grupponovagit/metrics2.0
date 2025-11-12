{{-- TABELLA SINTETICO - LEAD MARKETING (Aggregato per Ragione Sociale + Provenienza) --}}
<x-ui.table-advanced id="table-sintetico" :enable-drag-scroll="true" :enable-column-toggle="false" max-height="calc(100vh - 280px)" :sticky-columns="[
    ['key' => 'ragione_sociale', 'label' => 'Ragione Sociale', 'width' => 200, 'wrap' => 'normal'],
    ['key' => 'provenienza', 'label' => 'Provenienza', 'width' => 170, 'wrap' => 'normal'],
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
                <th class="sticky-table-sintetico-ragione_sociale font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important; border-bottom: 1px solid #e5e7eb !important;">
                    Ragione Sociale</th>
                <th class="sticky-table-sintetico-provenienza font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important; border-bottom: 1px solid #e5e7eb !important;">
                    Provenienza</th>

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
                // Raggruppa i dati per Ragione Sociale e Provenienza
                $grouped = [];
                foreach($datiDettagliati as $dato) {
                    $key = ($dato->ragione_sociale ?? 'N/D') . '|' . ($dato->provenienza ?? 'N/D');
                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'ragione_sociale' => $dato->ragione_sociale ?? 'N/D',
                            'provenienza' => $dato->provenienza ?? 'N/D',
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
                $datiSintetici = [];
                foreach($grouped as $data) {
                    $data['cpl'] = $data['leads'] > 0 ? $data['costo'] / $data['leads'] : 0;
                    $data['cpa'] = $data['conv'] > 0 ? $data['costo'] / $data['conv'] : 0;
                    $data['cpc'] = $data['click'] > 0 ? $data['costo'] / $data['click'] : 0;
                    $data['roas'] = $data['costo'] > 0 ? ($data['ricavi'] / $data['costo']) * 100 : 0;
                    $data['roi'] = $data['costo'] > 0 ? (($data['ricavi'] - $data['costo']) / $data['costo']) * 100 : 0;
                    $datiSintetici[] = (object)$data;
                }
                
                // Raggruppa per contare rowspan
                $ragioneSocialeCount = [];
                foreach($datiSintetici as $dato) {
                    if (!isset($ragioneSocialeCount[$dato->ragione_sociale])) {
                        $ragioneSocialeCount[$dato->ragione_sociale] = 0;
                    }
                    $ragioneSocialeCount[$dato->ragione_sociale]++;
                }
                
                $prevRagioneSociale = null;
            @endphp
            
            @if(count($datiSintetici) === 0)
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
            
            @foreach($datiSintetici as $dato)
            <tr class="hover:bg-base-200/50 transition-colors">
                @if($prevRagioneSociale !== $dato->ragione_sociale)
                    @php
                        $rowspan = $ragioneSocialeCount[$dato->ragione_sociale];
                        $prevRagioneSociale = $dato->ragione_sociale;
                    @endphp
                    <td class="sticky-table-sintetico-ragione_sociale font-semibold border-r-2 border-base-300 bg-base-100"
                        rowspan="{{ $rowspan }}">
                        {{ $dato->ragione_sociale }}
                    </td>
                @endif

                <td class="sticky-table-sintetico-provenienza text-sm border-r-2 border-base-300 bg-base-100">
                    {{ $dato->provenienza }}
                </td>

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
                <td class="sticky-table-sintetico-ragione_sociale border-r-2 border-base-300" colspan="2">TOTALE GENERALE</td>
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
