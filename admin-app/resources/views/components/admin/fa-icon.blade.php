@props([
    'name' => '',
    'size' => '',
    'color' => '',
    'class' => '',
    'style' => 'solid', // solid, regular, light, brands, etc.
    'spin' => false,
    'pulse' => false,
    'fixed' => false
])

@php
    $classes = collect([
        'fa-' . $style,
        'fa-' . $name,
        $size ? 'fa-' . $size : '',
        $color ? 'text-' . $color : '',
        $spin ? 'fa-spin' : '',
        $pulse ? 'fa-pulse' : '',
        $fixed ? 'fa-fw' : '',
        $class
    ])->filter()->join(' ');
@endphp

<i {{ $attributes->merge(['class' => $classes]) }}></i>
