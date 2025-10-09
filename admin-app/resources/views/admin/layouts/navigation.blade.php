@php
    use Illuminate\Support\Facades\Auth;
@endphp

@isset($menus)
    <div x-data="{
            collapsed: false,
            activeModule: null,
            modules: ['home', 'hr', 'amministrazione', 'produzione', 'marketing', 'ict'],
            
            init() {
                // Apre automaticamente il modulo corretto basandosi sull'URL corrente
                const currentPath = window.location.pathname;
                const activeModule = this.modules.find(module => currentPath.includes('/' + module + '/'));
                this.activeModule = activeModule || null;
            },
            
            toggleSidebar() {
                this.collapsed = !this.collapsed;
            },
            
            toggleModule(moduleKey) {
                if (this.collapsed) {
                    this.collapsed = false;
                    setTimeout(() => {
                        // Solo un modulo aperto alla volta - chiude gli altri
                        this.activeModule = this.activeModule === moduleKey ? null : moduleKey;
                    }, 100);
                } else {
                    // Solo un modulo aperto alla volta - chiude gli altri
                    this.activeModule = this.activeModule === moduleKey ? null : moduleKey;
                }
            },
            
            isModuleExpanded(moduleKey) {
                return this.activeModule === moduleKey;
            },
            
            getTooltipText(text) {
                return this.collapsed ? text : '';
            }
         }"
         :class="collapsed ? 'sidebar-collapsed' : 'sidebar-expanded'" 
         class="sidebar-main"
         x-cloak>
        
        <!-- Header con Logo e Toggle -->
        <div class="sidebar-header">
            <!-- Logo -->
            <div class="flex items-center justify-center mb-3">
                <a href="{{ route('admin.dashboard') }}" class="logo-container group">
                    <div class="w-20 h-16 rounded-xl overflow-hidden bg-white flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('assets/logo.png') }}" alt="Metrics Logo" class="w-16 h-12 object-contain">
                    </div>
                </a>
            </div>
            
            <!-- Toggle Button - SEMPRE SOTTO IL LOGO -->
            <div class="flex justify-center">
                <button @click="toggleSidebar()" 
                        class="btn btn-primary btn-sm hidden lg:flex w-full max-w-[200px] gap-2 shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200"
                        :class="collapsed ? 'btn-circle !w-12 !max-w-none' : ''"
                        :title="collapsed ? 'Espandi sidebar' : 'Comprimi sidebar'">
                    <i class="fas fa-angles-left text-base transition-transform duration-300"
                       :class="collapsed ? 'rotate-180' : ''"></i>
                    <span x-show="!collapsed" 
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          class="font-medium">
                        Comprimi
                    </span>
                </button>
            </div>
        </div>

        <!-- Menu Content -->
        <div class="sidebar-content">
            <div class="menu-section">
            
            <!-- Moduli Aziendali -->
            <div x-show="!collapsed" x-transition class="section-title">
                <i class="fas fa-building mr-2"></i>
                Moduli Aziendali
            </div>
            
            <!-- Componente Moduli -->
            <x-admin.module-navigation />
            
            <!-- Menu Sistema -->
            @php
                $user = Auth::user();
                $canAccessSystem = $user->hasRole(config('admin.roles.super_admin')) || 
                                  $user->hasAnyRole(['CEO', 'CFO', 'CTO', 'SVILUPPO', 'WAR_ROOM']) ||
                                  \App\Services\ModuleAccessService::canAccess('ict');
            @endphp
            
            @if($canAccessSystem)
                <div class="system-section mt-6">
                    <div x-show="!collapsed" x-transition class="system-title">
                        <i class="fas fa-cog mr-2"></i>
                        Sistema
                    </div>
                    
                    @foreach ($menus as $menu)
                        <div class="relative group">
                            <a href="{{ $menu['link'] }}" 
                               class="system-link {{ request()->is(ltrim($menu['link'], '/')) ? 'system-link-active' : '' }}">
                                @if ($menu['icon'])
                                    <div class="system-icon">
                                        <x-admin.base-icon path="{{ $menu['icon'] }}" />
                                    </div>
                                @endif
                                <span x-show="!collapsed" 
                                      x-transition:enter="transition ease-out duration-200" 
                                      x-transition:enter-start="opacity-0 transform translate-x-4" 
                                      x-transition:enter-end="opacity-100 transform translate-x-0"
                                      class="font-medium text-sm">{{ $menu['name'] }}</span>
                            </a>
                            
                            <!-- Tooltip per sidebar collassata -->
                            <div x-show="collapsed" 
                                 class="tooltip-custom group-hover:tooltip-show">
                                {{ $menu['name'] }}
                            </div>
                            
                            @isset($menu['children'])
                                <div x-show="!collapsed" x-transition class="ml-4 mt-2 space-y-1">
                                    @foreach ($menu['children'] as $child)
                                        <a href="{{ $child['link'] }}"
                                           class="system-link {{ request()->is(ltrim($child['link'], '/')) ? 'system-link-active' : '' }}">
                                            <div class="system-icon">
                                                <i class="fas fa-chevron-right text-xs"></i>
                                            </div>
                                            <span class="font-medium text-sm">{{ $child['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endisset
                        </div>
                    @endforeach
                </div>
            @endif
            
            </div>
        </div>
    </div>
@endisset