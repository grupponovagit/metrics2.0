<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Modifica KPI Target') }}</x-slot>
    
    <x-admin.page-header 
        title="Modifica KPI Target" 
        subtitle="Aggiorna target mensile"
        icon="pencil"
        iconColor="warning"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.produzione.kpi_target', ['anno' => $kpi->anno, 'mese' => sprintf('%02d', $kpi->mese)]) }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="normal">
        <form method="POST" action="{{ route('admin.produzione.kpi_target.update_full', $kpi->id) }}" id="form-kpi">
            @csrf
            @method('PUT')
            
            {{-- Campo nascosto per sede_id --}}
            <input type="hidden" name="sede_id" id="sede_id_hidden" value="{{ old('sede_id', $kpi->sede_id) }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Istanza --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-building text-info mr-1"></i>
                            Istanza <span class="text-error">*</span>
                        </span>
                    </label>
                    <select 
                        name="istanza" 
                        id="select-istanza"
                        class="select select-bordered uppercase @error('istanza') select-error @enderror" 
                        style="text-transform: uppercase;"
                        onchange="filtraCommesse()"
                        required
                    >
                        <option value="">-- Seleziona istanza --</option>
                        @foreach($istanze as $istanza)
                            <option value="{{ $istanza }}" {{ old('istanza', $kpi->istanza) == $istanza ? 'selected' : '' }}>
                                {{ strtoupper($istanza) }}
                            </option>
                        @endforeach
                    </select>
                    @error('istanza')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Commessa --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-briefcase text-warning mr-1"></i>
                            Commessa <span class="text-error">*</span>
                        </span>
                    </label>
                    <select 
                        name="commessa" 
                        id="select-commessa"
                        class="select select-bordered uppercase @error('commessa') select-error @enderror" 
                        style="text-transform: uppercase;"
                        onchange="filtraMacroCampagne()"
                        required
                        disabled
                    >
                        <option value="">-- Seleziona prima un'istanza --</option>
                    </select>
                    @error('commessa')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Macro Campagna --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-bullhorn text-success mr-1"></i>
                            Macro Campagna <span class="text-error">*</span>
                        </span>
                    </label>
                    <select 
                        name="macro_campagna" 
                        id="select-macro"
                        class="select select-bordered uppercase @error('macro_campagna') select-error @enderror" 
                        style="text-transform: uppercase;"
                        onchange="filtraSedi()"
                        required
                        disabled
                    >
                        <option value="">-- Seleziona prima una commessa --</option>
                    </select>
                    @error('macro_campagna')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Sede CRM --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-map-marker-alt text-error mr-1"></i>
                            Sede CRM <span class="text-error">*</span>
                        </span>
                    </label>
                    <div id="sede-container" class="border border-base-300 rounded-lg p-3 bg-base-100 max-h-[180px] overflow-y-auto">
                        <p class="text-sm text-base-content/50 text-center py-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Seleziona istanza, commessa e macro campagna
                        </p>
                    </div>
                    @error('sede_crm')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipologia Obiettivo --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-bullseye text-primary mr-1"></i>
                            Tipologia Obiettivo <span class="text-error">*</span>
                        </span>
                    </label>
                    <select 
                        name="tipologia_obiettivo" 
                        class="select select-bordered uppercase @error('tipologia_obiettivo') select-error @enderror" 
                        style="text-transform: uppercase;"
                        required
                    >
                        <option value="">-- Seleziona tipologia --</option>
                        @foreach($tipologieObiettivo as $tipologia)
                            <option value="{{ $tipologia }}" {{ old('tipologia_obiettivo', $kpi->tipologia_obiettivo) == $tipologia ? 'selected' : '' }}>
                                {{ strtoupper($tipologia) }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipologia_obiettivo')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Nome KPI --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-chart-line text-info mr-1"></i>
                            Nome KPI <span class="text-error">*</span>
                        </span>
                    </label>
                    <select 
                        name="nome_kpi" 
                        class="select select-bordered @error('nome_kpi') select-error @enderror" 
                        required
                    >
                        <option value="">-- Seleziona nome KPI --</option>
                        @foreach($nomiKpi as $nomeKpi)
                            <option value="{{ $nomeKpi }}" {{ old('nome_kpi', $kpi->nome_kpi) == $nomeKpi ? 'selected' : '' }}>
                                {{ $nomeKpi }}
                            </option>
                        @endforeach
                    </select>
                    @error('nome_kpi')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipo KPI --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-tags text-secondary mr-1"></i>
                            Tipo KPI
                        </span>
                    </label>
                    <select name="tipo_kpi" class="select select-bordered uppercase @error('tipo_kpi') select-error @enderror" style="text-transform: uppercase;">
                        <option value="">-- Seleziona tipo --</option>
                        @foreach($tipiKpi as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo_kpi', $kpi->tipo_kpi) == $tipo ? 'selected' : '' }}>
                                {{ strtoupper($tipo) }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_kpi')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Tipologia del KPI (Residenziali o Business)</span>
                    </label>
                </div>
                
                {{-- Anno --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-calendar text-info mr-1"></i>
                            Anno <span class="text-error">*</span>
                        </span>
                    </label>
                    <input 
                        type="number" 
                        name="anno" 
                        value="{{ old('anno', $kpi->anno) }}"
                        min="2020"
                        max="2030"
                        class="input input-bordered @error('anno') input-error @enderror" 
                        required
                    />
                    @error('anno')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Mese --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-calendar-alt text-success mr-1"></i>
                            Mese <span class="text-error">*</span>
                        </span>
                    </label>
                    <select name="mese" class="select select-bordered @error('mese') select-error @enderror" required>
                        <option value="">Seleziona mese</option>
                        <option value="1" {{ old('mese', $kpi->mese) == 1 ? 'selected' : '' }}>Gennaio</option>
                        <option value="2" {{ old('mese', $kpi->mese) == 2 ? 'selected' : '' }}>Febbraio</option>
                        <option value="3" {{ old('mese', $kpi->mese) == 3 ? 'selected' : '' }}>Marzo</option>
                        <option value="4" {{ old('mese', $kpi->mese) == 4 ? 'selected' : '' }}>Aprile</option>
                        <option value="5" {{ old('mese', $kpi->mese) == 5 ? 'selected' : '' }}>Maggio</option>
                        <option value="6" {{ old('mese', $kpi->mese) == 6 ? 'selected' : '' }}>Giugno</option>
                        <option value="7" {{ old('mese', $kpi->mese) == 7 ? 'selected' : '' }}>Luglio</option>
                        <option value="8" {{ old('mese', $kpi->mese) == 8 ? 'selected' : '' }}>Agosto</option>
                        <option value="9" {{ old('mese', $kpi->mese) == 9 ? 'selected' : '' }}>Settembre</option>
                        <option value="10" {{ old('mese', $kpi->mese) == 10 ? 'selected' : '' }}>Ottobre</option>
                        <option value="11" {{ old('mese', $kpi->mese) == 11 ? 'selected' : '' }}>Novembre</option>
                        <option value="12" {{ old('mese', $kpi->mese) == 12 ? 'selected' : '' }}>Dicembre</option>
                    </select>
                    @error('mese')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipologia Valore Obiettivo --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-percent text-purple-600 mr-1"></i>
                            Tipologia Valore <span class="text-error">*</span>
                        </span>
                    </label>
                    <div class="btn-group w-full">
                        <input 
                            type="radio" 
                            name="tipologia_valore_obiettivo" 
                            value="INTERO" 
                            class="btn btn-outline" 
                            id="tipo-intero"
                            {{ old('tipologia_valore_obiettivo', $kpi->tipologia_valore_obiettivo ?? 'INTERO') == 'INTERO' ? 'checked' : '' }}
                            required
                        />
                        <label for="tipo-intero" class="btn">
                            <i class="fas fa-hashtag mr-1"></i>
                            INTERO
                        </label>
                        
                        <input 
                            type="radio" 
                            name="tipologia_valore_obiettivo" 
                            value="DECIMALE" 
                            class="btn btn-outline" 
                            id="tipo-decimale"
                            {{ old('tipologia_valore_obiettivo', $kpi->tipologia_valore_obiettivo) == 'DECIMALE' ? 'checked' : '' }}
                        />
                        <label for="tipo-decimale" class="btn">
                            <i class="fas fa-percentage mr-1"></i>
                            DECIMALE (%)
                        </label>
                    </div>
                    @error('tipologia_valore_obiettivo')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Seleziona se il valore è un numero intero o una percentuale decimale</span>
                    </label>
                </div>
                
                {{-- Valore KPI --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-calculator text-warning mr-1"></i>
                            Valore KPI <span class="text-error">*</span>
                        </span>
                    </label>
                    <input 
                        type="number" 
                        name="valore_kpi" 
                        id="input-valore-kpi"
                        value="{{ old('valore_kpi', $kpi->valore_kpi) }}"
                        step="0.01"
                        min="0"
                        placeholder="Valore iniziale"
                        class="input input-bordered @error('valore_kpi') input-error @enderror" 
                        required
                    />
                    @error('valore_kpi')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt" id="hint-valore-kpi">Inserisci un numero intero</span>
                    </label>
                </div>
            </div>
            
            {{-- SEZIONE VARIAZIONE (opzionale) --}}
            <div class="divider my-8">Variazione KPI (Opzionale)</div>
            
            <div class="alert alert-info mb-6">
                <x-ui.icon name="info-circle" class="h-5 w-5" />
                <span>Se il target cambia durante il mese, compila questi campi per tracciare il nuovo valore e da quando si applica.</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- KPI Variato --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">KPI Variato</span>
                    </label>
                    <input 
                        type="number" 
                        name="kpi_variato" 
                        value="{{ old('kpi_variato', $kpi->kpi_variato) }}"
                        step="0.01"
                        min="0"
                        placeholder="Nuovo valore (se cambia)"
                        class="input input-bordered @error('kpi_variato') input-error @enderror"
                        id="kpi_variato"
                    />
                    @error('kpi_variato')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Data Validità Inizio --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Data Cambio</span>
                    </label>
                    <input 
                        type="date" 
                        name="data_validita_inizio" 
                        value="{{ old('data_validita_inizio', $kpi->data_validita_inizio?->format('Y-m-d')) }}"
                        class="input input-bordered @error('data_validita_inizio') input-error @enderror"
                        id="data_validita_inizio"
                    />
                    @error('data_validita_inizio')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Da quando si applica il nuovo valore</span>
                    </label>
                </div>
                
                {{-- Data Validità Fine --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Data Fine (opz.)</span>
                    </label>
                    <input 
                        type="date" 
                        name="data_validita_fine" 
                        value="{{ old('data_validita_fine', $kpi->data_validita_fine?->format('Y-m-d')) }}"
                        class="input input-bordered @error('data_validita_fine') input-error @enderror"
                    />
                    @error('data_validita_fine')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Lascia vuoto per fine mese</span>
                    </label>
                </div>
            </div>
            
            {{-- Pulsanti --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.produzione.kpi_target') }}" class="btn btn-ghost">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-2"></i>
                    Aggiorna KPI
                </button>
            </div>
        </form>
    </x-admin.card>
    
    <script>
        // Dati gerarchici caricati dal controller
        const datiGerarchici = @json(json_decode($datiGerarchici));
        console.log('Dati caricati:', datiGerarchici);
        
        // Valori correnti da pre-selezionare
        const istanzaCorrente = '{{ old('istanza', $kpi->istanza) }}';
        const commessaCorrente = '{{ old('commessa', $kpi->commessa) }}';
        const macroCampagnaCorrente = '{{ old('macro_campagna', $kpi->macro_campagna) }}';
        const sedeCrmCorrente = '{{ old('sede_crm', $kpi->sede_crm) }}';
        
        // Filtra commesse in base all'istanza selezionata
        function filtraCommesse() {
            const istanza = document.getElementById('select-istanza').value;
            const selectCommessa = document.getElementById('select-commessa');
            const selectMacro = document.getElementById('select-macro');
            
            // Reset commessa, macro e sede
            selectCommessa.innerHTML = '<option value="">-- Seleziona commessa --</option>';
            selectMacro.innerHTML = '<option value="">-- Seleziona prima una commessa --</option>';
            selectMacro.disabled = true;
            
            document.getElementById('sede-container').innerHTML = `
                <p class="text-sm text-base-content/50 text-center py-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Seleziona istanza, commessa e macro campagna
                </p>
            `;
            
            if (!istanza || !datiGerarchici[istanza]) {
                selectCommessa.disabled = true;
                return;
            }
            
            // Popola commesse
            selectCommessa.disabled = false;
            const commesse = Object.keys(datiGerarchici[istanza]).sort();
            commesse.forEach(commessa => {
                const option = document.createElement('option');
                option.value = commessa;
                option.textContent = commessa.toUpperCase();
                if (commessa === commessaCorrente) {
                    option.selected = true;
                }
                selectCommessa.appendChild(option);
            });
            
            // Trigger automatico per caricare macro campagne se c'è una commessa corrente
            if (commessaCorrente && istanza === istanzaCorrente) {
                filtraMacroCampagne();
            }
        }
        
        // Filtra macro campagne in base a istanza e commessa
        function filtraMacroCampagne() {
            const istanza = document.getElementById('select-istanza').value;
            const commessa = document.getElementById('select-commessa').value;
            const selectMacro = document.getElementById('select-macro');
            
            // Reset macro e sede
            selectMacro.innerHTML = '<option value="">-- Seleziona macro campagna --</option>';
            document.getElementById('sede-container').innerHTML = `
                <p class="text-sm text-base-content/50 text-center py-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Seleziona istanza, commessa e macro campagna
                </p>
            `;
            
            if (!istanza || !commessa || !datiGerarchici[istanza]?.[commessa]) {
                selectMacro.disabled = true;
                return;
            }
            
            // Popola macro campagne
            selectMacro.disabled = false;
            const macroCampagne = Object.keys(datiGerarchici[istanza][commessa]).sort();
            macroCampagne.forEach(macro => {
                const option = document.createElement('option');
                option.value = macro;
                option.textContent = macro.toUpperCase();
                if (macro === macroCampagnaCorrente) {
                    option.selected = true;
                }
                selectMacro.appendChild(option);
            });
            
            // Trigger automatico per caricare sedi se c'è una macro campagna corrente
            if (macroCampagnaCorrente && istanza === istanzaCorrente && commessa === commessaCorrente) {
                filtraSedi();
            }
        }
        
        // Filtra sedi in base a istanza, commessa e macro campagna
        function filtraSedi() {
            const istanza = document.getElementById('select-istanza').value;
            const commessa = document.getElementById('select-commessa').value;
            const macro = document.getElementById('select-macro').value;
            const container = document.getElementById('sede-container');
            
            if (!istanza || !commessa || !macro || !datiGerarchici[istanza]?.[commessa]?.[macro]) {
                container.innerHTML = `
                    <p class="text-sm text-base-content/50 text-center py-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Seleziona istanza, commessa e macro campagna
                    </p>
                `;
                return;
            }
            
            // Ottieni sedi (ora sono oggetti con {id, nome})
            const sedi = datiGerarchici[istanza][commessa][macro];
            
            if (sedi.length === 0) {
                container.innerHTML = `
                    <p class="text-sm text-base-content/50 text-center py-4">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Nessuna sede disponibile
                    </p>
                `;
                return;
            }
            
            // Crea radio buttons per ogni sede con campo nascosto per sede_estesa
            let html = '<div class="grid grid-cols-1 gap-1">';
            sedi.forEach(sede => {
                const isChecked = sede.id == sedeCrmCorrente ? ' checked' : '';
                html += `
                    <label class="flex items-center gap-2 p-2 border border-base-300 rounded-lg cursor-pointer hover:bg-base-200 transition-all sede-label ${isChecked ? 'bg-success/10 border-success' : ''}">
                        <input type="radio" 
                               name="sede_crm" 
                               value="${sede.id}" 
                               data-sede-nome="${sede.nome}"
                               class="radio radio-success radio-sm sede-radio"
                               ${isChecked}
                               required>
                        <div class="flex items-center gap-2 flex-1">
                            <i class="fas fa-map-marker-alt text-error text-xs"></i>
                            <span class="font-medium uppercase text-sm">${sede.nome.toUpperCase()}</span>
                        </div>
                        <i class="fas fa-check text-success ${isChecked ? '' : 'hidden'} radio-icon"></i>
                    </label>
                `;
            });
            html += '</div>';
            
            // Aggiungi campo nascosto per sede_estesa (nome della sede)
            html += '<input type="hidden" name="sede_estesa" id="sede_estesa_hidden" value="{{ old('sede_estesa', $kpi->sede_estesa) }}">';
            
            container.innerHTML = html;
            
            // Aggiungi event listener per mostrare/nascondere icona check e aggiornare sede_estesa
            document.querySelectorAll('.sede-radio').forEach(radio => {
                // Aggiorna sede_estesa se il radio è già checked (pre-selezione)
                if (radio.checked) {
                    const sedeEstesaHidden = document.getElementById('sede_estesa_hidden');
                    if (sedeEstesaHidden) {
                        sedeEstesaHidden.value = radio.getAttribute('data-sede-nome');
                        console.log('Sede pre-selezionata - ID:', radio.value, 'Nome:', radio.getAttribute('data-sede-nome'));
                    }
                }
                
                radio.addEventListener('change', function() {
                    // Rimuovi evidenziazione da tutte le label
                    document.querySelectorAll('.sede-label').forEach(label => {
                        label.classList.remove('bg-success/10', 'border-success');
                        label.querySelector('.radio-icon').classList.add('hidden');
                    });
                    
                    // Aggiungi evidenziazione alla label selezionata
                    if (this.checked) {
                        const label = this.closest('.sede-label');
                        label.classList.add('bg-success/10', 'border-success');
                        label.querySelector('.radio-icon').classList.remove('hidden');
                        
                        // Aggiorna campo nascosto sede_estesa con il nome della sede
                        const sedeEstesaHidden = document.getElementById('sede_estesa_hidden');
                        if (sedeEstesaHidden) {
                            sedeEstesaHidden.value = this.getAttribute('data-sede-nome');
                            console.log('Sede selezionata - ID:', this.value, 'Nome:', this.getAttribute('data-sede-nome'));
                        }
                    }
                });
            });
        }
        
        // Abilita/disabilita campi data in base a kpi_variato
        document.getElementById('kpi_variato').addEventListener('input', function() {
            const dataInizio = document.getElementById('data_validita_inizio');
            if (this.value) {
                dataInizio.required = true;
                dataInizio.parentElement.querySelector('.label-text').innerHTML = '<i class="fas fa-calendar-plus text-info mr-1"></i> Data Cambio <span class="text-error">*</span>';
            } else {
                dataInizio.required = false;
                dataInizio.parentElement.querySelector('.label-text').innerHTML = '<i class="fas fa-calendar-plus text-info mr-1"></i> Data Cambio';
            }
        });
        
        // Gestione tipologia valore obiettivo (INTERO vs DECIMALE)
        function aggiornaInputValoreKpi() {
            const inputValoreKpi = document.getElementById('input-valore-kpi');
            const hintValoreKpi = document.getElementById('hint-valore-kpi');
            const tipoIntero = document.getElementById('tipo-intero');
            const tipoDecimale = document.getElementById('tipo-decimale');
            
            if (tipoIntero.checked) {
                // Modalità INTERO
                inputValoreKpi.step = "1";
                inputValoreKpi.placeholder = "Valore intero (es: 1000)";
                hintValoreKpi.textContent = "Inserisci un numero intero";
            } else if (tipoDecimale.checked) {
                // Modalità DECIMALE/PERCENTUALE
                inputValoreKpi.step = "0.01";
                inputValoreKpi.placeholder = "Valore percentuale (es: 15.50)";
                hintValoreKpi.textContent = "Inserisci una percentuale decimale (es: 15.50 per 15,50%)";
            }
        }
        
        // Aggiungi listener ai radio button
        document.getElementById('tipo-intero').addEventListener('change', aggiornaInputValoreKpi);
        document.getElementById('tipo-decimale').addEventListener('change', aggiornaInputValoreKpi);
        
        // Imposta stato iniziale
        aggiornaInputValoreKpi();
        
        // Carica i filtri all'avvio della pagina
        document.addEventListener('DOMContentLoaded', function() {
            if (istanzaCorrente) {
                console.log('Caricamento automatico filtri per istanza:', istanzaCorrente);
                filtraCommesse();
            }
        });
    </script>
</x-admin.wrapper>
