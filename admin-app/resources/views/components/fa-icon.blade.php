@props([
    'name' => '',
    'size' => '',
    'color' => '',
    'class' => '',
    'style' => 'solid' // solid, regular, light, brands, etc.
])

@php
    $classes = collect([
        'fa-' . $style,
        'fa-' . $name,
        $size ? 'fa-' . $size : '',
        $color ? 'text-' . $color : '',
        $class
    ])->filter()->join(' ');
@endphp

<i {{ $attributes->merge(['class' => $classes]) }}></i>
