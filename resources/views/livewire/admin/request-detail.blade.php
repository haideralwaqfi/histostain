<div class="safe-top">
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.requests') }}" wire:navigate class="text-ink-muted hover:text-ink">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-semibold text-ink truncate">{{ $request->type->label() }}</h1>
                <p class="text-xs text-ink-muted">Case {{ $request->case_number }}</p>
            </div>
            <x-status-badge :status="$request->status" />
        </div>
    </div>

    <div class="px-4 py-4 space-y-4">

        @if($request->isStat())
            <div class="flex items-center gap-2 rounded-xl bg-red-600 px-4 py-3 text-white stat-ring">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span class="text-sm font-bold">STAT Priority</span>
            </div>
        @endif

        {{-- Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white divide-y divide-gray-100">
            <div class="flex justify-between px-4 py-3 text-sm">
                <span class="text-ink-muted">Doctor</span>
                <span class="font-medium text-ink">{{ $request->doctor->name }}</span>
            </div>
            @if($request->mrn)
                <div class="flex justify-between px-4 py-3 text-sm">
                    <span class="text-ink-muted">MRN</span>
                    <span class="font-medium text-ink">{{ $request->mrn }}</span>
                </div>
            @endif
            @if($request->lab_number)
                <div class="flex justify-between px-4 py-3 text-sm">
                    <span class="text-ink-muted">Lab number</span>
                    <span class="font-medium text-ink">{{ $request->lab_number }}</span>
                </div>
            @endif
            <div class="flex justify-between px-4 py-3 text-sm">
                <span class="text-ink-muted">Priority</span>
                <span class="font-semibold {{ $request->priority->value === 'stat' ? 'text-red-600' : ($request->priority->value === 'urgent' ? 'text-amber-600' : 'text-ink') }}">
                    {{ $request->priority->label() }}
                </span>
            </div>
            @if($request->notes)
                <div class="px-4 py-3 text-sm">
                    <span class="text-ink-muted block mb-1">Notes</span>
                    <p class="text-ink">{{ $request->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Assign tech --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="text-sm font-semibold text-ink mb-3">Assigned Technician</h3>
            @if($request->assignedTech)
                <p class="text-sm font-medium text-ink mb-3">{{ $request->assignedTech->name }}</p>
            @else
                <p class="text-sm text-ink-muted mb-3">No tech assigned.</p>
            @endif

            @if(!in_array($request->status->value, ['completed', 'cancelled']))
                <div class="flex gap-2">
                    <div class="flex-1">
                        <x-form.select wire:model="selectedTechId">
                            <option value="">Select a tech…</option>
                            @foreach($techs as $tech)
                                <option value="{{ $tech->id }}" @selected($request->assigned_tech_id === $tech->id)>{{ $tech->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>
                    <button
                        wire:click="assignTech"
                        wire:loading.attr="disabled"
                        class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white min-h-11 transition hover:bg-primary-dark active:scale-[0.98] disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="assignTech">Assign</span>
                        <span wire:loading wire:target="assignTech">…</span>
                    </button>
                </div>
                @error('selectedTechId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @endif
        </div>

        {{-- Type data --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Request Details</h3>
            @if(isset($request->type_data['blocks']))
                @foreach($request->type_data['blocks'] as $i => $block)
                    <div class="mb-3 last:mb-0 rounded-xl border border-gray-100 bg-gray-50 p-3">
                        <p class="text-xs font-semibold text-ink-muted mb-2">Block {{ $i + 1 }}@if(isset($block['block_id'])) — {{ $block['block_id'] }}@endif</p>
                        <dl class="space-y-1">
                            @foreach($block as $key => $val)
                                @if($key !== 'block_id' && $val !== null && $val !== '' && $val !== false)
                                    <div class="flex gap-2 text-sm">
                                        <dt class="shrink-0 text-ink-muted capitalize">{{ str_replace('_', ' ', $key) }}:</dt>
                                        <dd class="text-ink">{{ is_bool($val) ? ($val ? 'Yes' : 'No') : $val }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            @else
                <dl class="space-y-1">
                    @foreach($request->type_data as $key => $val)
                        @if($val !== null && $val !== '')
                            <div class="flex gap-2 text-sm">
                                <dt class="shrink-0 text-ink-muted capitalize">{{ str_replace('_', ' ', $key) }}:</dt>
                                <dd class="text-ink">{{ $val }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            @endif
        </div>

        {{-- Attachments --}}
        @if($request->getMedia('requisition_attachments')->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Requisition Attachments</h3>
                <ul class="space-y-2">
                    @foreach($request->getMedia('requisition_attachments') as $media)
                        <li><a href="{{ $media->getUrl() }}" target="_blank" class="text-sm text-primary hover:underline">{{ $media->file_name }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($request->getMedia('result_attachments')->isNotEmpty())
            <div class="rounded-2xl border border-green-200 bg-green-50 p-4">
                <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-3">Results</h3>
                <ul class="space-y-2">
                    @foreach($request->getMedia('result_attachments') as $media)
                        <li><a href="{{ $media->getUrl() }}" target="_blank" class="text-sm text-green-700 hover:underline">{{ $media->file_name }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Timeline --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-4">History</h3>
            <ol class="relative border-l border-gray-200 ml-2 space-y-5">
                @foreach($request->transitions as $t)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full border-2 border-white
                            {{ $t->to_status?->value === 'completed' ? 'bg-green-500' : ($t->to_status?->value === 'cancelled' ? 'bg-gray-400' : 'bg-primary') }}">
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-ink">
                                    @if($t->from_status) {{ $t->from_status->label() }} → @endif{{ $t->to_status->label() }}
                                </p>
                                <p class="text-xs text-ink-muted">by {{ $t->performedBy?->name }}</p>
                                @if($t->note)
                                    <p class="mt-1 text-xs text-ink bg-gray-50 border border-gray-100 rounded-lg px-2 py-1">{{ $t->note }}</p>
                                @endif
                            </div>
                            <time class="shrink-0 text-xs text-ink-muted">{{ $t->created_at->diffForHumans() }}</time>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        {{-- Admin cancel --}}
        @if(!in_array($request->status->value, ['completed', 'cancelled']))
            @if($confirmingCancel)
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 space-y-3">
                    <p class="text-sm font-semibold text-red-700">Cancel this request?</p>
                    <div class="flex gap-3">
                        <button wire:click="cancelRequest" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white min-h-11">
                            <span wire:loading.remove wire:target="cancelRequest">Confirm cancel</span>
                            <span wire:loading wire:target="cancelRequest">…</span>
                        </button>
                        <button wire:click="dismissCancel" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-ink-muted min-h-11">Keep</button>
                    </div>
                </div>
            @else
                <button wire:click="startCancel" class="w-full rounded-xl border border-red-200 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition min-h-12">
                    Cancel request
                </button>
            @endif
        @endif

    </div>
</div>
