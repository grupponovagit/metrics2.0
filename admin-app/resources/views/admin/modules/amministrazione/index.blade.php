{{-- Dashboard Amministrazione: Full-Width con Dark Theme --}}
<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Dashboard Amministrazione') }}</x-slot>

    {{-- Page Header --}}
    <x-admin.page-header 
        title="Dashboard Amministrazione" 
        subtitle="Gestione amministrativa e finanziaria"
        icon="calculator"
        iconColor="accent"
    />

    {{-- Statistiche Rapide: Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card
            title="Fatture in Sospeso"
            :value="$stats['pending_invoices'] ?? 0"
            icon="file-invoice"
            color="warning"
        />
        
        <x-admin.stat-card
            title="Ricavi Mensili"
            :value="'€' . number_format($stats['monthly_revenue'] ?? 0)"
            icon="chart-line"
            color="success"
            trend="up"
            trendValue="+12.5%"
        />
        
        <x-admin.stat-card
            title="Spese Mensili"
            :value="'€' . number_format($stats['expenses_month'] ?? 0)"
            icon="credit-card"
            color="error"
        />
        
        <x-admin.stat-card
            title="Margine di Profitto"
            :value="($stats['profit_margin'] ?? 0) . '%'"
            icon="percentage"
            color="info"
            trend="up"
            trendValue="+3.2%"
        />
            </div>

    {{-- Sezioni Principali: Cards Bianche --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.amministrazione.pda_media') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-admin.fa-icon name="chart-pie" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            PDA Media
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Analisi Performance e Dati Aggregati
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.amministrazione.costi_stipendi') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-admin.fa-icon name="money-bills" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            Costi Stipendi
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione costi stipendiali
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.amministrazione.costi_generali') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-admin.fa-icon name="receipt" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            Costi Generali
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione spese operative
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.amministrazione.inviti_a_fatturare') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-admin.fa-icon name="file-invoice-dollar" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            Inviti a Fatturare
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione richieste fatturazione
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.amministrazione.lettere_canvass') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-admin.fa-icon name="envelope" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            Lettere Canvass
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione comunicazioni canvass
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.amministrazione.budget') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-accent-content transition-all">
                        <x-admin.fa-icon name="wallet" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-accent transition-colors">
                            Gestione Budget
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Pianificazione e controllo budget
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-accent">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-admin.fa-icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
    </div>
</x-admin.wrapper>
