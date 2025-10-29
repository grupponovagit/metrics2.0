<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Dettaglio KPI Target') }}</x-slot>
    
    <x-admin.page-header 
        title="Dettaglio KPI Target" 
        subtitle="Visualizza informazioni complete del target"
        icon="eye"
        iconColor="info"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.kpi_target', ['anno' => $kpi->anno, 'mese' => sprintf('%02d', $kpi->mese)]) }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Card Principale --}}
        <div class="lg:col-span-2">
            <x-admin.card tone="light" shadow="lg" padding="normal">
                <h3 class="text-xl font-bold text-base-content mb-6 flex items-center gap-2">
                    <x-ui.icon name="chart-bar" class="h-5 w-5 text-primary" />
                    Informazioni KPI
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Commessa</span>
                        </label>
                        <div class="text-lg font-medium">{{ $kpi->commessa }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Sede CRM</span>
                        </label>
                        <div class="text-lg font-medium">{{ $kpi->sede_crm }}</div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Sede Estesa</span>
                        </label>
                        <div class="text-lg font-medium">{{ $kpi->sede_estesa ?? 'N/D' }}</div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Nome KPI</span>
                        </label>
                        <div class="text-2xl font-bold text-primary">{{ $kpi->nome_kpi }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Tipo KPI</span>
                        </label>
                        <div class="text-lg font-medium">
                            @if($kpi->tipo_kpi)
                                <span class="badge badge-primary">{{ $kpi->tipo_kpi }}</span>
                            @else
                                <span class="text-base-content/40">Non assegnato</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Anno</span>
                        </label>
                        <div class="text-lg font-medium">{{ $kpi->anno }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-semibold text-base-content/70">Mese</span>
                        </label>
                        <div class="text-lg font-medium">
                            {{ \Carbon\Carbon::create($kpi->anno, $kpi->mese, 1)->locale('it')->translatedFormat('F Y') }}
                        </div>
                    </div>
                </div>
                
                <div class="divider my-6"></div>
                
                <h4 class="text-lg font-bold text-base-content mb-4">Valori KPI</h4>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="stat bg-base-200 rounded-lg">
                        <div class="stat-title">Valore KPI Iniziale</div>
                        <div class="stat-value text-success">{{ number_format($kpi->valore_kpi, 2) }}</div>
                        <div class="stat-desc">Valore target di inizio mese</div>
                    </div>
                    
                    @if($kpi->kpi_variato)
                        <div class="stat bg-warning/20 rounded-lg border-2 border-warning">
                            <div class="stat-title flex items-center gap-2">
                                <x-ui.icon name="exclamation-triangle" class="h-4 w-4 text-warning" />
                                KPI Variato
                            </div>
                            <div class="stat-value text-warning">{{ number_format($kpi->kpi_variato, 2) }}</div>
                            <div class="stat-desc">
                                @if($kpi->data_validita_inizio)
                                    Applicato dal {{ \Carbon\Carbon::parse($kpi->data_validita_inizio)->format('d/m/Y') }}
                                    @if($kpi->data_validita_fine)
                                        al {{ \Carbon\Carbon::parse($kpi->data_validita_fine)->format('d/m/Y') }}
                                    @else
                                        fino a fine mese
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        {{-- Calcolo Media Ponderata --}}
                        @php
                            $dataInizio = \Carbon\Carbon::create($kpi->anno, $kpi->mese, 1);
                            $dataFineMese = $dataInizio->copy()->endOfMonth();
                            $totaleGiorni = $dataFineMese->day;
                            
                            $dataCambio = \Carbon\Carbon::parse($kpi->data_validita_inizio);
                            $dataFineVariato = $kpi->data_validita_fine ? \Carbon\Carbon::parse($kpi->data_validita_fine) : $dataFineMese;
                            
                            $giorniIniziale = $dataCambio->day - 1;
                            $giorniVariato = $dataFineVariato->day - $dataCambio->day + 1;
                            
                            $mediaPonderata = (($kpi->valore_kpi * $giorniIniziale) + ($kpi->kpi_variato * $giorniVariato)) / $totaleGiorni;
                        @endphp
                        
                        <div class="stat bg-info/20 rounded-lg border-2 border-info">
                            <div class="stat-title">Media Ponderata Mensile</div>
                            <div class="stat-value text-info">{{ number_format($mediaPonderata, 2) }}</div>
                            <div class="stat-desc">
                                Calcolata su {{ $giorniIniziale }} giorni (iniziale) + {{ $giorniVariato }} giorni (variato)
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>
        
        {{-- Sidebar Info --}}
        <div class="lg:col-span-1">
            {{-- Timeline Variazioni --}}
            @if($kpi->kpi_variato)
                <x-admin.card tone="light" shadow="lg" padding="normal" class="mb-6">
                    <h4 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                        <x-ui.icon name="clock" class="h-5 w-5 text-warning" />
                        Timeline Variazioni
                    </h4>
                    
                    <ul class="steps steps-vertical">
                        <li class="step step-success">
                            <div class="text-left ml-4">
                                <div class="font-semibold">{{ number_format($kpi->valore_kpi, 2) }}</div>
                                <div class="text-xs text-base-content/60">
                                    Dal {{ \Carbon\Carbon::create($kpi->anno, $kpi->mese, 1)->format('d/m/Y') }}
                                </div>
                            </div>
                        </li>
                        <li class="step step-warning">
                            <div class="text-left ml-4">
                                <div class="font-semibold">{{ number_format($kpi->kpi_variato, 2) }}</div>
                                <div class="text-xs text-base-content/60">
                                    Dal {{ \Carbon\Carbon::parse($kpi->data_validita_inizio)->format('d/m/Y') }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </x-admin.card>
            @endif
            
            {{-- Azioni --}}
            <x-admin.card tone="light" shadow="lg" padding="normal">
                <h4 class="text-lg font-bold text-base-content mb-4">Azioni</h4>
                
                <div class="flex flex-col gap-2">
                    <button type="button" onclick="openEditModal()" class="btn btn-primary btn-block">
                        <x-ui.icon name="pencil" class="h-4 w-4" />
                        Modifica Dati Base
                    </button>
                    
                    <button type="button" onclick="openVariazioneModal()" class="btn btn-warning btn-block">
                        <x-ui.icon name="chart-line" class="h-4 w-4" />
                        Gestisci Variazione
                    </button>
                    
                    <a href="{{ route('admin.ict.kpi_target', ['anno' => $kpi->anno, 'mese' => sprintf('%02d', $kpi->mese)]) }}" class="btn btn-outline btn-secondary btn-block">
                        <x-ui.icon name="arrow-left" class="h-4 w-4" />
                        Torna alla Lista
                    </a>
                    
                    <form action="{{ route('admin.ict.kpi_target.delete', $kpi->id) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo KPI?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error btn-block">
                            <x-ui.icon name="trash" class="h-4 w-4" />
                            Elimina
                        </button>
                    </form>
                </div>
            </x-admin.card>
            
            {{-- Metadati --}}
            <x-admin.card tone="light" shadow="lg" padding="normal" class="mt-6">
                <h4 class="text-lg font-bold text-base-content mb-4">Informazioni Sistema</h4>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-base-content/60">ID:</span>
                        <span class="font-mono font-semibold ml-2">{{ $kpi->id }}</span>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
    
    {{-- MODAL MODIFICA DATI BASE --}}
    <dialog id="edit-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">
                <x-ui.icon name="pencil" class="h-5 w-5 inline text-primary" />
                Modifica Dati Base
            </h3>
            
            <form action="{{ route('admin.ict.kpi_target.store') }}" method="POST" id="form-edit">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Commessa</span>
                        </label>
                        <input type="text" name="commessa" value="{{ $kpi->commessa }}" class="input input-bordered" required>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Sede CRM</span>
                        </label>
                        <input type="text" name="sede_crm" value="{{ $kpi->sede_crm }}" class="input input-bordered" required>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Sede Estesa</span>
                        </label>
                        <input type="text" name="sede_estesa" value="{{ $kpi->sede_estesa }}" class="input input-bordered">
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Nome KPI</span>
                        </label>
                        <input type="text" name="nome_kpi" value="{{ $kpi->nome_kpi }}" class="input input-bordered" required>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tipo KPI</span>
                        </label>
                        <select name="tipo_kpi" class="select select-bordered">
                            <option value="">-- Seleziona tipo (opzionale) --</option>
                            <option value="PRODOTTO" {{ $kpi->tipo_kpi == 'PRODOTTO' ? 'selected' : '' }}>Prodotto</option>
                            <option value="INSERITO" {{ $kpi->tipo_kpi == 'INSERITO' ? 'selected' : '' }}>Inserito</option>
                            <option value="KO" {{ $kpi->tipo_kpi == 'KO' ? 'selected' : '' }}>KO</option>
                            <option value="BACKLOG" {{ $kpi->tipo_kpi == 'BACKLOG' ? 'selected' : '' }}>BackLog</option>
                            <option value="BACKLOG_PARTNER" {{ $kpi->tipo_kpi == 'BACKLOG_PARTNER' ? 'selected' : '' }}>BackLog Partner</option>
                            <option value="RESA_PRODOTTO" {{ $kpi->tipo_kpi == 'RESA_PRODOTTO' ? 'selected' : '' }}>Resa Prodotto</option>
                            <option value="RESA_INSERITO" {{ $kpi->tipo_kpi == 'RESA_INSERITO' ? 'selected' : '' }}>Resa Inserito</option>
                            <option value="ORE" {{ $kpi->tipo_kpi == 'ORE' ? 'selected' : '' }}>Ore</option>
                            <option value="OBIETTIVO" {{ $kpi->tipo_kpi == 'OBIETTIVO' ? 'selected' : '' }}>Obiettivo</option>
                            <option value="PASSO_GIORNO" {{ $kpi->tipo_kpi == 'PASSO_GIORNO' ? 'selected' : '' }}>Passo Giorno</option>
                            <option value="ORE_PAF" {{ $kpi->tipo_kpi == 'ORE_PAF' ? 'selected' : '' }}>Ore PAF</option>
                            <option value="PEZZI_PAF" {{ $kpi->tipo_kpi == 'PEZZI_PAF' ? 'selected' : '' }}>Pezzi PAF</option>
                            <option value="RESA_PAF" {{ $kpi->tipo_kpi == 'RESA_PAF' ? 'selected' : '' }}>Resa PAF</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Valore KPI</span>
                        </label>
                        <input type="number" name="valore_kpi" value="{{ $kpi->valore_kpi }}" step="0.01" min="0" class="input input-bordered" required>
                    </div>
                </div>
                
                <div class="modal-action">
                    <button type="button" class="btn" onclick="document.getElementById('edit-modal').close()">Annulla</button>
                    <button type="submit" class="btn btn-success">
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
    
    {{-- MODAL VARIAZIONE KPI --}}
    <dialog id="variazione-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">
                <x-ui.icon name="chart-line" class="h-5 w-5 inline text-warning" />
                Gestisci Variazione KPI
            </h3>
            
            <div class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Nuovo Valore KPI</span>
                    </label>
                    <input 
                        type="number" 
                        id="variazione-kpi-variato"
                        step="0.01"
                        min="0"
                        value="{{ $kpi->kpi_variato }}"
                        placeholder="Lascia vuoto per rimuovere variazione"
                        class="input input-bordered"
                    />
                    <label class="label">
                        <span class="label-text-alt">Valore KPI modificato (se cambia nel mese)</span>
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Data Cambio <span class="text-error" id="label-required">*</span></span>
                    </label>
                    <input 
                        type="date" 
                        id="variazione-data-inizio"
                        value="{{ $kpi->data_validita_inizio }}"
                        class="input input-bordered"
                    />
                    <label class="label">
                        <span class="label-text-alt">Da quale giorno si applica il nuovo valore</span>
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Data Fine (opzionale)</span>
                    </label>
                    <input 
                        type="date" 
                        id="variazione-data-fine"
                        value="{{ $kpi->data_validita_fine }}"
                        class="input input-bordered"
                    />
                    <label class="label">
                        <span class="label-text-alt">Lascia vuoto per applicare fino a fine mese</span>
                    </label>
                </div>
                
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
                @if($kpi->kpi_variato)
                    <button type="button" class="btn btn-error" onclick="rimuoviVariazione()">
                        <x-ui.icon name="trash" class="h-4 w-4" />
                        Rimuovi Variazione
                    </button>
                @endif
                <button type="button" class="btn btn-success" onclick="salvaVariazione()">
                    <x-ui.icon name="save" class="h-4 w-4" />
                    Salva
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    
    <script>
        const kpiId = {{ $kpi->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function openEditModal() {
            document.getElementById('edit-modal').showModal();
        }
        
        function openVariazioneModal() {
            document.getElementById('variazione-modal').showModal();
        }
        
        function closeVariazioneModal() {
            document.getElementById('variazione-modal').close();
        }
        
        function salvaVariazione() {
            const kpiVariato = document.getElementById('variazione-kpi-variato').value;
            const dataInizio = document.getElementById('variazione-data-inizio').value;
            const dataFine = document.getElementById('variazione-data-fine').value;
            
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
            fetch(`/admin/ict/kpi-target/${kpiId}/update-variazione`, {
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
                    location.reload();
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
            
            fetch(`/admin/ict/kpi-target/${kpiId}/update-variazione`, {
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
        
        // Gestione form modifica dati base
        document.getElementById('form-edit').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch(`/admin/ict/kpi-target/${kpiId}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Dati aggiornati con successo!');
                    location.reload();
                } else {
                    throw new Error(data.message || 'Errore durante il salvataggio');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore durante il salvataggio: ' + error.message);
            });
        });
    </script>
</x-admin.wrapper>

