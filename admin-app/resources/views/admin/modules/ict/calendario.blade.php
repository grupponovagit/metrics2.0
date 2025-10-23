<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Calendario Aziendale') }}</x-slot>
    
    <x-admin.page-header 
        title="Calendario Aziendale" 
        subtitle="Gestione giorni lavorativi, festività ed eccezioni per mandato"
        icon="calendar-days"
        iconColor="secondary"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.index') }}" class="btn btn-outline btn-secondary">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- FILTRI --}}
    <x-admin.card tone="light" shadow="md" padding="normal" class="mb-6">
        <form method="GET" action="{{ route('admin.ict.calendario') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label">
                    <span class="label-text font-semibold">Anno</span>
                </label>
                <select name="anno" class="select select-bordered w-full">
                    @for($y = 2024; $y <= 2030; $y++)
                        <option value="{{ $y }}" {{ $anno == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="label">
                    <span class="label-text font-semibold">Filtro Mandato (Eccezioni)</span>
                </label>
                <select name="mandato" class="select select-bordered w-full">
                    <option value="">Tutti</option>
                    @foreach($mandati as $mandato)
                        <option value="{{ $mandato }}" {{ $mandatoFiltro == $mandato ? 'selected' : '' }}>
                            {{ $mandato }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <x-ui.icon name="search" class="h-4 w-4" />
                    Applica Filtri
                </button>
            </div>
        </form>
    </x-admin.card>
    
    {{-- STATISTICHE ANNO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-admin.stat-card
            title="Giorni Totali"
            :value="$totaleDays"
            icon="calendar"
            color="info"
        />
        
        <x-admin.stat-card
            title="Giorni Lavorativi"
            :value="round($giorniLavorativiTotali, 1)"
            icon="briefcase"
            color="success"
        />
        
        <x-admin.stat-card
            title="Festività"
            :value="$festivita"
            icon="gift"
            color="error"
        />
        
        <x-admin.stat-card
            title="Sabati"
            :value="$sabati"
            icon="calendar-check"
            color="warning"
        />
    </div>
    
    {{-- STATISTICHE MESE CORRENTE --}}
    @if($meseCorrente)
    <x-admin.card tone="light" shadow="md" class="mb-6">
        <h3 class="text-xl font-bold mb-4">Mese Corrente ({{ date('F Y') }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="stat">
                <div class="stat-title">Giorni Lavorativi Totali</div>
                <div class="stat-value text-primary">{{ round($meseCorrente['giorni_lavorativi'], 1) }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">Giorni Trascorsi</div>
                <div class="stat-value text-secondary">{{ round($meseCorrente['giorni_trascorsi'], 1) }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">Giorni Rimanenti</div>
                <div class="stat-value text-accent">{{ round($meseCorrente['giorni_rimanenti'], 1) }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">% Trascorsa</div>
                <div class="stat-value text-info">{{ $meseCorrente['percentuale_trascorsa'] }}%</div>
            </div>
        </div>
    </x-admin.card>
    @endif
    
    {{-- AGGIUNGI ECCEZIONE MANDATO --}}
    <x-admin.card tone="light" shadow="md" class="mb-6">
        <h3 class="text-xl font-bold mb-4">
            <x-ui.icon name="triangle-exclamation" class="h-5 w-5 text-warning" />
            Aggiungi Eccezione per Mandato/Fornitore
        </h3>
        <p class="text-sm text-base-content/70 mb-4">
            Usa questo form per registrare blocchi operativi comunicati dai fornitori (es. manutenzione Plenitude, blocco TIM, etc.)
        </p>
        
        <form method="POST" action="{{ route('admin.ict.calendario.add_eccezione_mandato') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            
            <div>
                <label class="label">
                    <span class="label-text font-semibold">Data</span>
                </label>
                <input type="date" name="data" required class="input input-bordered w-full" min="{{ date('Y-m-d') }}">
            </div>
            
            <div>
                <label class="label">
                    <span class="label-text font-semibold">Mandato/Fornitore</span>
                </label>
                <input type="text" name="mandato" required placeholder="es. PLENITUDE, TIM" class="input input-bordered w-full" list="mandati-list">
                <datalist id="mandati-list">
                    <option value="PLENITUDE">
                    <option value="TIM">
                    <option value="ENI">
                    <option value="ENEL">
                </datalist>
            </div>
            
            <div>
                <label class="label">
                    <span class="label-text font-semibold">Tipo Blocco</span>
                </label>
                <select name="peso_giornata" required class="select select-bordered w-full">
                    <option value="0.00">Blocco Totale (giorno non lavorativo)</option>
                    <option value="0.50">Blocco Parziale (mezza giornata)</option>
                </select>
            </div>
            
            <div>
                <label class="label">
                    <span class="label-text font-semibold">Descrizione</span>
                </label>
                <input type="text" name="descrizione" required placeholder="Manutenzione sistema..." class="input input-bordered w-full">
            </div>
            
            <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="btn btn-warning">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Aggiungi Eccezione
                </button>
            </div>
        </form>
    </x-admin.card>
    
    {{-- ECCEZIONI PER MANDATO --}}
    @if($mandatoFiltro && count($eccezioniMandato) > 0)
    <x-admin.card tone="light" shadow="lg" class="mb-6">
        <h3 class="text-xl font-bold mb-4">Eccezioni per {{ $mandatoFiltro }} - Anno {{ $anno }}</h3>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Giorno Settimana</th>
                        <th>Tipo Blocco</th>
                        <th>Descrizione</th>
                        <th class="text-center">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eccezioniMandato as $eccezione)
                    <tr>
                        <td>{{ $eccezione->data->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $giorni = ['', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];
                            @endphp
                            {{ $giorni[$eccezione->giorno_settimana] }}
                        </td>
                        <td>
                            @if($eccezione->peso_giornata == 0)
                                <span class="badge badge-error">Blocco Totale</span>
                            @else
                                <span class="badge badge-warning">Blocco Parziale ({{ $eccezione->peso_giornata }})</span>
                            @endif
                        </td>
                        <td>{{ $eccezione->descrizione }}</td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.ict.calendario.delete', $eccezione->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm text-error" onclick="return confirm('Eliminare questa eccezione?')">
                                    <x-ui.icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-admin.card>
    @endif
    
    {{-- FESTIVITÀ ANNO --}}
    <x-admin.card tone="light" shadow="lg">
        <h3 class="text-xl font-bold mb-4">Festività {{ $anno }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($festivitaAnno as $festa)
            <div class="flex items-center gap-3 p-3 bg-error/10 rounded-lg">
                <x-ui.icon name="gift" class="h-6 w-6 text-error" />
                <div>
                    <div class="font-semibold">{{ $festa->descrizione }}</div>
                    <div class="text-sm text-base-content/70">{{ $festa->data->format('d/m/Y') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </x-admin.card>
    
    @if(session('success'))
        <div class="toast toast-end">
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-admin.wrapper>
