@php
    use Illuminate\Support\Facades\Auth;
@endphp

@isset($menus)
    <div x-data="{ collapsed: false }" 
         x-init="collapsed = localStorage.getItem('sidebar-collapsed') === 'true'; $watch('collapsed', value => localStorage.setItem('sidebar-collapsed', value))"
         :class="collapsed ? 'sidebar-collapsed' : 'sidebar-expanded'" 
         class="bg-base-100 text-base-content min-h-full transition-all duration-500 ease-in-out flex flex-col shadow-2xl border-r border-base-300/50 relative z-10">
        
        <!-- Header con toggle button -->
        <div class="p-4 border-b border-base-300/50">
            <div class="flex items-center justify-between">
                <!-- Logo collassabile -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    <div class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-lg">
                        <img src="{{ asset('assets/icon.png') }}" alt="Icon" class="w-6 h-6 object-contain">
                    </div>
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="flex flex-col">
                        <span class="font-bold text-lg text-base-content">Metrics</span>
                        <span class="text-xs text-base-content/60">v2.0</span>
                    </div>
                </a>
                
                <!-- Toggle button -->
                <div class="tooltip tooltip-left hidden lg:block" :data-tip="collapsed ? 'Espandi sidebar' : 'Comprimi sidebar'">
                    <button @click="collapsed = !collapsed" 
                            class="btn btn-ghost btn-sm btn-circle hover:bg-base-200 hover:scale-110 transition-all duration-200 hover:shadow-lg">
                        <x-admin.fa-icon name="angles-left" 
                                       class="h-4 w-4 transition-all duration-300" 
                                       ::class="collapsed ? 'rotate-180' : ''" />
                    </button>
                </div>
                
                <!-- Mobile close button -->
                <label for="drawer" class="btn btn-ghost btn-sm btn-circle lg:hidden hover:bg-base-200">
                    <x-admin.fa-icon name="xmark" class="h-4 w-4" />
                </label>
            </div>
        </div>

        <!-- Menu content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="p-3">

        {{-- Moduli Aziendali --}}
        <x-admin.module-navigation />
        
        {{-- Divider --}}
        <div x-show="!collapsed" x-transition class="divider my-4 mx-2"></div>
        
        {{-- Menu Sistema - Solo per IT e Super Admin --}}
        @php
            $user = Auth::user();
            $canAccessSystem = $user->hasRole(config('admin.roles.super_admin')) || 
                              $user->hasAnyRole(['CEO', 'CFO', 'CTO', 'SVILUPPO', 'WAR_ROOM']) ||
                              \App\Services\ModuleAccessService::canAccess('ict');
        @endphp
        
        @if($canAccessSystem)
            <div class="mb-4">
                <div x-show="!collapsed" x-transition class="text-sm font-semibold text-base-content/60 uppercase tracking-wider px-3 mb-3">
                    <x-admin.fa-icon name="cog" class="h-3 w-3 mr-2 inline" />
                    Sistema
                </div>
                
                @foreach ($menus as $menu)
                    <div class="mb-1">
                        <div class="tooltip tooltip-right" :data-tip="collapsed ? '{{ $menu['name'] }}' : ''">
                            <a href="{{ $menu['link'] }}" 
                               class="flex items-center space-x-3 px-3 py-3 rounded-xl hover:bg-base-200 hover:shadow-md transition-all duration-200 group {{ request()->is(ltrim($menu['link'], '/')) ? 'bg-neutral text-neutral-content shadow-lg' : '' }}">
                                @if ($menu['icon'])
                                    <div class="w-6 h-6 flex items-center justify-center">
                                        <x-admin.base-icon path="{{ $menu['icon'] }}" />
                                    </div>
                                @endif
                                <span x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="font-medium">{{ $menu['name'] }}</span>
                            </a>
                        </div>
                        
                        @isset($menu['children'])
                            <div x-show="!collapsed" x-transition class="ml-8 mt-2 space-y-1 border-l-2 border-neutral/20 pl-4">
                                @foreach ($menu['children'] as $child)
                                    <a href="{{ $child['link'] }}"
                                       class="block px-3 py-2 text-sm rounded-lg hover:bg-base-200 hover:text-neutral transition-all duration-200 {{ request()->is(ltrim($child['link'], '/')) ? 'bg-neutral/20 text-neutral font-medium' : 'text-base-content/70' }}">
                                        <x-admin.fa-icon name="chevron-right" class="h-2 w-2 mr-2 inline opacity-50" />
                                        {{ $child['name'] }}
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
