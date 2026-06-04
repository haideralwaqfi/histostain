@props(['label' => null, 'required' => false])

<div>
    @if($label)
        <label class="block text-sm font-medium text-ink mb-1">
            {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
        </label>
    @endif
    <select
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-ink shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary bg-white']) }}
    >
        {{ $slot }}
    </select>
</div>
