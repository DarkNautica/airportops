@props([
    'color' => 'neutral',
    'pulse' => false,
    'size' => 'sm',
])

@php
    $dotColor = match ($color) {
        'green', 'active', 'online' => 'bg-emerald-500',
        'amber', 'warning' => 'bg-amber-500',
        'red', 'error', 'critical' => 'bg-red-500',
        'blue', 'info' => 'bg-blue-500',
        default => 'bg-slate-400',
    };

    $sizeClass = match ($size) {
        'xs' => 'h-1.5 w-1.5',
        'sm' => 'h-2 w-2',
        'md' => 'h-2.5 w-2.5',
        'lg' => 'h-3 w-3',
        default => 'h-2 w-2',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-block rounded-full {$sizeClass} {$dotColor}" . ($pulse ? ' pulse-dot' : '')]) }}></span>
