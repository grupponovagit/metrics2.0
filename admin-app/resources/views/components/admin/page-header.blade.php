@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconClass' => null,
    'iconColor' => 'primary',
    'actions' => null,            // Slot per azioni (bottoni, link)
])

@php
    /**
     * Page Header Component
     * 
     * Intestazione pagina con:
     * - Titolo principale (grande, bold)
     * - Sottotitolo descrittivo
     * - Icona opzionale
     * - Slot actions per pulsanti/link
     * 
     * Icon colors disponibili:
     * - primary, secondary, success, info, warning, error, accent
     */
    
    $iconColorClasses = [
        'primary' => 'text-primary',
        'secondary' => 'text-secondary',
        'success' => 'text-success',
        'info' => 'text-info',
        'warning' => 'text-warning',
        'error' => 'text-error',
        'accent' => 'text-accent',
    ];
    
    $iconTextColor = $iconColorClasses[$iconColor] ?? $iconColorClasses['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'mb-8 pb-6 border-b border-base-300']) }}>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-4">
                @if($icon || $iconClass)
                    <div class="hidden sm:flex items-center justify-center w-16 h-16 bg-{{ $iconColor }}/10 rounded-2xl">
                        <x-ui.icon 
                            :name="$icon ?? $iconClass" 
                            size="xl"
                            :class="$iconTextColor"
                            aria-hidden="true"
                        />
                    </div>
                @endif
                
                <div>
                    @if($title)
                        <h1 class="text-3xl lg:text-4xl font-bold text-base-content tracking-tight">
                            {{ $title }}
                        </h1>
                    @endif
                    
                    @if($subtitle)
                        <p class="text-base text-base-content/70 mt-2 font-medium">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($actions ?? false)
            <div class="flex items-center gap-3 flex-wrap">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>

