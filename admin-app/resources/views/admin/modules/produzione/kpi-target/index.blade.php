<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('KPI Target') }}</x-slot>
    
    <x-admin.page-header 
        title="KPI Target" 
        subtitle="Gestione Target Mensili e Rendiconto Produzione"
        icon="bullseye"
        iconColor="secondary"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.produzione.kpi_target.create') }}" class="btn btn-success">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Nuovo KPI
            </a>
            <a href="{{ route('admin.produzione.index') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- FILTRI OBBLIGATORI --}}
    <x-admin.card tone="light" shadow="md" padding="normal" class="mb-6">
        <form method="GET" action="{{ route('admin.produzione.kpi_target') }}" class="space-y-4">
            {{-- Filtri Base: Mese e Anno --}}
            <div class="flex flex-wrap items-end gap-4 pb-4 border-b border-base-300">
                <div class="flex-1 min-w-[200px]">
                    <label for="mese" class="block text-sm font-medium text-base-content mb-2">
                        <x-ui.icon name="calendar" class="h-4 w-4 inline mr-1" />
                        Mese
                    </label>
                    <select name="mese" id="mese" class="select select-bordered w-full">
                        <option value="01" {{ $mese == '01' ? 'selected' : '' }}>Gennaio</option>
                        <option value="02" {{ $mese == '02' ? 'selected' : '' }}>Febbraio</option>
                        <option value="03" {{ $mese == '03' ? 'selected' : '' }}>Marzo</option>
                        <option value="04" {{ $mese == '04' ? 'selected' : '' }}>Aprile</option>
                        <option value="05" {{ $mese == '05' ? 'selected' : '' }}>Maggio</option>
                        <option value="06" {{ $mese == '06' ? 'selected' : '' }}>Giugno</option>
                        <option value="07" {{ $mese == '07' ? 'selected' : '' }}>Luglio</option>
                        <option value="08" {{ $mese == '08' ? 'selected' : '' }}>Agosto</option>
                        <option value="09" {{ $mese == '09' ? 'selected' : '' }}>Settembre</option>
                        <option value="10" {{ $mese == '10' ? 'selected' : '' }}>Ottobre</option>
                        <option value="11" {{ $mese == '11' ? 'selected' : '' }}>Novembre</option>
                        <option value="12" {{ $mese == '12' ? 'selected' : '' }}>Dicembre</option>
                    </select>
                </div>
                
                <div class="flex-1 min-w-[150px]">
                    <label for="anno" class="block text-sm font-medium text-base-content mb-2">
                        Anno
                    </label>
                    <input 
                        type="number" 
                        name="anno" 
                        id="anno" 
                        value="{{ $anno }}" 
                        min="2020" 
                        max="2030"
                        class="input input-bordered w-full"
                    />
                </div>
            </div>
            
            {{-- Filtri per la Ricerca (OBBLIGATORI) --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-base-content">
                        <x-ui.icon name="filter" class="h-4 w-4 inline mr-1 text-warning" />
                        Filtri di Ricerca
                        <span class="badge badge-warning badge-sm ml-2">Seleziona una commessa per iniziare</span>
                    </h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    {{-- Filtro Commessa (PRIMO LIVELLO) --}}
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text font-semibold text-xs">
                                1. Commessa
                            </span>
                            <div class="flex gap-1">
                                <button type="button" onclick="toggleAll('commessa', true)" class="btn btn-xs btn-success">
                                    <x-ui.icon name="check" class="h-3 w-3" />
                                </button>
                                <button type="button" onclick="toggleAll('commessa', false)" class="btn btn-xs btn-outline btn-success">
                                    <x-ui.icon name="x" class="h-3 w-3" />
                                </button>
                            </div>
                        </label>
                        <div class="border border-base-300 rounded-lg p-2 h-[120px] overflow-y-auto bg-base-100">
                            @foreach($commesse as $commessa)
                                <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                    <input type="checkbox" name="filter_commessa[]" value="{{ $commessa }}" 
                                           class="checkbox checkbox-xs commessa-checkbox" 
                                           onchange="debounce(loadSedi, 'sedi', 200)"
                                           {{ is_array(request('filter_commessa')) && in_array($commessa, request('filter_commessa')) ? 'checked' : '' }}>
                                    <span class="text-xs">{{ $commessa }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Filtro Sede CRM (SECONDO LIVELLO) --}}
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text font-semibold text-xs">
                                2. Sede CRM
                            </span>
                            <div class="flex gap-1">
                                <button type="button" onclick="toggleAll('sede', true)" class="btn btn-xs btn-success">
                                    <x-ui.icon name="check" class="h-3 w-3" />
                                </button>
                                <button type="button" onclick="toggleAll('sede', false)" class="btn btn-xs btn-outline btn-success">
                                    <x-ui.icon name="x" class="h-3 w-3" />
                                </button>
                            </div>
                        </label>
                        <div id="sedeContainer" class="border border-base-300 rounded-lg p-2 h-[120px] overflow-y-auto bg-base-100">
                            @if($sediFiltered->isNotEmpty())
                                @foreach($sediFiltered as $sede)
                                    <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                        <input type="checkbox" name="filter_sede[]" value="{{ $sede }}" 
                                               class="checkbox checkbox-xs sede-checkbox uppercase" 
                                               onchange="debounce(loadMacroCampagne, 'macro', 200)"
                                               {{ is_array(request('filter_sede')) && in_array($sede, request('filter_sede')) ? 'checked' : '' }}>
                                        <span class="text-xs uppercase">{{ strtoupper($sede) }}</span>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-xs text-base-content/50 text-center py-4">Seleziona una commessa</p>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Filtro Macro Campagna (TERZO LIVELLO) --}}
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text font-semibold text-xs">
                                3. Macro Campagna
                            </span>
                            <div class="flex gap-1">
                                <button type="button" onclick="toggleAll('macro', true)" class="btn btn-xs btn-success">
                                    <x-ui.icon name="check" class="h-3 w-3" />
                                </button>
                                <button type="button" onclick="toggleAll('macro', false)" class="btn btn-xs btn-outline btn-success">
                                    <x-ui.icon name="x" class="h-3 w-3" />
                                </button>
                            </div>
                        </label>
                        <div id="macroContainer" class="border border-base-300 rounded-lg p-2 h-[120px] overflow-y-auto bg-base-100">
                            @if($macroCampagneFiltered->isNotEmpty())
                                @foreach($macroCampagneFiltered as $macro)
                                    <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                        <input type="checkbox" name="filter_macro_campagna[]" value="{{ $macro }}" 
                                               class="checkbox checkbox-xs macro-checkbox uppercase" 
                                               onchange="debounce(loadNomiKpi, 'kpi', 200)"
                                               {{ is_array(request('filter_macro_campagna')) && in_array($macro, request('filter_macro_campagna')) ? 'checked' : '' }}>
                                        <span class="text-xs uppercase">{{ strtoupper($macro) }}</span>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-xs text-base-content/50 text-center py-4">Seleziona sedi</p>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Filtro Nome KPI (QUARTO LIVELLO) --}}
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text font-semibold text-xs">
                                4. Nome KPI
                            </span>
                            <div class="flex gap-1">
                                <button type="button" onclick="toggleAll('kpi', true)" class="btn btn-xs btn-success">
                                    <x-ui.icon name="check" class="h-3 w-3" />
                                </button>
                                <button type="button" onclick="toggleAll('kpi', false)" class="btn btn-xs btn-outline btn-success">
                                    <x-ui.icon name="x" class="h-3 w-3" />
                                </button>
                            </div>
                        </label>
                        <div id="kpiContainer" class="border border-base-300 rounded-lg p-2 h-[120px] overflow-y-auto bg-base-100">
                            @if($nomiKpiFiltered->isNotEmpty())
                                @foreach($nomiKpiFiltered as $nomeKpi)
                                    <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                        <input type="checkbox" name="filter_nome_kpi[]" value="{{ $nomeKpi }}" 
                                               class="checkbox checkbox-xs kpi-checkbox" 
                                               onchange="debounce(loadTipologie, 'tipologie', 200)"
                                               {{ is_array(request('filter_nome_kpi')) && in_array($nomeKpi, request('filter_nome_kpi')) ? 'checked' : '' }}>
                                        <span class="text-xs">{{ $nomeKpi }}</span>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-xs text-base-content/50 text-center py-4">Seleziona macro campagne</p>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Filtro Tipologia Obiettivo (QUINTO LIVELLO) --}}
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text font-semibold text-xs">
                                5. Tipologia Obiettivo
                            </span>
                            <div class="flex gap-1">
                                <button type="button" onclick="toggleAll('tipologia', true)" class="btn btn-xs btn-success">
                                    <x-ui.icon name="check" class="h-3 w-3" />
                                </button>
                                <button type="button" onclick="toggleAll('tipologia', false)" class="btn btn-xs btn-outline btn-success">
                                    <x-ui.icon name="x" class="h-3 w-3" />
                                </button>
                            </div>
                        </label>
                        <div id="tipologiaContainer" class="border border-base-300 rounded-lg p-2 h-[120px] overflow-y-auto bg-base-100">
                            @if($tipologieObiettivoFiltered->isNotEmpty())
                                @foreach($tipologieObiettivoFiltered as $tipologia)
                                    <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                        <input type="checkbox" name="filter_tipologia_obiettivo[]" value="{{ $tipologia }}" 
                                               class="checkbox checkbox-xs tipologia-checkbox uppercase"
                                               {{ is_array(request('filter_tipologia_obiettivo')) && in_array($tipologia, request('filter_tipologia_obiettivo')) ? 'checked' : '' }}>
                                        <span class="text-xs uppercase">{{ strtoupper($tipologia) }}</span>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-xs text-base-content/50 text-center py-4">Seleziona nomi KPI</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Pulsanti Azione --}}
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <x-ui.icon name="search" class="h-4 w-4" />
                        Cerca
                    </button>
                    
                    @if($hasFiltri)
                        <a href="{{ route('admin.produzione.kpi_target', ['anno' => $anno, 'mese' => $mese]) }}" class="btn btn-outline btn-secondary btn-sm">
                            <x-ui.icon name="x" class="h-4 w-4" />
                            Reset Filtri
                        </a>
                    @endif
                    
                    <button type="button" onclick="openInizializzaMeseModal()" class="btn btn-success btn-outline btn-sm ml-auto">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Inizializza Mese
                    </button>
                </div>
            </div>
            
            {{-- Info Filtro Attivo --}}
            @if($hasFiltri)
                <div class="text-xs text-base-content/60 pt-3 border-t border-base-300">
                    <strong>Periodo:</strong> {{ date('F Y', mktime(0, 0, 0, $mese, 1, $anno)) }}
                    <span class="mx-2">•</span>
                    <span class="badge badge-success badge-sm">
                        <x-ui.icon name="check" class="h-3 w-3 inline mr-1" />
                        Filtri Attivi: 
                        @if(!empty($filterCommessa)) Commessa ({{ count($filterCommessa) }}) @endif
                        @if(!empty($filterSede)) Sede ({{ count($filterSede) }}) @endif
                        @if(!empty($filterMacroCampagna)) Macro Campagna ({{ count($filterMacroCampagna) }}) @endif
                        @if(!empty($filterNomeKpi)) Nome KPI ({{ count($filterNomeKpi) }}) @endif
                        @if(!empty($filterTipologiaObiettivo)) Tipologia ({{ count($filterTipologiaObiettivo) }}) @endif
                    </span>
                </div>
            @endif
        </form>
    </x-admin.card>
    
    {{-- TABS --}}
    @if($hasFiltri)
        <div role="tablist" class="tabs tabs-boxed bg-base-200 mb-6">
            <a role="tab" class="tab tab-active" id="tab-target">
                <x-ui.icon name="chart-bar" class="h-4 w-4 mr-2" />
                Target Mensili (Pianificazione)
            </a>
            {{-- Tab Rendiconto nascosta temporaneamente --}}
            {{-- <a role="tab" class="tab" id="tab-rendiconto" onclick="switchTab('rendiconto')">
                <x-ui.icon name="chart-line" class="h-4 w-4 mr-2" />
                Rendiconto Produzione (Consuntivo)
            </a> --}}
        </div>
    @endif
    
    {{-- MESSAGGIO: NESSUN FILTRO APPLICATO --}}
    @if(!$hasFiltri)
        <x-admin.card tone="light" shadow="md" padding="normal">
            <div class="text-center py-12">
                <x-ui.icon name="filter" class="h-16 w-16 mx-auto text-base-content/30 mb-4" />
                <h3 class="text-lg font-semibold text-base-content mb-2">
                    Nessun Filtro Applicato
                </h3>
                <p class="text-sm text-base-content/60 mb-4">
                    Per visualizzare i dati, applica almeno un filtro di ricerca (Commessa, Sede, Macro Campagna, Nome KPI o Tipologia Obiettivo).
                </p>
                <div class="badge badge-warning">
                    <x-ui.icon name="info-circle" class="h-4 w-4 mr-1" />
                    Utilizza i filtri sopra per cercare i KPI Target
                </div>
            </div>
        </x-admin.card>
    @endif
    
    {{-- TABELLA 1: TARGET MENSILI --}}
    @if($hasFiltri)
    <x-admin.card tone="light" shadow="lg" padding="none" id="table-target">
        <div class="p-6 border-b border-base-300 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-base-content">
                    Target Mensili - {{ date('F Y', mktime(0, 0, 0, $mese, 1, $anno)) }}
                </h3>
                <p class="text-sm text-base-content/60 mt-1">
                    KPI di target o obiettivi mensili (dati di pianificazione)
                    <br>
                    <span class="font-semibold">{{ $targetMensili->total() }}</span> record totali
                    @if($targetMensili->hasPages())
                        | Pagina {{ $targetMensili->currentPage() }} di {{ $targetMensili->lastPage() }}
                    @endif
                </p>
            </div>
            
            {{-- Pulsanti Bulk Actions --}}
            <div class="flex gap-2" id="bulk-actions-target" style="display: none;">
                <button type="button" onclick="bulkDeleteTarget()" class="btn btn-error btn-sm">
                    <x-ui.icon name="trash" class="h-4 w-4" />
                    Elimina Selezionati
                </button>
                <button type="button" onclick="deselectAllTarget()" class="btn btn-outline btn-sm">
                    Deseleziona Tutto
                </button>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.produzione.kpi_target.update') }}" id="form-target">
            @csrf
            <input type="hidden" name="tabella" value="target_mensili">
            
            <div class="overflow-x-auto">
                <table class="table table-zebra table-compact w-full text-sm">
                    <thead class="bg-base-200 sticky top-0">
                        <tr class="text-xs">
                            <th class="w-10">
                                <input type="checkbox" id="select-all-target" class="checkbox checkbox-xs" onchange="toggleAllTarget(this)">
                            </th>
                            <th class="font-bold">Commessa</th>
                            <th class="font-bold">Sede CRM</th>
                            <th class="font-bold">Macro Campagna</th>
                            <th class="font-bold">Nome KPI</th>
                            <th class="font-bold">Tipo KPI</th>
                            <th class="font-bold">Tipologia Obiettivo</th>
                            <th class="font-bold text-center">Anno</th>
                            <th class="font-bold text-center">Mese</th>
                            <th class="font-bold text-center">Valore</th>
                            <th class="font-bold text-center">Variazione KPI</th>
                            <th class="font-bold text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($targetMensili as $kpi)
                            <tr class="hover:bg-base-200/50">
                                <td class="py-2">
                                    <input type="checkbox" class="checkbox checkbox-xs kpi-checkbox-target" value="{{ $kpi->id }}" onchange="updateBulkActionsTarget()">
                                </td>
                                <td class="font-medium editable-cell py-2" contenteditable="true" data-field="commessa" data-id="{{ $kpi->id }}" data-original="{{ $kpi->commessa }}">{{ $kpi->commessa }}</td>
                                
                                {{-- SELECT SEDE CRM --}}
                                <td class="py-2">
                                    <select 
                                        class="select select-md select-bordered w-full sede-select uppercase" 
                                        data-field="sede_crm" 
                                        data-id="{{ $kpi->id }}" 
                                        onchange="saveFieldChangeSelect(this)"
                                        style="text-transform: uppercase;">
                                        <option value="">-- Non assegnata --</option>
                                        @foreach($sediSelect as $sede)
                                            @php
                                                // Determina se questa sede è selezionata
                                                $isSelected = ($kpi->sede_crm == $sede->id_sede) ||
                                                             ($kpi->sede_crm == $sede->nome_sede);
                                            @endphp
                                            <option value="{{ $sede->id_sede }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ strtoupper($sede->nome_sede) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                {{-- SELECT MACRO CAMPAGNA --}}
                                <td class="py-2">
                                    <select 
                                        class="select select-md select-bordered w-full uppercase" 
                                        data-field="macro_campagna" 
                                        data-id="{{ $kpi->id }}" 
                                        onchange="saveFieldChangeSelect(this)"
                                        style="text-transform: uppercase;">
                                        <option value="TUTTE" {{ ($kpi->macro_campagna ?? 'TUTTE') == 'TUTTE' ? 'selected' : '' }}>TUTTE</option>
                                        @foreach($macroCampagne as $macro)
                                            <option value="{{ $macro }}" {{ ($kpi->macro_campagna ?? '') == $macro ? 'selected' : '' }}>
                                                {{ strtoupper($macro) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                {{-- SELECT NOME KPI --}}
                                <td class="py-2">
                                    <select 
                                        class="select select-md select-bordered w-full" 
                                        data-field="nome_kpi" 
                                        data-id="{{ $kpi->id }}" 
                                        onchange="saveFieldChangeSelect(this)">
                                        @foreach($nomiKpi as $nomeKpi)
                                            <option value="{{ $nomeKpi }}" {{ $kpi->nome_kpi == $nomeKpi ? 'selected' : '' }}>
                                                {{ $nomeKpi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                {{-- TIPO KPI - Badge con Select al click --}}
                                <td class="relative py-2">
                                    <div class="tipo-kpi-container" data-id="{{ $kpi->id }}">
                                        {{-- Badge visibile --}}
                                        <span 
                                            class="badge badge-primary badge-sm cursor-pointer hover:badge-primary-focus transition-all tipo-kpi-badge" 
                                            onclick="toggleTipoKpiSelect({{ $kpi->id }})"
                                            id="badge-tipo-{{ $kpi->id }}">
                                            {{ strtoupper($kpi->tipo_kpi ?? 'Non assegnato') }}
                                        </span>
                                        
                                        {{-- Select nascosta --}}
                                        <select 
                                            class="select select-md select-bordered absolute top-0 left-0 w-full hidden uppercase tipo-kpi-select" 
                                            data-field="tipo_kpi" 
                                            data-id="{{ $kpi->id }}" 
                                            id="select-tipo-{{ $kpi->id }}"
                                            onchange="saveTipoKpi(this)"
                                            onblur="hideTipoKpiSelect({{ $kpi->id }})"
                                            style="text-transform: uppercase; z-index: 10;">
                                            <option value="">NON ASSEGNATO</option>
                                            <option value="RESIDENZIALI" {{ strtoupper($kpi->tipo_kpi ?? '') == 'RESIDENZIALI' ? 'selected' : '' }}>RESIDENZIALI</option>
                                            <option value="BUSINESS" {{ strtoupper($kpi->tipo_kpi ?? '') == 'BUSINESS' ? 'selected' : '' }}>BUSINESS</option>
                                        </select>
                                    </div>
                                </td>
                                
                                {{-- SELECT TIPOLOGIA OBIETTIVO --}}
                                <td class="py-2">
                                    <select 
                                        class="select select-md select-bordered w-full uppercase" 
                                        data-field="tipologia_obiettivo" 
                                        data-id="{{ $kpi->id }}" 
                                        onchange="saveFieldChangeSelect(this)"
                                        style="text-transform: uppercase;">
                                        @foreach($tipologieObiettivo as $tipologia)
                                            <option value="{{ $tipologia }}" {{ $kpi->tipologia_obiettivo == $tipologia ? 'selected' : '' }}>
                                                {{ strtoupper($tipologia) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                <td class="text-center py-2">{{ $kpi->anno }}</td>
                                <td class="text-center py-2">{{ $kpi->mese }}</td>
                                <td class="text-center font-semibold editable-cell py-2" contenteditable="true" data-field="valore_kpi" data-id="{{ $kpi->id }}" data-original="{{ $kpi->valore_kpi }}">{{ number_format($kpi->valore_kpi, 2) }}</td>
                                <td class="text-center py-2">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($kpi->kpi_variato)
                                            <div class="flex flex-col items-start text-xs">
                                                <span class="badge badge-warning badge-xs">{{ number_format($kpi->kpi_variato, 2) }}</span>
                                                <span class="text-xs text-base-content/60">
                                                    {{ $kpi->data_validita_inizio ? \Carbon\Carbon::parse($kpi->data_validita_inizio)->format('d/m') : '' }}
                                                    @if($kpi->data_validita_fine)
                                                        - {{ \Carbon\Carbon::parse($kpi->data_validita_fine)->format('d/m') }}
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <span class="badge badge-ghost badge-xs">Nessuna</span>
                                        @endif
                                        <button type="button" onclick="openVariazioneModal({{ json_encode($kpi) }})" class="btn btn-xs btn-outline" title="Modifica Variazione">
                                            <x-ui.icon name="pencil" class="h-3 w-3" />
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center py-2">
                                    <div class="flex gap-1 justify-center">
                                        <a href="{{ route('admin.produzione.kpi_target.show', $kpi->id) }}" class="btn btn-xs btn-info" title="Visualizza">
                                            <x-ui.icon name="eye" class="h-3 w-3" />
                                        </a>
                                        <form action="{{ route('admin.produzione.kpi_target.delete', $kpi->id) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo KPI?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-error" title="Elimina">
                                                <x-ui.icon name="trash" class="h-3 w-3" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-8">
                                    <div>
                                        <h3 class="text-base font-semibold text-base-content mb-1">Nessun target trovato</h3>
                                        <p class="text-sm text-base-content/60">Prova con filtri diversi o crea un nuovo KPI</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINAZIONE --}}
            @if($targetMensili->hasPages())
                <div class="p-6 bg-base-200/50 border-t border-base-300">
                    <div class="flex justify-center">
                        {{ $targetMensili->appends(['anno' => $anno, 'mese' => $mese])->links() }}
                    </div>
                </div>
            @endif
            
            @if($targetMensili->count() > 0)
                <div class="p-6 bg-base-200/50 border-t border-base-300 flex justify-end">
                    <button type="submit" class="btn btn-success">
                        <x-ui.icon name="save" class="h-4 w-4" />
                        Salva Modifiche
                    </button>
                </div>
            @endif
        </form>
    </x-admin.card>
    @endif
    
    {{-- TABELLA 2: RENDICONTO PRODUZIONE --}}
    <x-admin.card tone="light" shadow="lg" padding="none" id="table-rendiconto" class="hidden">
        <div class="p-6 border-b border-base-300">
            <h3 class="text-xl font-bold text-base-content">
                Rendiconto Produzione (Consuntivo)
            </h3>
            <p class="text-sm text-base-content/60 mt-1">
                KPI effettivi o consuntivo produzione (dati di esecuzione)
            </p>
        </div>
        
        <form method="POST" action="{{ route('admin.produzione.kpi_target.update') }}" id="form-rendiconto">
            @csrf
            <input type="hidden" name="tabella" value="rendiconto_produzione">
            
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200 sticky top-0">
                        <tr>
                            <th class="font-bold">Commessa</th>
                            <th class="font-bold">Istanza</th>
                            <th class="font-bold">Servizio/Mandato</th>
                            <th class="font-bold">Macrocampagna</th>
                            <th class="font-bold">Nome KPI</th>
                            <th class="font-bold text-center">Valore</th>
                            <th class="font-bold">Descrizione</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendicontoProduzione as $kpi)
                            <tr class="hover:bg-base-200/50">
                                <td class="font-medium">{{ $kpi->commessa }}</td>
                                <td>{{ $kpi->istanza }}</td>
                                <td>{{ $kpi->servizio_mandato }}</td>
                                <td>{{ $kpi->macrocampagna }}</td>
                                <td>{{ $kpi->nome_kpi }}</td>
                                <td class="text-center">
                                    <input 
                                        type="number" 
                                        name="kpi[{{ $kpi->id }}]" 
                                        value="{{ $kpi->valore_kpi }}"
                                        step="1"
                                        min="0"
                                        class="input input-sm input-bordered w-24 text-center"
                                    />
                                </td>
                                <td class="text-sm text-base-content/70">{{ $kpi->descrizione }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content mb-1">Nessun rendiconto trovato</h3>
                                        <p class="text-sm text-base-content/60">Nessun dato disponibile</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($rendicontoProduzione->count() > 0)
                <div class="p-6 bg-base-200/50 border-t border-base-300 flex justify-end">
                    <button type="submit" class="btn btn-success">
                        <x-ui.icon name="save" class="h-4 w-4" />
                        Salva Modifiche
                    </button>
                </div>
            @endif
        </form>
    </x-admin.card>
    
    {{-- MESSAGGI --}}
    @if(session('success'))
        <div class="alert alert-success mt-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error mt-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    
    {{-- Form nascosto per bulk delete --}}
    <form id="bulk-delete-form-target" action="{{ route('admin.produzione.kpi_target.bulk_delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulk-delete-ids-target">
    </form>
    
    {{-- MODAL INIZIALIZZA MESE --}}
    <dialog id="inizializza-mese-modal" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4">
                <x-ui.icon name="plus-circle" class="h-5 w-5 inline text-success" />
                Inizializza Target per Nuovo Mese
            </h3>
            
            <form id="form-inizializza-mese" action="{{ route('admin.produzione.kpi_target.inizializza_mese') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    {{-- Alert Info --}}
                    <div class="alert alert-info">
                        <x-ui.icon name="info-circle" class="h-5 w-5" />
                        <div>
                            <p class="font-semibold">Cosa fa questa funzione?</p>
                            <p class="text-sm">Copia tutti i target del mese precedente nel mese selezionato, inizializzando i valori a 0. Successivamente potrai modificare manualmente i valori.</p>
                        </div>
                    </div>
                    
                    {{-- Mese di Destinazione --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Mese Destinazione <span class="text-error">*</span></span>
                            </label>
                            <select name="mese_destinazione" id="init-mese" class="select select-bordered" required>
                                <option value="">-- Seleziona mese --</option>
                                <option value="01">Gennaio</option>
                                <option value="02">Febbraio</option>
                                <option value="03">Marzo</option>
                                <option value="04">Aprile</option>
                                <option value="05">Maggio</option>
                                <option value="06">Giugno</option>
                                <option value="07">Luglio</option>
                                <option value="08">Agosto</option>
                                <option value="09">Settembre</option>
                                <option value="10">Ottobre</option>
                                <option value="11">Novembre</option>
                                <option value="12">Dicembre</option>
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Anno Destinazione <span class="text-error">*</span></span>
                            </label>
                            <input 
                                type="number" 
                                name="anno_destinazione" 
                                id="init-anno"
                                value="{{ date('Y') }}" 
                                min="2020" 
                                max="2030"
                                class="input input-bordered"
                                required
                            />
                        </div>
                    </div>
                    
                    {{-- Anteprima Sorgente --}}
                    <div class="bg-base-200 p-4 rounded-lg">
                        <p class="text-sm font-semibold mb-2">
                            <x-ui.icon name="arrow-down" class="h-4 w-4 inline" />
                            Verrà copiata la struttura dal mese precedente:
                        </p>
                        <p class="text-sm text-base-content/70" id="preview-mese-sorgente">
                            (seleziona un mese per vedere l'anteprima)
                        </p>
                    </div>
                    
                    {{-- Alert Warning --}}
                    <div class="alert alert-warning">
                        <x-ui.icon name="exclamation-triangle" class="h-5 w-5" />
                        <div>
                            <p class="font-semibold">Attenzione!</p>
                            <p class="text-sm">Se esistono già target per il mese selezionato, questa operazione li eliminerà e li sostituirà con i nuovi.</p>
                        </div>
                    </div>
                </div>
                
                <div class="modal-action">
                    <button type="button" class="btn" onclick="closeInizializzaMeseModal()">Annulla</button>
                    <button type="submit" class="btn btn-success">
                        <x-ui.icon name="check" class="h-4 w-4" />
                        Conferma Inizializzazione
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    
    {{-- MODAL VARIAZIONE KPI --}}
    <dialog id="variazione-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">
                <x-ui.icon name="chart-line" class="h-5 w-5 inline text-warning" />
                Gestisci Variazione KPI
            </h3>
            
            <form id="form-variazione">
                <input type="hidden" id="variazione-kpi-id">
                
                <div class="space-y-4">
                    {{-- KPI Variato --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Nuovo Valore KPI</span>
                        </label>
                        <input 
                            type="number" 
                            id="variazione-kpi-variato"
                            step="0.01"
                            min="0"
                            placeholder="Lascia vuoto per rimuovere variazione"
                            class="input input-bordered"
                        />
                        <label class="label">
                            <span class="label-text-alt">Valore KPI modificato (se cambia nel mese)</span>
                        </label>
                    </div>
                    
                    {{-- Data Inizio --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Data Cambio <span class="text-error" id="label-required">*</span></span>
                        </label>
                        <input 
                            type="date" 
                            id="variazione-data-inizio"
                            class="input input-bordered"
                        />
                        <label class="label">
                            <span class="label-text-alt">Da quale giorno si applica il nuovo valore</span>
                        </label>
                    </div>
                    
                    {{-- Data Fine --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Data Fine (opzionale)</span>
                        </label>
                        <input 
                            type="date" 
                            id="variazione-data-fine"
                            class="input input-bordered"
                        />
                        <label class="label">
                            <span class="label-text-alt">Lascia vuoto per applicare fino a fine mese</span>
                        </label>
                    </div>
                    
                    {{-- Alert Info --}}
                    <div class="alert alert-info">
                        <x-ui.icon name="info-circle" class="h-5 w-5" />
                        <div>
                            <p class="font-semibold">Come funziona:</p>
                            <p class="text-sm">Se imposti un nuovo valore KPI con una data, il sistema userà il valore iniziale fino al giorno prima e il nuovo valore dalla data specificata in poi.</p>
                        </div>
                    </div>
                </div>
                
                <div class="modal-action">
                    <button type="button" class="btn" onclick="closeVariazioneModal()">Annulla</button>
                    <button type="button" class="btn btn-error" onclick="rimuoviVariazione()" id="btn-rimuovi-variazione">
                        <x-ui.icon name="trash" class="h-4 w-4" />
                        Rimuovi Variazione
                    </button>
                    <button type="button" class="btn btn-success" onclick="salvaVariazione()">
                        <x-ui.icon name="save" class="h-4 w-4" />
                        Salva
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    
    <script>
        // ===== GESTIONE FILTRI MULTI-SELEZIONE =====
        let lastChecked = {};
        let debounceTimers = {};
        
        // Funzione debounce per evitare chiamate multiple
        function debounce(func, key, delay = 300) {
            if (debounceTimers[key]) {
                clearTimeout(debounceTimers[key]);
            }
            debounceTimers[key] = setTimeout(func, delay);
        }
        
        // Funzione per selezionare/deselezionare tutte le checkbox di un tipo
        function toggleAll(type, checked) {
            const checkboxes = document.querySelectorAll(`.${type}-checkbox`);
            checkboxes.forEach(cb => {
                cb.checked = checked;
            });
            
            // Trigger caricamento successivo se necessario
            if (type === 'commessa') {
                debounce(loadSedi, 'sedi', 100);
            } else if (type === 'sede') {
                debounce(loadMacroCampagne, 'macro', 100);
            } else if (type === 'macro') {
                debounce(loadNomiKpi, 'kpi', 100);
            } else if (type === 'kpi') {
                debounce(loadTipologie, 'tipologie', 100);
            }
        }
        
        // ===== FILTRI CONCATENATI =====
        function loadSedi() {
            const commesse = Array.from(document.querySelectorAll('.commessa-checkbox:checked')).map(cb => cb.value);
            const sedeContainer = document.getElementById('sedeContainer');
            const anno = document.getElementById('anno').value;
            const mese = document.getElementById('mese').value;
            
            console.log('loadSedi chiamato con commesse:', commesse);
            
            // Reset filtri successivi
            document.getElementById('macroContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona sedi</p>';
            document.getElementById('kpiContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona macro campagne</p>';
            document.getElementById('tipologiaContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona nomi KPI</p>';
            
            if (commesse.length === 0) {
                sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona una commessa</p>';
                return;
            }
            
            sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Caricamento...</p>';
            
            const params = new URLSearchParams();
            commesse.forEach(c => params.append('commesse[]', c));
            params.append('anno', anno);
            params.append('mese', mese);
            
            console.log('Fetching sedi:', `/admin/produzione/kpi-target/get-sedi?${params.toString()}`);
            
            fetch(`/admin/produzione/kpi-target/get-sedi?${params.toString()}`)
                .then(response => {
                    console.log('Risposta sedi:', response.status);
                    if (!response.ok) throw new Error('Errore nel caricamento sedi');
                    return response.json();
                })
                .then(sedi => {
                    console.log('Sedi ricevute:', sedi);
                    
                    let html = '';
                    
                    if (sedi.length > 0) {
                        sedi.forEach(sede => {
                            html += `
                                <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                    <input type="checkbox" name="filter_sede[]" value="${sede}" 
                                           class="checkbox checkbox-xs sede-checkbox uppercase" 
                                           onchange="debounce(loadMacroCampagne, 'macro', 200)">
                                    <span class="text-xs uppercase">${sede.toUpperCase()}</span>
                                </label>
                            `;
                        });
                    } else {
                        html += '<p class="text-xs text-base-content/50 text-center py-4">Nessuna sede disponibile</p>';
                    }
                    
                    sedeContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Errore loadSedi:', error);
                    sedeContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore nel caricamento</p>';
                });
        }
        
        // Carica macro campagne in base a commesse e sedi
        function loadMacroCampagne() {
            const commesse = Array.from(document.querySelectorAll('.commessa-checkbox:checked')).map(cb => cb.value);
            const sedi = Array.from(document.querySelectorAll('.sede-checkbox:checked')).map(cb => cb.value);
            const macroContainer = document.getElementById('macroContainer');
            const anno = document.getElementById('anno').value;
            const mese = document.getElementById('mese').value;
            
            console.log('loadMacroCampagne chiamato con sedi:', sedi);
            
            // Reset filtri successivi
            document.getElementById('kpiContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona macro campagne</p>';
            document.getElementById('tipologiaContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona nomi KPI</p>';
            
            if (sedi.length === 0) {
                macroContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona sedi</p>';
                return;
            }
            
            macroContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Caricamento...</p>';
            
            const params = new URLSearchParams();
            commesse.forEach(c => params.append('commesse[]', c));
            sedi.forEach(s => params.append('sedi[]', s));
            params.append('anno', anno);
            params.append('mese', mese);
            
            fetch(`/admin/produzione/kpi-target/get-macro-campagne?${params.toString()}`)
                .then(response => {
                    if (!response.ok) throw new Error('Errore nel caricamento macro campagne');
                    return response.json();
                })
                .then(macroCampagne => {
                    console.log('Macro campagne ricevute:', macroCampagne);
                    
                    let html = '';
                    
                    if (macroCampagne.length > 0) {
                        macroCampagne.forEach(macro => {
                            html += `
                                <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                    <input type="checkbox" name="filter_macro_campagna[]" value="${macro}" 
                                           class="checkbox checkbox-xs macro-checkbox uppercase" 
                                           onchange="debounce(loadNomiKpi, 'kpi', 200)">
                                    <span class="text-xs uppercase">${macro.toUpperCase()}</span>
                                </label>
                            `;
                        });
                    } else {
                        html += '<p class="text-xs text-base-content/50 text-center py-4">Nessuna macro campagna disponibile</p>';
                    }
                    
                    macroContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Errore loadMacroCampagne:', error);
                    macroContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore nel caricamento</p>';
                });
        }
        
        // Carica nomi KPI
        function loadNomiKpi() {
            const commesse = Array.from(document.querySelectorAll('.commessa-checkbox:checked')).map(cb => cb.value);
            const sedi = Array.from(document.querySelectorAll('.sede-checkbox:checked')).map(cb => cb.value);
            const macroCampagne = Array.from(document.querySelectorAll('.macro-checkbox:checked')).map(cb => cb.value);
            const kpiContainer = document.getElementById('kpiContainer');
            const anno = document.getElementById('anno').value;
            const mese = document.getElementById('mese').value;
            
            console.log('loadNomiKpi chiamato con macro campagne:', macroCampagne);
            
            // Reset filtri successivi
            document.getElementById('tipologiaContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona nomi KPI</p>';
            
            if (macroCampagne.length === 0) {
                kpiContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona macro campagne</p>';
                return;
            }
            
            kpiContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Caricamento...</p>';
            
            const params = new URLSearchParams();
            commesse.forEach(c => params.append('commesse[]', c));
            sedi.forEach(s => params.append('sedi[]', s));
            macroCampagne.forEach(m => params.append('macro_campagne[]', m));
            params.append('anno', anno);
            params.append('mese', mese);
            
            fetch(`/admin/produzione/kpi-target/get-nomi-kpi?${params.toString()}`)
                .then(response => {
                    if (!response.ok) throw new Error('Errore nel caricamento nomi KPI');
                    return response.json();
                })
                .then(nomiKpi => {
                    console.log('Nomi KPI ricevuti:', nomiKpi);
                    
                    let html = '';
                    
                    if (nomiKpi.length > 0) {
                        nomiKpi.forEach(nomeKpi => {
                            html += `
                                <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                    <input type="checkbox" name="filter_nome_kpi[]" value="${nomeKpi}" 
                                           class="checkbox checkbox-xs kpi-checkbox" 
                                           onchange="debounce(loadTipologie, 'tipologie', 200)">
                                    <span class="text-xs">${nomeKpi}</span>
                                </label>
                            `;
                        });
                    } else {
                        html += '<p class="text-xs text-base-content/50 text-center py-4">Nessun KPI disponibile</p>';
                    }
                    
                    kpiContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Errore loadNomiKpi:', error);
                    kpiContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore nel caricamento</p>';
                });
        }
        
        // Carica tipologie obiettivo
        function loadTipologie() {
            const commesse = Array.from(document.querySelectorAll('.commessa-checkbox:checked')).map(cb => cb.value);
            const sedi = Array.from(document.querySelectorAll('.sede-checkbox:checked')).map(cb => cb.value);
            const macroCampagne = Array.from(document.querySelectorAll('.macro-checkbox:checked')).map(cb => cb.value);
            const nomiKpi = Array.from(document.querySelectorAll('.kpi-checkbox:checked')).map(cb => cb.value);
            const tipologiaContainer = document.getElementById('tipologiaContainer');
            const anno = document.getElementById('anno').value;
            const mese = document.getElementById('mese').value;
            
            console.log('loadTipologie chiamato con nomi KPI:', nomiKpi);
            
            if (nomiKpi.length === 0) {
                tipologiaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona nomi KPI</p>';
                return;
            }
            
            tipologiaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Caricamento...</p>';
            
            const params = new URLSearchParams();
            commesse.forEach(c => params.append('commesse[]', c));
            sedi.forEach(s => params.append('sedi[]', s));
            macroCampagne.forEach(m => params.append('macro_campagne[]', m));
            nomiKpi.forEach(k => params.append('nomi_kpi[]', k));
            params.append('anno', anno);
            params.append('mese', mese);
            
            fetch(`/admin/produzione/kpi-target/get-tipologie?${params.toString()}`)
                .then(response => {
                    if (!response.ok) throw new Error('Errore nel caricamento tipologie');
                    return response.json();
                })
                .then(tipologie => {
                    console.log('Tipologie ricevute:', tipologie);
                    
                    let html = '';
                    
                    if (tipologie.length > 0) {
                        tipologie.forEach(tipologia => {
                            html += `
                                <label class="flex items-center gap-2 py-1 px-2 cursor-pointer hover:bg-base-200 rounded-md">
                                    <input type="checkbox" name="filter_tipologia_obiettivo[]" value="${tipologia}" 
                                           class="checkbox checkbox-xs tipologia-checkbox uppercase">
                                    <span class="text-xs uppercase">${tipologia.toUpperCase()}</span>
                                </label>
                            `;
                        });
                    } else {
                        html += '<p class="text-xs text-base-content/50 text-center py-4">Nessuna tipologia disponibile</p>';
                    }
                    
                    tipologiaContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Errore loadTipologie:', error);
                    tipologiaContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore nel caricamento</p>';
                });
        }
        
        // ===== GESTIONE MODAL INIZIALIZZA MESE =====
        function openInizializzaMeseModal() {
            const modal = document.getElementById('inizializza-mese-modal');
            
            // Pre-compila con il mese corrente (quello visualizzato)
            const meseCorrente = '{{ str_pad($mese, 2, "0", STR_PAD_LEFT) }}';
            const annoCorrente = '{{ $anno }}';
            
            document.getElementById('init-mese').value = meseCorrente;
            document.getElementById('init-anno').value = annoCorrente;
            
            // Aggiorna preview
            updatePreviewMeseSorgente();
            
            // Apri modal
            modal.showModal();
        }
        
        function closeInizializzaMeseModal() {
            const modal = document.getElementById('inizializza-mese-modal');
            modal.close();
        }
        
        function updatePreviewMeseSorgente() {
            const mese = parseInt(document.getElementById('init-mese').value);
            const anno = parseInt(document.getElementById('init-anno').value);
            
            if (!mese || !anno) {
                document.getElementById('preview-mese-sorgente').textContent = '(seleziona un mese per vedere l\'anteprima)';
                return;
            }
            
            // Calcola mese precedente
            let mesePrecedente = mese - 1;
            let annoPrecedente = anno;
            
            if (mesePrecedente === 0) {
                mesePrecedente = 12;
                annoPrecedente = anno - 1;
            }
            
            const nomiMesi = [
                '', 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
                'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'
            ];
            
            document.getElementById('preview-mese-sorgente').innerHTML = 
                `<strong>${nomiMesi[mesePrecedente]} ${annoPrecedente}</strong> → <strong>${nomiMesi[mese]} ${anno}</strong> (valori inizializzati a 0)`;
        }
        
        // Event listener per aggiornare preview quando cambiano i valori
        document.addEventListener('DOMContentLoaded', function() {
            const initMese = document.getElementById('init-mese');
            const initAnno = document.getElementById('init-anno');
            
            if (initMese && initAnno) {
                initMese.addEventListener('change', updatePreviewMeseSorgente);
                initAnno.addEventListener('input', updatePreviewMeseSorgente);
            }
        });
        
        // Switch tra tab
        function switchTab(tab) {
            document.getElementById('tab-target').classList.remove('tab-active');
            document.getElementById('tab-rendiconto').classList.remove('tab-active');
            document.getElementById('tab-' + tab).classList.add('tab-active');
            
            document.getElementById('table-target').classList.add('hidden');
            document.getElementById('table-rendiconto').classList.add('hidden');
            document.getElementById('table-' + tab).classList.remove('hidden');
        }
        
        // Seleziona/deseleziona tutti
        function toggleAllTarget(checkbox) {
            const checkboxes = document.querySelectorAll('.kpi-checkbox-target');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkActionsTarget();
        }
        
        // Aggiorna visibilità pulsanti bulk
        function updateBulkActionsTarget() {
            const checkboxes = document.querySelectorAll('.kpi-checkbox-target:checked');
            const bulkActions = document.getElementById('bulk-actions-target');
            bulkActions.style.display = checkboxes.length > 0 ? 'flex' : 'none';
        }
        
        // Deseleziona tutti
        function deselectAllTarget() {
            document.getElementById('select-all-target').checked = false;
            toggleAllTarget(document.getElementById('select-all-target'));
        }
        
        // Elimina selezionati
        function bulkDeleteTarget() {
            const checkboxes = document.querySelectorAll('.kpi-checkbox-target:checked');
            if (checkboxes.length === 0) {
                alert('Seleziona almeno un KPI da eliminare');
                return;
            }
            
            if (!confirm(`Sei sicuro di voler eliminare ${checkboxes.length} KPI selezionati?`)) {
                return;
            }
            
            const ids = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulk-delete-ids-target').value = JSON.stringify(ids);
            document.getElementById('bulk-delete-form-target').submit();
        }
        
        // ===== MODIFICA INLINE CELLE =====
        document.addEventListener('DOMContentLoaded', function() {
            const editableCells = document.querySelectorAll('.editable-cell');
            
            editableCells.forEach(cell => {
                // Evidenzia la cella quando è in focus
                cell.addEventListener('focus', function() {
                    this.style.backgroundColor = '#fef3c7';
                    this.style.outline = '2px solid #f59e0b';
                });
                
                // Rimuovi evidenziazione quando perde il focus
                cell.addEventListener('blur', function() {
                    this.style.backgroundColor = '';
                    this.style.outline = '';
                    
                    const originalValue = this.getAttribute('data-original');
                    const newValue = this.textContent.trim();
                    
                    // Se il valore è cambiato, salva
                    if (originalValue !== newValue) {
                        saveFieldChange(this);
                    }
                });
                
                // Salva con Enter
                cell.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur(); // Trigger il blur che salva
                    }
                    // Annulla con Escape
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        this.textContent = this.getAttribute('data-original');
                        this.blur();
                    }
                });
            });
        });
        
        // Funzione per salvare la modifica via AJAX
        function saveFieldChange(cell) {
            const id = cell.getAttribute('data-id');
            const field = cell.getAttribute('data-field');
            let value = cell.textContent.trim();
            
            // Se è un campo numerico, rimuovi eventuali separatori di migliaia e converti
            if (field === 'valore_kpi') {
                value = value.replace(/[^0-9.,]/g, '').replace(',', '.');
                const numValue = parseFloat(value);
                if (isNaN(numValue) || numValue < 0) {
                    alert('Inserisci un valore numerico valido (maggiore o uguale a 0)');
                    cell.textContent = cell.getAttribute('data-original');
                    return;
                }
                value = numValue.toString();
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Mostra loading
            const originalContent = cell.textContent;
            cell.textContent = '⏳ Salvataggio...';
            cell.style.opacity = '0.6';
            
            fetch(`/admin/produzione/kpi-target/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: field,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna il valore originale
                    const displayValue = field === 'valore_kpi' 
                        ? parseFloat(value).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                        : value;
                    
                    cell.setAttribute('data-original', value);
                    cell.textContent = displayValue;
                    cell.style.opacity = '1';
                    
                    // Flash verde
                    cell.style.backgroundColor = '#d1fae5';
                    setTimeout(() => {
                        cell.style.backgroundColor = '';
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Errore durante il salvataggio');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore durante il salvataggio: ' + error.message);
                
                // Ripristina valore originale
                cell.textContent = cell.getAttribute('data-original');
                cell.style.opacity = '1';
                cell.style.backgroundColor = '#fecaca';
                setTimeout(() => {
                    cell.style.backgroundColor = '';
                }, 1000);
            });
        }
        
        // ===== SALVATAGGIO SELECT =====
        function saveFieldChangeSelect(selectElement) {
            const id = selectElement.getAttribute('data-id');
            const field = selectElement.getAttribute('data-field');
            const value = selectElement.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Mostra loading
            const originalBg = selectElement.style.backgroundColor;
            selectElement.style.backgroundColor = '#fef3c7';
            selectElement.disabled = true;
            
            fetch(`/admin/produzione/kpi-target/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: field,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Flash verde
                    selectElement.style.backgroundColor = '#d1fae5';
                    setTimeout(() => {
                        selectElement.style.backgroundColor = originalBg;
                        selectElement.disabled = false;
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Errore durante il salvataggio');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore durante il salvataggio: ' + error.message);
                
                // Flash rosso
                selectElement.style.backgroundColor = '#fecaca';
                setTimeout(() => {
                    selectElement.style.backgroundColor = originalBg;
                    selectElement.disabled = false;
                }, 1000);
            });
        }
        
        // ===== GESTIONE TIPO KPI (Badge + Select) =====
        function toggleTipoKpiSelect(id) {
            const badge = document.getElementById(`badge-tipo-${id}`);
            const select = document.getElementById(`select-tipo-${id}`);
            
            // Nascondi badge, mostra select
            badge.classList.add('hidden');
            select.classList.remove('hidden');
            
            // Focus sulla select per aprirla immediatamente
            setTimeout(() => {
                select.focus();
                select.click(); // Apre il dropdown
            }, 50);
        }
        
        function hideTipoKpiSelect(id) {
            const badge = document.getElementById(`badge-tipo-${id}`);
            const select = document.getElementById(`select-tipo-${id}`);
            
            // Mostra badge, nascondi select
            setTimeout(() => {
                select.classList.add('hidden');
                badge.classList.remove('hidden');
            }, 200); // Delay per permettere l'onChange di eseguirsi
        }
        
        function saveTipoKpi(selectElement) {
            const id = selectElement.getAttribute('data-id');
            const field = selectElement.getAttribute('data-field');
            const value = selectElement.value;
            const badge = document.getElementById(`badge-tipo-${id}`);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Aggiorna subito il badge con il nuovo valore
            const displayValue = value ? value.toUpperCase() : 'NON ASSEGNATO';
            badge.textContent = displayValue;
            
            // Cambia colore badge in base al tipo
            badge.className = 'badge badge-sm cursor-pointer hover:badge-primary-focus transition-all tipo-kpi-badge';
            if (value) {
                badge.classList.add('badge-primary');
            } else {
                badge.classList.add('badge-ghost');
            }
            
            // Salva via AJAX
            fetch(`/admin/produzione/kpi-target/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: field,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Flash verde sul badge
                    const originalBg = badge.style.backgroundColor;
                    badge.style.backgroundColor = '#d1fae5';
                    setTimeout(() => {
                        badge.style.backgroundColor = '';
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Errore durante il salvataggio');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore durante il salvataggio: ' + error.message);
                
                // Flash rosso sul badge
                badge.style.backgroundColor = '#fecaca';
                setTimeout(() => {
                    badge.style.backgroundColor = '';
                }, 1000);
            });
        }
        
        // ===== GESTIONE MODAL VARIAZIONE =====
        function openVariazioneModal(kpi) {
            const modal = document.getElementById('variazione-modal');
            
            // Popola i campi
            document.getElementById('variazione-kpi-id').value = kpi.id;
            document.getElementById('variazione-kpi-variato').value = kpi.kpi_variato || '';
            document.getElementById('variazione-data-inizio').value = kpi.data_validita_inizio || '';
            document.getElementById('variazione-data-fine').value = kpi.data_validita_fine || '';
            
            // Mostra/nascondi pulsante rimuovi
            const btnRimuovi = document.getElementById('btn-rimuovi-variazione');
            btnRimuovi.style.display = kpi.kpi_variato ? 'flex' : 'none';
            
            // Apri modal
            modal.showModal();
        }
        
        function closeVariazioneModal() {
            const modal = document.getElementById('variazione-modal');
            modal.close();
        }
        
        function salvaVariazione() {
            const id = document.getElementById('variazione-kpi-id').value;
            const kpiVariato = document.getElementById('variazione-kpi-variato').value;
            const dataInizio = document.getElementById('variazione-data-inizio').value;
            const dataFine = document.getElementById('variazione-data-fine').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Validazione
            if (kpiVariato && !dataInizio) {
                alert('Se imposti un nuovo valore KPI, devi specificare anche la data di inizio!');
                return;
            }
            
            if (dataFine && dataInizio && dataFine < dataInizio) {
                alert('La data fine deve essere uguale o successiva alla data inizio!');
                return;
            }
            
            // Salva via AJAX
            fetch(`/admin/produzione/kpi-target/${id}/update-variazione`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    kpi_variato: kpiVariato || null,
                    data_validita_inizio: dataInizio || null,
                    data_validita_fine: dataFine || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Variazione salvata con successo!');
                    closeVariazioneModal();
                    location.reload(); // Ricarica la pagina per vedere le modifiche
                } else {
                    throw new Error(data.message || 'Errore durante il salvataggio');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore durante il salvataggio: ' + error.message);
            });
        }
        
        function rimuoviVariazione() {
            if (!confirm('Sei sicuro di voler rimuovere la variazione KPI?')) {
                return;
            }
            
            const id = document.getElementById('variazione-kpi-id').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/admin/produzione/kpi-target/${id}/update-variazione`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    kpi_variato: null,
                    data_validita_inizio: null,
                    data_validita_fine: null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Variazione rimossa con successo!');
                    closeVariazioneModal();
                    location.reload();
                } else {
                    throw new Error(data.message || 'Errore durante la rimozione');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore durante la rimozione: ' + error.message);
            });
        }
    </script>
</x-admin.wrapper>
