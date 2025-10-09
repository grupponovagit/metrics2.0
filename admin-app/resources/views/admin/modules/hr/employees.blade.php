@php
// Dati fake dipendenti (base dataset)
$allEmployees = [
    [
        'id' => 1,
        'nome' => 'Mario Rossi',
        'email' => 'mario.rossi@company.it',
        'ruolo' => 'Senior Developer',
        'dipartimento' => 'IT',
        'assunzione' => '2020-03-15',
        'stato' => 'Attivo'
    ],
    [
        'id' => 2,
        'nome' => 'Laura Bianchi',
        'email' => 'laura.bianchi@company.it',
        'ruolo' => 'HR Manager',
        'dipartimento' => 'HR',
        'assunzione' => '2019-01-20',
        'stato' => 'Attivo'
    ],
    [
        'id' => 3,
        'nome' => 'Giuseppe Verdi',
        'email' => 'giuseppe.verdi@company.it',
        'ruolo' => 'Marketing Specialist',
        'dipartimento' => 'Marketing',
        'assunzione' => '2021-06-10',
        'stato' => 'Attivo'
    ],
    [
        'id' => 4,
        'nome' => 'Anna Ferrari',
        'email' => 'anna.ferrari@company.it',
        'ruolo' => 'Sales Manager',
        'dipartimento' => 'Sales',
        'assunzione' => '2018-09-05',
        'stato' => 'In Ferie'
    ],
    [
        'id' => 5,
        'nome' => 'Marco Neri',
        'email' => 'marco.neri@company.it',
        'ruolo' => 'UI/UX Designer',
        'dipartimento' => 'Design',
        'assunzione' => '2022-02-14',
        'stato' => 'Attivo'
    ],
    [
        'id' => 6,
        'nome' => 'Silvia Marino',
        'email' => 'silvia.marino@company.it',
        'ruolo' => 'Project Manager',
        'dipartimento' => 'PMO',
        'assunzione' => '2020-11-30',
        'stato' => 'Attivo'
    ],
];

// Applica filtri
$employees = collect($allEmployees);

// Filtro ricerca (nome, email, ruolo)
if ($search = request('search')) {
    $employees = $employees->filter(function($emp) use ($search) {
        $searchLower = strtolower($search);
        return str_contains(strtolower($emp['nome']), $searchLower) ||
               str_contains(strtolower($emp['email']), $searchLower) ||
               str_contains(strtolower($emp['ruolo']), $searchLower);
    });
}

// Filtro dipartimento
if ($department = request('department')) {
    $employees = $employees->filter(function($emp) use ($department) {
        return $emp['dipartimento'] === $department;
    });
}

// Filtro stato
if ($status = request('status')) {
    $statusMap = [
        'active' => 'Attivo',
        'on_leave' => 'In Ferie',
        'inactive' => 'Inattivo',
    ];
    $employees = $employees->filter(function($emp) use ($status, $statusMap) {
        return $emp['stato'] === ($statusMap[$status] ?? $status);
    });
}

// Filtro ruolo
if ($role = request('role')) {
    $employees = $employees->filter(function($emp) use ($role) {
        return str_contains(strtolower($emp['ruolo']), strtolower($role));
    });
}

// Converti a array
$employees = $employees->values()->toArray();

// Totali per stats
$totalEmployees = count($allEmployees);
$activeEmployees = collect($allEmployees)->where('stato', 'Attivo')->count();
$onLeaveEmployees = collect($allEmployees)->where('stato', 'In Ferie')->count();

// Prepara headers tabella
$headers = [
    ['label' => 'ID', 'class' => 'text-center w-20', 'sortable' => true],
    ['label' => 'Dipendente', 'sortable' => true],
    ['label' => 'Ruolo', 'sortable' => true],
    ['label' => 'Dipartimento', 'sortable' => true],
    ['label' => 'Data Assunzione', 'class' => 'text-center', 'sortable' => true],
    ['label' => 'Stato', 'class' => 'text-center'],
    ['label' => 'Azioni', 'class' => 'text-center w-32'],
];

