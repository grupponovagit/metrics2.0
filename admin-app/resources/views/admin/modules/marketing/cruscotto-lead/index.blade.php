<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Cruscotto Lead Marketing') }}</x-slot>
    
    <x-admin.page-header 
        title="Cruscotto Lead Marketing" 
        subtitle="Dashboard lead generation con KPI e metriche digital"
        icon="bullhorn"
        iconColor="info"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.marketing.index') }}" class="btn btn-outline btn-info">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- FILTRI DINAMICI A CASCATA - UI MIGLIORATA --}}
    <x-admin.card tone="light" shadow="lg" padding="lg" class="mb-6">
        <form method="GET" action="{{ route('admin.marketing.cruscotto_lead') }}" id="filterForm">
            
            {{-- RIGA 1: Data Inizio, Data Fine, Ragione Sociale --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                
                {{-- Data Inizio --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="calendar" class="h-4 w-4 inline mr-1" />
                            Data Inizio
                        </span>
                    </label>
                    <input 
                        type="date" 
                        name="data_inizio" 
                        class="input input-bordered w-full"
                        value="{{ $filtri['data_inizio'] ?? '' }}"
                    >
                </div>

                {{-- Data Fine --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="calendar" class="h-4 w-4 inline mr-1" />
                            Data Fine
                        </span>
                    </label>
                    <input 
                        type="date" 
                        name="data_fine" 
                        class="input input-bordered w-full"
                        value="{{ $filtri['data_fine'] ?? '' }}"
                    >
                </div>

                {{-- Ragione Sociale (Multi-select con Checkbox) --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="building" class="h-4 w-4 inline mr-1" />
                            Ragione Sociale
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllRagioneSociale(true)" class="btn btn-xs btn-info gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllRagioneSociale(false)" class="btn btn-xs btn-outline btn-info gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div class="border border-base-300 rounded-lg p-3 bg-base-100" style="height: 120px; max-height: 120px; overflow-y: auto;">
                        @foreach($opzioniRagioneSociale as $rs)
                        <label class="flex items-center gap-3 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors">
                            <input type="checkbox" name="ragione_sociale[]" value="{{ $rs }}" 
                                   class="checkbox checkbox-info checkbox-sm ragione-checkbox" 
                                   {{ in_array($rs, $filtri['ragione_sociale'] ?? []) ? 'checked' : '' }}
                                   onchange="debounce(loadProvenienze, 'provenienza', 200)">
                            <span class="text-sm">{{ $rs }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGA 2: Provenienza, Campagne, Azioni --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Provenienza (Multi-select con Checkbox) --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="location-dot" class="h-4 w-4 inline mr-1" />
                            Provenienza
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllProvenienza(true)" class="btn btn-xs btn-success gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllProvenienza(false)" class="btn btn-xs btn-outline btn-success gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div id="provenienzaContainer" class="border border-base-300 rounded-lg p-3 bg-base-100" style="height: 120px; max-height: 120px; overflow-y: auto;">
                        @if($opzioniProvenienzaFiltered->isNotEmpty())
                            @foreach($opzioniProvenienzaFiltered as $prov)
                            <label class="flex items-center gap-3 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors">
                                <input type="checkbox" name="provenienza[]" value="{{ $prov }}" 
                                       class="checkbox checkbox-success checkbox-sm provenienza-checkbox" 
                                       {{ in_array($prov, $filtri['provenienza'] ?? []) ? 'checked' : '' }}
                                       onchange="debounce(loadCampagne, 'campagne', 200)">
                                <span class="text-sm">{{ $prov }}</span>
                            </label>
                            @endforeach
                        @else
                            <p class="text-xs text-base-content/50 text-center py-4">Seleziona Ragione Sociale</p>
                        @endif
                    </div>
                </div>

                {{-- Campagne (Multi-select con Checkbox Dinamico) --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="tags" class="h-4 w-4 inline mr-1" />
                            Campagne
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllCampagne(true)" class="btn btn-xs btn-warning gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllCampagne(false)" class="btn btn-xs btn-outline btn-warning gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div id="campagneContainer" class="border border-base-300 rounded-lg p-3 bg-base-100" style="height: 120px; max-height: 120px; overflow-y: auto;">
                        @if($opzioniCampagneFiltered->isNotEmpty())
                            @foreach($opzioniCampagneFiltered as $camp)
                            <label class="flex items-center gap-3 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors">
                                <input type="checkbox" name="utm_campaign[]" value="{{ $camp }}" 
                                       class="checkbox checkbox-warning checkbox-sm campagna-checkbox" 
                                       {{ in_array($camp, $filtri['utm_campaign'] ?? []) ? 'checked' : '' }}>
                                <span class="text-sm">{{ $camp }}</span>
                            </label>
                            @endforeach
                        @else
                            <p class="text-xs text-base-content/50 text-center py-4">Seleziona Ragione Sociale o Provenienza</p>
                        @endif
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
                        <a href="{{ route('admin.marketing.cruscotto_lead') }}" class="btn btn-outline btn-md w-full gap-2">
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
    @if(request()->hasAny(['data_inizio', 'data_fine', 'ragione_sociale', 'provenienza', 'utm_campaign']))
    
    {{-- Card Wrapper per tutte le tabelle --}}
    <x-admin.card tone="light" shadow="lg" padding="none">
        <div class="p-6 border-b border-base-300 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-base-content">
                    Dettaglio KPI Lead Generation
                </h3>
                <p class="text-sm text-base-content/60 mt-1">
                    Metriche per Ragione Sociale, Provenienza e Campagna
                </p>
            </div>
            
            {{-- Pulsanti Sintetico/Dettagliato/Giornaliero --}}
            <div class="flex gap-2 items-center">
                <button 
                    onclick="switchView('sintetico')" 
                    id="btn-sintetico"
                    class="btn btn-sm btn-info"
                >
                    Sintetico
                </button>
                <button 
                    onclick="switchView('dettagliato')" 
                    id="btn-dettagliato"
                    class="btn btn-sm btn-outline btn-info"
                >
                    Dettagliato
                </button>
                <button 
                    onclick="switchView('giornaliero')" 
                    id="btn-giornaliero"
                    class="btn btn-sm btn-outline btn-info"
                >
                    Giornaliero
                </button>
                
                {{-- Separatore --}}
                <div class="divider divider-horizontal mx-2"></div>
                
                {{-- Dropdown Gestione Colonne (cambia in base alla vista attiva) --}}
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
                        
                        {{-- Contenitore dinamico per le checkbox delle colonne --}}
                        <div id="column-controls-container" class="space-y-1 max-h-96 overflow-y-auto">
                            {{-- Popolato dinamicamente da JavaScript --}}
                        </div>
                        
                        <div class="divider my-2"></div>
                        
                        {{-- Pulsanti rapidi --}}
                        <div class="flex gap-2 px-2">
                            <button onclick="toggleAllColumnsInActiveTable(true)" class="btn btn-xs btn-success flex-1">
                                Tutte
                            </button>
                            <button onclick="toggleAllColumnsInActiveTable(false)" class="btn btn-xs btn-outline btn-success flex-1">
                                Nessuna
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
     
        {{-- TABELLA SINTETICO --}}
        @include('admin.modules.marketing.cruscotto-lead._table-sintetico')

        {{-- TABELLA DETTAGLIATO --}}
        @include('admin.modules.marketing.cruscotto-lead._table-dettagliato')

        {{-- TABELLA GIORNALIERO --}}
        @include('admin.modules.marketing.cruscotto-lead._table-giornaliero')
     
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
                    @if($filtri['data_inizio'] && $filtri['data_fine'])
                        <span>Periodo: <strong>{{ \Carbon\Carbon::parse($filtri['data_inizio'])->format('d/m/Y') }}</strong> - <strong>{{ \Carbon\Carbon::parse($filtri['data_fine'])->format('d/m/Y') }}</strong></span>
                    @else
                        <span>Periodo: <strong>Mese Corrente</strong></span>
                    @endif
                </div>
                <div>
                    <span>Totale Lead: <strong>{{ number_format($totali['leads']) }}</strong></span>
                </div>
            </div>
        </div>

    </x-admin.card>
    
    {{-- CSS aggiuntivo per righe totale sticky --}}
    @include('admin.modules.marketing.cruscotto-lead._styles')

    {{-- Script per switchare tra visualizzazioni --}}
    @include('admin.modules.marketing.cruscotto-lead._scripts')
    
    @else
        {{-- Messaggio iniziale --}}
        <x-admin.card tone="light" shadow="md" padding="lg">
            <div class="text-center py-12">
                <x-ui.icon name="filter" class="h-16 w-16 mx-auto text-base-content/20 mb-4" />
                <h3 class="text-xl font-semibold text-base-content mb-2">Nessun filtro applicato</h3>
                <p class="text-base-content/60 max-w-md mx-auto">
                    Utilizza i filtri qui sopra per visualizzare i dati del cruscotto lead. 
                    Seleziona almeno un periodo per iniziare.
                </p>
            </div>
        </x-admin.card>
    @endif
</x-admin.wrapper>

<script>
// =====================================================
// DEBOUNCE FUNCTION
// =====================================================
let debounceTimers = {};
function debounce(func, key, delay) {
    if (debounceTimers[key]) {
        clearTimeout(debounceTimers[key]);
    }
    debounceTimers[key] = setTimeout(() => {
        func();
        delete debounceTimers[key];
    }, delay);
}

// =====================================================
// TOGGLE ALL CHECKBOXES
// =====================================================
function toggleAllRagioneSociale(select) {
    document.querySelectorAll('.ragione-checkbox').forEach(checkbox => {
        checkbox.checked = select;
    });
    if (select) toggleAllProvenienza(true);
    debounce(loadProvenienze, 'provenienza', 200);
}

function toggleAllProvenienza(select) {
    document.querySelectorAll('.provenienza-checkbox').forEach(checkbox => {
        checkbox.checked = select;
    });
    if (select) toggleAllCampagne(true);
    debounce(loadCampagne, 'campagne', 200);
}

function toggleAllCampagne(select) {
    document.querySelectorAll('.campagna-checkbox').forEach(checkbox => {
        checkbox.checked = select;
    });
}

// =====================================================
// LOAD PROVENIENZE DINAMICAMENTE
// =====================================================
function loadProvenienze() {
    const provenienzaContainer = document.getElementById('provenienzaContainer');
    const selectedRagioneSociale = Array.from(document.querySelectorAll('.ragione-checkbox:checked')).map(cb => cb.value);
    
    // Mostra loading
    provenienzaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4"><span class="loading loading-spinner loading-sm"></span> Caricamento...</p>';
    
    // Resetta campagne
    document.getElementById('campagneContainer').innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona Provenienza</p>';
    
    if (selectedRagioneSociale.length === 0) {
        provenienzaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona Ragione Sociale</p>';
        return;
    }
    
    const params = new URLSearchParams();
    selectedRagioneSociale.forEach(rs => params.append('ragione_sociale[]', rs));
    
    // Salva provenienze selezionate
    const selectedProvenienze = Array.from(document.querySelectorAll('.provenienza-checkbox:checked')).map(cb => cb.value);
    
    fetch(`{{ route('admin.marketing.cruscotto_lead.get_provenienze') }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            provenienzaContainer.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(prov => {
                    const isChecked = selectedProvenienze.includes(prov);
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-3 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors';
                    label.innerHTML = `
                        <input type="checkbox" name="provenienza[]" value="${prov}" 
                               class="checkbox checkbox-success checkbox-sm provenienza-checkbox" 
                               ${isChecked ? 'checked' : ''}
                               onchange="debounce(loadCampagne, 'campagne', 200)">
                        <span class="text-sm">${prov}</span>
                    `;
                    provenienzaContainer.appendChild(label);
                });
                
                // Se ci sono provenienze selezionate, carica le campagne
                if (selectedProvenienze.length > 0) {
                    loadCampagne();
                }
            } else {
                provenienzaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Nessuna provenienza disponibile</p>';
            }
        })
        .catch(error => {
            provenienzaContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore caricamento</p>';
        });
}

// =====================================================
// LOAD CAMPAGNE DINAMICAMENTE
// =====================================================
function loadCampagne() {
    const campagneContainer = document.getElementById('campagneContainer');
    const selectedRagioneSociale = Array.from(document.querySelectorAll('.ragione-checkbox:checked')).map(cb => cb.value);
    const selectedProvenienze = Array.from(document.querySelectorAll('.provenienza-checkbox:checked')).map(cb => cb.value);
    
    // Mostra loading
    campagneContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4"><span class="loading loading-spinner loading-sm"></span> Caricamento...</p>';
    
    if (selectedRagioneSociale.length === 0 && selectedProvenienze.length === 0) {
        campagneContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona Ragione Sociale o Provenienza</p>';
        return;
    }
    
    const params = new URLSearchParams();
    selectedRagioneSociale.forEach(rs => params.append('ragione_sociale[]', rs));
    selectedProvenienze.forEach(prov => params.append('provenienza[]', prov));
    
    // Salva campagne selezionate
    const selectedCampagne = Array.from(document.querySelectorAll('.campagna-checkbox:checked')).map(cb => cb.value);
    
    fetch(`{{ route('admin.marketing.cruscotto_lead.get_campagne') }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            campagneContainer.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(camp => {
                    const isChecked = selectedCampagne.includes(camp);
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-3 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors';
                    label.innerHTML = `
                        <input type="checkbox" name="utm_campaign[]" value="${camp}" 
                               class="checkbox checkbox-warning checkbox-sm campagna-checkbox" 
                               ${isChecked ? 'checked' : ''}>
                        <span class="text-sm">${camp}</span>
                    `;
                    campagneContainer.appendChild(label);
                });
            } else {
                campagneContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Nessuna campagna disponibile</p>';
            }
        })
        .catch(error => {
            campagneContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore caricamento</p>';
        });
}
</script>

