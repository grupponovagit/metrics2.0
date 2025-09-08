@php
    use App\Services\ModuleAccessService;
    $accessibleModules = ModuleAccessService::getAccessibleModules();
    $currentRoute = request()->route()->getName();
@endphp

<!-- Moduli Aziendali -->
<div class="space-y-3" x-data="{ 
    activeModule: '{{ str_starts_with($currentRoute, 'admin.home') ? 'home' : (str_starts_with($currentRoute, 'admin.hr') ? 'hr' : (str_starts_with($currentRoute, 'admin.amministrazione') ? 'amministrazione' : (str_starts_with($currentRoute, 'admin.produzione') ? 'produzione' : (str_starts_with($currentRoute, 'admin.marketing') ? 'marketing' : (str_starts_with($currentRoute, 'admin.ict') ? 'ict' : ''))))) }}',
    toggleModule(moduleKey) {
        if (this.activeModule === moduleKey) {
            this.activeModule = '';
        } else {
            this.activeModule = moduleKey;
        }
    }
}">
    <div x-show="!$parent.collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex items-center text-xs font-bold text-base-content/50 uppercase tracking-wider px-3 py-2 bg-base-200/30 rounded-lg mx-2">
        <x-admin.fa-icon name="building" class="h-3 w-3 mr-2" />
        Moduli Aziendali
    </div>
    
    @if(count($accessibleModules) > 0)
        @foreach($accessibleModules as $module)
            @php
                $isActive = str_starts_with($currentRoute, 'admin.' . $module['key']);
                $iconMap = [
                    'home' => 'house',
                    'hr' => 'users',
                    'amministrazione' => 'calculator',
                    'produzione' => 'industry',
                    'marketing' => 'bullhorn',
                    'ict' => 'desktop'
                ];
                $icon = $iconMap[$module['key']] ?? 'circle';
                $colorMap = [
                    'home' => 'primary',
                    'hr' => 'secondary',
                    'amministrazione' => 'accent',
                    'produzione' => 'warning',
                    'marketing' => 'info',
                    'ict' => 'success'
                ];
                $color = $colorMap[$module['key']] ?? 'primary';
            @endphp
            
            <div class="mb-2 w-full">
                <div class="tooltip tooltip-right w-full" :data-tip="$parent.collapsed ? '{{ $module['name'] }}' : ''">
                    <div @click="toggleModule('{{ $module['key'] }}')" @dblclick="window.location.href = '{{ $module['url'] }}'"
                       data-url="{{ $module['url'] }}"
                       tabindex="0"
                       class="module-button w-full mx-2 flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-' . $color . '/90 to-' . $color . ' text-' . $color . '-content shadow-2xl shadow-' . $color . '/25 border border-' . $color . '/30' : 'bg-base-200/40 hover:bg-' . $color . '/10 hover:shadow-lg hover:shadow-' . $color . '/10 border border-transparent hover:border-' . $color . '/20' }}"
                       :class="activeModule === '{{ $module['key'] }}' && !{{ $isActive ? 'true' : 'false' }} ? 'ring-2 ring-{{ $color }}/50' : ''">
                        
                        <!-- Icona modulo -->
                        <div class="flex items-center space-x-3">
                            <div class="w-7 h-7 flex items-center justify-center {{ $isActive ? 'bg-white/20 rounded-xl' : '' }}">
                                <x-admin.fa-icon name="{{ $icon }}" class="h-5 w-5 {{ $isActive ? 'text-' . $color . '-content' : 'text-' . $color }} transition-all duration-300 group-hover:scale-110 {{ $isActive ? '' : 'group-hover:text-' . $color }}" />
                            </div>
                            
                            <!-- Nome modulo -->
                            <div x-show="!$parent.collapsed" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-6" x-transition:enter-end="opacity-100 transform translate-x-0">
                                <span class="font-bold text-sm {{ $isActive ? 'text-' . $color . '-content' : 'text-base-content group-hover:text-' . $color }}">{{ $module['name'] }}</span>
                            </div>
                        </div>
                        
                        <!-- Area destra con badge e freccia espansione -->
                        <div x-show="!$parent.collapsed" x-transition class="flex items-center space-x-2">
                    @if(count($module['permissions']) > 0)
                                <div class="flex items-center space-x-1">
                                    <div class="w-2 h-2 rounded-full {{ $isActive ? 'bg-' . $color . '-content' : 'bg-' . $color . '/60' }}"></div>
                                    <span class="text-xs font-medium {{ $isActive ? 'text-' . $color . '-content/80' : 'text-' . $color . '/80' }}">{{ count($module['permissions']) }}</span>
                                </div>
                            @endif
                            
                            <!-- Freccia espansione -->
                            <div class="w-5 h-5 flex items-center justify-center">
                                <x-admin.fa-icon name="chevron-down" 
                                               class="h-3 w-3 {{ $isActive ? 'text-' . $color . '-content' : 'text-' . $color }} chevron-rotate"
                                               ::class="activeModule === '{{ $module['key'] }}' ? 'rotate-180' : ''" />
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Sottomenu con toggle expanded --}}
                <div x-show="activeModule === '{{ $module['key'] }}' && !$parent.collapsed" 
                     x-transition:enter="transition ease-out duration-400" 
                     x-transition:enter-start="opacity-0 transform -translate-y-4" 
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mx-2 mt-3 bg-gradient-to-br from-{{ $color }}/5 to-{{ $color }}/10 border border-{{ $color }}/20 rounded-2xl p-3 space-y-2 backdrop-blur-sm shadow-lg">
                        @if(in_array('view', $module['permissions']))
                                <a href="{{ route('admin.' . $module['key'] . '.index') }}" 
                               class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                    <x-admin.fa-icon name="tachometer-alt" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                </div>
                                <span class="font-semibold">Dashboard</span>
                            </a>
                        @endif
                        
                        @switch($module['key'])
                            @case('home')
                                @if(in_array('view', $module['permissions']))
                                    <a href="{{ route('admin.home.dashboard_obiettivi') }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                        <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                            <x-admin.fa-icon name="bullseye" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                        </div>
                                        <span class="font-semibold">Dashboard Obiettivi</span>
                                    </a>
                                @endif
                                @break
                                
                            @case('hr')
                                @if(in_array('view', $module['permissions']))
                                    {{-- HR Links con nuovo design --}}
                                    @php
                                        $hrLinks = [
                                            ['route' => 'admin.hr.employees', 'icon' => 'users', 'title' => 'Dipendenti'],
                                            ['route' => 'admin.hr.cruscotto_lead_recruit', 'icon' => 'chart-line', 'title' => 'Cruscotto Lead Recruit'],
                                            ['route' => 'admin.hr.gara_ore', 'icon' => 'clock', 'title' => 'Gara Ore'],
                                            ['route' => 'admin.hr.gara_punti', 'icon' => 'trophy', 'title' => 'Gara Punti'],
                                            ['route' => 'admin.hr.formazione', 'icon' => 'graduation-cap', 'title' => 'Formazione'],
                                            ['route' => 'admin.hr.stringhe', 'icon' => 'code', 'title' => 'Stringhe'],
                                            ['route' => 'admin.hr.cruscotto_assenze', 'icon' => 'calendar-xmark', 'title' => 'Cruscotto Assenze'],
                                            ['route' => 'admin.hr.gestione_operatori', 'icon' => 'users-gear', 'title' => 'Gestione Operatori'],
                                            ['route' => 'admin.hr.pes', 'icon' => 'shield-check', 'title' => 'PES'],
                                            ['route' => 'admin.hr.tabella_per_mese', 'icon' => 'calendar-month', 'title' => 'Tabella per Mese'],
                                            ['route' => 'admin.hr.tabella_per_operatore', 'icon' => 'user-chart', 'title' => 'Tabella per Operatore'],
                                            ['route' => 'admin.hr.archivio_iban_operatori', 'icon' => 'bank', 'title' => 'Archivio IBAN'],
                                            ['route' => 'admin.hr.import_indeed', 'icon' => 'upload', 'title' => 'Import Indeed']
                                        ];
                                    @endphp
                                    
                                    @foreach($hrLinks as $link)
                                        <a href="{{ route($link['route']) }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                            <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                                <x-admin.fa-icon name="{{ $link['icon'] }}" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                            </div>
                                            <span class="font-semibold">{{ $link['title'] }}</span>
                                        </a>
                                    @endforeach
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <a href="{{ route('admin.hr.reports') }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                        <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                            <x-admin.fa-icon name="chart-bar" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                        </div>
                                        <span class="font-semibold">Report</span>
                                    </a>
                                @endif
                                @break
                                
                            @case('amministrazione')
                                @if(in_array('view', $module['permissions']))
                                    @php
                                        $adminLinks = [
                                            ['route' => 'admin.amministrazione.invoices', 'icon' => 'file-invoice', 'title' => 'Fatture'],
                                            ['route' => 'admin.amministrazione.budget', 'icon' => 'chart-pie', 'title' => 'Budget'],
                                            ['route' => 'admin.amministrazione.pda_media', 'icon' => 'chart-area', 'title' => 'PDA Media'],
                                            ['route' => 'admin.amministrazione.costi_stipendi', 'icon' => 'money-bill-wave', 'title' => 'Costi Stipendi'],
                                            ['route' => 'admin.amministrazione.costi_generali', 'icon' => 'receipt', 'title' => 'Costi Generali'],
                                            ['route' => 'admin.amministrazione.inviti_a_fatturare', 'icon' => 'envelope-open-text', 'title' => 'Inviti a Fatturare'],
                                            ['route' => 'admin.amministrazione.lettere_canvass', 'icon' => 'envelope', 'title' => 'Lettere Canvass']
                                        ];
                                    @endphp
                                    
                                    @foreach($adminLinks as $link)
                                        <a href="{{ route($link['route']) }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                            <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                                <x-admin.fa-icon name="{{ $link['icon'] }}" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                            </div>
                                            <span class="font-semibold">{{ $link['title'] }}</span>
                                        </a>
                                    @endforeach
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <a href="{{ route('admin.amministrazione.reports') }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                        <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                            <x-admin.fa-icon name="chart-line" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                        </div>
                                        <span class="font-semibold">Report</span>
                                    </a>
                                @endif
                                @break
                                
                            @case('produzione')
                                @if(in_array('view', $module['permissions']))
                                    @php
                                        $prodLinks = [
                                            ['route' => 'admin.produzione.orders', 'icon' => 'clipboard-list', 'title' => 'Ordini'],
                                            ['route' => 'admin.produzione.quality', 'icon' => 'star', 'title' => 'Qualità'],
                                            ['route' => 'admin.produzione.tabella_obiettivi', 'icon' => 'table', 'title' => 'Tabella Obiettivi'],
                                            ['route' => 'admin.produzione.cruscotto_produzione', 'icon' => 'chart-line', 'title' => 'Cruscotto Produzione'],
                                            ['route' => 'admin.produzione.cruscotto_operatore', 'icon' => 'user-chart', 'title' => 'Cruscotto Operatore'],
                                            ['route' => 'admin.produzione.cruscotto_mensile', 'icon' => 'calendar-chart', 'title' => 'Cruscotto Mensile'],
                                            ['route' => 'admin.produzione.input_manuale', 'icon' => 'keyboard', 'title' => 'Input Manuale'],
                                            ['route' => 'admin.produzione.avanzamento_mensile', 'icon' => 'chart-column', 'title' => 'Avanzamento Mensile'],
                                            ['route' => 'admin.produzione.kpi_lead_quartili', 'icon' => 'chart-simple', 'title' => 'KPI Lead Quartili'],
                                            ['route' => 'admin.produzione.controllo_stato_lead', 'icon' => 'clipboard-check', 'title' => 'Controllo Stato Lead']
                                        ];
                                    @endphp
                                    
                                    @foreach($prodLinks as $link)
                                        <a href="{{ route($link['route']) }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                            <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                                <x-admin.fa-icon name="{{ $link['icon'] }}" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                            </div>
                                            <span class="font-semibold">{{ $link['title'] }}</span>
                                        </a>
                                    @endforeach
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <a href="{{ route('admin.produzione.reports') }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                        <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                            <x-admin.fa-icon name="chart-bar" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                        </div>
                                        <span class="font-semibold">Report</span>
                                    </a>
                                @endif
                                @break
                                
                            @case('marketing')
                                @if(in_array('view', $module['permissions']))
                                    @php
                                        $marketingLinks = [
                                            ['route' => 'admin.marketing.campaigns', 'icon' => 'bullhorn', 'title' => 'Campagne'],
                                            ['route' => 'admin.marketing.leads', 'icon' => 'users', 'title' => 'Lead'],
                                            ['route' => 'admin.marketing.cruscotto_lead', 'icon' => 'chart-line', 'title' => 'Cruscotto Lead'],
                                            ['route' => 'admin.marketing.costi_invio_messaggi', 'icon' => 'envelope-open-dollar', 'title' => 'Costi Invio Messaggi'],
                                            ['route' => 'admin.marketing.controllo_sms', 'icon' => 'mobile-alt', 'title' => 'Controllo SMS']
                                        ];
                                    @endphp
                                    
                                    @foreach($marketingLinks as $link)
                                        <a href="{{ route($link['route']) }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                            <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                                <x-admin.fa-icon name="{{ $link['icon'] }}" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                            </div>
                                            <span class="font-semibold">{{ $link['title'] }}</span>
                                        </a>
                                    @endforeach
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <a href="{{ route('admin.marketing.reports') }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                        <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                            <x-admin.fa-icon name="chart-line" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                        </div>
                                        <span class="font-semibold">Report</span>
                                    </a>
                                @endif
                                @break
                                
                            @case('ict')
                                @if(in_array('view', $module['permissions']))
                                    @php
                                        $ictLinks = [
                                            ['route' => 'admin.ict.system', 'icon' => 'server', 'title' => 'Sistema'],
                                            ['route' => 'admin.ict.tickets', 'icon' => 'ticket', 'title' => 'Ticket'],
                                            ['route' => 'admin.ict.calendario', 'icon' => 'calendar', 'title' => 'Calendario'],
                                            ['route' => 'admin.ict.stato', 'icon' => 'signal', 'title' => 'Stato'],
                                            ['route' => 'admin.ict.categoria_utm_campagna', 'icon' => 'tag', 'title' => 'Categoria UTM'],
                                            ['route' => 'admin.ict.aggiorna_mandati', 'icon' => 'sync', 'title' => 'Aggiorna Mandati']
                                        ];
                                    @endphp
                                    
                                    @foreach($ictLinks as $link)
                                        <a href="{{ route($link['route']) }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                            <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                                <x-admin.fa-icon name="{{ $link['icon'] }}" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                            </div>
                                            <span class="font-semibold">{{ $link['title'] }}</span>
                                        </a>
                                    @endforeach
                                @endif
                                @if(in_array('admin', $module['permissions']))
                                    @php
                                        $adminLinks = [
                                            ['route' => 'admin.ict.users', 'icon' => 'users-cog', 'title' => 'Utenti'],
                                            ['route' => 'admin.ict.security', 'icon' => 'shield-alt', 'title' => 'Sicurezza']
                                        ];
                                    @endphp
                                    
                                    @foreach($adminLinks as $link)
                                        <a href="{{ route($link['route']) }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                            <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                                <x-admin.fa-icon name="{{ $link['icon'] }}" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                            </div>
                                            <span class="font-semibold">{{ $link['title'] }}</span>
                                        </a>
                                    @endforeach
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <a href="{{ route('admin.ict.reports') }}" class="flex items-center text-sm text-base-content/90 hover:text-{{ $color }} hover:bg-white/80 dark:hover:bg-base-300/50 px-3 py-2 rounded-xl transition-all duration-200 group border border-transparent hover:border-{{ $color }}/30 hover:shadow-md">
                                        <div class="w-6 h-6 flex items-center justify-center bg-{{ $color }}/10 rounded-lg mr-3 group-hover:bg-{{ $color }}/20 transition-colors">
                                            <x-admin.fa-icon name="chart-bar" class="h-3 w-3 text-{{ $color }}/70 group-hover:text-{{ $color }} transition-colors" />
                                        </div>
                                        <span class="font-semibold">Report</span>
                                    </a>
                                @endif
                                @break
                        @endswitch
                </div>
            </div>
        @endforeach
    @else
        <div class="px-3 py-2 text-sm text-base-content/50 text-center">
            <div x-show="!$parent.collapsed" x-transition class="flex items-center justify-center">
                <x-admin.fa-icon name="exclamation-triangle" class="h-4 w-4 mr-2" />
            Nessun modulo accessibile
            </div>
            <div x-show="$parent.collapsed" class="w-6 h-6 flex items-center justify-center mx-auto">
                <x-admin.fa-icon name="exclamation" class="h-4 w-4" />
            </div>
        </div>
    @endif
</div>
