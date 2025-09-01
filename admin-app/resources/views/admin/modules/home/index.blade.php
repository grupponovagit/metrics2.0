<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Dashboard Home') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-base-content">
                    {{ $dashboardData['welcome_message'] }}
                </h1>
                <p class="text-base-content/70 mt-2">
                    Ultimo accesso: {{ $dashboardData['last_login']->format('d/m/Y H:i') }}
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($userRoles as $role)
                        <span class="badge badge-primary badge-sm">{{ $role }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Statistiche Rapide --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @if(isset($dashboardData['hr_stats']))
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-base-content/70">Dipendenti Totali</p>
                                    <p class="text-2xl font-bold text-primary">{{ $dashboardData['hr_stats']['total_employees'] }}</p>
                                </div>
                                <x-admin.fa-icon name="users" class="h-8 w-8 text-primary" />
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-success">+{{ $dashboardData['hr_stats']['new_hires'] }}</span>
                                <span class="text-sm text-base-content/70">nuovi assunti</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($dashboardData['production_stats']))
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-base-content/70">Produzione Giornaliera</p>
                                    <p class="text-2xl font-bold text-secondary">{{ number_format($dashboardData['production_stats']['daily_production']) }}</p>
                                </div>
                                <x-admin.fa-icon name="industry" class="h-8 w-8 text-secondary" />
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-success">{{ $dashboardData['production_stats']['efficiency'] }}%</span>
                                <span class="text-sm text-base-content/70">efficienza</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($dashboardData['admin_stats']))
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-base-content/70">Fatture in Sospeso</p>
                                    <p class="text-2xl font-bold text-warning">{{ $dashboardData['admin_stats']['pending_invoices'] }}</p>
                                </div>
                                <x-admin.fa-icon name="file-invoice" class="h-8 w-8 text-warning" />
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-success">€{{ number_format($dashboardData['admin_stats']['monthly_revenue']) }}</span>
                                <span class="text-sm text-base-content/70">ricavi mensili</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($dashboardData['marketing_stats']))
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-base-content/70">Campagne Attive</p>
                                    <p class="text-2xl font-bold text-accent">{{ $dashboardData['marketing_stats']['active_campaigns'] }}</p>
                                </div>
                                <x-admin.fa-icon name="bullhorn" class="h-8 w-8 text-accent" />
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-success">{{ $dashboardData['marketing_stats']['conversion_rate'] }}%</span>
                                <span class="text-sm text-base-content/70">conversione</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Moduli Accessibili --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <x-admin.fa-icon name="grid" class="h-5 w-5" />
                        Moduli Accessibili
                    </h2>
                    
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
                            
                            <a href="{{ $module['url'] }}" class="card bg-base-200 hover:bg-base-300 transition-colors cursor-pointer">
                                <div class="card-body p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-{{ $colorMap[$module['key']] }} text-{{ $colorMap[$module['key']] }}-content rounded-full w-12 h-12">
                                                <x-admin.fa-icon name="{{ $iconMap[$module['key']] }}" class="h-6 w-6" />
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold">{{ $module['name'] }}</h3>
                                            <p class="text-sm text-base-content/70">
                                                {{ count($module['permissions']) }} permessi disponibili
                                            </p>
                                        </div>
                                        <x-admin.fa-icon name="chevron-right" class="h-4 w-4 text-base-content/50" />
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Notifiche Recenti --}}
           
        </div>
    </div>
</x-admin.wrapper>
