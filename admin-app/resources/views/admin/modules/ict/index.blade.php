<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Dashboard ICT') }}</x-slot>

    <x-admin.page-header 
        title="Dashboard ICT" 
        subtitle="Gestione sistemi e infrastruttura IT"
        icon="desktop"
        iconColor="success"
    />

    {{-- Statistiche KPI ICT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card
            title="Server Online"
            :value="($stats['servers_online'] ?? 0) . '/' . ($stats['servers_total'] ?? 0)"
            icon="server"
            color="success"
        />

        <x-admin.stat-card
            title="Uptime"
            value="{{ $stats['uptime'] ?? 99.9 }}%"
            icon="chart-line"
            color="info"
            trend="up"
            trendValue="+0.5%"
        />

        <x-admin.stat-card
            title="Utenti Attivi"
            :value="$stats['active_users'] ?? 0"
            icon="users"
            color="warning"
        />

        <x-admin.stat-card
            title="Ticket Aperti"
            :value="$stats['pending_tickets'] ?? 0"
            icon="ticket"
            color="error"
            trend="down"
            trendValue="-3"
        />
    </div>

    {{-- Sezioni Principali: Cards Bianche --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.calendario') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="calendar-days" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Calendario
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione eventi e manutenzioni
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.stato') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="heartbeat" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Stato Sistema
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Monitor e health check
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.tickets') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="ticket" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Tickets
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione ticket assistenza
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.categoria_utm_campagna') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="tags" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Categoria UTM
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione categorie UTM campagne
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.aggiorna_mandati') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="rotate" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Aggiorna Mandati
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Sincronizzazione mandati sistema
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.esiti_conversione.index') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <i class="fas fa-arrows-left-right text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Esiti Committenti
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Mappa esiti committenti (Plenitude, Enel, ecc.)
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.esiti_vendita_conversione.index') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Esiti Vendita
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Mappa esiti vendite in stati globali
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.google_ads_api') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-primary-content transition-all">
                        <i class="fab fa-google text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-primary transition-colors">
                            Google Ads API
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Gestione autenticazioni Google Ads API
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-primary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.system') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="gear" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Sistema
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Configurazione sistema
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.ict.reports') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-2xl flex items-center justify-center text-success group-hover:bg-success group-hover:text-success-content transition-all">
                        <x-ui.icon name="chart-bar" class="h-6 w-6" />
            </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-success transition-colors">
                            Reports
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Report e statistiche ICT
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-success">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
            </div>
            </div>
        </div>
            </a>
        </x-admin.card>
    </div>
</x-admin.wrapper>
