{{-- Dashboard Marketing: Full-Width con Modern Stat Cards --}}
<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Dashboard Marketing') }}</x-slot>
    
    {{-- Page Header --}}
    <x-admin.page-header 
        title="Dashboard Marketing" 
        subtitle="Gestione campagne e lead generation"
        icon="bullhorn"
        iconColor="info"
    />
    
    {{-- Grid statistiche: sfrutta tutta la larghezza disponibile --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card
            title="Campagne Attive"
            :value="$stats['active_campaigns'] ?? 0"
            icon="campaign"
            color="info"
            trend="up"
            trendValue="+8%"
        />
        
        <x-admin.stat-card
            title="Tasso Conversione"
            :value="($stats['conversion_rate'] ?? 0) . '%'"
            icon="chart-line"
            color="success"
            trend="up"
            trendValue="+2.3%"
        />
        
        <x-admin.stat-card
            title="Lead Mensili"
            :value="$stats['monthly_leads'] ?? 0"
            icon="users"
            color="warning"
        />
        
        <x-admin.stat-card
            title="ROI"
            :value="($stats['roi'] ?? 0) . '%'"
            icon="chart-pie"
            color="accent"
            trend="up"
            trendValue="+15%"
        />
    </div>
    
    {{-- Grid moduli: Cards Bianche --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.cruscotto_lead') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-info/10 rounded-2xl flex items-center justify-center text-info group-hover:bg-info group-hover:text-info-content transition-all">
                        <x-admin.fa-icon name="users" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-info transition-colors">
                            Cruscotto Lead
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Dashboard gestione lead
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-info">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.costi_invio_messaggi') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-info/10 rounded-2xl flex items-center justify-center text-info group-hover:bg-info group-hover:text-info-content transition-all">
                        <x-admin.fa-icon name="envelope-open-text" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-info transition-colors">
                            Costi Invio Messaggi
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Monitoraggio costi messaggi
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-info">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.controllo_sms') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-info/10 rounded-2xl flex items-center justify-center text-info group-hover:bg-info group-hover:text-info-content transition-all">
                        <x-admin.fa-icon name="mobile" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-info transition-colors">
                            Controllo SMS
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione invii SMS
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-info">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
    </div>
</x-admin.wrapper>
