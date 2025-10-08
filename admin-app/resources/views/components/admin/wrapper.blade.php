@props([
    'maxWidth' => 'full',      // 'full' | 'xl' | '2xl' | '7xl'
    'padded' => true,           // Applica padding responsivo
])

@php
    /**
     * Definizione classi per max-width
     * - full: w-full (100% larghezza disponibile)
     * - xl: max-w-xl mx-auto (centrato, max 36rem)
     * - 2xl: max-w-2xl mx-auto (centrato, max 42rem)
     * - 7xl: max-w-7xl mx-auto (centrato, max 80rem)
     */
    $maxWidthClasses = [
        'full' => 'w-full',
        'xl' => 'max-w-xl mx-auto',
        '2xl' => 'max-w-2xl mx-auto',
        '7xl' => 'max-w-7xl mx-auto',
    ];

    /**
     * Padding responsivo: mobile-first approach
     * - px-4: padding orizzontale su mobile (1rem)
     * - sm:px-6: padding medio su small screens (1.5rem)
     * - lg:px-8: padding più ampio su large screens (2rem)
     * - py-6: padding verticale consistente (1.5rem)
     */
    $paddingClasses = $padded ? 'px-4 sm:px-6 lg:px-8 py-6' : '';

    $containerClasses = trim(($maxWidthClasses[$maxWidth] ?? $maxWidthClasses['full']) . ' ' . $paddingClasses);
@endphp

<x-admin.layout>
    <x-slot name="header">
        {{ $title }}
    </x-slot>

    <div class="{{ $containerClasses }}">
        <x-admin.message />
        
        <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-base-200">
                <div class="flex flex-col">
                    <div>
                        <x-admin.breadcrumb />
                        <x-admin.form.errors />
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
