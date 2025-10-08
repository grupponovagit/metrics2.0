<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Reports ICT') }}</x-slot>
    
    <x-admin.page-header 
        title="Reports ICT" 
        subtitle="Report e statistiche ICT"
        icon="chart-bar"
        iconColor="success"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.ict.index') }}" class="btn btn-outline btn-success">
                <x-admin.fa-icon name="arrow-left" class="h-4 w-4" />
                Torna a ICT
            </a>
        </x-slot>
    </x-admin.page-header>
    
    <x-admin.card tone="light" shadow="lg" padding="loose">
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-success/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <x-admin.fa-icon name="chart-bar" class="h-16 w-16 text-success" />
            </div>
            <h2 class="text-2xl font-bold text-base-content mb-4">Reports ICT</h2>
            <p class="text-base-content/70 text-lg mb-6">
                Questa vista è pronta per essere personalizzata con reports ICT.
            </p>
            <div class="badge badge-success badge-lg">Modulo ICT</div>
        </div>
    </x-admin.card>
</x-admin.wrapper>
