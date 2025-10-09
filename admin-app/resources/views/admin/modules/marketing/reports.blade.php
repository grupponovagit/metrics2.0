<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Reports Marketing') }}</x-slot>
    
    <x-admin.page-header 
        title="Reports Marketing" 
        subtitle="Report e analisi performance"
        icon="chart-bar"
        iconColor="info"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.marketing.index') }}" class="btn btn-outline btn-info">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Torna
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-info/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <x-ui.icon name="chart-bar" class="h-16 w-16 text-info" />
            </div>
            <h2 class="text-2xl font-bold text-base-content mb-4">Reports Marketing</h2>
            <p class="text-base-content/70 text-lg mb-6">
                Questa vista è pronta per essere personalizzata con report e statistiche.
            </p>
            <div class="badge badge-info badge-lg">Modulo Marketing</div>
        </div>
    </x-admin.card>
</x-admin.wrapper>
