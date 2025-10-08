<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Tabella per Mese') }}</x-slot>
    
    <x-admin.page-header 
        title="Tabella per Mese" 
        subtitle="Analisi dati mensili HR"
        icon="calendar"
        iconColor="secondary"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.hr.index') }}" class="btn btn-outline btn-secondary">
                <x-admin.fa-icon name="arrow-left" class="h-4 w-4" />
                Torna a HR
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-secondary/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <x-admin.fa-icon name="calendar" class="h-16 w-16 text-secondary" />
            </div>
            <h2 class="text-2xl font-bold text-base-content mb-4">Tabella per Mese</h2>
            <p class="text-base-content/70 text-lg mb-6">
                Questa vista è pronta per essere personalizzata con tabella mensile.
            </p>
            <div class="badge badge-secondary badge-lg">Modulo HR</div>
        </div>
    </x-admin.card>
</x-admin.wrapper>
