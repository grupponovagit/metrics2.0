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
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('assets/icon.png') }}" type="image/png">

        <!-- Scripts -->
        @vite(['resources/css/admin/app.css', 'resources/js/admin/app.js'])
    </head>
    <body class="font-[Poppins] antialiased">
        
        {{-- Theme Toggle - Floating Top Right --}}
        <div class="fixed top-6 right-6 z-50">
            <div class="btn btn-circle btn-ghost bg-base-100/80 backdrop-blur-xl shadow-lg hover:shadow-xl border border-base-300/50 transition-all">
                <x-ui.theme-toggle size="md" />
            </div>
        </div>
        
        {{-- Animated Background --}}
        <div class="fixed inset-0 -z-10 overflow-hidden bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/10">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/2 -left-40 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-accent/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="min-h-screen flex flex-col justify-center items-center px-6 py-12">
            <div>
                <a href="/" class="block mb-8">
                    <img src="{{ asset('assets/logo-dark.png') }}" alt="Metrics" class="w-16 h-16 mx-auto">
                </a>
            </div>

            <div class="w-full max-w-md">
                <div class="card bg-base-100/80 backdrop-blur-xl shadow-2xl border border-base-300/50">
                    <div class="card-body p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-8 text-base-content/60 text-sm">
                <p>Metrics 2.0 © {{ date('Y') }} - Sistema Gestionale</p>
            </div>
        </div>
    </body>
</html>
