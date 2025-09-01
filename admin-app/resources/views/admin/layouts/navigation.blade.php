@php
    use Illuminate\Support\Facades\Auth;
@endphp

@isset($menus)
    <ul class="menu pt-2 w-80 bg-base-100 text-base-content min-h-full">
        <label for="drawer" class="btn btn-ghost bg-base-300 btn-circle z-50 top-0 right-0 mt-2 mr-2 absolute lg:hidden">
            <x-admin.fa-icon name="xmark" class="h-5 w-5" />
        </label>
        <li class="mb-2 font-semibold text-xl">
            <a href="{{ route('admin.dashboard') }}">
                <img id="nav-logo-light-theme" src="{{ asset('assets/logo-dark.png') }}" alt="Logo"
                    class="block h-18c w-auto d-flex flex-col">
                <img id="nav-logo-dark-theme" src="{{ asset('assets/logo-light.png') }}" alt="Logo"
                    class="hidden h-18c w-auto d-flex flex-col">
            </a>
        </li>
        <div>
            <p class="text-center mb-4 font-bold text-md">
                Metrics 2.0
            </p>
        </div>

        {{-- Moduli Aziendali --}}
        <x-admin.module-navigation />
        
        {{-- Divider --}}
        <div class="divider my-2"></div>
        
        {{-- Menu Sistema - Solo per IT e Super Admin --}}
        @php
            $user = Auth::user();
            $canAccessSystem = $user->hasRole(config('admin.roles.super_admin')) || 
                              $user->hasAnyRole(['CEO', 'CFO', 'CTO', 'SVILUPPO', 'WAR_ROOM']) ||
                              \App\Services\ModuleAccessService::canAccess('ict');
        @endphp
        
        @if($canAccessSystem)
            <div class="mb-4">
                <p class="text-sm font-semibold text-base-content/70 uppercase tracking-wide px-4 mb-2">
                    Sistema
                </p>
                @foreach ($menus as $menu)
                    <li>
                        <a href="{{ $menu['link'] }}" class="{{ request()->is(ltrim($menu['link'], '/')) ? 'active' : '' }}">
                            @if ($menu['icon'])
                                <x-admin.base-icon path="{{ $menu['icon'] }}" />
                            @endif
                            {{ $menu['name'] }}
                        </a>
                        @isset($menu['children'])
                            <ul class="bg-base-100 p-2">
                                @foreach ($menu['children'] as $child)
                                    <li><a href="{{ $child['link'] }}"
                                            class="{{ request()->is(ltrim($child['link'], '/')) ? 'active' : '' }}">{{ $child['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endisset
                    </li>
                @endforeach
            </div>
        @endif
    </ul>
@endisset
