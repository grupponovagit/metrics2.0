<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Metrics') }}</title>

        <!-- Fonts: Poppins -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/admin/app.css', 'resources/js/admin/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen overflow-x-hidden">
        <div class="bg-base-100 flex min-h-screen w-full max-w-full overflow-x-hidden">
            <!-- Sidebar sempre visibile su desktop -->
            <div class="hidden lg:block">
                @include('admin.layouts.navigation')
            </div>
            
            <!-- Mobile drawer -->
            <div class="lg:hidden drawer">
                <input id="drawer" type="checkbox" class="drawer-toggle">
                <div class="drawer-content">
                    <div class="navbar flex justify-between bg-base-100 z-10 shadow-md">
                        <div class="">
                            <label for="drawer" class="btn btn-primary drawer-button">
                                <x-ui.icon name="bars" class="h-5 w-5" />
                            </label>
                            <h1 class="text-2xl font-semibold ml-2">{{ $header }}</h1>
                        </div>
                    </div>
                </div>
                <div class="drawer-side z-40">
                    <label for="drawer" class="drawer-overlay" aria-label="Close menu"></label>
                    @include('admin.layouts.navigation')
                </div>
            </div>
            
            <!-- Main content -->
            <div class="flex-1 flex flex-col">
                <!-- Page Heading per desktop -->
                <div class="hidden lg:flex navbar justify-between bg-base-100 z-10 shadow-md">
                    <div class="">
                        <h1 class="text-2xl font-semibold ml-2">{{ $header }}</h1>
                    </div>
                    <div class="order-last">
                        <label class="swap">
                            <input class="hidden" id="theme-change" type="checkbox" />
                            <x-ui.icon 
                                name="sun" 
                                class="w-6 h-6 swap-off ACTIVECLASS"
                                data-set-theme="light"
                                data-act-class="ACTIVECLASS"
                            />
                            <x-ui.icon 
                                name="moon" 
                                class="w-6 h-6 swap-on"
                                data-set-theme="dark"
                                data-act-class="ACTIVECLASS"
                            />
                        </label>
                        <div class="dropdown dropdown-end ml-4">
                            <label tabindex="0" class="btn btn-ghost">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <x-ui.icon name="chevron-down" class="h-4 w-4" />
                                </div>
                            </label>
                            <ul tabindex="0" class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52">
                                <li class="justify-between">
                                    <a href="{{ route('admin.profile.edit') }}">
                                        <x-ui.icon name="user" class="h-4 w-4" />
                                        Profilo
                                    </a>
                                </li>
                                <div class="divider mt-0 mb-0"></div>
                                <li>
                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 bg-base-200">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
