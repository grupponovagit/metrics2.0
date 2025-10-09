<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Metrics 2.0 - Sistema Gestionale</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/icon.png') }}" type="image/png">

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
    <div class="fixed inset-0 -z-10 overflow-hidden bg-gradient-to-br from-primary/10 via-secondary/10 to-accent/10">
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-primary/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -left-40 w-[600px] h-[600px] bg-secondary/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-40 right-1/3 w-[600px] h-[600px] bg-accent/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
                    </div>

    <div class="min-h-screen flex items-center justify-center px-6">
        
        <div class="text-center max-w-4xl">
            
            {{-- Logo --}}
            <div class="mb-12 inline-flex items-center gap-4 px-6 py-3 bg-base-100/80 backdrop-blur-xl rounded-full shadow-2xl border border-base-300/50">
                <img src="{{ asset('assets/logo-dark.png') }}" alt="Metrics" class="h-12 w-auto">
               
            </div>

            {{-- Hero Title --}}
            <h1 class="text-7xl md:text-8xl font-black leading-tight mb-8">
                Gestisci la tua
                <span class="relative inline-block">
                    <span class="bg-gradient-to-r from-primary via-secondary to-primary bg-clip-text text-transparent animate-gradient">
                        Azienda
                    </span>
                    <span class="absolute -bottom-2 left-0 right-0 h-2 bg-gradient-to-r from-primary/50 to-secondary/50 rounded-full blur-sm"></span>
                </span>
                <br>
                con Intelligenza
            </h1>

            {{-- Subtitle --}}
            <p class="text-2xl text-base-content/70 mb-12 max-w-2xl mx-auto leading-relaxed">
                Sistema gestionale completo per HR, Amministrazione, Produzione, Marketing e ICT
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg h-16 px-10 text-xl gap-4 shadow-2xl hover:shadow-primary/50 hover:scale-105 transition-all">
                        <x-ui.icon name="home" size="lg" />
                        Vai alla Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg h-16 px-10 text-xl gap-4 shadow-2xl hover:shadow-primary/50 hover:scale-105 transition-all group">
                        <x-ui.icon name="arrow-right" size="lg" class="group-hover:translate-x-1 transition-transform" />
                        Accedi al Sistema
                    </a>
                @endauth
            </div>
            {{-- Status Indicator --}}
            <div class="mt-16 inline-flex items-center gap-3 px-6 py-3 bg-success/10 backdrop-blur-xl rounded-full border border-success/20">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-success"></span>
                </span>
                <span class="text-success font-semibold">Sistema Operativo</span>
            </div>

        </div>

    </div>

    <style>
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }
    </style>

</body>
</html>
