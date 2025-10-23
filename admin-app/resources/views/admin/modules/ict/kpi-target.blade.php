<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('KPI Target') }}</x-slot>
    
    <x-admin.page-header 
        title="KPI Target" 
        subtitle="Gestione Target Mensili e Rendiconto Produzione"
        icon="bullseye"
        iconColor="secondary"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.index') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- FILTRO MESE/ANNO --}}
    <x-admin.card tone="light" shadow="md" padding="normal" class="mb-6">
        <form method="GET" action="{{ route('admin.ict.kpi_target') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="mese" class="block text-sm font-medium text-base-content mb-2">
                    Mese
                </label>
                <select name="mese" id="mese" class="select select-bordered w-full">
                    <option value="01" {{ $mese == '01' ? 'selected' : '' }}>Gennaio</option>
                    <option value="02" {{ $mese == '02' ? 'selected' : '' }}>Febbraio</option>
                    <option value="03" {{ $mese == '03' ? 'selected' : '' }}>Marzo</option>
                    <option value="04" {{ $mese == '04' ? 'selected' : '' }}>Aprile</option>
                    <option value="05" {{ $mese == '05' ? 'selected' : '' }}>Maggio</option>
                    <option value="06" {{ $mese == '06' ? 'selected' : '' }}>Giugno</option>
                    <option value="07" {{ $mese == '07' ? 'selected' : '' }}>Luglio</option>
                    <option value="08" {{ $mese == '08' ? 'selected' : '' }}>Agosto</option>
                    <option value="09" {{ $mese == '09' ? 'selected' : '' }}>Settembre</option>
                    <option value="10" {{ $mese == '10' ? 'selected' : '' }}>Ottobre</option>
                    <option value="11" {{ $mese == '11' ? 'selected' : '' }}>Novembre</option>
                    <option value="12" {{ $mese == '12' ? 'selected' : '' }}>Dicembre</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[150px]">
                <label for="anno" class="block text-sm font-medium text-base-content mb-2">
                    Anno
                </label>
                <input 
                    type="number" 
                    name="anno" 
                    id="anno" 
                    value="{{ $anno }}" 
                    min="2020" 
                    max="2030"
                    class="input input-bordered w-full"
                />
            </div>
            
            <div>
                <button type="submit" class="btn btn-primary">
                    <x-ui.icon name="search" class="h-4 w-4" />
                    Filtra
                </button>
            </div>
        </form>
    </x-admin.card>
    
    {{-- TABS --}}
    <div role="tablist" class="tabs tabs-boxed bg-base-200 mb-6">
        <a role="tab" class="tab tab-active" id="tab-target" onclick="switchTab('target')">
            <x-ui.icon name="chart-bar" class="h-4 w-4 mr-2" />
            Target Mensili (Pianificazione)
        </a>
        <a role="tab" class="tab" id="tab-rendiconto" onclick="switchTab('rendiconto')">
            <x-ui.icon name="chart-line" class="h-4 w-4 mr-2" />
            Rendiconto Produzione (Consuntivo)
        </a>
    </div>
    
    {{-- TABELLA 1: TARGET MENSILI --}}
    <x-admin.card tone="light" shadow="lg" padding="none" id="table-target">
        <div class="p-6 border-b border-base-300">
            <h3 class="text-xl font-bold text-base-content">
                Target Mensili - {{ date('F Y', mktime(0, 0, 0, $mese, 1, $anno)) }}
            </h3>
            <p class="text-sm text-base-content/60 mt-1">
                KPI di target o obiettivi mensili (dati di pianificazione)
            </p>
        </div>
        
        <form method="POST" action="{{ route('admin.ict.kpi_target.update') }}" id="form-target">
            @csrf
            <input type="hidden" name="tabella" value="target">
            
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200 sticky top-0">
                        <tr>
                            <th class="font-bold">Commessa</th>
                            <th class="font-bold">Sede CRM</th>
                            <th class="font-bold">Sede Estesa</th>
                            <th class="font-bold">Nome KPI</th>
                            <th class="font-bold text-center">Valore</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($targetMensili as $kpi)
                            <tr class="hover:bg-base-200/50">
                                <td class="font-medium">{{ $kpi->commessa }}</td>
                                <td>{{ $kpi->sede_crm }}</td>
                                <td class="text-sm text-base-content/70">{{ $kpi->sede_estesa }}</td>
                                <td>{{ $kpi->nome_kpi }}</td>
                                <td class="text-center">
                                    <input 
                                        type="number" 
                                        name="kpi[{{ $kpi->id }}]" 
                                        value="{{ $kpi->valore_kpi }}"
                                        step="1"
                                        min="0"
                                        class="input input-sm input-bordered w-24 text-center"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content mb-1">Nessun target trovato</h3>
                                        <p class="text-sm text-base-content/60">Seleziona un periodo diverso</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($targetMensili->count() > 0)
                <div class="p-6 bg-base-200/50 border-t border-base-300 flex justify-end">
                    <button type="submit" class="btn btn-success">
                        <x-ui.icon name="save" class="h-4 w-4" />
                        Salva Modifiche
                    </button>
                </div>
            @endif
        </form>
    </x-admin.card>
    
    {{-- TABELLA 2: RENDICONTO PRODUZIONE --}}
    <x-admin.card tone="light" shadow="lg" padding="none" id="table-rendiconto" class="hidden">
        <div class="p-6 border-b border-base-300">
            <h3 class="text-xl font-bold text-base-content">
                Rendiconto Produzione (Consuntivo)
            </h3>
            <p class="text-sm text-base-content/60 mt-1">
                KPI effettivi o consuntivo produzione (dati di esecuzione)
            </p>
        </div>
        
        <form method="POST" action="{{ route('admin.ict.kpi_target.update') }}" id="form-rendiconto">
            @csrf
            <input type="hidden" name="tabella" value="rendiconto">
            
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200 sticky top-0">
                        <tr>
                            <th class="font-bold">Commessa</th>
                            <th class="font-bold">Istanza</th>
                            <th class="font-bold">Servizio/Mandato</th>
                            <th class="font-bold">Macrocampagna</th>
                            <th class="font-bold">Nome KPI</th>
                            <th class="font-bold text-center">Valore</th>
                            <th class="font-bold">Descrizione</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendicontoProduzione as $kpi)
                            <tr class="hover:bg-base-200/50">
                                <td class="font-medium">{{ $kpi->commessa }}</td>
                                <td>{{ $kpi->istanza }}</td>
                                <td>{{ $kpi->servizio_mandato }}</td>
                                <td>{{ $kpi->macrocampagna }}</td>
                                <td>{{ $kpi->nome_kpi }}</td>
                                <td class="text-center">
                                    <input 
                                        type="number" 
                                        name="kpi[{{ $kpi->id }}]" 
                                        value="{{ $kpi->valore_kpi }}"
                                        step="1"
                                        min="0"
                                        class="input input-sm input-bordered w-24 text-center"
                                    />
                                </td>
                                <td class="text-sm text-base-content/70">{{ $kpi->descrizione }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content mb-1">Nessun rendiconto trovato</h3>
                                        <p class="text-sm text-base-content/60">Nessun dato disponibile</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($rendicontoProduzione->count() > 0)
                <div class="p-6 bg-base-200/50 border-t border-base-300 flex justify-end">
                    <button type="submit" class="btn btn-success">
                        <x-ui.icon name="save" class="h-4 w-4" />
                        Salva Modifiche
                    </button>
                </div>
            @endif
        </form>
    </x-admin.card>
    
    {{-- MESSAGGI --}}
    @if(session('success'))
        <div class="alert alert-success mt-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error mt-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    
    <script>
        function switchTab(tab) {
            // Update tabs
            document.getElementById('tab-target').classList.remove('tab-active');
            document.getElementById('tab-rendiconto').classList.remove('tab-active');
            document.getElementById('tab-' + tab).classList.add('tab-active');
            
            // Update tables
            document.getElementById('table-target').classList.add('hidden');
            document.getElementById('table-rendiconto').classList.add('hidden');
            document.getElementById('table-' + tab).classList.remove('hidden');
        }
    </script>
</x-admin.wrapper>

