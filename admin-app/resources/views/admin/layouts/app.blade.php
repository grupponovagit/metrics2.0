<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Metrics') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Loader Accattivante -->
        <div id="app-loader" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-base-100/80 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-6">
                <!-- Spinner principale -->
                <div class="relative">
                    <!-- Anello esterno -->
                    <div class="w-24 h-24 rounded-full border-4 border-primary/20"></div>
                    
                    <!-- Anello rotante 1 -->
                    <div class="absolute inset-0 w-24 h-24 rounded-full border-4 border-transparent border-t-primary animate-spin"></div>
                    
                    <!-- Anello rotante 2 (più lento, inverso) -->
                    <div class="absolute inset-2 w-20 h-20 rounded-full border-4 border-transparent border-b-secondary animate-spin-slow-reverse"></div>
                    
                    <!-- Centro pulsante -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full animate-pulse shadow-lg"></div>
                    </div>
                </div>
                
                <!-- Testo -->
                <div class="flex flex-col items-center gap-2">
                    <p class="text-lg font-semibold text-base-content">Caricamento in corso...</p>
                    
                    <!-- Barra di progresso animata -->
                    <div class="w-48 h-1 bg-base-300 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary via-secondary to-primary animate-progress-bar"></div>
                    </div>
                </div>
                
                <!-- Punti animati -->
                <div class="flex gap-2">
                    <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-3 h-3 bg-secondary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-3 h-3 bg-accent rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
        </div>
        
        <div class="min-h-screen bg-gray-100">
            @include('admin.layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <style>
            @keyframes spin-slow-reverse {
                from {
                    transform: rotate(360deg);
                }
                to {
                    transform: rotate(0deg);
                }
            }
            
            @keyframes progress-bar {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }
            
            .animate-spin-slow-reverse {
                animation: spin-slow-reverse 2s linear infinite;
            }
            
            .animate-progress-bar {
                animation: progress-bar 1.5s ease-in-out infinite;
            }
        </style>
        
        <script>
            // Test immediato del loader
            console.log('[Loader Test] Inizio test visibilità');
            
            // Aspetta che il DOM sia pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLoader);
            } else {
                initLoader();
            }
            
            function initLoader() {
                const loader = document.getElementById('app-loader');
                console.log('[Loader Test] Loader element:', loader);
                
                if (loader) {
                    console.log('[Loader Test] Loader trovato, classi:', loader.className);
                    
                    // Test: mostra il loader per 2 secondi al caricamento della pagina
                    // COMMENTARE DOPO IL TEST
                    setTimeout(() => {
                        loader.classList.remove('hidden');
                        loader.classList.add('flex');
                        console.log('[Loader Test] Loader mostrato per test');
                        
                        setTimeout(() => {
                            loader.classList.add('hidden');
                            loader.classList.remove('flex');
                            console.log('[Loader Test] Loader nascosto dopo test');
                        }, 3000);
                    }, 500);
                } else {
                    console.error('[Loader Test] Loader NON trovato nel DOM!');
                }
            }
        </script>
    </body>
</html>
