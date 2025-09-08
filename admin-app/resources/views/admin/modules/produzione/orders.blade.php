<x-admin.wrapper>
    <x-slot name="title">{{ __('Ordini Produzione') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-base-content">
                            <x-admin.fa-icon name="clipboard-list" class="h-8 w-8 text-warning mr-3" />
                            Ordini Produzione
                        </h1>
                        <p class="text-base-content/70 mt-2">Gestione ordini di produzione</p>
                    </div>
                    <a href="{{ route('admin.produzione.index') }}" class="btn btn-outline btn-warning">
                        <x-admin.fa-icon name="arrow-left" class="h-4 w-4 mr-2" />
                        Torna a Produzione
                    </a>
                </div>
            </div>
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-16">
                    <x-admin.fa-icon name="clipboard-list" class="h-24 w-24 text-warning mx-auto mb-6" />
                    <h2 class="text-2xl font-bold text-base-content mb-4">Ordini Produzione</h2>
                    <p class="text-base-content/70 text-lg mb-6">Questa vista è pronta per essere personalizzata con la gestione ordini.</p>
                    <div class="badge badge-warning badge-lg">Modulo Produzione</div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
