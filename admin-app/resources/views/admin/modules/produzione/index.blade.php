<x-admin.wrapper>
    <x-slot name="title">{{ __('Dashboard Produzione') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-base-content">
                    <x-admin.fa-icon name="industry" class="h-8 w-8 text-warning mr-3" />
                    Dashboard Produzione
                </h1>
                <p class="text-base-content/70 mt-2">Monitoraggio e gestione processi produttivi</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Produzione Giornaliera</p><p class="text-2xl font-bold text-warning">{{ $stats['daily_production'] ?? 0 }}</p></div><x-admin.fa-icon name="chart-bar" class="h-8 w-8 text-warning" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Efficienza</p><p class="text-2xl font-bold text-success">{{ $stats['efficiency'] ?? 0 }}%</p></div><x-admin.fa-icon name="chart-line" class="h-8 w-8 text-success" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Qualità</p><p class="text-2xl font-bold text-info">{{ $stats['quality_score'] ?? 0 }}%</p></div><x-admin.fa-icon name="star" class="h-8 w-8 text-info" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Ordini Attivi</p><p class="text-2xl font-bold text-accent">{{ $stats['active_orders'] ?? 0 }}</p></div><x-admin.fa-icon name="clipboard-list" class="h-8 w-8 text-accent" /></div></div></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="target" class="h-6 w-6" />Tabella Obiettivi</h2><p class="text-base-content/70">Gestione obiettivi produzione</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.tabella_obiettivi') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="chart-area" class="h-6 w-6" />Cruscotto Produzione</h2><p class="text-base-content/70">Dashboard produzione generale</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.cruscotto_produzione') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="user-gear" class="h-6 w-6" />Cruscotto Operatore</h2><p class="text-base-content/70">Dashboard personale operatore</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.cruscotto_operatore') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="calendar-check" class="h-6 w-6" />Cruscotto Mensile</h2><p class="text-base-content/70">Analisi mensile produzione</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.cruscotto_mensile') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="keyboard" class="h-6 w-6" />Input Manuale</h2><p class="text-base-content/70">Inserimento dati manuale</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.input_manuale') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="chart-line" class="h-6 w-6" />Avanzamento Mensile</h2><p class="text-base-content/70">Monitoraggio avanzamento</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.avanzamento_mensile') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="chart-bar" class="h-6 w-6" />KPI Lead Quartili</h2><p class="text-base-content/70">Analisi KPI per quartili</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.kpi_lead_quartili') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-warning"><x-admin.fa-icon name="list-check" class="h-6 w-6" />Controllo Stato Lead</h2><p class="text-base-content/70">Monitoraggio stato lead</p><div class="card-actions justify-end"><a href="{{ route('admin.produzione.controllo_stato_lead') }}" class="btn btn-warning btn-sm">Accedi</a></div></div></div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
