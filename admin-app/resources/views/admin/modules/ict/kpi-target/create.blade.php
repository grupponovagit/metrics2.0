<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Nuovo KPI Target') }}</x-slot>
    
    <x-admin.page-header 
        title="Nuovo KPI Target" 
        subtitle="Crea un nuovo target mensile"
        icon="plus-circle"
        iconColor="success"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.kpi_target') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="normal">
        <form method="POST" action="{{ route('admin.ict.kpi_target.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Commessa --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Commessa <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="commessa" 
                        value="{{ old('commessa') }}"
                        placeholder="es. TIM_CONSUMER"
                        class="input input-bordered @error('commessa') input-error @enderror" 
                        list="commesse-list"
                        required
                    />
                    <datalist id="commesse-list">
                        @foreach($commesse as $commessa)
                            <option value="{{ $commessa }}">
                        @endforeach
                    </datalist>
                    @error('commessa')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Sede CRM --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Sede CRM <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="sede_crm" 
                        value="{{ old('sede_crm') }}"
                        placeholder="es. LAMEZIA"
                        class="input input-bordered @error('sede_crm') input-error @enderror" 
                        list="sedi-list"
                        required
                    />
                    <datalist id="sedi-list">
                        @foreach($sedi as $sede)
                            <option value="{{ $sede }}">
                        @endforeach
                    </datalist>
                    @error('sede_crm')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Sede Estesa --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Sede Estesa</span>
                    </label>
                    <input 
                        type="text" 
                        name="sede_estesa" 
                        value="{{ old('sede_estesa') }}"
                        placeholder="es. LAMEZIA TERME"
                        class="input input-bordered @error('sede_estesa') input-error @enderror"
                    />
                    @error('sede_estesa')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Nome KPI --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Nome KPI <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="nome_kpi" 
                        value="{{ old('nome_kpi') }}"
                        placeholder="es. Vendite, Lead, Conversioni"
                        class="input input-bordered @error('nome_kpi') input-error @enderror" 
                        required
                    />
                    @error('nome_kpi')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Tipo KPI --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Tipo KPI</span>
                    </label>
                    <select name="tipo_kpi" class="select select-bordered @error('tipo_kpi') select-error @enderror">
                        <option value="">-- Seleziona tipo (opzionale) --</option>
                        <option value="PRODOTTO" {{ old('tipo_kpi') == 'PRODOTTO' ? 'selected' : '' }}>Prodotto</option>
                        <option value="INSERITO" {{ old('tipo_kpi') == 'INSERITO' ? 'selected' : '' }}>Inserito</option>
                        <option value="KO" {{ old('tipo_kpi') == 'KO' ? 'selected' : '' }}>KO</option>
                        <option value="BACKLOG" {{ old('tipo_kpi') == 'BACKLOG' ? 'selected' : '' }}>BackLog</option>
                        <option value="BACKLOG_PARTNER" {{ old('tipo_kpi') == 'BACKLOG_PARTNER' ? 'selected' : '' }}>BackLog Partner</option>
                        <option value="RESA_PRODOTTO" {{ old('tipo_kpi') == 'RESA_PRODOTTO' ? 'selected' : '' }}>Resa Prodotto</option>
                        <option value="RESA_INSERITO" {{ old('tipo_kpi') == 'RESA_INSERITO' ? 'selected' : '' }}>Resa Inserito</option>
                        <option value="ORE" {{ old('tipo_kpi') == 'ORE' ? 'selected' : '' }}>Ore</option>
                        <option value="OBIETTIVO" {{ old('tipo_kpi') == 'OBIETTIVO' ? 'selected' : '' }}>Obiettivo</option>
                        <option value="PASSO_GIORNO" {{ old('tipo_kpi') == 'PASSO_GIORNO' ? 'selected' : '' }}>Passo Giorno</option>
                        <option value="ORE_PAF" {{ old('tipo_kpi') == 'ORE_PAF' ? 'selected' : '' }}>Ore PAF</option>
                        <option value="PEZZI_PAF" {{ old('tipo_kpi') == 'PEZZI_PAF' ? 'selected' : '' }}>Pezzi PAF</option>
                        <option value="RESA_PAF" {{ old('tipo_kpi') == 'RESA_PAF' ? 'selected' : '' }}>Resa PAF</option>
                    </select>
                    @error('tipo_kpi')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Categorizza il tipo di KPI (Prodotto, Inserito, Resa, ecc.)</span>
                    </label>
                </div>
                
                {{-- Anno --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Anno <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="number" 
                        name="anno" 
                        value="{{ old('anno', date('Y')) }}"
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
                        <span class="label-text font-semibold">Mese <span class="text-error">*</span></span>
                    </label>
                    <select name="mese" class="select select-bordered @error('mese') select-error @enderror" required>
                        <option value="">Seleziona mese</option>
                        <option value="1" {{ old('mese', date('n')) == 1 ? 'selected' : '' }}>Gennaio</option>
                        <option value="2" {{ old('mese', date('n')) == 2 ? 'selected' : '' }}>Febbraio</option>
                        <option value="3" {{ old('mese', date('n')) == 3 ? 'selected' : '' }}>Marzo</option>
                        <option value="4" {{ old('mese', date('n')) == 4 ? 'selected' : '' }}>Aprile</option>
                        <option value="5" {{ old('mese', date('n')) == 5 ? 'selected' : '' }}>Maggio</option>
                        <option value="6" {{ old('mese', date('n')) == 6 ? 'selected' : '' }}>Giugno</option>
                        <option value="7" {{ old('mese', date('n')) == 7 ? 'selected' : '' }}>Luglio</option>
                        <option value="8" {{ old('mese', date('n')) == 8 ? 'selected' : '' }}>Agosto</option>
                        <option value="9" {{ old('mese', date('n')) == 9 ? 'selected' : '' }}>Settembre</option>
                        <option value="10" {{ old('mese', date('n')) == 10 ? 'selected' : '' }}>Ottobre</option>
                        <option value="11" {{ old('mese', date('n')) == 11 ? 'selected' : '' }}>Novembre</option>
                        <option value="12" {{ old('mese', date('n')) == 12 ? 'selected' : '' }}>Dicembre</option>
                    </select>
                    @error('mese')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
                
                {{-- Valore KPI --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Valore KPI <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="number" 
                        name="valore_kpi" 
                        value="{{ old('valore_kpi') }}"
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
                        value="{{ old('kpi_variato') }}"
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
                        value="{{ old('data_validita_inizio') }}"
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
                        value="{{ old('data_validita_fine') }}"
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
                <a href="{{ route('admin.ict.kpi_target') }}" class="btn btn-ghost">
                    Annulla
                </a>
                <button type="submit" class="btn btn-success">
                    <x-ui.icon name="save" class="h-4 w-4" />
                    Salva KPI
                </button>
            </div>
        </form>
    </x-admin.card>
    
    <script>
        // Abilita/disabilita campi data in base a kpi_variato
        document.getElementById('kpi_variato').addEventListener('input', function() {
            const dataInizio = document.getElementById('data_validita_inizio');
            if (this.value) {
                dataInizio.required = true;
                dataInizio.parentElement.querySelector('.label-text').innerHTML = 'Data Cambio <span class="text-error">*</span>';
            } else {
                dataInizio.required = false;
                dataInizio.parentElement.querySelector('.label-text').innerHTML = 'Data Cambio';
            }
        });
    </script>
</x-admin.wrapper>

