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
                            Ragione Sociale <span class="text-[10px] opacity-60">(Shift+Click)</span>
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
                    <div class="border border-base-300 rounded-lg p-2.5 h-[120px] overflow-y-auto bg-base-100">
                        @foreach($opzioniRagioneSociale as $rs)
                        <label class="flex items-center gap-2 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors">
                            <input type="checkbox" name="ragione_sociale[]" value="{{ $rs }}" 
                                   class="checkbox checkbox-info checkbox-sm" 
                                   {{ in_array($rs, $filtri['ragione_sociale'] ?? []) ? 'checked' : '' }}
                                   onchange="updateCampagneFilter()">
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
                            Provenienza <span class="text-[10px] opacity-60">(Shift+Click)</span>
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
                    <div class="border border-base-300 rounded-lg p-2.5 h-[120px] overflow-y-auto bg-base-100">
                        @foreach($opzioniProvenienza as $prov)
                        <label class="flex items-center gap-2 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors">
                            <input type="checkbox" name="provenienza[]" value="{{ $prov }}" 
                                   class="checkbox checkbox-success checkbox-sm" 
                                   {{ in_array($prov, $filtri['provenienza'] ?? []) ? 'checked' : '' }}
                                   onchange="updateCampagneFilter()">
                            <span class="text-sm">{{ $prov }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Campagne (Multi-select con Checkbox Dinamico) --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="tags" class="h-4 w-4 inline mr-1" />
                            Campagne <span class="text-[10px] opacity-60">(Shift+Click)</span>
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
                    <div id="campagneContainer" class="border border-base-300 rounded-lg p-2.5 h-[120px] overflow-y-auto bg-base-100">
                        @if($opzioniCampagne->isEmpty())
                        <p class="text-xs text-base-content/50 text-center py-4">Seleziona Ragione Sociale o Provenienza</p>
                        @else
                        @foreach($opzioniCampagne as $camp)
                        <label class="flex items-center gap-2 py-1 px-2 hover:bg-base-200 rounded cursor-pointer transition-colors">
                            <input type="checkbox" name="utm_campaign[]" value="{{ $camp }}" 
                                   class="checkbox checkbox-warning checkbox-sm" 
                                   {{ in_array($camp, $filtri['utm_campaign'] ?? []) ? 'checked' : '' }}>
                            <span class="text-sm">{{ $camp }}</span>
                        </label>
                        @endforeach
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
            
            {{-- Pulsanti Sintetico/Giornaliero --}}
            <div class="flex gap-2 items-center">
                <button 
                    onclick="switchView('sintetico')" 
                    id="btn-sintetico"
                    class="btn btn-sm btn-info"
                >
                    Sintetico
                </button>
                {{--
                <button 
                    onclick="switchView('dettagliato')" 
                    id="btn-dettagliato"
                    class="btn btn-sm btn-outline btn-info"
                >
                    Dettagliato
                </button>
                --}}
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

        {{-- TABELLA DETTAGLIATO (Commentato per ora) --}}
        {{-- @include('admin.modules.marketing.cruscotto-lead._table-dettagliato') --}}

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
// TOGGLE CHECKBOX (Ragione Sociale, Provenienza, Campagne)
// =====================================================
function toggleAllRagioneSociale(select) {
    document.querySelectorAll('input[name="ragione_sociale[]"]').forEach(checkbox => {
        checkbox.checked = select;
    });
    updateCampagneFilter();
}

function toggleAllProvenienza(select) {
    document.querySelectorAll('input[name="provenienza[]"]').forEach(checkbox => {
        checkbox.checked = select;
    });
    updateCampagneFilter();
}

function toggleAllCampagne(select) {
    document.querySelectorAll('input[name="utm_campaign[]"]').forEach(checkbox => {
        checkbox.checked = select;
    });
}

// =====================================================
// AGGIORNA FILTRO CAMPAGNE DINAMICO
// =====================================================
function updateCampagneFilter() {
    const ragioneSocialeChecked = Array.from(document.querySelectorAll('input[name="ragione_sociale[]"]:checked'))
        .map(cb => cb.value);
    const provenienzaChecked = Array.from(document.querySelectorAll('input[name="provenienza[]"]:checked'))
        .map(cb => cb.value);
    
    // ✅ SALVA le campagne attualmente selezionate PRIMA di aggiornare
    const campagneSelezionate = Array.from(document.querySelectorAll('input[name="utm_campaign[]"]:checked'))
        .map(cb => cb.value);
    
    // Fai una chiamata AJAX per recuperare le campagne filtrate
    const params = new URLSearchParams();
    ragioneSocialeChecked.forEach(rs => params.append('ragione_sociale[]', rs));
    provenienzaChecked.forEach(prov => params.append('provenienza[]', prov));
    
    fetch(`{{ route('admin.marketing.cruscotto_lead') }}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Estrai solo le opzioni del container campagne dalla risposta
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newCampagneHtml = doc.getElementById('campagneContainer').innerHTML;
        document.getElementById('campagneContainer').innerHTML = newCampagneHtml;
        
        // ✅ RIPRISTINA le checkbox selezionate dopo l'aggiornamento
        campagneSelezionate.forEach(campagna => {
            const checkbox = document.querySelector(`input[name="utm_campaign[]"][value="${campagna}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    })
    .catch(error => console.error('Errore aggiornamento campagne:', error));
}
</script>

