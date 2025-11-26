<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Nuovo Mantenimento Bonus/Incentivo') }}</x-slot>
    
    <x-admin.page-header 
        title="Nuovo Mantenimento Bonus/Incentivo" 
        subtitle="Crea un nuovo mantenimento"
        icon="plus-circle"
        iconColor="success"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="normal">
        <form method="POST" action="{{ route('admin.ict.mantenimenti_bonus_incentivi.store') }}" id="form-mantenimento">
            @csrf
            
            {{-- Campo nascosto per sede_id --}}
            <input type="hidden" name="sede_id" id="sede_id_hidden" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Istanza --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-building text-info mr-1"></i>
                            ISTANZA
                        </span>
                    </label>
                    <select 
                        name="istanza" 
                        id="select-istanza"
                        class="select select-bordered uppercase @error('istanza') select-error @enderror" 
                        style="text-transform: uppercase;"
                        onchange="filtraCommesse()"
                    >
                        <option value="">-- SELEZIONA ISTANZA --</option>
                        @foreach($istanze as $istanza)
                            <option value="{{ $istanza }}" {{ old('istanza') == $istanza ? 'selected' : '' }}>
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
                            COMMESSA
                        </span>
                    </label>
                    <select 
                        name="commessa" 
                        id="select-commessa"
                        class="select select-bordered uppercase @error('commessa') select-error @enderror" 
                        style="text-transform: uppercase;"
                        onchange="filtraMacroCampagne()"
                        disabled
                    >
                        <option value="">-- SELEZIONA PRIMA UN'ISTANZA --</option>
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
                            MACRO CAMPAGNA
                        </span>
                    </label>
                    <select 
                        name="macro_campagna" 
                        id="select-macro"
                        class="select select-bordered uppercase @error('macro_campagna') select-error @enderror" 
                        style="text-transform: uppercase;"
                        onchange="filtraSedi()"
                        disabled
                    >
                        <option value="">-- SELEZIONA PRIMA UNA COMMESSA --</option>
                    </select>
                    @error('macro_campagna')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Sede Ripartizione --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-map-marker-alt text-error mr-1"></i>
                            SEDE RIPARTIZIONE
                        </span>
                    </label>
                    <select 
                        name="sedi_ripartizione" 
                        id="select-sede"
                        class="select select-bordered uppercase @error('sedi_ripartizione') select-error @enderror" 
                        style="text-transform: uppercase;"
                        disabled
                    >
                        <option value="">-- SELEZIONA PRIMA MACRO CAMPAGNA --</option>
                    </select>
                    @error('sedi_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Liste Ripartizione --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-list text-primary mr-1"></i>
                            LISTE RIPARTIZIONE
                        </span>
                    </label>
                    <textarea 
                        name="liste_ripartizione" 
                        rows="3"
                        class="textarea textarea-bordered uppercase @error('liste_ripartizione') textarea-error @enderror"
                        style="text-transform: uppercase;"
                        placeholder="INSERISCI LISTE (OPZIONALE)"
                    >{{ old('liste_ripartizione') }}</textarea>
                    @error('liste_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipologia Ripartizione --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-percentage text-secondary mr-1"></i>
                            TIPOLOGIA RIPARTIZIONE
                        </span>
                    </label>
                    <select 
                        name="tipologia_ripartizione" 
                        class="select select-bordered uppercase @error('tipologia_ripartizione') select-error @enderror"
                        style="text-transform: uppercase;"
                    >
                        <option value="">-- SELEZIONA TIPOLOGIA --</option>
                        <option value="Fissa" {{ old('tipologia_ripartizione') == 'Fissa' ? 'selected' : '' }}>FISSA</option>
                        <option value="Pezzi" {{ old('tipologia_ripartizione') == 'Pezzi' ? 'selected' : '' }}>PEZZI</option>
                        <option value="Fatturato" {{ old('tipologia_ripartizione') == 'Fatturato' ? 'selected' : '' }}>FATTURATO</option>
                        <option value="Ore" {{ old('tipologia_ripartizione') == 'Ore' ? 'selected' : '' }}>ORE</option>
                        <option value="ContattiUtili" {{ old('tipologia_ripartizione') == 'ContattiUtili' ? 'selected' : '' }}>CONTATTI UTILI</option>
                        <option value="ContattiChiusi" {{ old('tipologia_ripartizione') == 'ContattiChiusi' ? 'selected' : '' }}>CONTATTI CHIUSI</option>
                    </select>
                    @error('tipologia_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Extra Bonus --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-euro-sign text-success mr-1"></i>
                            EXTRA BONUS
                        </span>
                    </label>
                    <input 
                        type="number" 
                        name="extra_bonus" 
                        value="{{ old('extra_bonus') }}"
                        step="0.01"
                        min="0"
                        class="input input-bordered @error('extra_bonus') input-error @enderror"
                        placeholder="0.00"
                    />
                    @error('extra_bonus')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Valido Dal --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-calendar-check text-info mr-1"></i>
                            VALIDO DAL
                        </span>
                    </label>
                    <input 
                        type="date" 
                        name="valido_dal" 
                        value="{{ old('valido_dal') }}"
                        class="input input-bordered @error('valido_dal') input-error @enderror"
                    />
                    @error('valido_dal')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Valido Al --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-calendar-times text-warning mr-1"></i>
                            VALIDO AL
                        </span>
                    </label>
                    <input 
                        type="date" 
                        name="valido_al" 
                        value="{{ old('valido_al') }}"
                        class="input input-bordered @error('valido_al') input-error @enderror"
                    />
                    @error('valido_al')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Data fine validità (opzionale)</span>
                    </label>
                </div>
            </div>
            
            {{-- Pulsanti --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="btn btn-ghost">
                    <i class="fas fa-times mr-2"></i>
                    ANNULLA
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>
                    SALVA
                </button>
            </div>
        </form>
    </x-admin.card>
    
    <script>
        // Dati gerarchici caricati dal controller
        const datiGerarchici = @json(json_decode($datiGerarchici));
        console.log('Dati caricati:', datiGerarchici);
        
        // Filtra commesse in base all'istanza selezionata
        function filtraCommesse() {
            const istanza = document.getElementById('select-istanza').value;
            const selectCommessa = document.getElementById('select-commessa');
            const selectMacro = document.getElementById('select-macro');
            const selectSede = document.getElementById('select-sede');
            
            // Reset commessa, macro e sede
            selectCommessa.innerHTML = '<option value="">-- SELEZIONA COMMESSA --</option>';
            selectMacro.innerHTML = '<option value="">-- SELEZIONA PRIMA UNA COMMESSA --</option>';
            selectMacro.disabled = true;
            selectSede.innerHTML = '<option value="">-- SELEZIONA PRIMA MACRO CAMPAGNA --</option>';
            selectSede.disabled = true;
            
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
                selectCommessa.appendChild(option);
            });
        }
        
        // Filtra macro campagne in base a istanza e commessa
        function filtraMacroCampagne() {
            const istanza = document.getElementById('select-istanza').value;
            const commessa = document.getElementById('select-commessa').value;
            const selectMacro = document.getElementById('select-macro');
            const selectSede = document.getElementById('select-sede');
            
            // Reset macro e sede
            selectMacro.innerHTML = '<option value="">-- SELEZIONA MACRO CAMPAGNA --</option>';
            selectSede.innerHTML = '<option value="">-- SELEZIONA PRIMA MACRO CAMPAGNA --</option>';
            selectSede.disabled = true;
            
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
                selectMacro.appendChild(option);
            });
        }
        
        // Filtra sedi in base a istanza, commessa e macro campagna
        function filtraSedi() {
            const istanza = document.getElementById('select-istanza').value;
            const commessa = document.getElementById('select-commessa').value;
            const macro = document.getElementById('select-macro').value;
            const selectSede = document.getElementById('select-sede');
            const sedeIdHidden = document.getElementById('sede_id_hidden');
            
            // Reset sede
            selectSede.innerHTML = '<option value="">-- SELEZIONA SEDE --</option>';
            if (sedeIdHidden) sedeIdHidden.value = '';
            
            if (!istanza || !commessa || !macro || !datiGerarchici[istanza]?.[commessa]?.[macro]) {
                selectSede.disabled = true;
                return;
            }
            
            // Ottieni sedi (ora sono oggetti con {id, nome})
            const sedi = datiGerarchici[istanza][commessa][macro];
            
            if (sedi.length === 0) {
                selectSede.innerHTML = '<option value="">-- NESSUNA SEDE DISPONIBILE --</option>';
                selectSede.disabled = true;
                return;
            }
            
            // Popola sedi
            selectSede.disabled = false;
            sedi.forEach(sede => {
                const option = document.createElement('option');
                option.value = sede.nome;
                option.setAttribute('data-sede-id', sede.id);
                option.textContent = sede.nome.toUpperCase();
                selectSede.appendChild(option);
            });
        }
        
        // Event listener globale per select sede (al di fuori della funzione filtraSedi)
        document.addEventListener('DOMContentLoaded', function() {
            const selectSede = document.getElementById('select-sede');
            const sedeIdHidden = document.getElementById('sede_id_hidden');
            
            selectSede.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const sedeId = selectedOption.getAttribute('data-sede-id');
                console.log('Sede selezionata - ID:', sedeId, 'Nome:', this.value);
                if (sedeIdHidden && sedeId) {
                    sedeIdHidden.value = sedeId;
                    console.log('Campo nascosto sede_id aggiornato:', sedeIdHidden.value);
                }
            });
        });
    </script>
</x-admin.wrapper>
