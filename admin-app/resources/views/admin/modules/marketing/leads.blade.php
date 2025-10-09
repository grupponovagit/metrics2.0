@php
// Dati fake campagne marketing
$allCampaigns = [
    [
        'account' => 'Google Ads',
        'campagna' => 'Brand Awareness Q4',
        'provenienza' => 'Google Search',
        'costo' => 5000,
        'tipologia' => 'Search',
        'lead' => 250,
        'conversioni' => 45,
        'click' => 1200,
        'cp' => 38,
        'ok' => 32,
        'ko' => 6,
    ],
    [
        'account' => 'Facebook Ads',
        'campagna' => 'Retargeting Inverno',
        'provenienza' => 'Facebook',
        'costo' => 3500,
        'tipologia' => 'Social',
        'lead' => 180,
        'conversioni' => 35,
        'click' => 850,
        'cp' => 30,
        'ok' => 25,
        'ko' => 5,
    ],
    [
        'account' => 'Google Ads',
        'campagna' => 'Lead Gen B2B',
        'provenienza' => 'Google Display',
        'costo' => 4200,
        'tipologia' => 'Display',
        'lead' => 320,
        'conversioni' => 52,
        'click' => 1500,
        'cp' => 45,
        'ok' => 38,
        'ko' => 7,
    ],
    [
        'account' => 'LinkedIn Ads',
        'campagna' => 'Professionisti Tech',
        'provenienza' => 'LinkedIn',
        'costo' => 6000,
        'tipologia' => 'Social',
        'lead' => 150,
        'conversioni' => 28,
        'click' => 600,
        'cp' => 22,
        'ok' => 18,
        'ko' => 4,
    ],
    [
        'account' => 'Facebook Ads',
        'campagna' => 'Traffico Generale',
        'provenienza' => 'Instagram',
        'costo' => 2800,
        'tipologia' => 'Social',
        'lead' => 220,
        'conversioni' => 38,
        'click' => 920,
        'cp' => 32,
        'ok' => 27,
        'ko' => 5,
    ],
    [
        'account' => 'Google Ads',
        'campagna' => 'Shopping Black Friday',
        'provenienza' => 'Google Shopping',
        'costo' => 7500,
        'tipologia' => 'Shopping',
        'lead' => 480,
        'conversioni' => 85,
        'click' => 2100,
        'cp' => 72,
        'ok' => 65,
        'ko' => 7,
    ],
];

// Applica filtri
$campaigns = collect($allCampaigns);

// Filtro per data range (mock - in produzione userai date reali)
if ($dateFrom = request('date_from')) {
    // Filtra per data from
}
if ($dateTo = request('date_to')) {
    // Filtra per data to
}

// Filtro per account
if ($account = request('account')) {
    $campaigns = $campaigns->filter(fn($c) => $c['account'] === $account);
}

// Filtro per tipologia
if ($tipologia = request('tipologia')) {
    $campaigns = $campaigns->filter(fn($c) => $c['tipologia'] === $tipologia);
}

// Calcola KPI aggiuntivi
$campaigns = $campaigns->map(function($c) {
    // Calcoli KPI
    $c['conv_lead'] = $c['lead'] > 0 ? round(($c['conversioni'] / $c['lead']) * 100, 2) : 0;
    $c['cp_lead'] = $c['lead'] > 0 ? round(($c['cp'] / $c['lead']) * 100, 2) : 0;
    $c['ok_lead'] = $c['lead'] > 0 ? round(($c['ok'] / $c['lead']) * 100, 2) : 0;
    $c['media_cp_conv'] = $c['conversioni'] > 0 ? round($c['costo'] / $c['conversioni'], 2) : 0;
    $c['cpl'] = $c['lead'] > 0 ? round($c['costo'] / $c['lead'], 2) : 0;
    $c['cpa'] = $c['conversioni'] > 0 ? round($c['costo'] / $c['conversioni'], 2) : 0;
    $c['cpc'] = $c['click'] > 0 ? round($c['costo'] / $c['click'], 2) : 0;
    
    // ROAS (mock: assume revenue = cp * 500€ medio)
    $revenue = $c['cp'] * 500;
    $c['roas'] = $c['costo'] > 0 ? round($revenue / $c['costo'], 2) : 0;
    
    // ROI
    $profit = $revenue - $c['costo'];
    $c['roi'] = $c['costo'] > 0 ? round(($profit / $c['costo']) * 100, 2) : 0;
    
    return $c;
});

