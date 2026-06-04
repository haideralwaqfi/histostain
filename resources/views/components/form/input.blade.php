@props(['label' => null, 'required' => false, 'type' => 'text'])

<div>
    @if($label)
        <label class="block text-sm font-medium text-ink mb-1">
            {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
        </label>
    @endif
    <input
        {{ $attributes->merge(['type' => $type, 'class' => 'block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-ink shadow-sm placeholder-ink-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary']) }}
    >
</div>
