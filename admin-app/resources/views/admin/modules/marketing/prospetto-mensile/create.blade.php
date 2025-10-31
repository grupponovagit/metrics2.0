<x-admin.wrapper>
    <x-slot name="title">{{ __('Nuovo Prospetto Mensile') }}</x-slot>
    
    <x-admin.page-header 
        title="Crea Nuovo Prospetto Mensile" 
        subtitle="Inserisci i dati JSON del prospetto mensile campagne"
        icon="plus-circle"
        iconColor="success"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.marketing.prospetto_mensile.index') }}" class="btn btn-outline btn-info gap-2">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna alla Lista
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- Errori di validazione --}}
    @if($errors->any())
        <div class="alert alert-error mb-6">
            <x-ui.icon name="exclamation-circle" class="h-5 w-5" />
            <div>
                <h3 class="font-bold">Errori di validazione:</h3>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    <form method="POST" action="{{ route('admin.marketing.prospetto_mensile.store') }}" id="prospettoForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Colonna Sinistra: Form Dati Base --}}
            <div class="lg:col-span-1">
                <x-admin.card tone="light" shadow="lg" padding="lg">
                    <h3 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                        <x-ui.icon name="info-circle" class="h-5 w-5 text-info" />
                        Informazioni Base
                    </h3>
                    
                    {{-- Nome --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Nome Prospetto *</span>
                        </label>
                        <input 
                            type="text" 
                            name="nome" 
                            value="{{ old('nome') }}" 
                            placeholder="es: Novembre 2024" 
                            class="input input-bordered w-full @error('nome') input-error @enderror"
                            required
                        />
                        @error('nome')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    
                    {{-- Mese --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Mese *</span>
                        </label>
                        <select name="mese" class="select select-bordered w-full @error('mese') select-error @enderror" required>
                            <option value="">Seleziona mese...</option>
                            @foreach($mesi as $numero => $nome)
                                <option value="{{ $numero }}" {{ old('mese') == $numero ? 'selected' : '' }}>
                                    {{ $nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('mese')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    
                    {{-- Anno --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Anno *</span>
                        </label>
                        <select name="anno" class="select select-bordered w-full @error('anno') select-error @enderror" required>
                            <option value="">Seleziona anno...</option>
                            @foreach($anni as $anno)
                                <option value="{{ $anno }}" {{ old('anno', date('Y')) == $anno ? 'selected' : '' }}>
                                    {{ $anno }}
                                </option>
                            @endforeach
                        </select>
                        @error('anno')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    
                    {{-- Giorni Lavorativi --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Giorni Lavorativi *</span>
                        </label>
                        <input 
                            type="number" 
                            name="giorni_lavorativi" 
                            value="{{ old('giorni_lavorativi', 24) }}" 
                            min="1" 
                            max="31" 
                            placeholder="es: 24" 
                            class="input input-bordered w-full @error('giorni_lavorativi') input-error @enderror"
                            required
                        />
                        <label class="label">
                            <span class="label-text-alt text-gray-500">
                                <x-ui.icon name="info-circle" class="h-3.5 w-3.5 inline" />
                                Numero di giorni lavorativi nel mese (es: 24 = 6 giorni/settimana × 4 settimane)
                            </span>
                        </label>
                        @error('giorni_lavorativi')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    
                    {{-- Descrizione --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Descrizione</span>
                        </label>
                        <textarea 
                            name="descrizione" 
                            rows="3" 
                            placeholder="Scenario — Scalata graduale (+20% settimanale)" 
                            class="textarea textarea-bordered w-full @error('descrizione') textarea-error @enderror"
                        >{{ old('descrizione') }}</textarea>
                        @error('descrizione')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                    
                    <div class="divider"></div>
                    
                    {{-- Pulsanti Azione --}}
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-success flex-1 gap-2">
                            <x-ui.icon name="save" class="h-4 w-4" />
                            Salva Prospetto
                        </button>
                        <button type="button" onclick="validateJSON()" class="btn btn-info gap-2">
                            <x-ui.icon name="check" class="h-4 w-4" />
                            Valida JSON
                        </button>
                    </div>
                </x-admin.card>
            </div>
            
            {{-- Colonna Destra: Editor JSON --}}
            <div class="lg:col-span-2">
                <x-admin.card tone="light" shadow="lg" padding="lg">
                    <h3 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                        <x-ui.icon name="code" class="h-5 w-5 text-warning" />
                        Dati JSON Campagne
                    </h3>
                    
                    {{-- JSON Editor --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Struttura JSON *</span>
                            <span class="label-text-alt text-info cursor-pointer" onclick="showJSONExample()">
                                <x-ui.icon name="question-circle" class="h-4 w-4 inline" />
                                Mostra esempio
                            </span>
                        </label>
                        <textarea 
                            name="dati_json" 
                            id="jsonEditor" 
                            rows="25" 
                            placeholder='{ "accounts": [...] }' 
                            class="textarea textarea-bordered w-full font-mono text-sm @error('dati_json') textarea-error @enderror"
                            required
                        >{{ old('dati_json') }}</textarea>
                        @error('dati_json')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                        
                        {{-- Messaggio di validazione --}}
                        <div id="jsonValidation" class="hidden mt-2"></div>
                    </div>
                    
                    {{-- Suggerimenti --}}
                    <div class="alert alert-info">
                        <x-ui.icon name="lightbulb" class="h-5 w-5" />
                        <div class="text-sm">
                            <strong>Suggerimenti:</strong>
                            <ul class="list-disc list-inside mt-2">
                                <li>Il JSON deve contenere un array "accounts" con gli account Google Ads</li>
                                <li>Ogni account deve avere: id, name, code, color, icon, weeks</li>
                                <li>Ogni settimana deve avere: week, increment, budget, cpl</li>
                                <li>I lead vengono calcolati automaticamente (budget / cpl)</li>
                            </ul>
                        </div>
                    </div>
                </x-admin.card>
            </div>
            
        </div>
    </form>
    
    {{-- Modal Esempio JSON --}}
    <div id="jsonExampleModal" class="modal">
        <div class="modal-box max-w-3xl">
            <h3 class="font-bold text-lg mb-4">Esempio Struttura JSON</h3>
            <pre class="bg-base-200 p-4 rounded-lg overflow-x-auto text-xs"><code>{
  "accounts": [
    {
      "id": 1,
      "name": "Google Ads - Novadirect",
      "code": "Novadirect",
      "color": "#667eea",
      "icon": "fa-bolt",
      "weeks": [
        { "week": 0, "increment": "0%", "budget": 1750, "cpl": 8.6 },
        { "week": 1, "increment": "Stabilizzazione", "budget": 1750, "cpl": 8.6 },
        { "week": 2, "increment": "+20%", "budget": 2100, "cpl": 8.2 },
        { "week": 3, "increment": "+20%", "budget": 2520, "cpl": 8.0 },
        { "week": 4, "increment": "+20%", "budget": 3024, "cpl": 7.8 }
      ]
    },
    {
      "id": 2,
      "name": "Google Ads - Novastart",
      "code": "Novastart",
      "color": "#f59e0b",
      "icon": "fa-rocket",
      "weeks": [
        { "week": 0, "increment": "0%", "budget": 200, "cpl": 13.5 },
        { "week": 1, "increment": "+100%", "budget": 400, "cpl": 12.5 },
        { "week": 2, "increment": "Stabilizzazione", "budget": 400, "cpl": 10.5 }
      ]
    }
  ]
}</code></pre>
            <div class="modal-action">
                <button class="btn" onclick="copyJSONExample()">
                    <x-ui.icon name="copy" class="h-4 w-4" />
                    Copia Esempio
                </button>
                <button class="btn btn-ghost" onclick="closeJSONExample()">Chiudi</button>
            </div>
        </div>
        <label class="modal-backdrop" onclick="closeJSONExample()"></label>
    </div>
    
    <script>
        function validateJSON() {
            const jsonEditor = document.getElementById('jsonEditor');
            const jsonValidation = document.getElementById('jsonValidation');
            
            try {
                const data = JSON.parse(jsonEditor.value);
                
                // Validazione struttura
                if (!data.accounts || !Array.isArray(data.accounts)) {
                    throw new Error('Il JSON deve contenere un array "accounts"');
                }
                
                if (data.accounts.length === 0) {
                    throw new Error('L\'array "accounts" non può essere vuoto');
                }
                
                // Validazione singoli account
                data.accounts.forEach((account, index) => {
                    if (!account.id || !account.name || !account.code || !account.weeks) {
                        throw new Error(`Account ${index + 1}: Campi obbligatori mancanti (id, name, code, weeks)`);
                    }
                    
                    if (!Array.isArray(account.weeks) || account.weeks.length === 0) {
                        throw new Error(`Account ${index + 1}: L'array "weeks" è vuoto o non valido`);
                    }
                });
                
                jsonValidation.className = 'alert alert-success mt-2';
                jsonValidation.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>JSON valido! Trovati ${data.accounts.length} account(s).</span>
                `;
                jsonValidation.classList.remove('hidden');
                
            } catch (error) {
                jsonValidation.className = 'alert alert-error mt-2';
                jsonValidation.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>${error.message}</span>
                `;
                jsonValidation.classList.remove('hidden');
            }
        }
        
        function showJSONExample() {
            document.getElementById('jsonExampleModal').classList.add('modal-open');
        }
        
        function closeJSONExample() {
            document.getElementById('jsonExampleModal').classList.remove('modal-open');
        }
        
        function copyJSONExample() {
            const example = document.querySelector('#jsonExampleModal pre code').textContent;
            document.getElementById('jsonEditor').value = example;
            closeJSONExample();
            validateJSON();
        }
        
        // Validazione automatica al blur
        document.getElementById('jsonEditor').addEventListener('blur', function() {
            if (this.value.trim()) {
                validateJSON();
            }
        });
    </script>
    
</x-admin.wrapper>

