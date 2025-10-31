@php
    use App\Services\ModuleAccessService;
    $accessibleModules = ModuleAccessService::getAccessibleModules();
    $currentRoute = request()->route()->getName();
@endphp

<div class="space-y-3">
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
            @endphp
            
            <div class="module-item">
                <!-- Bottone Principale Modulo -->
                <div class="relative group">
                    <button @click="toggleModule('{{ $module['key'] }}')" 
                           class="module-btn {{ $isActive ? 'module-btn-active' : '' }}"
                           :class="isModuleExpanded('{{ $module['key'] }}') && !{{ $isActive ? 'true' : 'false' }} ? 'module-btn-active' : ''"
                           :title="getTooltipText('{{ $module['name'] }}')">
                        
                        <!-- Area Sinistra: Icona + Nome -->
                        <div class="flex items-center space-x-4">
                            <!-- Icona -->
                            <div class="module-icon-container">
                                <i class="fas fa-{{ $icon }} text-lg"></i>
                            </div>
                            
                            <!-- Nome Modulo -->
                            <div x-show="!collapsed" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0 transform translate-x-4" 
                                 x-transition:enter-end="opacity-100 transform translate-x-0">
                                <span class="module-title">{{ $module['name'] }}</span>
                                @php
                                    // Conta i sottomoduli effettivi per questo modulo
                                    $submoduleCount = 0;
                                    switch($module['key']) {
                                        case 'hr':
                                            $submoduleCount = 13 + (in_array('reports', $module['permissions']) ? 1 : 0);
                                            break;
                                        case 'amministrazione':
                                            $submoduleCount = 7 + (in_array('reports', $module['permissions']) ? 1 : 0);
                                            break;
                                        case 'produzione':
                                            $submoduleCount = 9 + (in_array('reports', $module['permissions']) ? 1 : 0);
                                            break;
                                        case 'marketing':
                                            $submoduleCount = 5 + (in_array('reports', $module['permissions']) ? 1 : 0);
                                            break;
                                        case 'ict':
                                            $viewLinks = 8; // Sistema, Ticket, Calendario, Esiti Committenti, Esiti Vendita, Stato, Categoria UTM, Aggiorna Mandati
                                            $adminLinks = in_array('admin', $module['permissions']) ? 2 : 0; // Utenti, Sicurezza
                                            $reportLinks = in_array('reports', $module['permissions']) ? 1 : 0;
                                            $submoduleCount = $viewLinks + $adminLinks + $reportLinks;
                                            break;
                                        default:
                                            $submoduleCount = count($module['permissions']);
                                    }
                                @endphp
                                @if($submoduleCount > 0)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $submoduleCount }} {{ $submoduleCount == 1 ? 'funzione' : 'funzioni' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Chevron -->
                        <div x-show="!collapsed" x-transition>
                            <i class="fas fa-chevron-down chevron-icon"
                               :class="isModuleExpanded('{{ $module['key'] }}') ? 'chevron-rotated' : ''"></i>
                        </div>
                    </button>
                    
                    <!-- Tooltip per sidebar collassata -->
                    <div x-show="collapsed" 
                         class="tooltip-custom group-hover:tooltip-show">
                        {{ $module['name'] }}
                    </div>
                </div>
                
                <!-- Submenu Dropdown -->
                <div x-show="isModuleExpanded('{{ $module['key'] }}') && !collapsed" 
                     x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0 transform -translate-y-2 scale-95" 
                     x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 transform -translate-y-2 scale-95"
                     class="submenu-container slide-down">
                    
                    @if(in_array('view', $module['permissions']))
                        <!-- Dashboard Link -->
                        <div class="submenu-item">
                            <a href="{{ route('admin.' . $module['key'] . '.index') }}" 
                               class="submenu-link {{ request()->routeIs('admin.' . $module['key'] . '.index') ? 'submenu-link-active' : '' }}">
                                <div class="submenu-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <span class="submenu-text">Dashboard</span>
                            </a>
                        </div>
                    @endif
                    
                    @switch($module['key'])
                        @case('home')
                            @if(in_array('view', $module['permissions']))
                                <div class="submenu-item">
                                    <a href="{{ route('admin.home.dashboard_obiettivi') }}" 
                                       class="submenu-link {{ request()->routeIs('admin.home.dashboard_obiettivi') ? 'submenu-link-active' : '' }}">
                                        <div class="submenu-icon">
                                            <i class="fas fa-bullseye"></i>
                                        </div>
                                        <span class="submenu-text">Dashboard Obiettivi</span>
                                    </a>
                                </div>
                            @endif
                            @break
                            
                        @case('hr')
                            @if(in_array('view', $module['permissions']))
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
                                        ['route' => 'admin.hr.pes', 'icon' => 'file-shield', 'title' => 'PES'],
                                        ['route' => 'admin.hr.tabella_per_mese', 'icon' => 'table-columns', 'title' => 'Tabella per Mese'],
                                        ['route' => 'admin.hr.tabella_per_operatore', 'icon' => 'table-list', 'title' => 'Tabella per Operatore'],
                                        ['route' => 'admin.hr.archivio_iban_operatori', 'icon' => 'bank', 'title' => 'Archivio IBAN'],
                                        ['route' => 'admin.hr.import_indeed', 'icon' => 'upload', 'title' => 'Import Indeed']
                                    ];
                                @endphp
                                
                                @foreach($hrLinks as $link)
                                    <div class="submenu-item">
                                        <a href="{{ route($link['route']) }}" 
                                           class="submenu-link {{ request()->routeIs($link['route']) ? 'submenu-link-active' : '' }}">
                                            <div class="submenu-icon">
                                                <i class="fas fa-{{ $link['icon'] }}"></i>
                                            </div>
                                            <span class="submenu-text">{{ $link['title'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            @if(in_array('reports', $module['permissions']))
                                <div class="submenu-item">
                                    <a href="{{ route('admin.hr.reports') }}" 
                                       class="submenu-link {{ request()->routeIs('admin.hr.reports') ? 'submenu-link-active' : '' }}">
                                        <div class="submenu-icon">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <span class="submenu-text">Report</span>
                                    </a>
                                </div>
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
                                    <div class="submenu-item">
                                        <a href="{{ route($link['route']) }}" 
                                           class="submenu-link {{ request()->routeIs($link['route']) ? 'submenu-link-active' : '' }}">
                                            <div class="submenu-icon">
                                                <i class="fas fa-{{ $link['icon'] }}"></i>
                                            </div>
                                            <span class="submenu-text">{{ $link['title'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            @if(in_array('reports', $module['permissions']))
                                <div class="submenu-item">
                                    <a href="{{ route('admin.amministrazione.reports') }}" 
                                       class="submenu-link {{ request()->routeIs('admin.amministrazione.reports') ? 'submenu-link-active' : '' }}">
                                        <div class="submenu-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <span class="submenu-text">Report</span>
                                    </a>
                                </div>
                            @endif
                            @break
                            
                        @case('produzione')
                            @if(in_array('view', $module['permissions']))
                                @php
                                    $prodLinks = [
                                        ['route' => 'admin.produzione.tabella_obiettivi', 'icon' => 'table', 'title' => 'Tabella Obiettivi'],
                                        ['route' => 'admin.produzione.cruscotto_produzione', 'icon' => 'chart-line', 'title' => 'Cruscotto Produzione'],
                                        ['route' => 'admin.produzione.cruscotto_operatore', 'icon' => 'user-gear', 'title' => 'Cruscotto Operatore'],
                                        ['route' => 'admin.produzione.input_manuale', 'icon' => 'keyboard', 'title' => 'Input Manuale'],
                                        ['route' => 'admin.produzione.avanzamento_mensile', 'icon' => 'chart-column', 'title' => 'Avanzamento Mensile'],
                                        ['route' => 'admin.produzione.kpi_lead_quartili', 'icon' => 'chart-simple', 'title' => 'KPI Lead Quartili'],
                                        ['route' => 'admin.produzione.controllo_stato_lead', 'icon' => 'clipboard-check', 'title' => 'Controllo Stato Lead'],
                                        ['route' => 'admin.produzione.kpi_target', 'icon' => 'bullseye', 'title' => 'KPI Target Mensili']
                                    ];
                                @endphp
                                
                                @foreach($prodLinks as $link)
                                    <div class="submenu-item">
                                        <a href="{{ route($link['route']) }}" 
                                           class="submenu-link {{ request()->routeIs($link['route']) ? 'submenu-link-active' : '' }}">
                                            <div class="submenu-icon">
                                                <i class="fas fa-{{ $link['icon'] }}"></i>
                                            </div>
                                            <span class="submenu-text">{{ $link['title'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            @if(in_array('reports', $module['permissions']))
                                <div class="submenu-item">
                                    <a href="{{ route('admin.produzione.reports') }}" 
                                       class="submenu-link {{ request()->routeIs('admin.produzione.reports') ? 'submenu-link-active' : '' }}">
                                        <div class="submenu-icon">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <span class="submenu-text">Report</span>
                                    </a>
                                </div>
                            @endif
                            @break
                            
                        @case('marketing')
                            @if(in_array('view', $module['permissions']))
                                @php
                                    $marketingLinks = [
                                        ['route' => 'admin.marketing.campaigns', 'icon' => 'bullhorn', 'title' => 'Campagne'],
                                        ['route' => 'admin.marketing.leads', 'icon' => 'users', 'title' => 'Lead'],
                                        ['route' => 'admin.marketing.cruscotto_lead', 'icon' => 'chart-line', 'title' => 'Cruscotto Lead'],
                                        ['route' => 'admin.marketing.prospetto_mensile.index', 'icon' => 'calendar-alt', 'title' => 'Prospetto Mensile'],
                                        ['route' => 'admin.marketing.costi_invio_messaggi', 'icon' => 'paper-plane', 'title' => 'Costi Invio Messaggi'],
                                        ['route' => 'admin.marketing.controllo_sms', 'icon' => 'mobile-alt', 'title' => 'Controllo SMS']
                                    ];
                                @endphp
                                
                                @foreach($marketingLinks as $link)
                                    <div class="submenu-item">
                                        @php
                                            // Per il prospetto mensile, evidenzia anche le sotto-rotte
                                            $isLinkActive = request()->routeIs($link['route']);
                                            if ($link['route'] === 'admin.marketing.prospetto_mensile.index') {
                                                $isLinkActive = request()->routeIs('admin.marketing.prospetto_mensile.*');
                                            }
                                        @endphp
                                        <a href="{{ route($link['route']) }}" 
                                           class="submenu-link {{ $isLinkActive ? 'submenu-link-active' : '' }}">
                                            <div class="submenu-icon">
                                                <i class="fas fa-{{ $link['icon'] }}"></i>
                                            </div>
                                            <span class="submenu-text">{{ $link['title'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            @if(in_array('reports', $module['permissions']))
                                <div class="submenu-item">
                                    <a href="{{ route('admin.marketing.reports') }}" 
                                       class="submenu-link {{ request()->routeIs('admin.marketing.reports') ? 'submenu-link-active' : '' }}">
                                        <div class="submenu-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <span class="submenu-text">Report</span>
                                    </a>
                                </div>
                            @endif
                            @break
                            
                        @case('ict')
                            @if(in_array('view', $module['permissions']))
                                @php
                                    $ictLinks = [
                                        ['route' => 'admin.ict.system', 'icon' => 'server', 'title' => 'Sistema'],
                                        ['route' => 'admin.ict.tickets', 'icon' => 'ticket', 'title' => 'Ticket'],
                                        ['route' => 'admin.ict.calendario', 'icon' => 'calendar', 'title' => 'Calendario'],
                                        ['route' => 'admin.ict.esiti_conversione.index', 'icon' => 'arrows-left-right', 'title' => 'Esiti Committenti'],
                                        ['route' => 'admin.ict.esiti_vendita_conversione.index', 'icon' => 'shopping-cart', 'title' => 'Esiti Vendita'],
                                        ['route' => 'admin.ict.stato', 'icon' => 'signal', 'title' => 'Stato'],
                                        ['route' => 'admin.ict.categoria_utm_campagna', 'icon' => 'tag', 'title' => 'Categoria UTM'],
                                        ['route' => 'admin.ict.aggiorna_mandati', 'icon' => 'sync', 'title' => 'Aggiorna Mandati']
                                    ];
                                @endphp
                                
                                @foreach($ictLinks as $link)
                                    <div class="submenu-item">
                                        <a href="{{ route($link['route']) }}" 
                                           class="submenu-link {{ request()->routeIs($link['route']) ? 'submenu-link-active' : '' }}">
                                            <div class="submenu-icon">
                                                <i class="fas fa-{{ $link['icon'] }}"></i>
                                            </div>
                                            <span class="submenu-text">{{ $link['title'] }}</span>
                                        </a>
                                    </div>
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
                                    <div class="submenu-item">
                                        <a href="{{ route($link['route']) }}" 
                                           class="submenu-link {{ request()->routeIs($link['route']) ? 'submenu-link-active' : '' }}">
                                            <div class="submenu-icon">
                                                <i class="fas fa-{{ $link['icon'] }}"></i>
                                            </div>
                                            <span class="submenu-text">{{ $link['title'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            @if(in_array('reports', $module['permissions']))
                                <div class="submenu-item">
                                    <a href="{{ route('admin.ict.reports') }}" 
                                       class="submenu-link {{ request()->routeIs('admin.ict.reports') ? 'submenu-link-active' : '' }}">
                                        <div class="submenu-icon">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <span class="submenu-text">Report</span>
                                    </a>
                                </div>
                            @endif
                            @break
                    @endswitch
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-8">
            <div x-show="!collapsed" x-transition class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p class="text-sm">Nessun modulo accessibile</p>
            </div>
            <div x-show="collapsed" class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-exclamation text-lg"></i>
            </div>
        </div>
    @endif
</div>