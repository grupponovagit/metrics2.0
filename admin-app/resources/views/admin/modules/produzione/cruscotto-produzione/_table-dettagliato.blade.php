{{-- TABELLA DETTAGLIATO --}}
<x-ui.table-advanced id="table-dettagliato" :enable-drag-scroll="true" :enable-column-toggle="false" max-height="70vh" :sticky-columns="[
    ['key' => 'cliente', 'label' => 'Commessa', 'width' => 150, 'wrap' => 'normal'],
    ['key' => 'sede', 'label' => 'Sede', 'width' => 180, 'wrap' => 'normal'],
    ['key' => 'campagna', 'label' => 'Macro Campagna', 'width' => 200, 'wrap' => 'normal'],
]"
    :columns="[
        ['key' => 'prodotto', 'label' => 'Prodotto', 'toggleable' => true],
        ['key' => 'inserito', 'label' => 'Inserito', 'toggleable' => true],
        ['key' => 'ko', 'label' => 'KO', 'toggleable' => true],
        ['key' => 'backlog', 'label' => 'BackLog', 'toggleable' => true],
        ['key' => 'backlog_partner', 'label' => 'BackLog Partner', 'toggleable' => true],
        ['key' => 'ore', 'label' => 'Ore', 'toggleable' => true],
        ['key' => 'resa_prodotto', 'label' => 'Resa Prodotto', 'toggleable' => true],
        ['key' => 'resa_inserito', 'label' => 'Resa Inserito', 'toggleable' => true],
        ['key' => 'resa_oraria', 'label' => 'R/H', 'toggleable' => true],
        ['key' => 'paf', 'label' => 'PAF', 'toggleable' => true],
    ]">
    <x-slot name="table">
        <thead class="bg-base-200" style="background-color: #f3f4f6 !important;">
            <tr>
                <th class="sticky-table-dettagliato-cliente font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important;">
                    Commessa</th>
                <th class="sticky-table-dettagliato-sede font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important;">Sede
                </th>
                <th class="sticky-table-dettagliato-campagna font-bold text-sm uppercase tracking-wider border-r-2 border-base-300 bg-base-200"
                    rowspan="2" style="position: sticky !important; top: 0 !important; z-index: 15 !important;">Macro
                    Campagna</th>

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

                {{-- RESA --}}
                <th class="col-resa_prodotto font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 90px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Resa Prod.</th>
                <th class="col-resa_inserito font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 90px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Resa Ins.</th>
                <th class="col-resa_oraria font-bold text-sm uppercase tracking-wider text-center bg-indigo-100 border-r-2 border-base-300"
                    rowspan="2"
                    style="min-width: 70px; width: auto; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    R/H</th>

                {{-- OBIETTIVI (3 sottocolonne) --}}
                <th class="col-obiettivi font-bold text-sm uppercase tracking-wider text-center bg-teal-100 border-r-2 border-base-300"
                    colspan="3"
                    style="min-width: 240px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Obiettivi</th>

                {{-- PAF MENSILE (3 sottocolonne) --}}
                <th class="col-paf-mensile font-bold text-sm uppercase tracking-wider text-center bg-purple-100 border-r-2 border-base-300"
                    colspan="3"
                    style="min-width: 240px; position: sticky !important; top: 0 !important; z-index: 10 !important;">
                    Paf Mensile</th>
            </tr>
            <tr style="position: sticky !important; top: 48px !important; z-index: 9 !important;">
                {{-- Sottocolonne Obiettivi --}}
                <th class="col-obiettivi col-obiettivi-mensile font-bold text-xs text-center bg-teal-50 border-r border-base-200"
                    style="min-width: 80px; width: auto;">Mensile</th>
                <th class="col-obiettivi col-obiettivi-passo font-bold text-xs text-center bg-teal-50 border-r border-base-200"
                    style="min-width: 80px; width: auto;">Passo Giorno</th>
                <th class="col-obiettivi col-obiettivi-diff font-bold text-xs text-center bg-teal-50 border-r-2 border-base-300"
                    style="min-width: 80px; width: auto;">Diff. Obj</th>

                {{-- Sottocolonne PAF --}}
                <th class="col-paf-mensile col-paf-ore font-bold text-xs text-center bg-purple-50 border-r border-base-200"
                    style="min-width: 80px; width: auto;">Ore Paf</th>
                <th class="col-paf-mensile col-paf-pezzi font-bold text-xs text-center bg-purple-50 border-r border-base-200"
                    style="min-width: 80px; width: auto;">Pezzi Paf</th>
                <th class="col-paf-mensile col-paf-resa font-bold text-xs text-center bg-purple-50 border-r-2 border-base-300"
                    style="min-width: 80px; width: auto;">Resa Paf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($datiRaggruppati as $mandato => $sediData)
                @php
                    $mandatoRowspan = $sediData->sum(fn($campagneData) => $campagneData->count());
                    $firstMandato = true;
                @endphp

                @foreach ($sediData as $sede => $campagneData)
                    @php
                        $sedeRowspan = $campagneData->count();
                        $firstSede = true;
                    @endphp

                    @foreach ($campagneData as $datiCampagna)
                        <tr>
                            {{-- Cliente --}}
                            @if ($firstMandato)
                                <td class="sticky-table-dettagliato-cliente font-bold border-r-2 border-base-300 bg-base-200/30"
                                    rowspan="{{ $mandatoRowspan }}">
                                    {{-- Mostra il nome originale dalla cache (es: TIM_CONSUMER) --}}
                                    {{ collect($campagneData)->first()['cliente_originale'] ?? $mandato }}
                                </td>
                                @php $firstMandato = false; @endphp
                            @endif

                            {{-- Sede --}}
                            @if ($firstSede)
                                <td class="sticky-table-dettagliato-sede font-semibold border-r-2 border-base-300 bg-base-100"
                                    rowspan="{{ $sedeRowspan }}">
                                    {{ $sede }}
                                </td>
                                @php $firstSede = false; @endphp
                            @endif

                            {{-- Campagna/Prodotto --}}
                            <td class="sticky-table-dettagliato-campagna border-r-2 border-base-300">
                                <div class="text-sm font-medium">{{ $datiCampagna['campagna'] }}</div>

                                {{-- Prodotti Aggiuntivi --}}
                                @if (!empty($datiCampagna['prodotti_aggiuntivi']))
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($datiCampagna['prodotti_aggiuntivi'] as $prodAggiuntivo)
                                            <span class="badge badge-xs badge-primary">{{ $prodAggiuntivo }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- Prodotto --}}
                            <td class="col-prodotto text-center text-sm bg-orange-50 border-r-2 border-base-300">
                                {{ number_format($datiCampagna['prodotto_pda']) }}</td>

                            {{-- Inserito --}}
                            <td class="col-inserito text-center text-sm bg-green-50 border-r-2 border-base-300">
                                {{ number_format($datiCampagna['inserito_pda']) }}</td>

                            {{-- KO --}}
                            <td class="col-ko text-center text-sm bg-red-50 border-r-2 border-base-300">
                                {{ number_format($datiCampagna['ko_pda']) }}</td>

                            {{-- BackLog --}}
                            <td class="col-backlog text-center text-sm bg-yellow-50 border-r-2 border-base-300">
                                {{ number_format($datiCampagna['backlog_pda']) }}</td>

                            {{-- BackLog Partner --}}
                            <td class="col-backlog_partner text-center text-sm bg-blue-50 border-r-2 border-base-300">
                                {{ number_format($datiCampagna['backlog_partner_pda']) }}</td>

                            {{-- Ore --}}
                            <td class="col-ore text-center text-sm bg-cyan-50 border-r-2 border-base-300">
                                {{ ($datiCampagna['ore'] ?? 0) > 0 ? number_format($datiCampagna['ore'], 2) : '-' }}
                            </td>

                            {{-- Resa Prodotto --}}
                            <td class="col-resa_prodotto text-center text-sm bg-indigo-50 border-r-2 border-base-300">
                                {{ $datiCampagna['resa_prodotto'] ?? '-' }}
                            </td>

                            {{-- Resa Inserito --}}
                            <td class="col-resa_inserito text-center text-sm bg-indigo-50 border-r-2 border-base-300">
                                {{ $datiCampagna['resa_inserito'] ?? '-' }}
                            </td>

                            {{-- R/H --}}
                            <td class="col-resa_oraria text-center text-sm bg-indigo-50 border-r-2 border-base-300">
                                {{ $datiCampagna['resa_oraria'] ?? '-' }}
                            </td>

                            {{-- OBIETTIVI --}}
                            <td
                                class="col-obiettivi col-obiettivi-mensile text-center text-xs bg-teal-50 border-r border-base-200">
                                {{ $datiCampagna['obiettivo_mensile'] ?? 0 }}</td>
                            <td
                                class="col-obiettivi col-obiettivi-passo text-center text-xs bg-teal-50 border-r border-base-200">
                                {{ $datiCampagna['passo_giorno'] ?? 0 }}</td>
                            <td
                                class="col-obiettivi col-obiettivi-diff text-center text-xs bg-teal-50 border-r-2 border-base-300 {{ ($datiCampagna['differenza_obj'] ?? 0) < 0 ? 'text-green-600 font-bold' : 'text-red-600' }}">
                                {{ $datiCampagna['differenza_obj'] ?? 0 }}
                            </td>

                            {{-- PAF MENSILE --}}
                            <td
                                class="col-paf-mensile col-paf-ore text-center text-xs bg-purple-50 border-r border-base-200">
                                {{ number_format($datiCampagna['ore_paf'] ?? 0, 2) }}</td>
                            <td
                                class="col-paf-mensile col-paf-pezzi text-center text-xs bg-purple-50 border-r border-base-200">
                                {{ number_format($datiCampagna['pezzi_paf'] ?? 0, 0) }}</td>
                            <td
                                class="col-paf-mensile col-paf-resa text-center text-xs bg-purple-50 border-r-2 border-base-300">
                                {{ $datiCampagna['resa_paf'] ?? 0 }}</td>
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

                    foreach ($sediData as $campagneData) {
                        foreach ($campagneData as $dati) {
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
                    $totaleCliente['resa_prodotto'] =
                        $totaleCliente['ore'] > 0
                            ? round($totaleCliente['prodotto_pda'] / $totaleCliente['ore'], 2)
                            : 0;
                    $totaleCliente['resa_inserito'] =
                        $totaleCliente['ore'] > 0
                            ? round($totaleCliente['inserito_pda'] / $totaleCliente['ore'], 2)
                            : 0;
                    $totaleCliente['resa_paf'] =
                        $totaleCliente['ore_paf'] > 0
                            ? round($totaleCliente['pezzi_paf'] / $totaleCliente['ore_paf'], 2)
                            : 0;

                    // Calcoli obiettivi
                    $diffObjCliente = $totaleCliente['obiettivo_mensile'] - $totaleCliente['inserito_pda'];

                    // Passo giorno: solo se ci sono giorni rimanenti E differenza positiva
                    $passoGiornoCliente = 0;
                    if (
                        isset($kpiArray['giorni_lavorativi_rimanenti']) &&
                        $kpiArray['giorni_lavorativi_rimanenti'] > 0 &&
                        $diffObjCliente > 0
                    ) {
                        $passoGiornoCliente = round($diffObjCliente / $kpiArray['giorni_lavorativi_rimanenti'], 2);
                    }
                @endphp
                <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                    <td colspan="3"
                        class="sticky-totale-table-dettagliato text-left text-sm font-bold py-2 px-4 border-r-2 border-slate-300">
                        TOTALE {{ $mandato }}</td>
                    <td class="col-prodotto text-center text-sm bg-orange-100 border-r-2 border-slate-300">
                        {{ number_format($totaleCliente['prodotto_pda']) }}</td>
                    <td class="col-inserito text-center text-sm bg-green-100 border-r-2 border-slate-300">
                        {{ number_format($totaleCliente['inserito_pda']) }}</td>
                    <td class="col-ko text-center text-sm bg-red-100 border-r-2 border-slate-300">
                        {{ number_format($totaleCliente['ko_pda']) }}</td>
                    <td class="col-backlog text-center text-sm bg-yellow-100 border-r-2 border-slate-300">
                        {{ number_format($totaleCliente['backlog_pda']) }}</td>
                    <td class="col-backlog_partner text-center text-sm bg-blue-100 border-r-2 border-slate-300">
                        {{ number_format($totaleCliente['backlog_partner_pda']) }}</td>
                    <td class="col-ore text-center text-sm bg-cyan-100 border-r-2 border-slate-300">
                        {{ number_format($totaleCliente['ore'], 2) }}</td>
                    <td class="col-resa_prodotto text-center text-sm bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totaleCliente['resa_prodotto'] }}</td>
                    <td class="col-resa_inserito text-center text-sm bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totaleCliente['resa_inserito'] }}</td>
                    <td class="col-resa_oraria text-center text-sm bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totaleCliente['resa_oraria'] ?? 0 }}</td>

                    {{-- Obiettivi --}}
                    <td
                        class="col-obiettivi col-obiettivi-mensile text-center text-xs bg-teal-100 border-r border-slate-200">
                        {{ number_format($totaleCliente['obiettivo_mensile'], 0) }}</td>
                    <td
                        class="col-obiettivi col-obiettivi-passo text-center text-xs bg-teal-100 border-r border-slate-200">
                        {{ $passoGiornoCliente }}</td>
                    <td
                        class="col-obiettivi col-obiettivi-diff text-center text-xs bg-teal-100 border-r-2 border-slate-300 {{ $diffObjCliente < 0 ? 'text-green-700 font-bold' : 'text-red-700' }}">
                        {{ number_format($diffObjCliente, 0) }}
                    </td>

                    {{-- PAF Mensile --}}
                    <td class="col-paf-mensile col-paf-ore text-center text-xs bg-purple-100 border-r border-slate-200">
                        {{ number_format($totaleCliente['ore_paf'], 2) }}</td>
                    <td
                        class="col-paf-mensile col-paf-pezzi text-center text-xs bg-purple-100 border-r border-slate-200">
                        {{ number_format($totaleCliente['pezzi_paf'], 0) }}</td>
                    <td
                        class="col-paf-mensile col-paf-resa text-center text-xs bg-purple-100 border-r-2 border-slate-300">
                        {{ $totaleCliente['resa_paf'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="text-center py-12">
                        <div>
                            <h3 class="text-lg font-semibold text-base-content mb-1">Nessun dato disponibile</h3>
                            <p class="text-sm text-base-content/60">Prova a modificare i filtri per visualizzare i dati
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse

            {{-- RIGA TOTALE --}}
            @if (!$datiRaggruppati->isEmpty())
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

                    foreach ($datiRaggruppati as $sediData) {
                        foreach ($sediData as $campagneData) {
                            foreach ($campagneData as $dati) {
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
                    $totali['resa_prodotto'] =
                        $totali['ore'] > 0 ? round($totali['prodotto_pda'] / $totali['ore'], 2) : 0;
                    $totali['resa_inserito'] =
                        $totali['ore'] > 0 ? round($totali['inserito_pda'] / $totali['ore'], 2) : 0;
                    $totali['resa_paf'] =
                        $totali['ore_paf'] > 0 ? round($totali['pezzi_paf'] / $totali['ore_paf'], 2) : 0;
                @endphp
                <tr class="bg-slate-200 font-bold border-t-4 border-slate-400">
                    <td colspan="3"
                        class="sticky-totale-table-dettagliato text-left text-base font-bold py-3 px-4 border-r-2 border-slate-300">
                        TOTALE</td>
                    <td class="col-prodotto text-center text-base bg-orange-100 border-r-2 border-slate-300">
                        {{ number_format($totali['prodotto_pda']) }}</td>
                    <td class="col-inserito text-center text-base bg-green-100 border-r-2 border-slate-300">
                        {{ number_format($totali['inserito_pda']) }}</td>
                    <td class="col-ko text-center text-base bg-red-100 border-r-2 border-slate-300">
                        {{ number_format($totali['ko_pda']) }}</td>
                    <td class="col-backlog text-center text-base bg-yellow-100 border-r-2 border-slate-300">
                        {{ number_format($totali['backlog_pda']) }}</td>
                    <td class="col-backlog_partner text-center text-base bg-blue-100 border-r-2 border-slate-300">
                        {{ number_format($totali['backlog_partner_pda']) }}</td>
                    <td class="col-ore text-center text-base bg-cyan-100 border-r-2 border-slate-300">
                        {{ number_format($totali['ore'], 2) }}</td>
                    <td class="col-resa_prodotto text-center text-base bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totali['resa_prodotto'] }}</td>
                    <td class="col-resa_inserito text-center text-base bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totali['resa_inserito'] }}</td>
                    <td class="col-resa_oraria text-center text-base bg-indigo-100 border-r-2 border-slate-300">
                        {{ $totali['resa_oraria'] ?? 0 }}</td>

                    {{-- Obiettivi (al momento a 0) --}}
                    <td
                        class="col-obiettivi col-obiettivi-mensile text-center text-sm bg-teal-100 border-r border-slate-200">
                        0</td>
                    <td
                        class="col-obiettivi col-obiettivi-passo text-center text-sm bg-teal-100 border-r border-slate-200">
                        0</td>
                    <td
                        class="col-obiettivi col-obiettivi-diff text-center text-sm bg-teal-100 border-r-2 border-slate-300">
                        0</td>

                    {{-- PAF Mensile --}}
                    <td
                        class="col-paf-mensile col-paf-ore text-center text-sm bg-purple-100 border-r border-slate-200">
                        {{ number_format($totali['ore_paf'], 2) }}</td>
                    <td
                        class="col-paf-mensile col-paf-pezzi text-center text-sm bg-purple-100 border-r border-slate-200">
                        {{ number_format($totali['pezzi_paf'], 0) }}</td>
                    <td
                        class="col-paf-mensile col-paf-resa text-center text-sm bg-purple-100 border-r-2 border-slate-300">
                        {{ $totali['resa_paf'] }}</td>
                </tr>
            @endif
        </tbody>
    </x-slot>
</x-ui.table-advanced>
