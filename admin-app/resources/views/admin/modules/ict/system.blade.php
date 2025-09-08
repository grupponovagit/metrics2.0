<x-admin.wrapper>
    <x-slot name="title">{{ __('Sistema ICT') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-base-content">
                            <x-admin.fa-icon name="server" class="h-8 w-8 text-success mr-3" />
                            Sistema ICT
                        </h1>
                        <p class="text-base-content/70 mt-2">Gestione sistemi e infrastruttura</p>
                    </div>
                    <a href="{{ route('admin.ict.index') }}" class="btn btn-outline btn-success">
                        <x-admin.fa-icon name="arrow-left" class="h-4 w-4 mr-2" />
                        Torna a ICT
                    </a>
                </div>
            </div>
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-16">
                    <x-admin.fa-icon name="server" class="h-24 w-24 text-success mx-auto mb-6" />
                    <h2 class="text-2xl font-bold text-base-content mb-4">Sistema ICT</h2>
                    <p class="text-base-content/70 text-lg mb-6">Questa vista è pronta per essere personalizzata con la gestione sistemi.</p>
                    <div class="badge badge-success badge-lg">Modulo ICT</div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
