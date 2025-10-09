<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Modulo HR - Risorse Umane') }}</x-slot>

    <x-admin.page-header 
        title="Risorse Umane" 
        subtitle="Gestione dipendenti e risorse umane"
        icon="users"
        iconColor="secondary"
    >
        <x-slot name="actions">
            @if($canCreate)
                <a href="{{ route('admin.hr.employees.create') }}" class="btn btn-primary">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Nuovo Dipendente
                </a>
            @endif
        </x-slot>
    </x-admin.page-header>

    {{-- Statistiche KPI HR --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card
            title="Dipendenti Totali"
            :value="$stats['total_employees']"
            icon="users"
            color="secondary"
            trend="up"
            trendValue="+5"
        />

        <x-admin.stat-card
            title="Nuovi Assunti"
            :value="$stats['new_hires']"
            icon="user-plus"
            color="success"
        />

        <x-admin.stat-card
            title="Assenze Mese"
            :value="$stats['absences']"
            icon="calendar-xmark"
            color="warning"
        />

        <x-admin.stat-card
            title="Turnover Rate"
            value="{{ $stats['turnover_rate'] }}%"
            icon="arrow-right-arrow-left"
            color="info"
            trend="down"
            trendValue="-2.1%"
        />
    </div>

    {{-- Sezioni Principali: Cards Bianche --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.hr.employees') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="users" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            Gestione Dipendenti
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Anagrafica e gestione dipendenti
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.hr.cruscotto_assenze') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="calendar-xmark" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            Cruscotto Assenze
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Monitoraggio assenze e presenze
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.hr.formazione') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="graduation-cap" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            Formazione
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Corsi e formazione dipendenti
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.hr.cruscotto_lead_recruit') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="user-plus" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            Recruiting
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Dashboard recruiting e selezione
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.hr.pes') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="star" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            PES
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Performance Evaluation System
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>

        <x-admin.card tone="light" shadow="lg" hoverable="true" class="group">
            <a href="{{ route('admin.hr.reports') }}" class="block">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-secondary-content transition-all">
                        <x-ui.icon name="chart-bar" class="h-6 w-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-base-content group-hover:text-secondary transition-colors">
                            Reports HR
                        </h3>
                        <p class="text-sm text-base-content/70 mt-1">
                            Report e statistiche
                        </p>
                        <div class="flex items-center gap-2 mt-3 text-secondary">
                            <span class="text-sm font-medium">Accedi</span>
                            <x-ui.icon name="arrow-right" class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </x-admin.card>
    </div>
</x-admin.wrapper>
