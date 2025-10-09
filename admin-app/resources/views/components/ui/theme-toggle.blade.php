@props([
    'size' => 'md', // sm|md|lg
    'class' => '',
])

@php
$sizeClasses = [
    'sm' => 'w-4 h-4',
    'md' => 'w-6 h-6',
    'lg' => 'w-8 h-8',
][$size] ?? 'w-6 h-6';
@endphp

<button 
    type="button"
    class="theme-toggle-btn swap swap-rotate {{ $class }}" 
    title="Cambia tema"
    aria-label="Cambia tema"
>
    {{-- Checkbox nascosto per gestire lo stato swap --}}
    <input 
        type="checkbox" 
        class="theme-toggle-input hidden" 
    />
    
    {{-- Icona Sole (tema chiaro attivo) - visibile quando dark NON è attivo --}}
    <x-ui.icon 
        name="sun" 
        :class="$sizeClasses . ' swap-off text-warning transition-colors'"
        aria-hidden="true"
    />
    
    {{-- Icona Luna (tema scuro attivo) - visibile quando dark è attivo --}}
    <x-ui.icon 
        name="moon" 
        :class="$sizeClasses . ' swap-on text-info transition-colors'"
        aria-hidden="true"
    />
</button>