// Prepara rows tabella
$rows = collect($employees)->map(function($emp) {
    $initials = strtoupper(substr($emp['nome'], 0, 1) . substr(explode(' ', $emp['nome'])[1] ?? '', 0, 1));
    $badgeColor = match($emp['dipartimento']) {
        'IT' => 'badge-info',
        'HR' => 'badge-primary',
        'Marketing' => 'badge-secondary',
        'Sales' => 'badge-accent',
        'Design' => 'badge-warning',
        default => 'badge-neutral'
    };
    
    return [
        // ID
        ['content' => '<span class="badge badge-ghost font-mono">#'.$emp['id'].'</span>', 'class' => 'text-center'],
        
        // Dipendente (Avatar + Nome + Email)
        ['content' => '
            <div class="flex items-center gap-3">
                <div class="avatar placeholder">
                    <div class="bg-gradient-to-br from-primary to-secondary text-white rounded-full w-10 h-10 shadow-lg">
                        <span class="text-sm font-bold">'.$initials.'</span>
                    </div>
                </div>
                <div>
                    <div class="font-semibold text-base-content">'.$emp['nome'].'</div>
                    <div class="text-xs text-base-content/60">'.$emp['email'].'</div>
                </div>
            </div>
        '],
        
        // Ruolo
        ['content' => '<span class="text-base-content">'.$emp['ruolo'].'</span>'],
        
        // Dipartimento (Badge)
        ['content' => '<span class="badge '.$badgeColor.' badge-sm">'.$emp['dipartimento'].'</span>'],
        
        // Data Assunzione
        ['content' => '<span class="text-sm">'.date('d/m/Y', strtotime($emp['assunzione'])).'</span>', 'class' => 'text-center'],
        
        // Stato
        ['content' => $emp['stato'] === 'Attivo' 
            ? '<span class="badge badge-success gap-2"><svg class="w-2 h-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>Attivo</span>'
            : '<span class="badge badge-warning gap-2"><svg class="w-2 h-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>'.$emp['stato'].'</span>',
        'class' => 'text-center'],
        
        // Azioni
        ['content' => '
            <div class="flex items-center justify-center gap-1">
                <button class="btn btn-ghost btn-xs tooltip" data-tip="Visualizza">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
                <button class="btn btn-ghost btn-xs tooltip" data-tip="Modifica">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button class="btn btn-ghost btn-xs text-error tooltip" data-tip="Elimina">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        ', 'class' => 'text-center'],
    ];
})->toArray();
@endphp

<x-admin.wrapper title="Dipendenti" :containerless="true">
    
    <div class="px-4 sm:px-6 lg:px-10 py-6 space-y-6">
        
        {{-- Page Header --}}
        <x-admin.page-header 
            title="Gestione Dipendenti" 
            subtitle="Anagrafica completa e gestione del personale aziendale"
            icon="users"
            icon-color="secondary"
        >
            <x-slot name="actions">
                <a href="{{ route('admin.hr.index') }}" class="btn btn-outline btn-secondary gap-2">
                    <x-ui.icon name="arrow-left" size="md" />
                    Torna a HR
                </a>
                <button class="btn btn-primary gap-2 shadow-lg hover:shadow-xl">
                    <x-ui.icon name="user-plus" size="md" />
                    Nuovo Dipendente
                </button>
            </x-slot>
        </x-admin.page-header>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-admin.stat-card
                title="Totale Dipendenti"
                :value="$totalEmployees"
                icon="users"
                color="primary"
            />
            <x-admin.stat-card
                title="Dipendenti Attivi"
                :value="$activeEmployees"
                icon="check-circle"
                color="success"
            />
            <x-admin.stat-card
                title="In Ferie"
                :value="$onLeaveEmployees"
                icon="calendar"
                color="warning"
            />
            <x-admin.stat-card
                title="Risultati Filtrati"
                :value="count($employees)"
                icon="filter"
                color="info"
            />
        </div>

        {{-- Filters Component --}}
        @php
        $filterConfig = [
            [
                'name' => 'department',
                'label' => 'Dipartimento',
                'icon' => 'building-columns',
                'type' => 'select',
                'placeholder' => 'Tutti i dipartimenti',
                'options' => [
                    'IT' => 'IT',
                    'HR' => 'Risorse Umane',
                    'Marketing' => 'Marketing',
                    'Sales' => 'Vendite',
                    'Design' => 'Design',
                    'PMO' => 'Project Management',
                ],
                'value' => request('department', ''),
            ],
            [
                'name' => 'status',
                'label' => 'Stato',
                'icon' => 'check-circle',
                'type' => 'select',
                'placeholder' => 'Tutti gli stati',
                'options' => [
                    'active' => 'Attivo',
                    'on_leave' => 'In Ferie',
                    'inactive' => 'Inattivo',
                ],
                'value' => request('status', ''),
            ],
            [
                'name' => 'role',
                'label' => 'Ruolo',
                'icon' => 'user',
                'type' => 'text',
                'placeholder' => 'Es: Manager, Developer...',
                'value' => request('role', ''),
            ],
        ];
        @endphp
        
        <x-filters 
            searchPlaceholder="Cerca per nome, email o ruolo..."
            searchName="search"
            :searchValue="request('search', '')"
            :filters="$filterConfig"
            :action="route('admin.hr.employees')"
            :showReset="true"
            :compact="true"
        />

        {{-- Table with Employees --}}
        <x-admin.card tone="light" shadow="lg" padding="none">
            <div class="p-6 border-b border-base-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-base-content">Elenco Dipendenti</h3>
                        <p class="text-sm text-base-content/60">
                            @if(count($employees) === $totalEmployees)
                                {{ $totalEmployees }} dipendenti totali
                            @else
                                Mostrando {{ count($employees) }} di {{ $totalEmployees }} dipendenti
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-outline gap-2">
                            <x-ui.icon name="download" size="sm" />
                            Esporta
                        </button>
                    </div>
                </div>
            </div>
            
            <x-table 
                :headers="$headers" 
                :rows="$rows"
                :striped="true"
                :hover="true"
            />

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-base-300 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-base-content/60">
                    Visualizzati <span class="font-semibold text-base-content">{{ count($employees) }}</span> di <span class="font-semibold text-base-content">{{ $totalEmployees }}</span> dipendenti
                    @if(count($employees) < $totalEmployees)
                        <span class="badge badge-info badge-sm ml-2">Filtrati</span>
                    @endif
                </div>
                <div class="join">
                    <button class="join-item btn btn-sm btn-disabled">«</button>
                    <button class="join-item btn btn-sm btn-active">1</button>
                    <button class="join-item btn btn-sm btn-disabled">»</button>
                </div>
            </div>
        </x-admin.card>

    </div>

</x-admin.wrapper>