$campaigns = $campaigns->values()->toArray();

// Totali per stats
$totalCampaigns = count($allCampaigns);
$totalCosto = collect($allCampaigns)->sum('costo');
$totalLead = collect($allCampaigns)->sum('lead');
$totalConversioni = collect($allCampaigns)->sum('conversioni');
$avgCPL = $totalLead > 0 ? round($totalCosto / $totalLead, 2) : 0;

// Headers tabella (molto ampi)
$headers = [
    ['label' => 'Account', 'class' => 'min-w-[140px]'],
    ['label' => 'Campagna', 'class' => 'min-w-[180px]', 'sortable' => true],
    ['label' => 'Provenienza', 'class' => 'min-w-[140px]'],
    ['label' => 'Costo', 'class' => 'text-right min-w-[100px]', 'sortable' => true],
    ['label' => 'Tipologia', 'class' => 'text-center min-w-[100px]'],
    ['label' => 'Lead', 'class' => 'text-right min-w-[80px]', 'sortable' => true],
    ['label' => 'Conv.', 'class' => 'text-right min-w-[80px]', 'sortable' => true],
    ['label' => 'Click', 'class' => 'text-right min-w-[80px]'],
    ['label' => 'CP', 'class' => 'text-right min-w-[70px]'],
    ['label' => 'OK', 'class' => 'text-right min-w-[70px]'],
    ['label' => 'KO', 'class' => 'text-right min-w-[70px]'],
    ['label' => '%Conv/Lead', 'class' => 'text-right min-w-[100px]'],
    ['label' => '%CP/Lead', 'class' => 'text-right min-w-[100px]'],
    ['label' => '%OK/Lead', 'class' => 'text-right min-w-[100px]'],
    ['label' => 'Media CP/Conv', 'class' => 'text-right min-w-[120px]'],
    ['label' => 'CPL', 'class' => 'text-right min-w-[90px]'],
    ['label' => 'CPA', 'class' => 'text-right min-w-[90px]'],
    ['label' => 'CPC', 'class' => 'text-right min-w-[90px]'],
    ['label' => 'ROAS', 'class' => 'text-right min-w-[90px]'],
    ['label' => 'ROI %', 'class' => 'text-right min-w-[90px]'],
];

