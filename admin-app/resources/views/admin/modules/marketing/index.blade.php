<x-admin.wrapper>
    <x-slot name="title">{{ __('Dashboard Marketing') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-base-content">
                    <x-admin.fa-icon name="bullhorn" class="h-8 w-8 text-info mr-3" />
                    Dashboard Marketing
                </h1>
                <p class="text-base-content/70 mt-2">Gestione campagne e lead generation</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Campagne Attive</p><p class="text-2xl font-bold text-info">{{ $stats['active_campaigns'] ?? 0 }}</p></div><x-admin.fa-icon name="campaign" class="h-8 w-8 text-info" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Tasso Conversione</p><p class="text-2xl font-bold text-success">{{ $stats['conversion_rate'] ?? 0 }}%</p></div><x-admin.fa-icon name="chart-line" class="h-8 w-8 text-success" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Lead Mensili</p><p class="text-2xl font-bold text-warning">{{ $stats['monthly_leads'] ?? 0 }}</p></div><x-admin.fa-icon name="users" class="h-8 w-8 text-warning" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">ROI</p><p class="text-2xl font-bold text-accent">{{ $stats['roi'] ?? 0 }}%</p></div><x-admin.fa-icon name="chart-pie" class="h-8 w-8 text-accent" /></div></div></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-info"><x-admin.fa-icon name="users" class="h-6 w-6" />Cruscotto Lead</h2><p class="text-base-content/70">Dashboard gestione lead</p><div class="card-actions justify-end"><a href="{{ route('admin.marketing.cruscotto_lead') }}" class="btn btn-info btn-sm">Accedi <x-admin.fa-icon name="arrow-right" class="h-4 w-4" /></a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-info"><x-admin.fa-icon name="envelope-open-text" class="h-6 w-6" />Costi Invio Messaggi</h2><p class="text-base-content/70">Monitoraggio costi messaggi</p><div class="card-actions justify-end"><a href="{{ route('admin.marketing.costi_invio_messaggi') }}" class="btn btn-info btn-sm">Accedi <x-admin.fa-icon name="arrow-right" class="h-4 w-4" /></a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-info"><x-admin.fa-icon name="mobile" class="h-6 w-6" />Controllo SMS</h2><p class="text-base-content/70">Gestione invii SMS</p><div class="card-actions justify-end"><a href="{{ route('admin.marketing.controllo_sms') }}" class="btn btn-info btn-sm">Accedi <x-admin.fa-icon name="arrow-right" class="h-4 w-4" /></a></div></div></div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
