@props([
    'name' => '',           // Nome icona (usato dalla mappa)
    'size' => 'md',         // sm|md|lg|xl
    'sr' => null,           // Screen reader text (accessibilità)
    'variant' => 'heroicon', // heroicon-o (outline) | heroicon-s (solid)
    'stroke' => null,       // Stroke width override (default: 1.5 per heroicons-o)
])

@php
/**
 * UI Icon Component v1.0
 * 
 * Wrapper unificato per icone Heroicons con supporto accessibilità.
 * 
 * Props:
 * - name: Nome icona dalla mappa (es: 'arrow-left', 'users', 'cog')
 * - size: Dimensione icona (sm=16px, md=20px, lg=24px, xl=28px)
 * - sr: Testo screen-reader per accessibilità (se presente, aggiunge role="img")
 * - variant: heroicon-o (outline) | heroicon-s (solid) - default outline
 * - stroke: Override stroke width (solo per outline)
 * 
 * Esempio uso:
 * <x-ui.icon name="settings" size="md" class="text-base-content/70" sr="Impostazioni" />
 */

// Carica mappa icone
$iconMap = include resource_path('icons/map.php');

// Default fallback se icona non trovata
$iconData = $iconMap[$name] ?? ['set' => 'heroicon-o', 'icon' => 'question-mark-circle'];

// Costruisci nome componente Blade (es: heroicon-o-user)
$iconComponent = $iconData['set'] . '-' . $iconData['icon'];

// Mappa dimensioni Tailwind
$sizeClasses = [
    'sm' => 'w-4 h-4',      // 16px
    'md' => 'w-5 h-5',      // 20px  
    'lg' => 'w-6 h-6',      // 24px
    'xl' => 'w-7 h-7',      // 28px
];

$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];

// Accessibilità
$ariaProps = [];
$showSrOnly = false;
if ($sr) {
    $ariaProps['role'] = 'img';
    $ariaProps['aria-label'] = $sr;
    $showSrOnly = true;
} else {
    $ariaProps['aria-hidden'] = 'true';
}

// Stroke width (solo per outline)
$strokeClass = '';
if ($stroke && str_contains($iconComponent, '-o-')) {
    $strokeClass = 'stroke-[' . $stroke . ']';
}

// Merge classi
$classes = collect([$sizeClass, $strokeClass, $attributes->get('class')])->filter()->join(' ');
@endphp

<x-dynamic-component 
    :component="$iconComponent" 
    :class="$classes"
    {{ $attributes->except(['class'])->merge($ariaProps) }}
/>
@if($showSrOnly)
    <span class="sr-only">{{ $sr }}</span>
@endif

