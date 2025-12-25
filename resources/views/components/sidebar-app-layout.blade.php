@props([
    'header' => null,
    'actions' => null,
])

<x-layouts.sidebar-app>
    @if ($header)
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endif

    @if ($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endif

    {{ $slot }}
</x-layouts.sidebar-app>
