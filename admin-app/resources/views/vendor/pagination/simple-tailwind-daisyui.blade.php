{{--
    =====================================================
    PAGINAZIONE SEMPLICE - DaisyUI + Tailwind
    =====================================================
    
    Versione semplificata per simplePaginate()
    Solo prev/next, senza numero di pagine
--}}

@if ($paginator->hasPages())
    <nav 
        role="navigation" 
        aria-label="Navigazione paginazione semplice"
        class="flex items-center justify-between"
    >
        {{-- Mobile --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Precedente
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
                    Successiva
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div class="join shadow-sm">
                {{-- Precedente --}}
                @if ($paginator->onFirstPage())
                    <button 
                        disabled 
                        class="join-item btn btn-sm btn-disabled"
                        aria-label="Pagina precedente (non disponibile)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Precedente
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
                        Precedente
                    </a>
                @endif

                {{-- Successiva --}}
                @if ($paginator->hasMorePages())
                    <a 
                        href="{{ $paginator->nextPageUrl() }}" 
                        class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-all"
                        rel="next"
                        aria-label="Vai alla pagina successiva"
                    >
                        Successiva
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
                        Successiva
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </nav>
@endif

