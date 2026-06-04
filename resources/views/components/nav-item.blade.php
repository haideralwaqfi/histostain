@props(['route', 'label', 'current'])

@php
    $isActive = str_starts_with($current ?? '', $route);
@endphp

<a
    href="{{ route($route) }}"
    wire:navigate
    class="flex flex-col items-center justify-center gap-0.5 min-h-[48px] text-xs font-medium transition-colors"
    :class="{ 'text-primary': {{ $isActive ? 'true' : 'false' }}, 'text-ink-muted hover:text-ink': {{ !$isActive ? 'true' : 'false' }} }"
    aria-current="{{ $isActive ? 'page' : 'false' }}"
>
    <span class="{{ $isActive ? 'text-primary' : 'text-ink-muted' }} transition-colors">
        {{ $slot }}
    </span>
    <span class="{{ $isActive ? 'text-primary font-semibold' : '' }}">{{ $label }}</span>
</a>
