<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Controllo Stato Lead') }}</x-slot>
    
    <x-admin.page-header 
        title="Controllo Stato Lead" 
        subtitle="Monitoraggio stato lead produzione"
        icon="list-check"
        iconColor="warning"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.produzione.index') }}" class="btn btn-outline btn-warning">
                <x-admin.fa-icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-warning/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <x-admin.fa-icon name="list-check" class="h-16 w-16 text-warning" />
            </div>
            <h2 class="text-2xl font-bold text-base-content mb-4">Controllo Stato Lead</h2>
            <p class="text-base-content/70 text-lg mb-6">
                Questa vista è pronta per essere personalizzata con controllo stato lead.
            </p>
            <div class="badge badge-warning badge-lg">Modulo Produzione</div>
        </div>
    </x-admin.card>
</x-admin.wrapper>
