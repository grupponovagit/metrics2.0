@props([
    'searchPlaceholder' => 'Cerca...',
    'searchName' => 'search',
    'searchValue' => '',
    'filters' => [],      // Array di filtri: ['name' => 'status', 'label' => 'Stato', 'options' => [...], 'value' => '']
    'action' => '',       // URL action del form (opzionale)
    'method' => 'GET',    // Metodo HTTP
    'showReset' => true,  // Mostra button reset
    'compact' => false,   // Layout compatto
])

<x-admin.card tone="light" shadow="md">
    <form action="{{ $action }}" method="{{ $method }}" class="space-y-4">
        @if($method !== 'GET')
            @csrf
            @method($method)
        @endif

        <div class="flex flex-col {{ $compact ? 'lg:flex-row' : 'xl:flex-row' }} gap-4 {{ $compact ? 'items-end' : 'items-stretch' }}">
            
            {{-- Search Input --}}
            <div class="flex-1 {{ $compact ? 'min-w-0' : '' }}">
                <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                        <x-ui.icon name="search" size="sm" class="text-base-content/60" />
                        Ricerca
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                        <x-ui.icon name="search" size="md" />
                    </div>
                    <input 
                        type="text" 
                        name="{{ $searchName }}"
                        value="{{ $searchValue }}"
                        placeholder="{{ $searchPlaceholder }}" 
                        class="input input-bordered w-full pl-12 focus:ring-2 focus:ring-primary/20 transition-all"
                    />
                </div>
            </div>

            {{-- Dynamic Filters --}}
            @foreach($filters as $filter)
                <div class="w-full {{ $compact ? 'lg:w-56' : 'xl:w-56' }}">
                    <label class="label">
                        <span class="label-text font-semibold flex items-center gap-2">
                            @if(isset($filter['icon']))
                                <x-ui.icon :name="$filter['icon']" size="sm" class="text-base-content/60" />
                            @endif
                            {{ $filter['label'] }}
                        </span>
                        @if(isset($filter['tooltip']))
                            <span class="label-text-alt tooltip tooltip-left" data-tip="{{ $filter['tooltip'] }}">
                                <x-ui.icon name="info" size="sm" class="text-base-content/40" />
                            </span>
                        @endif
                    </label>
                    
                    @if($filter['type'] === 'select' || !isset($filter['type']))
                        <select 
                            name="{{ $filter['name'] }}" 
                            class="select select-bordered w-full focus:ring-2 focus:ring-primary/20 transition-all"
                        >
                            <option value="">{{ $filter['placeholder'] ?? 'Tutti' }}</option>
                            @foreach($filter['options'] as $optValue => $optLabel)
                                <option 
                                    value="{{ $optValue }}" 
                                    {{ ($filter['value'] ?? '') == $optValue ? 'selected' : '' }}
                                >
                                    {{ $optLabel }}
                                </option>
                            @endforeach
                        </select>
                    
                    @elseif($filter['type'] === 'date')
                        <input 
                            type="date" 
                            name="{{ $filter['name'] }}"
                            value="{{ $filter['value'] ?? '' }}"
                            class="input input-bordered w-full focus:ring-2 focus:ring-primary/20 transition-all"
                        />
                    
                    @elseif($filter['type'] === 'number')
                        <input 
                            type="number" 
                            name="{{ $filter['name'] }}"
                            value="{{ $filter['value'] ?? '' }}"
                            placeholder="{{ $filter['placeholder'] ?? '' }}"
                            min="{{ $filter['min'] ?? '' }}"
                            max="{{ $filter['max'] ?? '' }}"
                            step="{{ $filter['step'] ?? '1' }}"
                            class="input input-bordered w-full focus:ring-2 focus:ring-primary/20 transition-all"
                        />
                    
                    @elseif($filter['type'] === 'text')
                        <input 
                            type="text" 
                            name="{{ $filter['name'] }}"
                            value="{{ $filter['value'] ?? '' }}"
                            placeholder="{{ $filter['placeholder'] ?? '' }}"
                            class="input input-bordered w-full focus:ring-2 focus:ring-primary/20 transition-all"
                        />
                    @endif
                </div>
            @endforeach

            {{-- Action Buttons --}}
            <div class="flex {{ $compact ? 'flex-row' : 'flex-col sm:flex-row' }} gap-2 {{ $compact ? '' : 'items-end' }}">
                <button type="submit" class="btn btn-primary gap-2 shadow-lg hover:shadow-xl transition-all {{ $compact ? 'btn-md' : '' }}">
                    <x-ui.icon name="filter" size="md" />
                    <span class="hidden sm:inline">Applica Filtri</span>
                    <span class="sm:hidden">Filtra</span>
                </button>
                
                @if($showReset)
                    <button 
                        type="button" 
                        onclick="window.location.href='{{ $action ?: url()->current() }}'"
                        class="btn btn-ghost gap-2 {{ $compact ? 'btn-md' : '' }}"
                    >
                        <x-ui.icon name="refresh" size="md" />
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Active Filters Display (Optional Slot) --}}
        @if(isset($activeFilters))
            <div class="pt-4 border-t border-base-300">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-base-content/70">Filtri attivi:</span>
                    {{ $activeFilters }}
                </div>
            </div>
        @endif
    </form>
</x-admin.card>

