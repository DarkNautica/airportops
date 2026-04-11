@props([
    'variant' => 'neutral',
    'dot' => true,
    'pulse' => false,
])

@php
    $styles = match ($variant) {
        'certified', 'satisfactory', 'green' => [
            'bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'dot' => 'bg-emerald-500',
        ],
        'active' => [
            'bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'dot' => 'bg-emerald-500',
        ],
        'draft', 'pending', 'open' => [
            'bg' => 'bg-blue-50 text-blue-700 ring-blue-200',
            'dot' => 'bg-blue-500',
        ],
        'in_progress', 'progress' => [
            'bg' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'dot' => 'bg-amber-500',
        ],
        'warning', 'overdue', 'unsatisfactory' => [
            'bg' => 'bg-red-50 text-red-700 ring-red-200',
            'dot' => 'bg-red-500',
        ],
        'critical' => [
            'bg' => 'bg-red-50 text-red-700 ring-red-200',
            'dot' => 'bg-red-500',
        ],
        'closed', 'cancelled', 'locked' => [
            'bg' => 'bg-slate-50 text-slate-600 ring-slate-200',
            'dot' => 'bg-slate-400',
        ],
        default => [
            'bg' => 'bg-slate-50 text-slate-600 ring-slate-200',
            'dot' => 'bg-slate-400',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ring-1 ring-inset ' . $styles['bg']]) }}>
    @if($dot)
        <span class="badge-dot {{ $styles['dot'] }} {{ $pulse ? 'pulse-dot' : '' }}"></span>
    @endif
    {{ $slot }}
</span>