// Prepara rows
$rows = collect($campaigns)->map(function($c) {
    $accountColor = match($c['account']) {
        'Google Ads' => 'badge-error',
        'Facebook Ads' => 'badge-info',
        'LinkedIn Ads' => 'badge-primary',
        default => 'badge-neutral'
    };
    
    $tipologiaColor = match($c['tipologia']) {
        'Search' => 'badge-warning',
        'Display' => 'badge-secondary',
        'Social' => 'badge-accent',
        'Shopping' => 'badge-success',
        default => 'badge-ghost'
    };
    
    return [
        // Account
        ['content' => '<span class="badge '.$accountColor.' badge-sm font-semibold">'.$c['account'].'</span>'],
        
        // Campagna
        ['content' => '<div class="font-semibold text-base-content">'.$c['campagna'].'</div>'],
        
        // Provenienza
        ['content' => '<span class="text-sm text-base-content/70">'.$c['provenienza'].'</span>'],
        
        // Costo
        ['content' => '<span class="font-bold text-error">€ '.number_format($c['costo'], 0, ',', '.').'</span>', 'class' => 'text-right'],
        
        // Tipologia
        ['content' => '<span class="badge '.$tipologiaColor.' badge-sm">'.$c['tipologia'].'</span>', 'class' => 'text-center'],
        
        // Lead
        ['content' => '<span class="font-semibold text-primary">'.number_format($c['lead']).'</span>', 'class' => 'text-right'],
        
        // Conversioni
        ['content' => '<span class="font-semibold text-success">'.number_format($c['conversioni']).'</span>', 'class' => 'text-right'],
        
        // Click
        ['content' => '<span class="text-info">'.number_format($c['click']).'</span>', 'class' => 'text-right'],
        
        // CP
        ['content' => '<span class="badge badge-outline badge-sm">'.number_format($c['cp']).'</span>', 'class' => 'text-right'],
        
        // OK
        ['content' => '<span class="text-success font-semibold">'.number_format($c['ok']).'</span>', 'class' => 'text-right'],
        
        // KO
        ['content' => '<span class="text-error">'.number_format($c['ko']).'</span>', 'class' => 'text-right'],
        
        // %Conv/Lead
        ['content' => '<span class="badge badge-success badge-sm">'.$c['conv_lead'].'%</span>', 'class' => 'text-right'],
        
        // %CP/Lead
        ['content' => '<span class="badge badge-warning badge-sm">'.$c['cp_lead'].'%</span>', 'class' => 'text-right'],
        
        // %OK/Lead
        ['content' => '<span class="badge badge-info badge-sm">'.$c['ok_lead'].'%</span>', 'class' => 'text-right'],
        
        // Media CP/Conv
        ['content' => '<span class="font-mono text-sm">€ '.number_format($c['media_cp_conv'], 2, ',', '.').'</span>', 'class' => 'text-right'],
        
        // CPL
        ['content' => '<span class="font-mono text-sm text-primary">€ '.number_format($c['cpl'], 2, ',', '.').'</span>', 'class' => 'text-right'],
        
        // CPA
        ['content' => '<span class="font-mono text-sm text-success">€ '.number_format($c['cpa'], 2, ',', '.').'</span>', 'class' => 'text-right'],
        
        // CPC
        ['content' => '<span class="font-mono text-sm text-info">€ '.number_format($c['cpc'], 2, ',', '.').'</span>', 'class' => 'text-right'],
        
        // ROAS
        ['content' => '<span class="badge badge-'.($c['roas'] >= 3 ? 'success' : ($c['roas'] >= 2 ? 'warning' : 'error')).' font-bold">'.$c['roas'].'x</span>', 'class' => 'text-right'],
        
        // ROI
        ['content' => '<span class="font-bold '.($c['roi'] > 0 ? 'text-success' : 'text-error').'">'.$c['roi'].'%</span>', 'class' => 'text-right'],
    ];
})->toArray();
@endphp

