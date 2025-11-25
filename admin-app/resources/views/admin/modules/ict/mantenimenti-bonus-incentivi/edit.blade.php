<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Modifica Bonus/Incentivo') }}</x-slot>
    
    <x-admin.page-header 
        title="Modifica Bonus/Incentivo" 
        subtitle="Modifica mantenimento bonus/incentivo #{{ $mantenimento->id }}"
        icon="edit"
        iconColor="info"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <form method="POST" action="{{ route('admin.ict.mantenimenti_bonus_incentivi.update', $mantenimento->id) }}" id="form-bonus">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Istanza --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-building text-info mr-1"></i>
                            Istanza
                        </span>
                    </label>
                    <select name="istanza" id="select-istanza" class="select select-bordered @error('istanza') select-error @enderror" onchange="filtraCommesse()">
                        <option value="">Seleziona istanza</option>
                        @foreach($istanze as $istanza)
                            <option value="{{ $istanza }}" {{ old('istanza', $mantenimento->istanza) == $istanza ? 'selected' : '' }}>
                                {{ $istanza }}
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
                            Commessa
                        </span>
                    </label>
                    <select name="commessa" id="select-commessa" class="select select-bordered @error('commessa') select-error @enderror" onchange="filtraMacroCampagne()">
                        <option value="">Seleziona prima un'istanza</option>
                    </select>
                    @error('commessa')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Macro Campagne - CHECKBOX MULTIPLI --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-bullhorn text-success mr-1"></i>
                            Macro Campagne (Selezione Multipla)
                        </span>
                        <div class="flex gap-2">
                            <button type="button" onclick="selezionaTutteMacro()" class="btn btn-xs btn-success">
                                <i class="fas fa-check-double mr-1"></i> Seleziona Tutte
                            </button>
                            <button type="button" onclick="deselezionaTutteMacro()" class="btn btn-xs btn-outline btn-success">
                                <i class="fas fa-times mr-1"></i> Deseleziona Tutte
                            </button>
                        </div>
                    </label>
                    <div id="macro-container" class="border border-base-300 rounded-lg p-4 bg-base-100 max-h-[200px] overflow-y-auto">
                        <p class="text-sm text-base-content/50 text-center py-6">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Caricamento macro campagne...
                        </p>
                    </div>
                    @error('macro_campagna')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipologia Ripartizione --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-tags text-primary mr-1"></i>
                            Tipologia Ripartizione
                        </span>
                    </label>
                    <select name="tipologia_ripartizione" class="select select-bordered @error('tipologia_ripartizione') select-error @enderror">
                        <option value="">Seleziona tipologia</option>
                        <option value="Fissa" {{ old('tipologia_ripartizione', $mantenimento->tipologia_ripartizione) == 'Fissa' ? 'selected' : '' }}>Fissa</option>
                        <option value="Pezzi" {{ old('tipologia_ripartizione', $mantenimento->tipologia_ripartizione) == 'Pezzi' ? 'selected' : '' }}>Pezzi</option>
                        <option value="Fatturato" {{ old('tipologia_ripartizione', $mantenimento->tipologia_ripartizione) == 'Fatturato' ? 'selected' : '' }}>Fatturato</option>
                        <option value="Ore" {{ old('tipologia_ripartizione', $mantenimento->tipologia_ripartizione) == 'Ore' ? 'selected' : '' }}>Ore</option>
                        <option value="ContattiUtili" {{ old('tipologia_ripartizione', $mantenimento->tipologia_ripartizione) == 'ContattiUtili' ? 'selected' : '' }}>Contatti Utili</option>
                        <option value="ContattiChiusi" {{ old('tipologia_ripartizione', $mantenimento->tipologia_ripartizione) == 'ContattiChiusi' ? 'selected' : '' }}>Contatti Chiusi</option>
                    </select>
                    @error('tipologia_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Sedi Ripartizione - CHECKBOX CON ICONE --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-map-marker-alt text-error mr-1"></i>
                            Sedi Ripartizione (Selezione Multipla)
                        </span>
                        <div class="flex gap-2">
                            <button type="button" onclick="selezionaTutteSedi()" class="btn btn-xs btn-success">
                                <i class="fas fa-check-double mr-1"></i> Seleziona Tutte
                            </button>
                            <button type="button" onclick="deselezionaTutteSedi()" class="btn btn-xs btn-outline btn-success">
                                <i class="fas fa-times mr-1"></i> Deseleziona Tutte
                            </button>
                        </div>
                    </label>
                    <div id="sedi-container" class="border border-base-300 rounded-lg p-4 bg-base-100 max-h-[300px] overflow-y-auto">
                        <p class="text-sm text-base-content/50 text-center py-8">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Caricamento sedi...
                        </p>
                    </div>
                    @error('sedi_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Liste Ripartizione - INPUT TESTO --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-list-ul text-secondary mr-1"></i>
                            Liste Ripartizione
                        </span>
                    </label>
                    <textarea name="liste_ripartizione" 
                              rows="3"
                              placeholder="es. LISTA_A, LISTA_B (separati da virgola o JSON)"
                              class="textarea textarea-bordered @error('liste_ripartizione') textarea-error @enderror">{{ old('liste_ripartizione', $mantenimento->liste_ripartizione) }}</textarea>
                    @error('liste_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Inserisci le liste separate da virgola oppure in formato JSON</span>
                    </label>
                </div>
                
                {{-- Extra Bonus --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">
                            <i class="fas fa-euro-sign text-success mr-1"></i>
                            Extra Bonus (€)
                        </span>
                    </label>
                    <input type="number" 
                           name="extra_bonus" 
                           value="{{ old('extra_bonus', $mantenimento->extra_bonus) }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="input input-bordered @error('extra_bonus') input-error @enderror">
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
                            <i class="fas fa-calendar-plus text-info mr-1"></i>
                            Valido Dal
                        </span>
                    </label>
                    <input type="date" 
                           name="valido_dal" 
                           value="{{ old('valido_dal', $mantenimento->valido_dal ? $mantenimento->valido_dal->format('Y-m-d') : '') }}"
                           class="input input-bordered @error('valido_dal') input-error @enderror">
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
                            <i class="fas fa-calendar-minus text-error mr-1"></i>
                            Valido Al
                        </span>
                    </label>
                    <input type="date" 
                           name="valido_al" 
                           value="{{ old('valido_al', $mantenimento->valido_al ? $mantenimento->valido_al->format('Y-m-d') : '') }}"
                           class="input input-bordered @error('valido_al') input-error @enderror">
                    @error('valido_al')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Lascia vuoto se non ha scadenza</span>
                    </label>
                </div>
            </div>
            
            {{-- Pulsanti --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="btn btn-ghost">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-save mr-2"></i>
                    Aggiorna Mantenimento
                </button>
            </div>
        </form>
    </x-admin.card>
    
    <script>
        // Dati gerarchici caricati dal controller
        const datiGerarchici = @json(json_decode($datiGerarchici));
        const sediSelezionate = @json($sediSelezionate);
        const macroCampagneSelezionate = @json($macroCampagneSelezionate);
        const valoriOriginali = {
            istanza: '{{ old('istanza', $mantenimento->istanza) }}',
            commessa: '{{ old('commessa', $mantenimento->commessa) }}'
        };
        
        console.log('Dati caricati:', datiGerarchici);
        console.log('Sedi selezionate:', sediSelezionate);
        console.log('Macro campagne selezionate:', macroCampagneSelezionate);
        
        // Inizializza i filtri con i valori esistenti
        document.addEventListener('DOMContentLoaded', function() {
            if (valoriOriginali.istanza) {
                filtraCommesse();
                setTimeout(() => {
                    document.getElementById('select-commessa').value = valoriOriginali.commessa;
                    filtraMacroCampagne();
                    setTimeout(() => {
                        // Seleziona le macro campagne salvate
                        macroCampagneSelezionate.forEach(macro => {
                            const checkbox = document.querySelector(`.macro-checkbox[value="${macro}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                                checkbox.dispatchEvent(new Event('change'));
                            }
                        });
                        filtraSedi();
                    }, 100);
                }, 100);
            }
        });
        
        // Filtra commesse in base all'istanza selezionata
        function filtraCommesse() {
            const istanza = document.getElementById('select-istanza').value;
            const selectCommessa = document.getElementById('select-commessa');
            
            // Reset commessa, macro e sedi
            selectCommessa.innerHTML = '<option value="">Seleziona commessa</option>';
            document.getElementById('macro-container').innerHTML = `
                <p class="text-sm text-base-content/50 text-center py-6">
                    <i class="fas fa-info-circle mr-1"></i>
                    Seleziona prima istanza e commessa per vedere le macro campagne disponibili
                </p>
            `;
            document.getElementById('sedi-container').innerHTML = `
                <p class="text-sm text-base-content/50 text-center py-8">
                    <i class="fas fa-info-circle mr-1"></i>
                    Seleziona prima istanza, commessa e macro campagne per vedere le sedi disponibili
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
                option.textContent = commessa;
                selectCommessa.appendChild(option);
            });
        }
        
        // Filtra macro campagne in base a istanza e commessa
        function filtraMacroCampagne() {
            const istanza = document.getElementById('select-istanza').value;
            const commessa = document.getElementById('select-commessa').value;
            const container = document.getElementById('macro-container');
            
            // Reset sedi
            document.getElementById('sedi-container').innerHTML = `
                <p class="text-sm text-base-content/50 text-center py-8">
                    <i class="fas fa-info-circle mr-1"></i>
                    Seleziona prima istanza, commessa e macro campagne per vedere le sedi disponibili
                </p>
            `;
            
            if (!istanza || !commessa || !datiGerarchici[istanza]?.[commessa]) {
                container.innerHTML = `
                    <p class="text-sm text-base-content/50 text-center py-6">
                        <i class="fas fa-info-circle mr-1"></i>
                        Seleziona prima istanza e commessa per vedere le macro campagne disponibili
                    </p>
                `;
                return;
            }
            
            // Ottieni macro campagne
            const macroCampagne = Object.keys(datiGerarchici[istanza][commessa]).sort();
            
            if (macroCampagne.length === 0) {
                container.innerHTML = `
                    <p class="text-sm text-base-content/50 text-center py-6">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Nessuna macro campagna disponibile
                    </p>
                `;
                return;
            }
            
            // Crea checkbox per ogni macro campagna
            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">';
            macroCampagne.forEach(macro => {
                html += `
                    <label class="flex items-center gap-2 p-2 border border-base-300 rounded-lg cursor-pointer hover:bg-base-200 transition-all">
                        <input type="checkbox" 
                               name="macro_campagna[]" 
                               value="${macro}" 
                               class="checkbox checkbox-success macro-checkbox"
                               onchange="filtraSedi()">
                        <div class="flex items-center gap-2 flex-1">
                            <i class="fas fa-bullhorn text-success text-xs"></i>
                            <span class="font-medium uppercase text-xs">${macro.toUpperCase()}</span>
                        </div>
                        <i class="fas fa-check text-success hidden checkbox-icon"></i>
                    </label>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
            
            // Aggiungi event listener per mostrare/nascondere icona check
            document.querySelectorAll('.macro-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const icon = this.closest('label').querySelector('.checkbox-icon');
                    if (this.checked) {
                        icon.classList.remove('hidden');
                        this.closest('label').classList.add('bg-success/10', 'border-success');
                    } else {
                        icon.classList.add('hidden');
                        this.closest('label').classList.remove('bg-success/10', 'border-success');
                    }
                });
            });
        }
        
        // Filtra sedi in base a istanza, commessa e macro campagne selezionate
        function filtraSedi() {
            const istanza = document.getElementById('select-istanza').value;
            const commessa = document.getElementById('select-commessa').value;
            const macroSelezionate = Array.from(document.querySelectorAll('.macro-checkbox:checked')).map(cb => cb.value);
            const container = document.getElementById('sedi-container');
            
            if (!istanza || !commessa || macroSelezionate.length === 0) {
                container.innerHTML = `
                    <p class="text-sm text-base-content/50 text-center py-8">
                        <i class="fas fa-info-circle mr-1"></i>
                        Seleziona prima istanza, commessa e macro campagne per vedere le sedi disponibili
                    </p>
                `;
                return;
            }
            
            // Ottieni sedi per tutte le macro campagne selezionate
            let tutteleSedi = new Set();
            macroSelezionate.forEach(macro => {
                if (datiGerarchici[istanza]?.[commessa]?.[macro]) {
                    datiGerarchici[istanza][commessa][macro].forEach(sede => tutteleSedi.add(sede));
                }
            });
            
            const sedi = Array.from(tutteleSedi).sort();
            
            if (sedi.length === 0) {
                container.innerHTML = `
                    <p class="text-sm text-base-content/50 text-center py-8">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Nessuna sede disponibile per questa combinazione
                    </p>
                `;
                return;
            }
            
            // Crea checkbox per ogni sede
            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';
            sedi.forEach(sede => {
                const isChecked = sediSelezionate.includes(sede);
                html += `
                    <label class="flex items-center gap-2 p-3 border border-base-300 rounded-lg cursor-pointer hover:bg-base-200 transition-all ${isChecked ? 'bg-success/10 border-success' : ''}">
                        <input type="checkbox" 
                               name="sedi_ripartizione[]" 
                               value="${sede}" 
                               class="checkbox checkbox-success sede-checkbox"
                               ${isChecked ? 'checked' : ''}>
                        <div class="flex items-center gap-2 flex-1">
                            <i class="fas fa-map-marker-alt text-error"></i>
                            <span class="font-medium uppercase text-sm">${sede.toUpperCase()}</span>
                        </div>
                        <i class="fas fa-check text-success ${isChecked ? '' : 'hidden'} checkbox-icon"></i>
                    </label>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
            
            // Aggiungi event listener per mostrare/nascondere icona check
            document.querySelectorAll('.sede-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const icon = this.closest('label').querySelector('.checkbox-icon');
                    if (this.checked) {
                        icon.classList.remove('hidden');
                        this.closest('label').classList.add('bg-success/10', 'border-success');
                    } else {
                        icon.classList.add('hidden');
                        this.closest('label').classList.remove('bg-success/10', 'border-success');
                    }
                });
            });
        }
        
        // Seleziona tutte le macro campagne
        function selezionaTutteMacro() {
            document.querySelectorAll('.macro-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
            filtraSedi(); // Aggiorna le sedi
        }
        
        // Deseleziona tutte le macro campagne
        function deselezionaTutteMacro() {
            document.querySelectorAll('.macro-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
            filtraSedi(); // Aggiorna le sedi
        }
        
        // Seleziona tutte le sedi
        function selezionaTutteSedi() {
            document.querySelectorAll('.sede-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
        }
        
        // Deseleziona tutte le sedi
        function deselezionaTutteSedi() {
            document.querySelectorAll('.sede-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
        }
    </script>
</x-admin.wrapper>
