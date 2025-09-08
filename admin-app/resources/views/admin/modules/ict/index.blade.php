<x-admin.wrapper>
    <x-slot name="title">{{ __('Dashboard ICT') }}</x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-base-content">
                    <x-admin.fa-icon name="desktop" class="h-8 w-8 text-success mr-3" />
                    Dashboard ICT
                </h1>
                <p class="text-base-content/70 mt-2">Gestione sistemi e infrastruttura IT</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Server Online</p><p class="text-2xl font-bold text-success">{{ $stats['servers_online'] ?? 0 }}/{{ $stats['servers_total'] ?? 0 }}</p></div><x-admin.fa-icon name="server" class="h-8 w-8 text-success" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Uptime</p><p class="text-2xl font-bold text-info">{{ $stats['uptime'] ?? 0 }}%</p></div><x-admin.fa-icon name="chart-line" class="h-8 w-8 text-info" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Utenti Attivi</p><p class="text-2xl font-bold text-warning">{{ $stats['active_users'] ?? 0 }}</p></div><x-admin.fa-icon name="users" class="h-8 w-8 text-warning" /></div></div></div>
                <div class="card bg-base-100 shadow"><div class="card-body"><div class="flex items-center justify-between"><div><p class="text-sm text-base-content/70">Ticket Aperti</p><p class="text-2xl font-bold text-error">{{ $stats['pending_tickets'] ?? 0 }}</p></div><x-admin.fa-icon name="ticket" class="h-8 w-8 text-error" /></div></div></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-success"><x-admin.fa-icon name="calendar-days" class="h-6 w-6" />Calendario</h2><p class="text-base-content/70">Gestione eventi IT</p><div class="card-actions justify-end"><a href="{{ route('admin.ict.calendario') }}" class="btn btn-success btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-success"><x-admin.fa-icon name="server" class="h-6 w-6" />Stato</h2><p class="text-base-content/70">Monitoraggio sistemi</p><div class="card-actions justify-end"><a href="{{ route('admin.ict.stato') }}" class="btn btn-success btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-success"><x-admin.fa-icon name="tags" class="h-6 w-6" />Categoria UTM</h2><p class="text-base-content/70">Gestione categorie UTM</p><div class="card-actions justify-end"><a href="{{ route('admin.ict.categoria_utm_campagna') }}" class="btn btn-success btn-sm">Accedi</a></div></div></div>
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow"><div class="card-body"><h2 class="card-title text-success"><x-admin.fa-icon name="sync" class="h-6 w-6" />Aggiorna Mandati</h2><p class="text-base-content/70">Sincronizzazione mandati</p><div class="card-actions justify-end"><a href="{{ route('admin.ict.aggiorna_mandati') }}" class="btn btn-success btn-sm">Accedi</a></div></div></div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
