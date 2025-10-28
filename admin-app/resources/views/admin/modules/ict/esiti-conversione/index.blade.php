<x-admin.wrapper>
    <x-slot name="title">Gestione Conversione Esiti Committenti</x-slot>

<div class="min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        {{-- Header con Breadcrumbs --}}
        <div class="mb-6">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.ict.index') }}">ICT</a></li>
                    <li>Conversione Esiti</li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold mt-2">Gestione Conversione Esiti</h1>
            <p class="text-sm text-base-content/70 mt-1">
                Mappa gli esiti specifici dei committenti in esiti globali standard per i calcoli KPI
            </p>
        </div>

        {{-- Messaggi di feedback --}}
        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Statistiche --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <div class="stat-title">Totale Conversioni</div>
                <div class="stat-value text-primary">{{ $stats['totale_conversioni'] }}</div>
                <div class="stat-desc">Regole di mappatura attive</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="stat-title">Commesse Mappate</div>
                <div class="stat-value text-secondary">{{ $stats['totale_commesse'] }}</div>
                <div class="stat-desc">Commesse con conversioni</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <div class="stat-title">Esiti Globali</div>
                <div class="stat-value text-accent">{{ count($esitiGlobali) }}</div>
                <div class="stat-desc">Categorie disponibili</div>
            </div>
        </div>

        {{-- Filtri e Azioni --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <div class="flex flex-col lg:flex-row gap-4 items-center">
                    {{-- Filtro Commessa --}}
                    <div class="flex-1 w-full">
                        <form method="GET" action="{{ route('admin.ict.esiti_conversione.index') }}" class="flex gap-2">
                            <select name="commessa" class="select select-bordered flex-1" onchange="this.form.submit()">
                                <option value="">📋 Tutte le Commesse</option>
                                @foreach($commesse as $commessa)
                                    <option value="{{ $commessa }}" {{ $commessaSelezionata === $commessa ? 'selected' : '' }}>
                                        {{ $commessa }}
                                    </option>
                                @endforeach
                            </select>
                            @if($commessaSelezionata)
                                <a href="{{ route('admin.ict.esiti_conversione.index') }}" class="btn btn-ghost">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- Pulsante Aggiungi --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.ict.esiti_conversione.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuova Conversione
                        </a>

                        {{-- Pulsante Elimina Multipli --}}
                        <button type="button" onclick="bulkDelete()" class="btn btn-error" id="bulkDeleteBtn" style="display:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Elimina Selezionati
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabella Esiti --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="w-12">
                                    <input type="checkbox" class="checkbox checkbox-sm" id="selectAll" onchange="toggleSelectAll(this)">
                                </th>
                                <th>Commessa</th>
                                <th>Esito Originale</th>
                                <th>Esito Globale</th>
                                <th>Note</th>
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($esiti as $esito)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox checkbox-sm esito-checkbox" value="{{ $esito->id }}" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td>
                                        <span class="badge badge-outline">{{ $esito->commessa }}</span>
                                    </td>
                                    <td>
                                        <span class="font-semibold">{{ $esito->esito_originale }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ \App\Models\EsitoConversione::getBadgeClass($esito->esito_globale) }}">
                                            {{ $esitiGlobali[$esito->esito_globale] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-base-content/70">
                                            {{ $esito->note ? Str::limit($esito->note, 50) : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex gap-2 justify-center">
                                            <a href="{{ route('admin.ict.esiti_conversione.edit', $esito->id) }}" class="btn btn-sm btn-ghost">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.ict.esiti_conversione.destroy', $esito->id) }}" method="POST" class="inline" onsubmit="return confirm('Confermi l\'eliminazione di questa conversione?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost text-error">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">
                                        <div class="text-base-content/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-lg font-semibold">Nessuna conversione trovata</p>
                                            <p class="text-sm">
                                                @if($commessaSelezionata)
                                                    Nessuna conversione per la commessa <strong>{{ $commessaSelezionata }}</strong>
                                                @else
                                                    Inizia aggiungendo la prima conversione esito
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginazione --}}
                @if($esiti->hasPages())
                    <div class="p-4 border-t border-base-300">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-base-content/70">
                                Mostrati {{ $esiti->firstItem() }} - {{ $esiti->lastItem() }} di {{ $esiti->total() }} risultati
                            </div>
                            <div class="join">
                                @if ($esiti->onFirstPage())
                                    <button class="join-item btn btn-disabled">«</button>
                                @else
                                    <a href="{{ $esiti->previousPageUrl() }}" class="join-item btn">«</a>
                                @endif

                                @foreach(range(1, $esiti->lastPage()) as $page)
                                    @if($page == $esiti->currentPage())
                                        <button class="join-item btn btn-active">{{ $page }}</button>
                                    @else
                                        <a href="{{ $esiti->url($page) }}" class="join-item btn">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($esiti->hasMorePages())
                                    <a href="{{ $esiti->nextPageUrl() }}" class="join-item btn">»</a>
                                @else
                                    <button class="join-item btn btn-disabled">»</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Form nascosto per bulk delete --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.ict.esiti_conversione.bulk_delete') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.esito-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkboxes = document.querySelectorAll('.esito-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkboxes.length > 0) {
        bulkDeleteBtn.style.display = 'inline-flex';
        bulkDeleteBtn.textContent = `Elimina ${checkboxes.length} Selezionati`;
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
    
    document.getElementById('selectAll').checked = checkboxes.length === document.querySelectorAll('.esito-checkbox').length;
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.esito-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Seleziona almeno una conversione da eliminare');
        return;
    }
    
    if (!confirm(`Confermi l'eliminazione di ${checkboxes.length} conversioni?`)) {
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    document.getElementById('bulkDeleteIds').value = JSON.stringify(ids);
    document.getElementById('bulkDeleteForm').submit();
}
</script>
</x-admin.wrapper>

