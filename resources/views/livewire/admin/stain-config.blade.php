<div class="safe-top">
    <div class="px-4 py-5 space-y-5">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-ink">Stain Configuration</h1>
        </div>

        {{-- Tab selector --}}
        <div class="flex rounded-xl border border-gray-200 bg-gray-100 p-1 gap-1">
            @foreach($tabs as $key => $label)
                <button
                    type="button"
                    wire:click="switchTab('{{ $key }}')"
                    class="flex-1 rounded-lg py-2 text-sm font-medium transition-colors
                        {{ $tab === $key
                            ? 'bg-white text-ink shadow-sm'
                            : 'text-ink-muted hover:text-ink' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Options list --}}
        <div class="space-y-2">
            @forelse($options as $i => $option)
                <div class="rounded-xl border px-3 py-2.5 transition-colors
                    {{ $option->is_active ? 'border-gray-200 bg-white' : 'border-red-200 bg-red-50' }}">

                    {{-- Main row --}}
                    <div class="flex items-center gap-2">

                        {{-- Reorder buttons --}}
                        <div class="flex flex-col gap-0.5 shrink-0">
                            <button type="button" wire:click="moveUp({{ $option->id }})"
                                @if($i === 0) disabled @endif
                                class="p-0.5 rounded text-ink-muted hover:text-ink disabled:opacity-20 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="moveDown({{ $option->id }})"
                                @if($i === $options->count() - 1) disabled @endif
                                class="p-0.5 rounded text-ink-muted hover:text-ink disabled:opacity-20 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Label (view or edit mode) --}}
                        <div class="flex-1 min-w-0">
                            @if($editingId === $option->id)
                                <input
                                    wire:model="editingLabel"
                                    wire:keydown.enter="saveEdit"
                                    wire:keydown.escape="cancelEdit"
                                    type="text"
                                    class="w-full rounded-lg border border-primary bg-primary/5 px-2.5 py-1 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-primary"
                                    autofocus
                                >
                                @error('editingLabel') <p class="mt-0.5 text-xs text-red-500">{{ $message }}</p> @enderror
                            @else
                                <span class="text-sm font-medium {{ $option->is_active ? 'text-ink' : 'text-red-700' }}">
                                    {{ $option->label }}
                                </span>
                                <span class="ml-1.5 text-[10px] font-mono {{ $option->is_active ? 'text-ink-muted bg-gray-100' : 'text-red-400 bg-red-100' }} rounded px-1.5 py-0.5">
                                    {{ $option->key }}
                                </span>
                            @endif
                        </div>

                        {{-- Active / Inactive toggle --}}
                        <button
                            type="button"
                            wire:click="toggleActive({{ $option->id }})"
                            wire:loading.attr="disabled"
                            class="shrink-0 rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors
                                {{ $option->is_active
                                    ? 'border-green-200 bg-green-100 text-green-700 hover:bg-green-200'
                                    : 'border-red-200 bg-red-100 text-red-700 hover:bg-red-200' }}"
                        >
                            {{ $option->is_active ? 'Active' : 'Inactive' }}
                        </button>

                        {{-- Edit / Delete --}}
                        <div class="flex items-center gap-1 shrink-0">
                            @if($editingId === $option->id)
                                <button type="button" wire:click="saveEdit"
                                    class="rounded-lg bg-primary px-2.5 py-1 text-xs font-semibold text-white hover:bg-primary/90 transition-colors">
                                    Save
                                </button>
                                <button type="button" wire:click="cancelEdit"
                                    class="rounded-lg px-2 py-1 text-xs font-medium text-ink-muted hover:text-ink transition-colors">
                                    Cancel
                                </button>
                            @else
                                <button type="button" wire:click="startEdit({{ $option->id }})"
                                    class="p-1.5 rounded-lg text-ink-muted hover:text-ink hover:bg-gray-100 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </button>
                                <button type="button"
                                    wire:click="removeOption({{ $option->id }})"
                                    wire:confirm="Remove '{{ $option->label }}'? This won't affect existing requests."
                                    class="p-1.5 rounded-lg text-ink-muted hover:text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Inactivation reason (only when inactive) --}}
                    @if(!$option->is_active)
                        <div class="mt-2 ml-8 flex items-center gap-2">
                            <svg class="h-3.5 w-3.5 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <input
                                wire:model.blur="reasons.{{ $option->id }}"
                                type="text"
                                placeholder="Reason for inactivation (shown to doctors)…"
                                class="flex-1 rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs text-ink placeholder-red-300 focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-300"
                            >
                        </div>
                        <p class="mt-1 ml-8 text-[10px] text-red-400">Saved automatically when you leave the field.</p>
                    @endif

                </div>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 py-8 text-center">
                    <p class="text-sm text-ink-muted">No options yet. Add one below.</p>
                </div>
            @endforelse
        </div>

        {{-- Add new option --}}
        <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 space-y-3">
            <p class="text-sm font-semibold text-ink">
                Add {{ $tab === 'ihc' ? 'Antibody' : ($tab === 'if_stains' ? 'Panel Option' : 'Stain') }}
            </p>
            <div class="flex gap-2">
                <div class="flex-1">
                    <input
                        wire:model="newLabel"
                        wire:keydown.enter="addOption"
                        type="text"
                        placeholder="{{ $tab === 'ihc' ? 'e.g. CD10, Ki-67, HER2' : ($tab === 'if_stains' ? 'e.g. IgG, C3, Kappa' : 'e.g. Oil Red O, Alizarin Red') }}"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-ink placeholder-ink-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                    @error('newLabel') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <button
                    type="button"
                    wire:click="addOption"
                    wire:loading.attr="disabled"
                    class="flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90 disabled:opacity-60 transition-colors shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Add
                </button>
            </div>
            <p class="text-xs text-ink-muted">
                A unique key is auto-generated from the label. New options are active by default.
            </p>
        </div>

        {{-- Info note --}}
        <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 text-xs text-blue-700 leading-relaxed">
            Inactive options are hidden from request forms and the inactivation reason is shown to doctors. Existing requests are not affected.
        </div>

    </div>
</div>
