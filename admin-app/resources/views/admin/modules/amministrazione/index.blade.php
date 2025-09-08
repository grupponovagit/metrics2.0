<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Dashboard Amministrazione') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-base-content">
                    <x-admin.fa-icon name="calculator" class="h-8 w-8 text-accent mr-3" />
                    Dashboard Amministrazione
                </h1>
                <p class="text-base-content/70 mt-2">
                    Gestione amministrativa e finanziaria
                </p>
            </div>

            {{-- Statistiche Rapide --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Fatture in Sospeso</p>
                                <p class="text-2xl font-bold text-warning">{{ $stats['pending_invoices'] ?? 0 }}</p>
                            </div>
                            <x-admin.fa-icon name="file-invoice" class="h-8 w-8 text-warning" />
                        </div>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Ricavi Mensili</p>
                                <p class="text-2xl font-bold text-success">€{{ number_format($stats['monthly_revenue'] ?? 0) }}</p>
                            </div>
                            <x-admin.fa-icon name="chart-line" class="h-8 w-8 text-success" />
                        </div>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Spese Mensili</p>
                                <p class="text-2xl font-bold text-error">€{{ number_format($stats['expenses_month'] ?? 0) }}</p>
                            </div>
                            <x-admin.fa-icon name="credit-card" class="h-8 w-8 text-error" />
                        </div>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-base-content/70">Margine di Profitto</p>
                                <p class="text-2xl font-bold text-info">{{ $stats['profit_margin'] ?? 0 }}%</p>
                            </div>
                            <x-admin.fa-icon name="percentage" class="h-8 w-8 text-info" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sezioni Principali --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body">
                        <h2 class="card-title text-accent">
                            <x-admin.fa-icon name="chart-pie" class="h-6 w-6" />
                            PDA Media
                        </h2>
                        <p class="text-base-content/70">Analisi Performance e Dati Aggregati</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.amministrazione.pda_media') }}" class="btn btn-accent btn-sm">
                                Accedi
                                <x-admin.fa-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body">
                        <h2 class="card-title text-accent">
                            <x-admin.fa-icon name="money-bills" class="h-6 w-6" />
                            Costi Stipendi
                        </h2>
                        <p class="text-base-content/70">Gestione costi stipendiali</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.amministrazione.costi_stipendi') }}" class="btn btn-accent btn-sm">
                                Accedi
                                <x-admin.fa-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body">
                        <h2 class="card-title text-accent">
                            <x-admin.fa-icon name="receipt" class="h-6 w-6" />
                            Costi Generali
                        </h2>
                        <p class="text-base-content/70">Gestione spese operative</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.amministrazione.costi_generali') }}" class="btn btn-accent btn-sm">
                                Accedi
                                <x-admin.fa-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body">
                        <h2 class="card-title text-accent">
                            <x-admin.fa-icon name="file-invoice-dollar" class="h-6 w-6" />
                            Inviti a Fatturare
                        </h2>
                        <p class="text-base-content/70">Gestione richieste fatturazione</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.amministrazione.inviti_a_fatturare') }}" class="btn btn-accent btn-sm">
                                Accedi
                                <x-admin.fa-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body">
                        <h2 class="card-title text-accent">
                            <x-admin.fa-icon name="envelope" class="h-6 w-6" />
                            Lettere Canvass
                        </h2>
                        <p class="text-base-content/70">Gestione comunicazioni canvass</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('admin.amministrazione.lettere_canvass') }}" class="btn btn-accent btn-sm">
                                Accedi
                                <x-admin.fa-icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.wrapper>
