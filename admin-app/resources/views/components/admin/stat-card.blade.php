@props([
    'title' => null,
    'value' => null,
    'icon' => null,
    'iconClass' => 'fa-chart-line',
    'trend' => null,              // 'up' | 'down' | null
    'trendValue' => null,         // es: '+12%'
    'color' => 'primary',         // 'primary' | 'secondary' | 'success' | 'info' | 'warning' | 'error'
    'hoverable' => true,
])

@php
    /**
     * Stat Card Component - Per statistiche/KPI
     * 
     * Colors disponibili (DaisyUI):
     * - primary: #F97316 (orange)
     * - secondary: #FACC15 (yellow)
     * - success: #10B981 (green)
     * - info: #3B82F6 (blue)
     * - warning: #F59E0B (amber)
     * - error: #EF4444 (red)
     * - accent: #06B6D4 (cyan)
     * 
     * Trend indicators:
     * - up: freccia su + verde
     * - down: freccia giù + rosso
     */
    
    $colorClasses = [
        'primary' => 'text-primary',
        'secondary' => 'text-secondary',
        'success' => 'text-success',
        'info' => 'text-info',
        'warning' => 'text-warning',
        'error' => 'text-error',
        'accent' => 'text-accent',
    ];
    
    $iconBgClasses = [
        'primary' => 'bg-primary/10',
        'secondary' => 'bg-secondary/10',
        'success' => 'bg-success/10',
        'info' => 'bg-info/10',
        'warning' => 'bg-warning/10',
        'error' => 'bg-error/10',
        'accent' => 'bg-accent/10',
    ];
    
    $textColor = $colorClasses[$color] ?? $colorClasses['primary'];
    $iconBg = $iconBgClasses[$color] ?? $iconBgClasses['primary'];
    
    $hoverClass = $hoverable ? 'hover:shadow-xl hover:-translate-y-1 cursor-pointer' : '';
@endphp

<div {{ $attributes->merge(['class' => "card bg-base-100 shadow-lg border border-base-300/50 $hoverClass transition-all duration-300"]) }}>
    <div class="card-body p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                @if($title)
                    <p class="text-sm text-base-content/70 font-medium mb-1">{{ $title }}</p>
                @endif
                
                @if($value)
                    <p class="text-3xl font-bold {{ $textColor }} mb-2">{{ $value }}</p>
                @endif
                
                @if($trend && $trendValue)
                    <div class="flex items-center gap-1">
                        @if($trend === 'up')
                            <x-admin.fa-icon 
                                name="arrow-up" 
                                class="h-4 w-4 text-success" 
                            />
                            <span class="text-sm font-semibold text-success">{{ $trendValue }}</span>
                        @elseif($trend === 'down')
                            <x-admin.fa-icon 
                                name="arrow-down" 
                                class="h-4 w-4 text-error" 
                            />
                            <span class="text-sm font-semibold text-error">{{ $trendValue }}</span>
                        @endif
                        <span class="text-xs text-base-content/60 ml-1">vs mese scorso</span>
                    </div>
                @endif
                
                {{ $slot }}
            </div>
            
            @if($icon || $iconClass)
                <div class="flex-shrink-0 ml-4">
                    <div class="w-14 h-14 {{ $iconBg }} rounded-2xl flex items-center justify-center {{ $textColor }}">
                        <x-admin.fa-icon 
                            :name="$icon ?? $iconClass" 
                            class="h-7 w-7" 
                        />
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

