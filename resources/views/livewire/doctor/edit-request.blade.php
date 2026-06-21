<div class="safe-top">

    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('doctor.requests.show', $request->ulid) }}" wire:navigate class="text-ink-muted hover:text-ink">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-semibold text-ink">Edit Request</h1>
                <p class="text-xs text-ink-muted">{{ $definition->label() }} · Case {{ $request->case_number }}</p>
            </div>
        </div>
    </div>

    <div class="px-4 py-5">
        <form wire:submit="save" class="space-y-4" novalidate>

            {{-- Patient / Case --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide">Patient / Case</h3>
                <x-form.input wire:model="caseNumber" label="Case number" required placeholder="e.g. 2024-12345" />
                @error('caseNumber') <x-form.error>{{ $message }}</x-form.error> @enderror
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-form.input wire:model="mrn" label="MRN" placeholder="Optional" />
                    </div>
                    <div>
                        <x-form.input wire:model="patientName" label="Patient name" placeholder="Optional" />
                    </div>
                </div>
            </div>

            {{-- Priority --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Priority</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($priorities as $p)
                        <button
                            type="button"
                            wire:click="$set('priority', '{{ $p->value }}')"
                            wire:loading.class="opacity-50 pointer-events-none"
                            class="rounded-xl border-2 py-2.5 text-sm font-semibold transition min-h-11
                                @if($priority === $p->value)
                                    @if($p->value === 'urgent') border-amber-400 bg-amber-50 text-amber-700
                                    @else border-primary bg-primary/5 text-primary @endif
                                @else border-gray-200 text-ink-muted hover:border-gray-300 @endif"
                        >
                            {{ $p->label() }}
                        </button>
                    @endforeach
                </div>
                @error('priority') <x-form.error class="mt-1">{{ $message }}</x-form.error> @enderror
            </div>

            {{-- Type-specific form --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">{{ $definition->label() }}</h3>
                @include($definition->formPartial(), [
                    'typeData' => $typeData,
                    'prefix'   => 'typeData',
                    'typeKey'  => $typeKey,
                ])
            </div>

            {{-- Notes --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <x-form.textarea wire:model="notes" label="Additional notes" rows="3" placeholder="Optional clinical notes…" />
            </div>

            {{-- Existing attachments --}}
            @if($request->getMedia('requisition_attachments')->isNotEmpty())
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Current Attachments</h3>
                    <ul class="space-y-2">
                        @foreach($request->getMedia('requisition_attachments') as $media)
                            <li class="flex items-center justify-between gap-3">
                                <a href="{{ $media->getUrl() }}" target="_blank"
                                    class="flex items-center gap-2 text-sm text-primary hover:underline truncate min-w-0">
                                    <svg class="h-4 w-4 shrink-0 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                    <span class="truncate">{{ $media->file_name }}</span>
                                </a>
                                <button
                                    type="button"
                                    wire:click="removeAttachment({{ $media->id }})"
                                    wire:loading.attr="disabled"
                                    wire:confirm="Remove '{{ $media->file_name }}'?"
                                    class="shrink-0 text-xs font-medium text-red-500 hover:text-red-700 transition">
                                    Remove
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- New attachments --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-2">Add Attachments (optional)</h3>
                <p class="text-xs text-ink-muted mb-2">PDF, JPG, PNG up to 10 MB each, max 5 files.</p>
                <input
                    wire:model="attachments"
                    type="file"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-primary hover:file:bg-primary/20"
                >
                @error('attachments.*') <x-form.error>{{ $message }}</x-form.error> @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled" wire:target="save"
                class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px] disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">Save changes</span>
                <span wire:loading wire:target="save" class="inline-flex items-center justify-center gap-2">
                    <x-spinner /> Saving…
                </span>
            </button>

        </form>
    </div>
</div>
