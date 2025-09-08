<x-admin.wrapper>
    <x-slot name="title">{{ __('Budget') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-base-content">
                            <x-admin.fa-icon name="chart-pie" class="h-8 w-8 text-accent mr-3" />
                            Gestione Budget
                        </h1>
                        <p class="text-base-content/70 mt-2">Pianificazione e controllo budget aziendale</p>
                    </div>
                    <a href="{{ route('admin.amministrazione.index') }}" class="btn btn-outline btn-accent">
                        <x-admin.fa-icon name="arrow-left" class="h-4 w-4 mr-2" />
                        Torna ad Amministrazione
                    </a>
                </div>
            </div>
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-16">
                    <x-admin.fa-icon name="chart-pie" class="h-24 w-24 text-accent mx-auto mb-6" />
                    <h2 class="text-2xl font-bold text-base-content mb-4">Gestione Budget</h2>
                    <p class="text-base-content/70 text-lg mb-6">Questa vista è pronta per essere personalizzata con la gestione budget.</p>
                    <div class="badge badge-accent badge-lg">Modulo Amministrazione</div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
