<x-admin.wrapper :containerless="true">
    <x-slot name="title">{{ __('Dashboard Home') }}</x-slot>

    {{-- Welcome Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">
            {{ $dashboardData['welcome_message'] }}
        </h1>
        <p class="text-base-content/70 mt-2 text-lg">
            Ultimo accesso: {{ $dashboardData['last_login']->format('d/m/Y H:i') }}
        </p>
        <div class="flex flex-wrap gap-2 mt-4">
            @foreach($userRoles as $role)
                <span class="badge badge-primary badge-lg">{{ $role }}</span>
            @endforeach
        </div>
    </div>

    {{-- Statistiche Rapide KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @if(isset($dashboardData['hr_stats']))
            <x-admin.stat-card
                title="Dipendenti Totali"
                :value="$dashboardData['hr_stats']['total_employees']"
                icon="users"
                color="primary"
            >
                <div class="mt-2">
                    <span class="text-sm text-success font-semibold">+{{ $dashboardData['hr_stats']['new_hires'] }}</span>
                    <span class="text-sm text-base-content/70">nuovi assunti</span>
                </div>
            </x-admin.stat-card>
        @endif

        @if(isset($dashboardData['production_stats']))
            <x-admin.stat-card
                title="Produzione Giornaliera"
                :value="number_format($dashboardData['production_stats']['daily_production'])"
                icon="industry"
                color="warning"
            >
                <div class="mt-2">
                    <span class="text-sm text-success font-semibold">{{ $dashboardData['production_stats']['efficiency'] }}%</span>
                    <span class="text-sm text-base-content/70">efficienza</span>
                </div>
            </x-admin.stat-card>
        @endif

        @if(isset($dashboardData['admin_stats']))
            <x-admin.stat-card
                title="Fatture in Sospeso"
                :value="$dashboardData['admin_stats']['pending_invoices']"
                icon="file-invoice"
                color="accent"
            >
                <div class="mt-2">
                    <span class="text-sm text-success font-semibold">€{{ number_format($dashboardData['admin_stats']['monthly_revenue']) }}</span>
                    <span class="text-sm text-base-content/70">ricavi mensili</span>
                </div>
            </x-admin.stat-card>
        @endif

        @if(isset($dashboardData['marketing_stats']))
            <x-admin.stat-card
                title="Campagne Attive"
                :value="$dashboardData['marketing_stats']['active_campaigns']"
                icon="bullhorn"
                color="info"
            >
                <div class="mt-2">
                    <span class="text-sm text-success font-semibold">{{ $dashboardData['marketing_stats']['conversion_rate'] }}%</span>
                    <span class="text-sm text-base-content/70">conversione</span>
                </div>
            </x-admin.stat-card>
        @endif
    </div>

    {{-- Moduli Accessibili --}}
    <x-admin.card tone="light" shadow="lg" title="Moduli Accessibili">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($modules as $module)
                @php
                    $iconMap = [
                        'home' => 'house',
                        'hr' => 'users', 
                        'amministrazione' => 'calculator',
                        'produzione' => 'industry',
                        'marketing' => 'bullhorn',
                        'ict' => 'desktop'
                    ];
                    $colorMap = [
                        'home' => 'primary',
                        'hr' => 'secondary',
                        'amministrazione' => 'accent',
                        'produzione' => 'warning',
                        'marketing' => 'info',
                        'ict' => 'success'
                    ];
                @endphp
                
                <a href="{{ $module['url'] }}" class="card bg-base-100 hover:bg-base-200 hover:shadow-md transition-all cursor-pointer group">
                    <div class="card-body p-4">
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-{{ $colorMap[$module['key']] }}/10 text-{{ $colorMap[$module['key']] }} rounded-full w-12 h-12 flex items-center justify-center group-hover:bg-{{ $colorMap[$module['key']] }} group-hover:text-{{ $colorMap[$module['key']] }}-content transition-colors">
                                    <x-admin.fa-icon name="{{ $iconMap[$module['key']] }}" class="h-6 w-6" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-base-content">{{ $module['name'] }}</h3>
                                <p class="text-sm text-base-content/70">
                                    {{ count($module['permissions']) }} permessi
                                </p>
                            </div>
                            <x-admin.fa-icon name="chevron-right" class="h-4 w-4 text-base-content/50 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </x-admin.card>
</x-admin.wrapper>
