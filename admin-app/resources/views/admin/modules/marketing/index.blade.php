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
        {{-- Cruscotto Lead --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.cruscotto_lead') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-info/10 rounded-2xl flex items-center justify-center text-info group-hover:bg-info group-hover:text-info-content transition-all">
                        <x-ui.icon name="chart-line" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-info transition-colors">
                            Cruscotto Lead
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Dashboard lead generation con KPI e metriche digital
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-info">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Campagne --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.campaigns') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="bullhorn" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Campagne Marketing
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione e monitoraggio campagne attive
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Lead --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.leads') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-warning/10 rounded-2xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                        <x-ui.icon name="users" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-warning transition-colors">
                            Lead Database
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Archivio completo contatti e lead
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-warning">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Report Marketing --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.reports') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-ui.icon name="chart-bar" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            Report Marketing
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Analytics e report performance
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Costi Invio Messaggi --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.costi_invio_messaggi') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-error/10 rounded-2xl flex items-center justify-center text-error group-hover:bg-error group-hover:text-error-content transition-all">
                        <x-ui.icon name="envelope-open-text" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-error transition-colors">
                            Costi Invio Messaggi
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Monitoraggio costi email e messaggistica
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-error">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Controllo SMS --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.controllo_sms') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-primary-content transition-all">
                        <x-ui.icon name="mobile" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-primary transition-colors">
                            Controllo SMS
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione e tracciamento invii SMS
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-primary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Prospetto Mensile --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.prospetto_mensile.index') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="calendar-alt" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            Prospetto Mensile
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Pianificazione e budget mensile
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
        
        {{-- Configurazione UTM --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.marketing.configurazione_utm.index') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <x-ui.icon name="code" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-purple-600 transition-colors">
                            UTM Campagne
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Configurazione parametri UTM tracking
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-purple-600">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
    </div>
</x-admin.wrapper>
