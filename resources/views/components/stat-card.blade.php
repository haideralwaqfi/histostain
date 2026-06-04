@props(['label', 'value', 'color' => 'primary', 'href' => null])

@php
$colorMap = [
    'amber'  => ['bg-amber-50 border-amber-200',  'text-amber-600', 'text-amber-800'],
    'indigo' => ['bg-indigo-50 border-indigo-200', 'text-indigo-600', 'text-indigo-800'],
    'orange' => ['bg-orange-50 border-orange-200', 'text-orange-600', 'text-orange-800'],
    'green'  => ['bg-green-50 border-green-200',   'text-green-600',  'text-green-800'],
    'blue'   => ['bg-blue-50 border-blue-200',     'text-blue-600',   'text-blue-800'],
    'primary'=> ['bg-primary/5 border-primary/20', 'text-primary',    'text-primary'],
];
[$bg, $icon, $text] = $colorMap[$color] ?? $colorMap['primary'];
@endphp

@if($href)
<a href="{{ $href }}" wire:navigate class="flex flex-col rounded-2xl border {{ $bg }} p-4 shadow-card hover:shadow-md transition">
@else
<div class="flex flex-col rounded-2xl border {{ $bg }} p-4 shadow-card">
@endif
    <span class="text-xs font-medium {{ $icon }} uppercase tracking-wide">{{ $label }}</span>
    <span class="mt-1 text-3xl font-bold {{ $text }}">{{ $value }}</span>
@if($href)
</a>
@else
</div>
@endif
