<x-admin.wrapper>
    <x-slot name="title">{{ __('Report Amministrazione') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-base-content">
                            <x-admin.fa-icon name="chart-line" class="h-8 w-8 text-accent mr-3" />
                            Report Amministrazione
                        </h1>
                        <p class="text-base-content/70 mt-2">Report finanziari e amministrativi</p>
                    </div>
                    <a href="{{ route('admin.amministrazione.index') }}" class="btn btn-outline btn-accent">
                        <x-admin.fa-icon name="arrow-left" class="h-4 w-4 mr-2" />
                        Torna ad Amministrazione
                    </a>
                </div>
            </div>
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-16">
                    <x-admin.fa-icon name="chart-line" class="h-24 w-24 text-accent mx-auto mb-6" />
                    <h2 class="text-2xl font-bold text-base-content mb-4">Report Amministrazione</h2>
                    <p class="text-base-content/70 text-lg mb-6">Questa vista è pronta per essere personalizzata con i report amministrativi.</p>
                    <div class="badge badge-accent badge-lg">Modulo Amministrazione</div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
