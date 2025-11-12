{{-- Modifica Configurazione UTM --}}
<x-admin.wrapper>
    <x-slot name="title">{{ __('Modifica Configurazione UTM') }}</x-slot>
    
    {{-- Page Header --}}
    <x-admin.page-header 
        title="Modifica Configurazione UTM" 
        subtitle="Aggiorna i dettagli della configurazione #{{ $configurazione->id }}"
        icon="edit"
        iconColor="warning"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.marketing.configurazione_utm.index') }}" class="btn btn-ghost gap-2">
                <x-ui.icon name="arrow-left" class="h-5 w-5" />
                Torna alla Lista
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- Form --}}
    <x-admin.card>
        <form action="{{ route('admin.marketing.configurazione_utm.update', $configurazione->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            {{-- Info Record --}}
            <div class="alert alert-info">
                <x-ui.icon name="info-circle" class="h-5 w-5" />
                <div class="text-sm">
                    <strong>ID Configurazione:</strong> {{ $configurazione->id }} 
                    <span class="ml-4"><strong>Creata il:</strong> {{ \Carbon\Carbon::parse($configurazione->created_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            
            {{-- Account ID --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                        <x-ui.icon name="id-card" class="h-4 w-4 text-info" />
                        Account ID
                    </span>
                    <span class="label-text-alt text-base-content/50">Max 100 caratteri</span>
                </label>
                <input 
                    type="text" 
                    name="account_id" 
                    value="{{ old('account_id', $configurazione->account_id) }}"
                    placeholder="es: 966-937-4086" 
                    maxlength="100"
                    class="input input-bordered @error('account_id') input-error @enderror" 
                />
                @error('account_id')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>
            
            {{-- Tipo Lavorazione --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                        <x-ui.icon name="cog" class="h-4 w-4 text-success" />
                        Tipo Lavorazione
                    </span>
                    <span class="label-text-alt text-base-content/50">Max 50 caratteri</span>
                </label>
                <input 
                    type="text" 
                    name="tipo_lavorazione" 
                    value="{{ old('tipo_lavorazione', $configurazione->tipo_lavorazione) }}"
                    placeholder="es: Lead Generation" 
                    maxlength="50"
                    class="input input-bordered @error('tipo_lavorazione') input-error @enderror" 
                />
                @error('tipo_lavorazione')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>
            
            {{-- UTM Campaign --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                        <x-ui.icon name="tags" class="h-4 w-4 text-warning" />
                        UTM Campaign
                    </span>
                    <span class="label-text-alt text-base-content/50">Max 100 caratteri</span>
                </label>
                <input 
                    type="text" 
                    name="utm_campaign" 
                    value="{{ old('utm_campaign', $configurazione->utm_campaign) }}"
                    placeholder="es: MQ-Sales-Tim-Copertura" 
                    maxlength="100"
                    class="input input-bordered @error('utm_campaign') input-error @enderror" 
                />
                @error('utm_campaign')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
                <label class="label">
                    <span class="label-text-alt">Nome della campagna UTM per il tracking</span>
                </label>
            </div>
            
            {{-- Campagna ID --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                        <x-ui.icon name="hashtag" class="h-4 w-4 text-accent" />
                        Campagna ID
                    </span>
                    <span class="label-text-alt text-base-content/50">Max 25 caratteri</span>
                </label>
                <input 
                    type="text" 
                    name="campagna_id" 
                    value="{{ old('campagna_id', $configurazione->campagna_id) }}"
                    placeholder="es: CAMP-2025-001" 
                    maxlength="25"
                    class="input input-bordered @error('campagna_id') input-error @enderror" 
                />
                @error('campagna_id')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>
            
            {{-- List ID --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                        <x-ui.icon name="list-ol" class="h-4 w-4 text-primary" />
                        List ID
                    </span>
                    <span class="label-text-alt text-base-content/50">Numero intero</span>
                </label>
                <input 
                    type="number" 
                    name="list_id" 
                    value="{{ old('list_id', $configurazione->list_id) }}"
                    placeholder="es: 12345" 
                    class="input input-bordered @error('list_id') input-error @enderror" 
                />
                @error('list_id')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>
            
            {{-- Divider --}}
            <div class="divider"></div>
            
            {{-- Actions --}}
            <div class="flex gap-3 justify-between">
                <form action="{{ route('admin.marketing.configurazione_utm.destroy', $configurazione->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Sei sicuro di voler eliminare questa configurazione? Questa azione è irreversibile.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-error btn-outline gap-2">
                        <x-ui.icon name="trash" class="h-5 w-5" />
                        Elimina Configurazione
                    </button>
                </form>
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.marketing.configurazione_utm.index') }}" class="btn btn-ghost">
                        Annulla
                    </a>
                    <button type="submit" class="btn btn-primary gap-2">
                        <x-ui.icon name="save" class="h-5 w-5" />
                        Aggiorna Configurazione
                    </button>
                </div>
            </div>
        </form>
    </x-admin.card>
    
    {{-- Info Box --}}
    <x-admin.card tone="warning" class="mt-6">
        <div class="flex items-start gap-3">
            <x-ui.icon name="exclamation-triangle" class="h-6 w-6 text-warning flex-shrink-0 mt-1" />
            <div>
                <h4 class="font-semibold text-warning mb-2">Attenzione alle modifiche</h4>
                <ul class="text-sm text-base-content/80 space-y-1">
                    <li>Modificare <strong>UTM Campaign</strong> o <strong>Campagna ID</strong> potrebbe influenzare il tracking esistente</li>
                    <li>Assicurati che i dati corrispondano a quelli configurati nelle piattaforme esterne (Google Ads, Analytics, ecc.)</li>
                    <li>Le modifiche saranno applicate immediatamente e potrebbero influenzare report in corso</li>
                </ul>
            </div>
        </div>
    </x-admin.card>
</x-admin.wrapper>

