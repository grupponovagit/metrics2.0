<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Tabella per Mese') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-base-content">
                            <x-admin.fa-icon name="calendar-month" class="h-8 w-8 text-secondary mr-3" />
                            Tabella per Mese
                        </h1>
                        <p class="text-base-content/70 mt-2">
                            Visualizzazione dati aggregati per mese
                        </p>
                    </div>
                    <a href="{{ route('admin.hr.index') }}" class="btn btn-outline btn-secondary">
                        <x-admin.fa-icon name="arrow-left" class="h-4 w-4 mr-2" />
                        Torna a HR
                    </a>
                </div>
            </div>

            {{-- Content Placeholder --}}
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-16">
                    <x-admin.fa-icon name="calendar-month" class="h-24 w-24 text-secondary mx-auto mb-6" />
                    <h2 class="text-2xl font-bold text-base-content mb-4">Tabella per Mese</h2>
                    <p class="text-base-content/70 text-lg mb-6">
                        Questa vista è pronta per essere personalizzata con la logica e i dati mensili.
                    </p>
                    <div class="badge badge-secondary badge-lg">Modulo HR</div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
