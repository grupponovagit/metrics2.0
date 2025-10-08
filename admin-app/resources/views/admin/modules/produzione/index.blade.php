{{-- Dashboard Produzione: Full-Width con Modern Design --}}
<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Dashboard Produzione') }}</x-slot>
    
    {{-- Page Header --}}
    <x-admin.page-header 
        title="Dashboard Produzione" 
        subtitle="Monitoraggio e gestione processi produttivi"
        icon="industry"
        iconColor="warning"
    />
    
    {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card
            title="Produzione Giornaliera"
            :value="$stats['daily_production'] ?? 0"
            icon="chart-bar"
            color="warning"
        />
        
        <x-admin.stat-card
            title="Efficienza"
            :value="($stats['efficiency'] ?? 0) . '%'"
            icon="chart-line"
            color="success"
            trend="up"
            trendValue="+5%"
        />
        
        <x-admin.stat-card
            title="Qualità"
            :value="($stats['quality_score'] ?? 0) . '%'"
            icon="star"
            color="info"
        />
        
        <x-admin.stat-card
            title="Ordini Attivi"
            :value="$stats['active_orders'] ?? 0"
            icon="clipboard-list"
            color="accent"
        />
            </div>
    
    {{-- Moduli Produzione: Grid Full-Width --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Tabella Obiettivi --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.tabella_obiettivi') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="target" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Tabella Obiettivi
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Gestione obiettivi produzione</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 2: Cruscotto Produzione --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.cruscotto_produzione') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="chart-area" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Cruscotto Produzione
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Dashboard produzione generale</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 3: Cruscotto Operatore --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.cruscotto_operatore') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="user-gear" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Cruscotto Operatore
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Dashboard personale operatore</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 4: Cruscotto Mensile --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.cruscotto_mensile') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="calendar-check" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Cruscotto Mensile
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Analisi mensile produzione</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 5: Input Manuale --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.input_manuale') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="keyboard" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Input Manuale
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Inserimento dati manuale</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 6: Avanzamento Mensile --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.avanzamento_mensile') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="chart-line" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Avanzamento Mensile
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Monitoraggio avanzamento</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 7: KPI Lead Quartili --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.kpi_lead_quartili') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="chart-bar" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            KPI Lead Quartili
                        </h3>
                    </div>
                    <p class="text-xs text-base-content/70">Analisi KPI per quartili</p>
                </div>
            </a>
        </x-admin.card>

        {{-- Card 8: Controllo Stato Lead --}}
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.produzione.controllo_stato_lead') }}" class="block">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center text-warning group-hover:bg-warning group-hover:text-warning-content transition-all">
                            <x-admin.fa-icon name="list-check" class="h-5 w-5" />
                        </div>
                        <h3 class="font-semibold text-base text-base-content group-hover:text-warning transition-colors">
                            Controllo Stato Lead
                        </h3>
            </div>
                    <p class="text-xs text-base-content/70">Monitoraggio stato lead</p>
        </div>
            </a>
        </x-admin.card>
    </div>
</x-admin.wrapper>
