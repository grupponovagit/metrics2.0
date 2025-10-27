<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('KPI Target') }}</x-slot>
    
    <x-admin.page-header 
        title="KPI Target" 
        subtitle="Gestione Target Mensili e Rendiconto Produzione"
        icon="bullseye"
        iconColor="secondary"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.kpi_target.create') }}" class="btn btn-success">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Nuovo KPI
            </a>
            <a href="{{ route('admin.ict.index') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- FILTRO MESE/ANNO --}}
    <x-admin.card tone="light" shadow="md" padding="normal" class="mb-6">
        <form method="GET" action="{{ route('admin.ict.kpi_target') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="mese" class="block text-sm font-medium text-base-content mb-2">
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
            
            <div>
                <button type="submit" class="btn btn-primary">
                    <x-ui.icon name="search" class="h-4 w-4" />
                    Filtra
                </button>
            </div>
        </form>
        
        {{-- Info Filtro Attivo --}}
        <div class="mt-4 text-sm text-base-content/70">
            <strong>Filtro attivo:</strong> 
            {{ date('F Y', mktime(0, 0, 0, $mese, 1, $anno)) }}
            <span class="ml-2 text-xs">(Anno: {{ $anno }}, Mese: {{ str_pad($mese, 2, '0', STR_PAD_LEFT) }})</span>
        </div>
    </x-admin.card>
    
    {{-- TABS --}}
    <div role="tablist" class="tabs tabs-boxed bg-base-200 mb-6">
        <a role="tab" class="tab tab-active" id="tab-target" onclick="switchTab('target')">
            <x-ui.icon name="chart-bar" class="h-4 w-4 mr-2" />
            Target Mensili (Pianificazione)
        </a>
        <a role="tab" class="tab" id="tab-rendiconto" onclick="switchTab('rendiconto')">
            <x-ui.icon name="chart-line" class="h-4 w-4 mr-2" />
            Rendiconto Produzione (Consuntivo)
        </a>
    </div>
    
    {{-- TABELLA 1: TARGET MENSILI --}}
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
        
        <form method="POST" action="{{ route('admin.ict.kpi_target.update') }}" id="form-target">
            @csrf
            <input type="hidden" name="tabella" value="target_mensili">
            
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200 sticky top-0">
                        <tr>
                            <th class="w-12">
                                <input type="checkbox" id="select-all-target" class="checkbox checkbox-sm" onchange="toggleAllTarget(this)">
                            </th>
                            <th class="font-bold">Commessa</th>
                            <th class="font-bold">Sede CRM</th>
                            <th class="font-bold">Sede Estesa</th>
                            <th class="font-bold">Nome KPI</th>
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
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-sm kpi-checkbox-target" value="{{ $kpi->id }}" onchange="updateBulkActionsTarget()">
                                </td>
                                <td class="font-medium editable-cell" contenteditable="true" data-field="commessa" data-id="{{ $kpi->id }}" data-original="{{ $kpi->commessa }}">{{ $kpi->commessa }}</td>
                                <td class="editable-cell" contenteditable="true" data-field="sede_crm" data-id="{{ $kpi->id }}" data-original="{{ $kpi->sede_crm }}">{{ $kpi->sede_crm }}</td>
                                <td class="text-sm text-base-content/70 editable-cell" contenteditable="true" data-field="sede_estesa" data-id="{{ $kpi->id }}" data-original="{{ $kpi->sede_estesa }}">{{ $kpi->sede_estesa }}</td>
                                <td class="editable-cell" contenteditable="true" data-field="nome_kpi" data-id="{{ $kpi->id }}" data-original="{{ $kpi->nome_kpi }}">{{ $kpi->nome_kpi }}</td>
                                <td class="editable-cell" contenteditable="true" data-field="tipologia_obiettivo" data-id="{{ $kpi->id }}" data-original="{{ $kpi->tipologia_obiettivo }}">{{ $kpi->tipologia_obiettivo }}</td>
                                <td class="text-center">{{ $kpi->anno }}</td>
                                <td class="text-center">{{ $kpi->mese }}</td>
                                <td class="text-center font-semibold editable-cell" contenteditable="true" data-field="valore_kpi" data-id="{{ $kpi->id }}" data-original="{{ $kpi->valore_kpi }}">{{ number_format($kpi->valore_kpi, 2) }}</td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($kpi->kpi_variato)
                                            <div class="flex flex-col items-start text-xs">
                                                <span class="badge badge-warning badge-sm">{{ number_format($kpi->kpi_variato, 2) }}</span>
                                                <span class="text-xs text-base-content/60">
                                                    {{ $kpi->data_validita_inizio ? \Carbon\Carbon::parse($kpi->data_validita_inizio)->format('d/m') : '' }}
                                                    @if($kpi->data_validita_fine)
                                                        - {{ \Carbon\Carbon::parse($kpi->data_validita_fine)->format('d/m') }}
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <span class="badge badge-ghost badge-sm">Nessuna</span>
                                        @endif
                                        <button type="button" onclick="openVariazioneModal({{ json_encode($kpi) }})" class="btn btn-xs btn-outline" title="Modifica Variazione">
                                            <x-ui.icon name="pencil" class="h-3 w-3" />
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex gap-1 justify-center">
                                        <a href="{{ route('admin.ict.kpi_target.show', $kpi->id) }}" class="btn btn-xs btn-info" title="Visualizza">
                                            <x-ui.icon name="eye" class="h-3 w-3" />
                                        </a>
                                        <form action="{{ route('admin.ict.kpi_target.delete', $kpi->id) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo KPI?')">
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
                                <td colspan="11" class="text-center py-12">
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content mb-1">Nessun target trovato</h3>
                                        <p class="text-sm text-base-content/60">Seleziona un periodo diverso o crea un nuovo KPI</p>
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
        
        <form method="POST" action="{{ route('admin.ict.kpi_target.update') }}" id="form-rendiconto">
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
    <form id="bulk-delete-form-target" action="{{ route('admin.ict.kpi_target.bulk_delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="ids" id="bulk-delete-ids-target">
    </form>
    
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
            
            fetch(`/admin/ict/kpi-target/${id}/update-field`, {
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
            fetch(`/admin/ict/kpi-target/${id}/update-variazione`, {
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
            
            fetch(`/admin/ict/kpi-target/${id}/update-variazione`, {
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
