<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-base-200 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Welcome Header --}}
            <div class="mb-8">
                <div class="bg-gradient-to-r from-primary to-secondary p-8 rounded-3xl shadow-xl text-white">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h1 class="text-4xl font-bold mb-2">
                                Benvenuto, {{ Auth::user()->name }}! 👋
                            </h1>
                            <p class="text-white/90 text-lg">
                                Ecco un riepilogo della tua attività
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.home.index') }}" class="btn btn-neutral">
                                <x-ui.icon name="home" size="md" />
                                Admin Panel
                            </a>
                            <a href="{{ route('profile.edit') }}" class="btn btn-neutral">
                                <x-ui.icon name="user" size="md" />
                                Profilo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Stat 1 --}}
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 border border-base-300/50">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70 font-medium mb-1">Accessi Oggi</p>
                                <p class="text-3xl font-bold text-primary">12</p>
                                <div class="flex items-center gap-1 mt-2">
                                    <x-ui.icon name="trending-up" size="sm" class="text-success" />
                                    <span class="text-sm font-semibold text-success">+8%</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                                <x-ui.icon name="eye" size="xl" aria-hidden="true" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stat 2 --}}
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 border border-base-300/50">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70 font-medium mb-1">Task Completati</p>
                                <p class="text-3xl font-bold text-success">24</p>
                                <div class="flex items-center gap-1 mt-2">
                                    <x-ui.icon name="check" size="sm" class="text-success" />
                                    <span class="text-sm text-base-content/60">questa settimana</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 bg-success/10 rounded-2xl flex items-center justify-center text-success">
                                <x-ui.icon name="check-circle" size="xl" aria-hidden="true" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stat 3 --}}
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 border border-base-300/50">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70 font-medium mb-1">Notifiche</p>
                                <p class="text-3xl font-bold text-info">5</p>
                                <div class="flex items-center gap-1 mt-2">
                                    <i class="fas fa-envelope text-sm text-info"></i>
                                    <span class="text-sm text-base-content/60">non lette</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 bg-info/10 rounded-2xl flex items-center justify-center text-info">
                                <i class="fas fa-envelope text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stat 4 --}}
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 border border-base-300/50">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70 font-medium mb-1">Tempo Attivo</p>
                                <p class="text-3xl font-bold text-warning">2.5h</p>
                                <div class="flex items-center gap-1 mt-2">
                                    <x-ui.icon name="clock" size="sm" class="text-warning" />
                                    <span class="text-sm text-base-content/60">oggi</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 bg-warning/10 rounded-2xl flex items-center justify-center text-warning">
                                <x-ui.icon name="clock" size="xl" aria-hidden="true" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Attività Recenti --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-lg border border-base-300/50">
                        <div class="card-body p-6">
                            <h3 class="text-xl font-bold text-base-content mb-4 flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                    <x-ui.icon name="clock" size="lg" />
                                </div>
                                Attività Recenti
                            </h3>
                            
                            <div class="space-y-4">
                                @foreach([
                                    ['icon' => 'user-plus', 'color' => 'success', 'title' => 'Nuovo accesso al sistema', 'time' => '5 minuti fa'],
                                    ['icon' => 'document', 'color' => 'info', 'title' => 'Report mensile generato', 'time' => '1 ora fa'],
                                    ['icon' => 'check-circle', 'color' => 'success', 'title' => 'Task completato con successo', 'time' => '2 ore fa'],
                                    ['icon' => 'bell', 'color' => 'warning', 'title' => 'Nuova notifica ricevuta', 'time' => '3 ore fa'],
                                ] as $activity)
                                    <div class="flex items-start gap-4 p-4 bg-base-200 rounded-xl hover:bg-base-300 transition-colors">
                                        <div class="w-10 h-10 bg-{{ $activity['color'] }}/10 rounded-full flex items-center justify-center text-{{ $activity['color'] }} flex-shrink-0">
                                            <x-ui.icon :name="$activity['icon']" size="md" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-base-content">{{ $activity['title'] }}</p>
                                            <p class="text-sm text-base-content/60 mt-1">{{ $activity['time'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                <a href="#" class="btn btn-outline btn-primary btn-block">
                                    <x-ui.icon name="clock" size="md" />
                                    Vedi Tutte le Attività
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-lg border border-base-300/50">
                        <div class="card-body p-6">
                            <h3 class="text-xl font-bold text-base-content mb-4 flex items-center gap-3">
                                <div class="w-10 h-10 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary">
                                    <x-ui.icon name="target" size="lg" />
                                </div>
                                Azioni Rapide
                            </h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('admin.home.index') }}" class="btn btn-primary btn-block justify-start">
                                    <x-ui.icon name="home" size="md" />
                                    Dashboard Admin
                                </a>
                                
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline btn-secondary btn-block justify-start">
                                    <x-ui.icon name="user" size="md" />
                                    Modifica Profilo
                                </a>
                                
                                <a href="#" class="btn btn-outline btn-info btn-block justify-start">
                                    <x-ui.icon name="bell" size="md" />
                                    Notifiche (5)
                                </a>
                                
                                <a href="#" class="btn btn-outline btn-success btn-block justify-start">
                                    <x-ui.icon name="document" size="md" />
                                    Report
                                </a>

                                <div class="divider my-4"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-error btn-block justify-start">
                                        <x-ui.icon name="arrow-right" size="md" />
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Info Card --}}
                    <div class="card bg-gradient-to-br from-primary/10 to-secondary/10 shadow-lg border border-primary/20 mt-6">
                        <div class="card-body p-6">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary flex-shrink-0">
                                    <x-ui.icon name="info" size="lg" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-base-content mb-2">Suggerimento</h4>
                                    <p class="text-sm text-base-content/70">
                                        Completa il tuo profilo per accedere a tutte le funzionalità del sistema.
                                    </p>
                                    <a href="{{ route('profile.edit') }}" class="text-sm text-primary font-semibold mt-2 inline-flex items-center gap-1 hover:gap-2 transition-all">
                                        Completa ora
                                        <x-ui.icon name="arrow-right" size="sm" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="card bg-base-100 shadow-lg border border-base-300/50">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center text-success">
                                <x-ui.icon name="check-circle" size="lg" />
                            </div>
                            <div>
                                <p class="font-bold text-base-content">Sistema Operativo</p>
                                <p class="text-sm text-base-content/60">Tutti i servizi funzionano correttamente</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 text-sm text-base-content/60">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
                                <span>Online</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.icon name="clock" size="sm" />
                                <span>Ultimo aggiornamento: {{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