<x-admin.wrapper title="Lead Marketing" :containerless="true">
    
    <div class="space-y-6">
        
        {{-- Page Header --}}
        <x-admin.page-header 
            title="Lead Marketing & Campagne" 
            subtitle="Analisi dettagliata performance campagne marketing e lead generation"
            icon="chart-pie"
            icon-color="info"
        >
            <x-slot name="actions">
                <a href="{{ route('admin.marketing.index') }}" class="btn btn-outline btn-info gap-2">
                    <x-ui.icon name="arrow-left" size="md" />
                    Torna a Marketing
                </a>
                <button class="btn btn-primary gap-2 shadow-lg hover:shadow-xl">
                    <x-ui.icon name="plus" size="md" />
                    Nuova Campagna
                </button>
            </x-slot>
        </x-admin.page-header>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
            <x-admin.stat-card
                title="Totale Campagne"
                :value="$totalCampaigns"
                icon="megaphone"
                color="primary"
            />
            <x-admin.stat-card
                title="Budget Investito"
                :value="'€ ' . number_format($totalCosto, 0, ',', '.')"
                icon="currency-dollar"
                color="error"
            />
            <x-admin.stat-card
                title="Lead Generati"
                :value="$totalLead"
                icon="users"
                color="success"
            />
            <x-admin.stat-card
                title="CPL Medio"
                :value="'€ ' . number_format($avgCPL, 2, ',', '.')"
                icon="chart-bar"
                color="info"
            />
        </div>

        {{-- Filters --}}
        @php
        $filterConfig = [
            [
                'name' => 'date_from',
                'label' => 'Data Inizio',
                'icon' => 'calendar',
                'type' => 'date',
                'value' => request('date_from', ''),
            ],
            [
                'name' => 'date_to',
                'label' => 'Data Fine',
                'icon' => 'calendar',
                'type' => 'date',
                'value' => request('date_to', ''),
            ],
            [
                'name' => 'account',
                'label' => 'Account',
                'icon' => 'building-columns',
                'type' => 'select',
                'placeholder' => 'Tutti gli account',
                'options' => [
                    'Google Ads' => 'Google Ads',
                    'Facebook Ads' => 'Facebook Ads',
                    'LinkedIn Ads' => 'LinkedIn Ads',
                ],
                'value' => request('account', ''),
            ],
            [
                'name' => 'tipologia',
                'label' => 'Tipologia',
                'icon' => 'tag',
                'type' => 'select',
                'placeholder' => 'Tutte le tipologie',
                'options' => [
                    'Search' => 'Search',
                    'Display' => 'Display',
                    'Social' => 'Social',
                    'Shopping' => 'Shopping',
                ],
                'value' => request('tipologia', ''),
            ],
        ];
        @endphp
        
        <x-filters 
            searchPlaceholder="Cerca per nome campagna..."
            searchName="search"
            :searchValue="request('search', '')"
            :filters="$filterConfig"
            :action="route('admin.marketing.leads')"
            :showReset="true"
            :compact="false"
        />

        {{-- Alert Info --}}
        <div class="alert alert-info shadow-lg">
            <x-ui.icon name="info" size="md" />
            <div>
                <h3 class="font-bold">Tabella con scroll orizzontale</h3>
                <div class="text-sm">La tabella contiene molti KPI. Usa lo scroll orizzontale per visualizzare tutte le colonne.</div>
            </div>
        </div>

        {{-- Wide Table with Campaigns --}}
        <div class="w-full">
            <x-admin.card tone="light" shadow="lg" padding="none">
                {{-- Header (fissa, non scrolla) --}}
                <div class="p-6 border-b border-base-300">
                <div class="flex items-center justify-between">
                    <div>
                            <h3 class="text-lg font-bold text-base-content">Performance Campagne</h3>
                            <p class="text-sm text-base-content/60">
                                @if(count($campaigns) === $totalCampaigns)
                                    {{ $totalCampaigns }} campagne attive
                                @else
                                    Mostrando {{ count($campaigns) }} di {{ $totalCampaigns }} campagne
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-outline gap-2">
                                <x-ui.icon name="download" size="sm" />
                                Esporta Excel
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Tabella con scroll responsive --}}
                <x-admin.table 
                    :headers="$headers" 
                    :rows="$rows"
                    minWidth="2400px"
                    maxHeight="60vh"
                    :striped="true"
                    :hover="true"
                    :stickyHeader="true"
                />

                {{-- Pagination (fissa, non scrolla) --}}
                <div class="px-6 py-4 border-t border-base-300 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-base-content/60">
                        Visualizzate <span class="font-semibold text-base-content">{{ count($campaigns) }}</span> di <span class="font-semibold text-base-content">{{ $totalCampaigns }}</span> campagne
                        @if(count($campaigns) < $totalCampaigns)
                            <span class="badge badge-info badge-sm ml-2">Filtrate</span>
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

    </div>

</x-admin.wrapper>
