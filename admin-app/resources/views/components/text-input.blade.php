@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'input input-bordered bg-base-100 text-base-content border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20 placeholder:text-base-content/40']) !!}>
