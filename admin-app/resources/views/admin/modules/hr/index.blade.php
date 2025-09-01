<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Modulo HR - Risorse Umane') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-base-content flex items-center gap-3">
                        <x-admin.fa-icon name="users" class="h-8 w-8 text-primary" />
                        Risorse Umane
                    </h1>
                    <p class="text-base-content/70 mt-2">
                        Gestione dipendenti e risorse umane
                    </p>
                </div>
                
                @if($canCreate)
                    <a href="{{ route('admin.hr.employees.create') }}" class="btn btn-primary">
                        <x-admin.fa-icon name="plus" class="h-4 w-4" />
                        Nuovo Dipendente
                    </a>
                @endif
            </div>

            {{-- Statistiche HR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Dipendenti Totali</p>
                                <p class="text-2xl font-bold text-primary">150</p>
                            </div>
                            <x-admin.fa-icon name="users" class="h-8 w-8 text-primary" />
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-success">+5</span>
                            <span class="text-sm text-base-content/70">questo mese</span>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Nuovi Assunti</p>
                                <p class="text-2xl font-bold text-success">5</p>
                            </div>
                            <x-admin.fa-icon name="user-plus" class="h-8 w-8 text-success" />
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-base-content/70">negli ultimi 30 giorni</span>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Dimissioni</p>
                                <p class="text-2xl font-bold text-warning">2</p>
                            </div>
                            <x-admin.fa-icon name="user-minus" class="h-8 w-8 text-warning" />
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-base-content/70">questo mese</span>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Dipartimenti</p>
                                <p class="text-2xl font-bold text-info">3</p>
                            </div>
                            <x-admin.fa-icon name="building" class="h-8 w-8 text-info" />
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-base-content/70">attivi</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Azioni Rapide --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow cursor-pointer">
                    <a href="{{ route('admin.hr.employees') }}" class="card-body">
                        <div class="flex items-center gap-4">
                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                                    <x-admin.fa-icon name="users" class="h-6 w-6" />
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Gestione Dipendenti</h3>
                                <p class="text-sm text-base-content/70">Visualizza e gestisci tutti i dipendenti</p>
                            </div>
                        </div>
                    </a>
                </div>

                @if($canViewReports)
                    <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow cursor-pointer">
                        <a href="{{ route('admin.hr.reports') }}" class="card-body">
                            <div class="flex items-center gap-4">
                                <div class="avatar placeholder">
                                    <div class="bg-info text-info-content rounded-full w-12 h-12">
                                        <x-admin.fa-icon name="chart-bar" class="h-6 w-6" />
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">Report HR</h3>
                                    <p class="text-sm text-base-content/70">Statistiche e analisi del personale</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if($canCreate)
                    <div class="card bg-base-100 shadow hover:shadow-lg transition-shadow cursor-pointer">
                        <a href="{{ route('admin.hr.employees.create') }}" class="card-body">
                            <div class="flex items-center gap-4">
                                <div class="avatar placeholder">
                                    <div class="bg-success text-success-content rounded-full w-12 h-12">
                                        <x-admin.fa-icon name="user-plus" class="h-6 w-6" />
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">Nuovo Dipendente</h3>
                                    <p class="text-sm text-base-content/70">Aggiungi un nuovo dipendente</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Permessi Utente --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <x-admin.fa-icon name="shield-alt" class="h-5 w-5" />
                        I Tuoi Permessi in questo Modulo
                    </h2>
                    
                    <div class="flex flex-wrap gap-2">
                        @if($canCreate)
                            <span class="badge badge-success">Creazione</span>
                        @endif
                        @if($canEdit)
                            <span class="badge badge-warning">Modifica</span>
                        @endif
                        @if($canDelete)
                            <span class="badge badge-error">Eliminazione</span>
                        @endif
                        @if($canViewReports)
                            <span class="badge badge-info">Report</span>
                        @endif
                        
                        @if(!$canCreate && !$canEdit && !$canDelete && !$canViewReports)
                            <span class="badge badge-ghost">Solo Visualizzazione</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
