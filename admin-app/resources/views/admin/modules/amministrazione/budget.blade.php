<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Budget') }}</x-slot>
    
    <x-admin.page-header 
        title="Gestione Budget" 
        subtitle="Pianificazione e controllo budget aziendale"
        icon="chart-pie"
        iconColor="accent"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.amministrazione.index') }}" class="btn btn-outline btn-accent">
                <x-admin.fa-icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-accent/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <x-admin.fa-icon name="chart-pie" class="h-16 w-16 text-accent" />
            </div>
            <h2 class="text-2xl font-bold text-base-content mb-4">Gestione Budget</h2>
            <p class="text-base-content/70 text-lg mb-6">
                Questa vista è pronta per essere personalizzata con la gestione budget.
            </p>
            <div class="badge badge-accent badge-lg">Modulo Amministrazione</div>
        </div>
    </x-admin.card>
</x-admin.wrapper>
