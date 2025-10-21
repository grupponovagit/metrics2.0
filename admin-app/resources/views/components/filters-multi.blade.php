@props([
    'searchPlaceholder' => 'Cerca...',
    'searchName' => 'search',
    'searchValue' => '',
    'filters' => [],      // Array di filtri con supporto multi-select
    'action' => '',       
    'method' => 'GET',    
    'showReset' => true,  
    'compact' => false,   
])

<x-admin.card tone="light" shadow="md">
    {{-- Messaggi di errore --}}
    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold">Errore nella validazione!</h3>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ $action }}" method="{{ $method }}" class="space-y-4" id="filtersForm">
        @if($method !== 'GET')
            @csrf
            @method($method)
        @endif

        <div class="flex flex-col lg:flex-row gap-4 items-stretch">
            
            {{-- Date Filters (Vertical) --}}
            @if(isset($filters['dates']))
            <div class="flex-shrink-0 w-full lg:w-48">
                @foreach($filters['dates'] as $filter)
                <div class="mb-2">
                    <label class="label">
                        <span class="label-text font-semibold">
                            {{ $filter['label'] }}
                        </span>
                    </label>
                    
                    @if($filter['type'] === 'date')
                        <input 
                            type="date" 
                            name="{{ $filter['name'] }}"
                            value="{{ $filter['value'] ?? '' }}"
                            class="input input-bordered w-full focus:ring-2 focus:ring-primary/20 transition-all {{ $errors->has($filter['name']) ? 'input-error' : '' }}"
                            onchange="validateForm()"
                            required
                        />
                        @error($filter['name'])
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    @endif
                </div>
                @endforeach
            </div>
            @endif
            
            {{-- Select Filters --}}
            @if(isset($filters['selects']))
            @foreach($filters['selects'] as $filter)
                <div class="flex-1">
                    <label class="label">
                        <span class="label-text font-semibold">
                            {{ $filter['label'] }}
                        </span>
                    </label>
                    
                    @if($filter['type'] === 'select-multiple' || (isset($filter['multiple']) && $filter['multiple']))
                        {{-- Select Multiple con opzione Tutti --}}
                        <div class="relative">
                            <button 
                                type="button" 
                                class="btn btn-xs btn-outline btn-primary mb-1 w-full"
                                onclick="selectAll{{ ucfirst($filter['name']) }}()"
                            >
                                Seleziona Tutti
                            </button>
                            <select 
                                name="{{ $filter['name'] }}[]" 
                                multiple
                                class="select select-bordered w-full focus:ring-2 focus:ring-primary/20 transition-all h-auto py-2"
                                style="min-height: 120px; max-height: 180px;"
                                id="select_{{ $filter['name'] }}"
                            >
                                @foreach($filter['options'] as $optValue => $optLabel)
                                    <option 
                                        value="{{ $optValue }}" 
                                        class="py-2 px-3 hover:bg-primary/10 cursor-pointer"
                                        {{ in_array($optValue, (array)($filter['value'] ?? [])) ? 'selected' : '' }}
                                    >
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                            <script>
                                function selectAll{{ ucfirst($filter['name']) }}() {
                                    const select = document.getElementById('select_{{ $filter['name'] }}');
                                    for (let i = 0; i < select.options.length; i++) {
                                        select.options[i].selected = true;
                                    }
                                }
                            </script>
                        </div>
                        <div class="text-xs text-base-content/60 mt-1">
                            Ctrl/Cmd + Click per selezione multipla
                        </div>
                    @endif
                </div>
            @endforeach
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-2 items-stretch justify-end w-full lg:w-auto lg:min-w-[140px]">
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="btn btn-primary shadow-lg hover:shadow-xl transition-all"
                    disabled
                >
                    Applica Filtri
                </button>
                
                @if($showReset)
                    <a 
                        href="{{ $action ?: url()->current() }}"
                        class="btn btn-ghost"
                    >
                        Reset
                    </a>
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

    {{-- Script di validazione --}}
    <script>
        function validateForm() {
            const form = document.getElementById('filtersForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Ottieni tutti gli input date required
            const dateInputs = form.querySelectorAll('input[type="date"][required]');
            let allFilled = true;
            let validDates = true;
            
            // Controlla che tutte le date siano compilate
            dateInputs.forEach(input => {
                if (!input.value) {
                    allFilled = false;
                }
            });
            
            // Se ci sono 2 date (inizio e fine), controlla che fine >= inizio
            if (dateInputs.length === 2 && allFilled) {
                const dataInizio = new Date(dateInputs[0].value);
                const dataFine = new Date(dateInputs[1].value);
                
                if (dataFine < dataInizio) {
                    validDates = false;
                    dateInputs[1].classList.add('input-error');
                } else {
                    dateInputs[1].classList.remove('input-error');
                }
            }
            
            // Abilita/disabilita il bottone
            if (allFilled && validDates) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-disabled');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-disabled');
            }
        }
        
        // Esegui validazione al caricamento della pagina
        document.addEventListener('DOMContentLoaded', function() {
            validateForm();
            
            // Aggiungi listener a tutti gli input date
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.addEventListener('change', validateForm);
                input.addEventListener('input', validateForm);
            });
        });
    </script>
</x-admin.card>

