{{-- Configurazione UTM Campagne --}}
<x-admin.wrapper>
    <x-slot name="title">{{ __('Configurazione UTM Campagne') }}</x-slot>
    
    {{-- Page Header --}}
    <x-admin.page-header 
        title="Configurazione UTM Campagne" 
        subtitle="Gestione configurazioni campagne digital"
        icon="tags"
        iconColor="info"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.marketing.configurazione_utm.create') }}" class="btn btn-primary gap-2">
                <x-ui.icon name="plus" class="h-5 w-5" />
                Nuova Configurazione
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- Tabella Configurazioni --}}
    <x-admin.card>
        @if($configurazioni->isEmpty())
            <div class="text-center py-12">
                <x-ui.icon name="tags" class="h-16 w-16 mx-auto text-base-content/30 mb-4" />
                <h3 class="text-lg font-semibold text-base-content mb-2">Nessuna configurazione trovata</h3>
                <p class="text-base-content/70 mb-6">Inizia creando la prima configurazione UTM campagna.</p>
                <a href="{{ route('admin.marketing.configurazione_utm.create') }}" class="btn btn-primary gap-2">
                    <x-ui.icon name="plus" class="h-5 w-5" />
                    Crea Prima Configurazione
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th class="bg-base-200">ID</th>
                            <th class="bg-base-200">Account ID</th>
                            <th class="bg-base-200">Tipo Lavorazione</th>
                            <th class="bg-base-200">UTM Campaign</th>
                            <th class="bg-base-200">Campagna ID</th>
                            <th class="bg-base-200">List ID</th>
                            <th class="bg-base-200">Data Creazione</th>
                            <th class="bg-base-200 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($configurazioni as $config)
                        <tr class="hover">
                            <td class="font-mono text-sm">{{ $config->id }}</td>
                            <td>
                                @if($config->account_id)
                                    <span class="badge badge-info badge-sm">{{ $config->account_id }}</span>
                                @else
                                    <span class="text-base-content/50 text-xs">N/D</span>
                                @endif
                            </td>
                            <td>
                                @if($config->tipo_lavorazione)
                                    <span class="badge badge-success badge-sm">{{ $config->tipo_lavorazione }}</span>
                                @else
                                    <span class="text-base-content/50 text-xs">N/D</span>
                                @endif
                            </td>
                            <td>
                                @if($config->utm_campaign)
                                    <span class="font-semibold text-sm">{{ $config->utm_campaign }}</span>
                                @else
                                    <span class="text-base-content/50 text-xs">N/D</span>
                                @endif
                            </td>
                            <td>
                                @if($config->campagna_id)
                                    <span class="badge badge-warning badge-sm">{{ $config->campagna_id }}</span>
                                @else
                                    <span class="text-base-content/50 text-xs">N/D</span>
                                @endif
                            </td>
                            <td>
                                @if($config->list_id)
                                    <span class="badge badge-accent badge-sm">{{ $config->list_id }}</span>
                                @else
                                    <span class="text-base-content/50 text-xs">N/D</span>
                                @endif
                            </td>
                            <td class="text-sm text-base-content/70">
                                {{ \Carbon\Carbon::parse($config->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.marketing.configurazione_utm.edit', $config->id) }}" 
                                       class="btn btn-sm btn-ghost btn-square text-info" 
                                       title="Modifica">
                                        <x-ui.icon name="edit" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.marketing.configurazione_utm.destroy', $config->id) }}" 
                                          method="POST" 
                                          class="inline-block"
                                          onsubmit="return confirm('Sei sicuro di voler eliminare questa configurazione?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-ghost btn-square text-error" 
                                                title="Elimina">
                                            <x-ui.icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Statistiche --}}
            <div class="mt-6 p-4 bg-base-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-base-content/70">
                        <strong>{{ $configurazioni->count() }}</strong> configurazioni totali
                    </div>
                    <div class="text-xs text-base-content/50">
                        Ultimo aggiornamento: {{ $configurazioni->first() ? \Carbon\Carbon::parse($configurazioni->first()->updated_at)->format('d/m/Y H:i') : 'N/D' }}
                    </div>
                </div>
            </div>
        @endif
    </x-admin.card>
</x-admin.wrapper>

