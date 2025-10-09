@props([
    'title' => null,
    'tone' => 'light',           // 'light' | 'dark' | 'darker'
    'shadow' => 'md',            // 'sm' | 'md' | 'lg' | 'xl'
    'rounded' => 'xl',           // 'lg' | 'xl' | '2xl'
    'padding' => 'normal',       // 'tight' | 'normal' | 'loose'
    'hoverable' => false,        // Aggiunge hover effect
])

@php
    /**
     * Card Component - Design System
     * 
     * Toni di colore (tone):
     * - light: bg-base-100 (bianco in light mode, gray-800 in dark)
     * - dark: bg-base-200 (gray-100 in light, gray-900 in dark)
     * - darker: bg-base-300 (gray-200 in light, slate-900 in dark)
     * 
     * Shadow options:
     * - sm: shadow-sm
     * - md: shadow-md
     * - lg: shadow-lg
     * - xl: shadow-xl
     * 
     * Rounded options:
     * - lg: rounded-lg
     * - xl: rounded-xl
     * - 2xl: rounded-2xl
     * 
     * Padding options:
     * - tight: p-4
     * - normal: p-6
     * - loose: p-8
     */
    
    $toneClasses = [
        'light' => 'bg-base-100',
        'dark' => 'bg-base-200',
        'darker' => 'bg-base-300',
    ];
    
    $shadowClasses = [
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
    ];
    
    $roundedClasses = [
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
    ];
    
    $paddingClasses = [
        'none' => '',
        'tight' => 'p-4',
        'normal' => 'p-6',
        'loose' => 'p-8',
    ];
    
    $hoverClass = $hoverable ? 'hover:shadow-xl hover:-translate-y-1 cursor-pointer' : '';
    
    $cardClasses = trim(
        ($toneClasses[$tone] ?? $toneClasses['light']) . ' ' .
        ($shadowClasses[$shadow] ?? $shadowClasses['md']) . ' ' .
        ($roundedClasses[$rounded] ?? $roundedClasses['xl']) . ' ' .
        ($paddingClasses[$padding] ?? $paddingClasses['normal']) . ' ' .
        $hoverClass . ' ' .
        'transition-all duration-300 border border-base-300/50'
    );
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($title)
        <div class="mb-4 pb-4 border-b border-base-300">
            <h3 class="text-lg font-semibold text-base-content tracking-wide">
                {{ $title }}
            </h3>
        </div>
    @endif
    
    <div class="text-base-content">
        {{ $slot }}
    </div>
</div>

