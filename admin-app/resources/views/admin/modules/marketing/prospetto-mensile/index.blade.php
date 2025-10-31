<x-admin.wrapper>
    <x-slot name="title">{{ __('Prospetto Mensile') }}</x-slot>
    
    <x-admin.page-header 
        title="Prospetto Mensile Campagne Marketing" 
        subtitle="Gestione e visualizzazione dei prospetti mensili Google Ads"
        icon="chart-line"
        iconColor="purple"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.marketing.prospetto_mensile.create') }}" class="btn btn-primary gap-2">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Nuovo Prospetto
            </a>
        </x-slot>
    </x-admin.page-header>
    
    {{-- Messaggi di successo/errore --}}
    @if(session('success'))
        <div class="alert alert-success mb-6">
            <x-ui.icon name="check-circle" class="h-5 w-5" />
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error mb-6">
            <x-ui.icon name="exclamation-circle" class="h-5 w-5" />
            <span>{{ session('error') }}</span>
        </div>
    @endif
    
    {{-- Lista Prospetti --}}
    <x-admin.card tone="light" shadow="lg" padding="none">
        
        @if($prospetti->count() > 0)
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th class="text-left">
                                <x-ui.icon name="calendar" class="h-4 w-4 inline mr-2" />
                                Nome Prospetto
                            </th>
                            <th class="text-left">
                                <x-ui.icon name="clock" class="h-4 w-4 inline mr-2" />
                                Periodo
                            </th>
                            <th class="text-left">
                                <x-ui.icon name="calendar-day" class="h-4 w-4 inline mr-2" />
                                Giorni Lav.
                            </th>
                            <th class="text-left">
                                <x-ui.icon name="euro-sign" class="h-4 w-4 inline mr-2" />
                                Budget Mensile
                            </th>
                            <th class="text-left">
                                <x-ui.icon name="users" class="h-4 w-4 inline mr-2" />
                                Account
                            </th>
                            <th class="text-center">
                                <x-ui.icon name="toggle-on" class="h-4 w-4 inline mr-2" />
                                Stato
                            </th>
                            <th class="text-center">
                                <x-ui.icon name="cog" class="h-4 w-4 inline mr-2" />
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prospetti as $prospetto)
                            <tr class="hover">
                                <td>
                                    <div class="font-bold text-base-content">{{ $prospetto->nome }}</div>
                                    @if($prospetto->descrizione)
                                        <div class="text-sm text-base-content/60">{{ Str::limit($prospetto->descrizione, 50) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="badge badge-outline badge-info">
                                            {{ \Carbon\Carbon::createFromDate($prospetto->anno, $prospetto->mese, 1)->locale('it')->translatedFormat('F Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-neutral">
                                        {{ $prospetto->giorni_lavorativi ?? 24 }} gg
                                    </span>
                                </td>
                                <td>
                                    <span class="font-semibold text-success">
                                        € {{ number_format($prospetto->budget_mensile, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $numAccounts = isset($prospetto->dati_accounts['accounts']) ? count($prospetto->dati_accounts['accounts']) : 0;
                                    @endphp
                                    <span class="badge badge-ghost badge-lg">
                                        {{ $numAccounts }} {{ $numAccounts === 1 ? 'account' : 'accounts' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.marketing.prospetto_mensile.toggle_attivo', $prospetto->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost">
                                            @if($prospetto->attivo)
                                                <span class="badge badge-success gap-2">
                                                    <x-ui.icon name="check" class="h-3 w-3" />
                                                    Attivo
                                                </span>
                                            @else
                                                <span class="badge badge-error gap-2">
                                                    <x-ui.icon name="times" class="h-3 w-3" />
                                                    Inattivo
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('admin.marketing.prospetto_mensile.view', $prospetto->id) }}" class="btn btn-sm btn-info gap-2">
                                            <x-ui.icon name="eye" class="h-4 w-4" />
                                            Visualizza
                                        </a>
                                        <a href="{{ route('admin.marketing.prospetto_mensile.edit', $prospetto->id) }}" class="btn btn-sm btn-warning gap-2">
                                            <x-ui.icon name="edit" class="h-4 w-4" />
                                            Modifica
                                        </a>
                                        <form action="{{ route('admin.marketing.prospetto_mensile.destroy', $prospetto->id) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo prospetto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-error gap-2">
                                                <x-ui.icon name="trash" class="h-4 w-4" />
                                                Elimina
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State --}}
            <div class="p-12 text-center">
                <div class="mb-4">
                    <x-ui.icon name="chart-line" class="h-20 w-20 mx-auto text-base-content/30" />
                </div>
                <h3 class="text-xl font-bold text-base-content mb-2">Nessun Prospetto Mensile</h3>
                <p class="text-base-content/60 mb-6">
                    Non hai ancora creato nessun prospetto mensile. Inizia creandone uno!
                </p>
                <a href="{{ route('admin.marketing.prospetto_mensile.create') }}" class="btn btn-primary gap-2">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Crea il Primo Prospetto
                </a>
            </div>
        @endif
        
    </x-admin.card>
    
</x-admin.wrapper>

