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
    
    {{-- FILTRI DINAMICI A CASCATA - UI MIGLIORATA --}}
    <x-admin.card tone="light" shadow="lg" padding="lg" class="mb-6">
        <form method="GET" action="{{ route('admin.produzione.cruscotto_produzione') }}" id="filterForm">
            
            {{-- RIGA 1: Data Inizio, Data Fine, Commessa --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                {{-- Data Inizio --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-base">
                            <x-ui.icon name="calendar" class="h-5 w-5 inline" />
                            Data Inizio
                        </span>
                    </label>
                    <input type="date" name="data_inizio" value="{{ $dataInizio }}" class="input input-bordered input-md w-full text-base" required>
                </div>
                
                {{-- Data Fine --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-base">
                            <x-ui.icon name="calendar" class="h-5 w-5 inline" />
                            Data Fine
                        </span>
                    </label>
                    <input type="date" name="data_fine" value="{{ $dataFine }}" class="input input-bordered input-md w-full text-base" required>
                </div>
                
                {{-- Commessa --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-base">
                            <x-ui.icon name="briefcase" class="h-5 w-5 inline text-warning" />
                            Commessa
                        </span>
                    </label>
                    <select name="commessa" id="commessaSelect" class="select select-bordered select-warning select-md w-full text-base" required>
                        <option value="">Seleziona commessa</option>
                        @foreach($commesse as $commessa)
                            <option value="{{ $commessa }}" {{ $commessaFilter == $commessa ? 'selected' : '' }}>
                                {{ $commessa }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- RIGA 2: Campagne, Sedi, Pulsanti --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Macro Campagna --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="bullseye" class="h-4 w-4 inline text-success" />
                            Campagne <span class="text-[10px] opacity-60">(Shift+Click)</span>
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllCampagne(true)" class="btn btn-xs btn-success gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllCampagne(false)" class="btn btn-xs btn-outline btn-success gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div id="campagnaContainer" class="border border-base-300 rounded-lg p-2.5 h-[180px] overflow-y-auto bg-base-100">
                        <p class="text-xs text-base-content/50 text-center py-4">Seleziona una commessa</p>
                    </div>
                </div>
                
                {{-- Sede --}}
                <div class="form-control">
                    <label class="label py-1 pb-2">
                        <span class="label-text font-semibold text-sm">
                            <x-ui.icon name="building" class="h-4 w-4 inline text-info" />
                            Sedi <span class="text-[10px] opacity-60">(Shift+Click)</span>
                        </span>
                        <div class="flex gap-1">
                            <button type="button" onclick="toggleAllSedi(true)" class="btn btn-xs btn-info gap-1">
                                <x-ui.icon name="check" class="h-3 w-3" />
                                Tutte
                            </button>
                            <button type="button" onclick="toggleAllSedi(false)" class="btn btn-xs btn-outline btn-info gap-1">
                                <x-ui.icon name="times" class="h-3 w-3" />
                                Nessuna
                            </button>
                        </div>
                    </label>
                    <div id="sedeContainer" class="border border-base-300 rounded-lg p-2.5 h-[180px] overflow-y-auto bg-base-100">
                        <p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>
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
                        <a href="{{ route('admin.produzione.cruscotto_produzione') }}" class="btn btn-outline btn-md w-full gap-2">
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
    @if(request()->hasAny(['data_inizio', 'data_fine', 'commessa', 'sede', 'macro_campagna']))
    
    {{-- Alert PAF --}}
    @if(!($kpiTotali['mostra_paf'] ?? false))
    <div class="alert alert-warning mb-4">
        <x-ui.icon name="info-circle" class="h-5 w-5" />
        <div>
            <div class="font-bold">Attenzione</div>
            <div class="text-sm">I dati PAF (Proiezione A Fine mese) sono visibili solo quando si filtra esclusivamente il mese corrente. Le colonne PAF verranno mostrate come zero poiché il periodo filtrato non corrisponde al mese corrente.</div>
        </div>
    </div>
    @endif
    
    {{-- Card Wrapper per tutte le tabelle --}}
    <x-admin.card tone="light" shadow="lg" padding="none">
        <div class="p-6 border-b border-base-300 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-base-content">
                    Dettaglio KPI per Commessa, Sede e Campagna
                </h3>
                <p class="text-sm text-base-content/60 mt-1">
                    Visualizzazione gerarchica delle metriche di produzione
                </p>
            </div>
            
            {{-- Pulsanti Sintetico/Dettagliato/Giornaliero/Grafico --}}
            <div class="flex gap-2 items-center">
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
                <button 
                    onclick="switchView('grafico')" 
                    id="btn-grafico"
                    class="btn btn-sm btn-outline btn-primary"
                >
                    <x-ui.icon name="chart-line" class="h-4 w-4" />
                    Grafico
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
        
     
        {{-- TABELLA DETTAGLIATO --}}
        @include('admin.modules.produzione.cruscotto-produzione._table-dettagliato')

        {{-- TABELLA SINTETICO --}}
        @include('admin.modules.produzione.cruscotto-produzione._table-sintetico')

        {{-- TABELLA GIORNALIERO --}}
        @include('admin.modules.produzione.cruscotto-produzione._table-giornaliero')
        
        {{-- VISTA GRAFICO --}}
        @include('admin.modules.produzione.cruscotto-produzione._table-grafico')
     
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
    
    {{-- CSS aggiuntivo per righe totale sticky --}}
    @include('admin.modules.produzione.cruscotto-produzione._styles')

    {{-- Script per switchare tra visualizzazioni --}}
    @include('admin.modules.produzione.cruscotto-produzione._scripts')
    
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

    {{-- Script per filtri dinamici a cascata --}}
    <script>
        // Dati per i filtri dinamici
        const filtersData = @json([
            'sedi' => $sediPerCommessa ?? [],
            'campagne' => $campagnePerSede ?? []
        ]);

        // Variabili per gestire la selezione con Shift
        let lastCheckedCampagna = null;
        let lastCheckedSede = null;

        // Funzione per gestire shift-click su checkbox o label
        function handleShiftClick(checkboxes, lastChecked, currentCheckbox, event) {
            if (!lastChecked) {
                return currentCheckbox;
            }

            if (event.shiftKey) {
                const start = Array.from(checkboxes).indexOf(lastChecked);
                const end = Array.from(checkboxes).indexOf(currentCheckbox);
                const checkboxesToToggle = Array.from(checkboxes).slice(
                    Math.min(start, end),
                    Math.max(start, end) + 1
                );
                
                checkboxesToToggle.forEach(checkbox => {
                    checkbox.checked = currentCheckbox.checked;
                });
            }

            return currentCheckbox;
        }
        
        // Funzione per aggiungere event listener a label con checkbox
        function attachLabelClickListeners(containerSelector, checkboxClass, lastCheckedVar) {
            const container = document.querySelector(containerSelector);
            if (!container) return;
            
            const labels = container.querySelectorAll('[data-checkbox-label]');
            labels.forEach(label => {
                const checkbox = label.querySelector(`.${checkboxClass}`);
                if (!checkbox) return;
                
                // Click sulla label (ma non sulla checkbox stessa)
                label.addEventListener('click', function(e) {
                    // Se il click è sulla checkbox, lascia che gestisca lei
                    if (e.target.type === 'checkbox') return;
                    
                    // Altrimenti, previeni il default e gestisci manualmente
                    e.preventDefault();
                    
                    // Toggle checkbox
                    checkbox.checked = !checkbox.checked;
                    
                    // Gestisci shift-click
                    const allCheckboxes = container.querySelectorAll(`.${checkboxClass}`);
                    if (checkboxClass === 'campagna-checkbox') {
                        lastCheckedCampagna = handleShiftClick(allCheckboxes, lastCheckedCampagna, checkbox, e);
                    } else if (checkboxClass === 'sede-checkbox') {
                        lastCheckedSede = handleShiftClick(allCheckboxes, lastCheckedSede, checkbox, e);
                    }
                });
                
                // Click diretto sulla checkbox
                checkbox.addEventListener('click', function(e) {
                    const allCheckboxes = container.querySelectorAll(`.${checkboxClass}`);
                    if (checkboxClass === 'campagna-checkbox') {
                        lastCheckedCampagna = handleShiftClick(allCheckboxes, lastCheckedCampagna, this, e);
                    } else if (checkboxClass === 'sede-checkbox') {
                        lastCheckedSede = handleShiftClick(allCheckboxes, lastCheckedSede, this, e);
                    }
                });
            });
        }
        
        // Funzioni per "Seleziona Tutte" / "Deseleziona Tutte"
        function toggleAllCampagne(selectAll) {
            const checkboxes = document.querySelectorAll('.campagna-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
            // Ricarica sedi dopo aver cambiato le campagne
            loadSedi();
        }
        
        function toggleAllSedi(selectAll) {
            const checkboxes = document.querySelectorAll('.sede-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
        }

        // Funzione per caricare sedi in base a commessa e campagne selezionate
        function loadSedi() {
            const commessa = document.getElementById('commessaSelect').value;
            const selectedCampagne = Array.from(document.querySelectorAll('.campagna-checkbox:checked')).map(cb => cb.value);
            const sedeContainer = document.getElementById('sedeContainer');
            
            console.log('loadSedi chiamato:', { commessa, selectedCampagne });
            
            // Reset dell'ultima checkbox sede selezionata
            lastCheckedSede = null;
            
            if (!commessa || selectedCampagne.length === 0) {
                sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
                return;
            }
            
            // Costruisci URL con parametri
            const params = new URLSearchParams();
            params.append('commessa', commessa);
            selectedCampagne.forEach(c => params.append('campagne[]', c));
            
            console.log('Fetching sedi con URL:', `/admin/produzione/get-sedi?${params.toString()}`);
            
            fetch(`/admin/produzione/get-sedi?${params.toString()}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Sedi ricevute:', data);
                    sedeContainer.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(sede => {
                            const label = document.createElement('label');
                            label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded-md select-sede';
                            label.setAttribute('data-checkbox-label', '');
                            label.innerHTML = `
                                <input type="checkbox" name="sede[]" value="${sede}" 
                                       class="checkbox checkbox-info checkbox-sm sede-checkbox">
                                <span class="text-sm leading-tight">${sede}</span>
                            `;
                            sedeContainer.appendChild(label);
                        });
                        
                        // Aggiungi event listener per shift-click e label click
                        attachLabelClickListeners('#sedeContainer', 'sede-checkbox');
                    } else {
                        sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Nessuna sede disponibile</p>';
                    }
                })
                .catch(error => {
                    console.error('Errore caricamento sedi:', error);
                    sedeContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore caricamento</p>';
                });
        }
        
        // Gestione select Commessa - aggiorna checkbox campagne
        document.getElementById('commessaSelect').addEventListener('change', function() {
            const commessa = this.value;
            const campagnaContainer = document.getElementById('campagnaContainer');
            const sedeContainer = document.getElementById('sedeContainer');
            
            // Reset delle ultime checkbox selezionate
            lastCheckedCampagna = null;
            lastCheckedSede = null;
            
            if (commessa) {
                // Carica campagne per la commessa selezionata via AJAX
                fetch(`/admin/produzione/get-campagne?commessa=${encodeURIComponent(commessa)}`)
                    .then(response => response.json())
                    .then(data => {
                        campagnaContainer.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(campagna => {
                                const label = document.createElement('label');
                                label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded-md select-campagna';
                                label.setAttribute('data-checkbox-label', '');
                                label.innerHTML = `
                                    <input type="checkbox" name="macro_campagna[]" value="${campagna}" 
                                           class="checkbox checkbox-success checkbox-sm campagna-checkbox">
                                    <span class="text-sm leading-tight">${campagna}</span>
                                `;
                                campagnaContainer.appendChild(label);
                            });
                            
                            // Aggiungi event listener per shift-click e label click
                            attachLabelClickListeners('#campagnaContainer', 'campagna-checkbox');
                            
                            // Reset sedi (nessuna campagna selezionata inizialmente)
                            sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
                        } else {
                            campagnaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Nessuna campagna disponibile</p>';
                            sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Errore caricamento campagne:', error);
                        campagnaContainer.innerHTML = '<p class="text-xs text-error text-center py-4">Errore caricamento</p>';
                    });
            } else {
                // Reset containers
                campagnaContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona una commessa</p>';
                sedeContainer.innerHTML = '<p class="text-xs text-base-content/50 text-center py-4">Seleziona almeno una campagna</p>';
            }
        });
        
        // Delega eventi per campagne: quando cambia una checkbox campagna, ricarica le sedi
        document.getElementById('campagnaContainer').addEventListener('click', function(e) {
            // Controlla se è una checkbox o il suo parent label
            const checkbox = e.target.classList.contains('campagna-checkbox') 
                ? e.target 
                : e.target.querySelector('.campagna-checkbox');
            
            if (checkbox || e.target.classList.contains('campagna-checkbox')) {
                console.log('Campagna checkbox cliccata');
                // Usa setTimeout per aspettare che lo stato della checkbox si aggiorni
                setTimeout(() => {
                    loadSedi();
                }, 50);
            }
        });

        // Inizializza shift-click per checkbox già presenti al caricamento pagina
        document.addEventListener('DOMContentLoaded', function() {
            attachLabelClickListeners('#campagnaContainer', 'campagna-checkbox');
            attachLabelClickListeners('#sedeContainer', 'sede-checkbox');
            
            // Se ci sono filtri già applicati (dopo submit), ricarica campagne e sedi via AJAX
            const commessaSelect = document.getElementById('commessaSelect');
            const commessaSelezionata = commessaSelect.value;
            
            if (commessaSelezionata) {
                console.log('Commessa già selezionata, carico campagne e sedi...');
                
                // Valori filtrati dal server (dopo submit form)
                const campagneFiltrate = @json($macroCampagnaFilters ?? []);
                const sediFiltrate = @json($sedeFilters ?? []);
                
                // Carica campagne per la commessa
                fetch(`/admin/produzione/get-campagne?commessa=${encodeURIComponent(commessaSelezionata)}`)
                    .then(response => response.json())
                    .then(campagneDisponibili => {
                        const campagnaContainer = document.getElementById('campagnaContainer');
                        campagnaContainer.innerHTML = '';
                        
                        if (campagneDisponibili.length > 0) {
                            campagneDisponibili.forEach(campagna => {
                                const isChecked = campagneFiltrate.includes(campagna);
                                const label = document.createElement('label');
                                label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded-md select-campagna';
                                label.setAttribute('data-checkbox-label', '');
                                label.innerHTML = `
                                    <input type="checkbox" name="macro_campagna[]" value="${campagna}" 
                                           class="checkbox checkbox-success checkbox-sm campagna-checkbox" ${isChecked ? 'checked' : ''}>
                                    <span class="text-sm leading-tight">${campagna}</span>
                                `;
                                campagnaContainer.appendChild(label);
                            });
                            
                            attachLabelClickListeners('#campagnaContainer', 'campagna-checkbox');
                            
                            // Se ci sono campagne selezionate, carica le sedi
                            if (campagneFiltrate.length > 0) {
                                const params = new URLSearchParams();
                                params.append('commessa', commessaSelezionata);
                                campagneFiltrate.forEach(c => params.append('campagne[]', c));
                                
                                fetch(`/admin/produzione/get-sedi?${params.toString()}`)
                                    .then(response => response.json())
                                    .then(sediDisponibili => {
                                        const sedeContainer = document.getElementById('sedeContainer');
                                        sedeContainer.innerHTML = '';
                                        
                                        if (sediDisponibili.length > 0) {
                                            sediDisponibili.forEach(sede => {
                                                const isChecked = sediFiltrate.includes(sede);
                                                const label = document.createElement('label');
                                                label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded-md select-sede';
                                                label.setAttribute('data-checkbox-label', '');
                                                label.innerHTML = `
                                                    <input type="checkbox" name="sede[]" value="${sede}" 
                                                           class="checkbox checkbox-info checkbox-sm sede-checkbox" ${isChecked ? 'checked' : ''}>
                                                    <span class="text-sm leading-tight">${sede}</span>
                                                `;
                                                sedeContainer.appendChild(label);
                                            });
                                            
                                            attachLabelClickListeners('#sedeContainer', 'sede-checkbox');
                                        }
                                    })
                                    .catch(error => console.error('Errore caricamento sedi:', error));
                            }
                        }
                    })
                    .catch(error => console.error('Errore caricamento campagne:', error));
            }
            
            // NOTA: drag-to-scroll e column-toggle sono ora gestiti dal componente table-advanced
        });
    </script>
</x-admin.wrapper>
