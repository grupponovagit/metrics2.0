<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Bonus & Incentivi') }}</x-slot>
    
    <x-admin.page-header 
        title="Bonus & Incentivi" 
        subtitle="Gestione mantenimenti bonus e incentivi per istanze e commesse"
        icon="coins"
        iconColor="success"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.create') }}" class="btn btn-success">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Nuovo Mantenimento
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- Filtri --}}
    <x-admin.card tone="light" shadow="lg" padding="loose" class="mb-6">
        <form method="GET" action="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Istanza --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Istanza</span>
                    </label>
                    <select name="istanza" class="select select-bordered">
                        <option value="">Tutte</option>
                        @foreach($istanze as $istanza)
                            <option value="{{ $istanza }}" {{ request('istanza') == $istanza ? 'selected' : '' }}>
                                {{ $istanza }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Commessa --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Commessa</span>
                    </label>
                    <select name="commessa" class="select select-bordered">
                        <option value="">Tutte</option>
                        @foreach($commesse as $commessa)
                            <option value="{{ $commessa }}" {{ request('commessa') == $commessa ? 'selected' : '' }}>
                                {{ $commessa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Tipologia Ripartizione --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Tipologia Ripartizione</span>
                    </label>
                    <select name="tipologia_ripartizione" class="select select-bordered">
                        <option value="">Tutte</option>
                        <option value="Fissa" {{ request('tipologia_ripartizione') == 'Fissa' ? 'selected' : '' }}>Fissa</option>
                        <option value="Pezzi" {{ request('tipologia_ripartizione') == 'Pezzi' ? 'selected' : '' }}>Pezzi</option>
                        <option value="Fatturato" {{ request('tipologia_ripartizione') == 'Fatturato' ? 'selected' : '' }}>Fatturato</option>
                        <option value="Ore" {{ request('tipologia_ripartizione') == 'Ore' ? 'selected' : '' }}>Ore</option>
                        <option value="ContattiUtili" {{ request('tipologia_ripartizione') == 'ContattiUtili' ? 'selected' : '' }}>Contatti Utili</option>
                        <option value="ContattiChiusi" {{ request('tipologia_ripartizione') == 'ContattiChiusi' ? 'selected' : '' }}>Contatti Chiusi</option>
                    </select>
                </div>
                
                {{-- Pulsanti --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text opacity-0">Azioni</span>
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary flex-1">
                            <x-ui.icon name="search" class="h-4 w-4" />
                            Cerca
                        </button>
                        <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.index') }}" class="btn btn-outline">
                            <x-ui.icon name="times" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </x-admin.card>
    
    {{-- Tabella --}}
    <x-admin.card tone="light" shadow="lg" padding="none">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Istanza</th>
                        <th>Commessa</th>
                        <th>Macro Campagna</th>
                        <th>Tipologia</th>
                        <th>Sedi</th>
                        <th>Extra Bonus</th>
                        <th>Valido Dal</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mantenimenti as $mantenimento)
                    <tr>
                        <td>{{ $mantenimento->id }}</td>
                        <td>
                            @if($mantenimento->istanza)
                                <span class="badge badge-info">{{ $mantenimento->istanza }}</span>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimento->commessa)
                                <span class="badge badge-warning">{{ $mantenimento->commessa }}</span>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimento->macro_campagna)
                                {{ $mantenimento->macro_campagna }}
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimento->tipologia_ripartizione)
                                <span class="badge badge-success badge-sm">{{ $mantenimento->tipologia_ripartizione }}</span>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimento->sedi_ripartizione)
                                <span class="text-xs">{{ Str::limit($mantenimento->sedi_ripartizione, 30) }}</span>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimento->extra_bonus)
                                <span class="font-semibold text-success">€ {{ number_format($mantenimento->extra_bonus, 2, ',', '.') }}</span>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimento->valido_dal)
                                {{ $mantenimento->valido_dal->format('d/m/Y') }}
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.ict.mantenimenti_bonus_incentivi.edit', $mantenimento->id) }}" 
                                   class="btn btn-sm btn-info">
                                    <x-ui.icon name="edit" class="h-4 w-4" />
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.ict.mantenimenti_bonus_incentivi.destroy', $mantenimento->id) }}"
                                      onsubmit="return confirm('Sei sicuro di voler eliminare questo mantenimento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error">
                                        <x-ui.icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8">
                            <div class="text-base-content/50">
                                <x-ui.icon name="inbox" class="h-12 w-12 mx-auto mb-2" />
                                <p>Nessun mantenimento trovato</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mantenimenti->hasPages())
        <div class="p-4 border-t border-base-300">
            {{ $mantenimenti->links() }}
        </div>
        @endif
    </x-admin.card>
</x-admin.wrapper>

