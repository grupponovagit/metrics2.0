{{--
    =====================================================
    PAGINAZIONE CUSTOM - DaisyUI + Tailwind
    =====================================================
    
    Design Decisions:
    - DaisyUI buttons (btn, btn-sm) per coerenza tema
    - Rounded-2xl per estetica moderna
    - Shadow-sm per depth sottile
    - Focus ring per accessibilità
    - Mobile-first: overflow-x-auto per scroll orizzontale
    - ARIA labels in italiano per screen readers
    - aria-current="page" per pagina attiva
    
    Props disponibili:
    - $paginator: istanza del paginator Laravel
    - $elements: array degli elementi di paginazione
    
    Classi Tailwind/DaisyUI:
    - btn: base button DaisyUI
    - btn-sm: dimensione compatta
    - btn-primary: colore primary per pagina attiva
    - btn-outline: outline per pulsanti normali
    - btn-ghost: stile ghost per prev/next
    - btn-disabled: stato disabilitato
--}}

@if ($paginator->hasPages())
    <nav 
        role="navigation" 
        aria-label="Navigazione paginazione" 
        class="flex items-center justify-between"
    >
        {{-- Mobile: Paginazione Compatta --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Pagina precedente</span>
                </span>
            @else
                <a 
                    href="{{ $paginator->previousPageUrl() }}" 
                    class="btn btn-sm btn-ghost"
                    aria-label="Vai alla pagina precedente"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Precedente
                </a>
            @endif

            <div class="flex items-center gap-2 text-sm text-base-content/70">
                <span>Pagina</span>
                <span class="font-semibold text-primary">{{ $paginator->currentPage() }}</span>
                <span>di</span>
                <span class="font-semibold">{{ $paginator->lastPage() }}</span>
            </div>

            @if ($paginator->hasMorePages())
                <a 
                    href="{{ $paginator->nextPageUrl() }}" 
                    class="btn btn-sm btn-ghost"
                    aria-label="Vai alla pagina successiva"
                >
                    Successiva
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="btn btn-sm btn-disabled">
                    <span class="sr-only">Pagina successiva</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop: Paginazione Completa --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            {{-- Info Risultati --}}
            <div>
                <p class="text-sm text-base-content/70">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-medium text-base-content">{{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-medium text-base-content">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-medium text-base-content">0</span>
                    @endif
                    di
                    <span class="font-medium text-base-content">{{ $paginator->total() }}</span>
                    risultati
                </p>
            </div>

            {{-- Pulsanti Paginazione --}}
            <div>
                {{-- Container scrollabile per molte pagine (overflow-x-auto) --}}
                <div class="overflow-x-auto">
                    <div class="join shadow-sm">
                        {{-- Pulsante Prima Pagina --}}
                        @if ($paginator->onFirstPage())
                            <button 
                                disabled 
                                class="join-item btn btn-sm btn-disabled"
                                aria-label="Prima pagina (non disponibile)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                </svg>
                                <span class="sr-only">Prima pagina</span>
                            </button>
                        @else
                            <a 
                                href="{{ $paginator->url(1) }}" 
                                class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-all"
                                aria-label="Vai alla prima pagina"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                </svg>
                                <span class="sr-only">Prima pagina</span>
                            </a>
                        @endif

                        {{-- Pulsante Pagina Precedente --}}
                        @if ($paginator->onFirstPage())
                            <button 
                                disabled 
                                class="join-item btn btn-sm btn-disabled"
                                aria-label="Pagina precedente (non disponibile)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @else
                            <a 
                                href="{{ $paginator->previousPageUrl() }}" 
                                class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-all"
                                rel="prev"
                                aria-label="Vai alla pagina precedente"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        {{-- Numeri Pagina --}}
                        @foreach ($elements as $element)
                            {{-- "Tre Punti" Separator --}}
                            @if (is_string($element))
                                <button 
                                    disabled 
                                    class="join-item btn btn-sm btn-disabled"
                                    aria-hidden="true"
                                >
                                    <span>{{ $element }}</span>
                                </button>
                            @endif

                            {{-- Array di Link --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <button 
                                            class="join-item btn btn-sm btn-primary text-primary-content font-semibold shadow-md"
                                            aria-current="page"
                                            aria-label="Pagina {{ $page }} (corrente)"
                                        >
                                            {{ $page }}
                                        </button>
                                    @else
                                        <a 
                                            href="{{ $url }}" 
                                            class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-all"
                                            aria-label="Vai alla pagina {{ $page }}"
                                        >
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Pulsante Pagina Successiva --}}
                        @if ($paginator->hasMorePages())
                            <a 
                                href="{{ $paginator->nextPageUrl() }}" 
                                class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-all"
                                rel="next"
                                aria-label="Vai alla pagina successiva"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <button 
                                disabled 
                                class="join-item btn btn-sm btn-disabled"
                                aria-label="Pagina successiva (non disponibile)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif

                        {{-- Pulsante Ultima Pagina --}}
                        @if ($paginator->hasMorePages())
                            <a 
                                href="{{ $paginator->url($paginator->lastPage()) }}" 
                                class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-all"
                                aria-label="Vai all'ultima pagina"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                </svg>
                                <span class="sr-only">Ultima pagina</span>
                            </a>
                        @else
                            <button 
                                disabled 
                                class="join-item btn btn-sm btn-disabled"
                                aria-label="Ultima pagina (non disponibile)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                </svg>
                                <span class="sr-only">Ultima pagina</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>
@endif

