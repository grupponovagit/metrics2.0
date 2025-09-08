@php
    use Illuminate\Support\Facades\Auth;
@endphp

@isset($menus)
    <div x-data="sidebar" 
         x-init="init()"
         :class="collapsed ? 'sidebar-collapsed' : 'sidebar-expanded'" 
         class="sidebar-main"
         x-cloak>
        
        <!-- Header con Logo e Toggle -->
        <div class="sidebar-header">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('admin.dashboard') }}" class="logo-container group">
                    <div class="logo-icon group-hover:scale-105 transition-transform duration-200">
                        M
                    </div>
                    <div x-show="!collapsed" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 transform translate-x-4" 
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         class="flex flex-col">
                        <span class="logo-text">Metrics</span>
                        <span class="logo-version">v2.0</span>
                    </div>
                </a>
                
                <!-- Toggle Button -->
                <button @click="toggleSidebar()" 
                        class="toggle-btn hidden lg:flex"
                        :title="collapsed ? 'Espandi sidebar' : 'Comprimi sidebar'">
                    <i class="fas fa-angles-left text-sm transition-transform duration-300"
                       :class="collapsed ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Mobile Close -->
                <label for="drawer" class="toggle-btn lg:hidden">
                    <i class="fas fa-xmark text-sm"></i>
                </label>
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