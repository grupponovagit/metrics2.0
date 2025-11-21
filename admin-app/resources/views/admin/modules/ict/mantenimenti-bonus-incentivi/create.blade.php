<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Nuovo Bonus/Incentivo') }}</x-slot>
    
    <x-admin.page-header 
        title="Nuovo Bonus/Incentivo" 
        subtitle="Crea un nuovo mantenimento bonus/incentivo"
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
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <form method="POST" action="{{ route('admin.ict.mantenimenti_bonus_incentivi.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Istanza --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Istanza</span>
                    </label>
                    <input type="text" 
                           name="istanza" 
                           value="{{ old('istanza') }}"
                           placeholder="es. NOVA, GT ENERGIE, MEGLIOQUESTO"
                           class="input input-bordered @error('istanza') input-error @enderror">
                    @error('istanza')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Commessa --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Commessa</span>
                    </label>
                    <input type="text" 
                           name="commessa" 
                           value="{{ old('commessa') }}"
                           placeholder="es. TIM_CONSUMER, ENI_CONSUMER"
                           class="input input-bordered @error('commessa') input-error @enderror">
                    @error('commessa')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Macro Campagna --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Macro Campagna</span>
                    </label>
                    <input type="text" 
                           name="macro_campagna" 
                           value="{{ old('macro_campagna') }}"
                           placeholder="es. TIM_FIBRA_Q4"
                           class="input input-bordered @error('macro_campagna') input-error @enderror">
                    @error('macro_campagna')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipologia Ripartizione --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Tipologia Ripartizione</span>
                    </label>
                    <select name="tipologia_ripartizione" class="select select-bordered @error('tipologia_ripartizione') select-error @enderror">
                        <option value="">Seleziona tipologia</option>
                        <option value="Fissa" {{ old('tipologia_ripartizione') == 'Fissa' ? 'selected' : '' }}>Fissa</option>
                        <option value="Pezzi" {{ old('tipologia_ripartizione') == 'Pezzi' ? 'selected' : '' }}>Pezzi</option>
                        <option value="Fatturato" {{ old('tipologia_ripartizione') == 'Fatturato' ? 'selected' : '' }}>Fatturato</option>
                        <option value="Ore" {{ old('tipologia_ripartizione') == 'Ore' ? 'selected' : '' }}>Ore</option>
                        <option value="ContattiUtili" {{ old('tipologia_ripartizione') == 'ContattiUtili' ? 'selected' : '' }}>Contatti Utili</option>
                        <option value="ContattiChiusi" {{ old('tipologia_ripartizione') == 'ContattiChiusi' ? 'selected' : '' }}>Contatti Chiusi</option>
                    </select>
                    @error('tipologia_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Sedi Ripartizione --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Sedi Ripartizione</span>
                    </label>
                    <textarea name="sedi_ripartizione" 
                              rows="3"
                              placeholder="es. LAMEZIA, TARANTO, VIGEVANO (separati da virgola o JSON)"
                              class="textarea textarea-bordered @error('sedi_ripartizione') textarea-error @enderror">{{ old('sedi_ripartizione') }}</textarea>
                    @error('sedi_ripartizione')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Inserisci le sedi separate da virgola oppure in formato JSON</span>
                    </label>
                </div>
                
                {{-- Liste Ripartizione --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Liste Ripartizione</span>
                    </label>
                    <textarea name="liste_ripartizione" 
                              rows="3"
                              placeholder="es. LISTA_A, LISTA_B (separati da virgola o JSON)"
                              class="textarea textarea-bordered @error('liste_ripartizione') textarea-error @enderror">{{ old('liste_ripartizione') }}</textarea>
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
                        <span class="label-text font-semibold">Extra Bonus (€)</span>
                    </label>
                    <input type="number" 
                           name="extra_bonus" 
                           value="{{ old('extra_bonus') }}"
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
                        <span class="label-text font-semibold">Valido Dal</span>
                    </label>
                    <input type="date" 
                           name="valido_dal" 
                           value="{{ old('valido_dal') }}"
                           class="input input-bordered @error('valido_dal') input-error @enderror">
                    @error('valido_dal')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
            </div>
            
            {{-- Pulsanti --}}
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="btn btn-ghost">
                    Annulla
                </a>
                <button type="submit" class="btn btn-success">
                    <x-ui.icon name="save" class="h-4 w-4" />
                    Salva Mantenimento
                </button>
            </div>
        </form>
    </x-admin.card>
</x-admin.wrapper>

