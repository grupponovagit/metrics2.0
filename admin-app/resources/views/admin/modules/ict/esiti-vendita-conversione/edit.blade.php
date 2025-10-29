<x-admin.wrapper>
    <x-slot name="title">Modifica Conversione Esito Vendita</x-slot>

<div class="min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        {{-- Header con Breadcrumbs --}}
        <div class="mb-6">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.ict.index') }}">ICT</a></li>
                    <li><a href="{{ route('admin.ict.esiti_vendita_conversione.index') }}">Conversione Esiti Vendita</a></li>
                    <li>Modifica</li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold mt-2">Modifica Conversione Esito Vendita</h1>
            <p class="text-sm text-base-content/70 mt-1">
                Modifica la mappatura tra esito specifico ed esito globale
            </p>
        </div>

        {{-- Messaggi di errore --}}
        @if(session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('admin.ict.esiti_vendita_conversione.update', $esito->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Esito Originale --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Esito Originale <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="esito_originale" class="input input-bordered" placeholder="Es: ATTIVATA, RESPINTA, IN LAVORAZIONE..." value="{{ old('esito_originale', $esito->esito_originale) }}" required>
                        @error('esito_originale')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt">L'esito specifico come viene ricevuto dal committente</span>
                        </label>
                    </div>

                    {{-- Esito Globale --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Esito Globale <span class="text-error">*</span></span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($esitiGlobali as $key => $label)
                                <label class="label cursor-pointer bg-base-200 p-4 rounded-lg hover:bg-base-300 transition">
                                    <span class="label-text flex items-center">
                                        <span class="badge {{ \App\Models\EsitoConversione::getBadgeClass($key) }} mr-2">{{ $label }}</span>
                                        @if($key === 'OK')
                                            <span class="text-xs text-base-content/70">Pratica completata con successo</span>
                                        @elseif($key === 'KO')
                                            <span class="text-xs text-base-content/70">Pratica fallita o respinta</span>
                                        @elseif($key === 'IN_ATTESA')
                                            <span class="text-xs text-base-content/70">In attesa di elaborazione</span>
                                        @elseif($key === 'BACKLOG')
                                            <span class="text-xs text-base-content/70">Coda interna</span>
                                        @elseif($key === 'BACKLOG_PARTNER')
                                            <span class="text-xs text-base-content/70">Coda partner esterno</span>
                                        @endif
                                    </span>
                                    <input type="radio" name="esito_globale" value="{{ $key }}" class="radio radio-primary" {{ old('esito_globale', $esito->esito_globale) === $key ? 'checked' : '' }} required>
                                </label>
                            @endforeach
                        </div>
                        @error('esito_globale')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    {{-- Note --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Note</span>
                        </label>
                        <textarea name="note" class="textarea textarea-bordered h-24" placeholder="Note aggiuntive sulla conversione (opzionale)">{{ old('note', $esito->note) }}</textarea>
                        @error('note')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    {{-- Info Timestamps --}}
                    <div class="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-xs">
                            <div><strong>Creato:</strong> {{ $esito->created_at->format('d/m/Y H:i') }}</div>
                            <div><strong>Ultima modifica:</strong> {{ $esito->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    {{-- Pulsanti --}}
                    <div class="card-actions justify-end pt-4 border-t border-base-300">
                        <a href="{{ route('admin.ict.esiti_vendita_conversione.index') }}" class="btn btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Annulla
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Aggiorna Conversione
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-admin.wrapper>

