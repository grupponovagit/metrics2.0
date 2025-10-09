@props([
    'title' => '',              // Titolo pagina per header
    'maxWidth' => 'full',      // 'full' | 'xl' | '2xl' | '7xl'
    'padded' => true,           // Applica padding responsivo
    'containerless' => false,   // Rimuove container card interno (per layout custom)
])

@php
    /**
     * Wrapper Component - v2.0 Full-Width Optimized
     * 
     * Definizione classi per max-width:
     * - full: w-full (100% larghezza disponibile) - DEFAULT
     * - xl: max-w-xl mx-auto (centrato, max 36rem)
     * - 2xl: max-w-2xl mx-auto (centrato, max 42rem)
     * - 7xl: max-w-7xl mx-auto (centrato, max 80rem)
     * 
     * Padding responsivo: mobile-first approach
     * - px-4: padding orizzontale su mobile (1rem)
     * - sm:px-6: padding medio su small screens (1.5rem)
     * - lg:px-10: padding più ampio su large screens (2.5rem)
     * - py-6: padding verticale consistente (1.5rem)
     * 
     * Containerless mode:
     * Quando true, rimuove il container card interno per permettere
     * layout custom full-width con dark background
     */
    $maxWidthClasses = [
        'full' => 'w-full',
        'xl' => 'max-w-xl mx-auto',
        '2xl' => 'max-w-2xl mx-auto',
        '7xl' => 'max-w-7xl mx-auto',
    ];

    $paddingClasses = $padded ? 'px-4 sm:px-6 lg:px-10 py-6' : '';

    $containerClasses = trim(($maxWidthClasses[$maxWidth] ?? $maxWidthClasses['full']) . ' ' . $paddingClasses);
@endphp

<x-admin.layout>
    <x-slot name="header">
        {{ $title }}
    </x-slot>

    <div class="{{ $containerClasses }}">
        <x-admin.message />
        
        @if($containerless)
            {{-- Mode containerless: full control del layout --}}
            <div>
                <x-admin.breadcrumb />
                <x-admin.form.errors />
                {{ $slot }}
            </div>
        @else
            {{-- Mode standard: card container con dark theme --}}
            <div class="bg-base-100 overflow-hidden shadow-lg rounded-2xl border border-base-300/50 transition-shadow hover:shadow-xl">
                <div class="p-6 lg:p-8">
                    <div class="flex flex-col">
                        <div class="mb-4">
                            <x-admin.breadcrumb />
                            <x-admin.form.errors />
                        </div>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-admin.layout>
