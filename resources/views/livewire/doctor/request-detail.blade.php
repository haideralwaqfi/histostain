<div class="safe-top">
    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('doctor.requests') }}" wire:navigate class="text-ink-muted hover:text-ink">
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

        {{-- STAT banner --}}
        @if($request->isStat())
            <div class="flex items-center gap-2 rounded-xl bg-red-600 px-4 py-3 text-white stat-ring">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span class="text-sm font-bold tracking-wide">STAT Priority</span>
            </div>
        @elseif($request->priority->value === 'urgent')
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-2.5 text-sm font-semibold text-amber-700">
                Urgent Priority
            </div>
        @endif

        {{-- Details card --}}
        <div class="rounded-2xl border border-gray-200 bg-white divide-y divide-gray-100">
            <div class="flex justify-between px-4 py-3 text-sm">
                <span class="text-ink-muted">Ordered</span>
                <span class="font-medium text-ink" title="{{ $request->created_at->toDateTimeString() }}">
                    {{ $request->created_at->format('d M Y, g:i A') }}
                </span>
            </div>
            @if($request->mrn)
                <div class="flex justify-between px-4 py-3 text-sm">
                    <span class="text-ink-muted">MRN</span>
                    <span class="font-medium text-ink">{{ $request->mrn }}</span>
                </div>
            @endif
            @if($request->patient_name)
                <div class="flex justify-between px-4 py-3 text-sm">
                    <span class="text-ink-muted">Patient name</span>
                    <span class="font-medium text-ink">{{ $request->patient_name }}</span>
                </div>
            @endif
            @if($request->assignedTech)
                <div class="flex justify-between px-4 py-3 text-sm">
                    <span class="text-ink-muted">Assigned tech</span>
                    <span class="font-medium text-ink">{{ $request->assignedTech->name }}</span>
                </div>
            @endif
            @if($request->notes)
                <div class="px-4 py-3 text-sm">
                    <span class="text-ink-muted block mb-1">Notes</span>
                    <p class="text-ink">{{ $request->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Stain type details --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Stain Details</h3>
            <x-stain-type-details :request="$request" :show-attachment-badge="false" />
        </div>

        {{-- Requisition attachments --}}
        @if($request->getMedia('requisition_attachments')->isNotEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Attachments</h3>
                <ul class="space-y-2">
                    @foreach($request->getMedia('requisition_attachments') as $media)
                        <li class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                            <a href="{{ $media->getUrl() }}" target="_blank" class="text-sm text-primary hover:underline truncate">{{ $media->file_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Result attachments --}}
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
                @foreach($transitions as $t)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full border-2 border-white
                            {{ $t->to_status?->value === 'completed' ? 'bg-green-500' : ($t->to_status?->value === 'cancelled' ? 'bg-gray-400' : ($t->to_status?->value === 'stat' ? 'bg-red-500' : 'bg-primary')) }}">
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-ink">
                                    @if($t->from_status)
                                        {{ $t->from_status->label() }} → {{ $t->to_status->label() }}
                                    @else
                                        Created ({{ $t->to_status->label() }})
                                    @endif
                                </p>
                                <p class="text-xs text-ink-muted">by {{ $t->performedBy?->name }}</p>
                                @if($t->note)
                                    <p class="mt-1 text-xs text-ink rounded-lg bg-gray-50 border border-gray-100 px-2 py-1">{{ $t->note }}</p>
                                @endif
                            </div>
                            <time class="shrink-0 text-xs text-ink-muted">{{ $t->created_at->diffForHumans() }}</time>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        {{-- Cancel action --}}
        @if($request->status->value === 'pending')
            @if($confirmingCancel)
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 space-y-3">
                    <p class="text-sm font-semibold text-red-700">Cancel this request?</p>
                    <p class="text-sm text-red-600">This action cannot be undone. The request will be marked as cancelled.</p>
                    <div class="flex gap-3">
                        <button wire:click="cancelRequest"
                            wire:loading.attr="disabled" wire:target="cancelRequest"
                            class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white min-h-11 disabled:opacity-60">
                            <span wire:loading.remove wire:target="cancelRequest">Yes, cancel</span>
                            <span wire:loading wire:target="cancelRequest" class="inline-flex items-center justify-center gap-1.5">
                                <x-spinner class="h-3.5 w-3.5" /> Cancelling…
                            </span>
                        </button>
                        <button wire:click="dismissCancel" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-ink-muted min-h-11">
                            Keep
                        </button>
                    </div>
                </div>
            @else
                <button wire:click="startCancel"
                    wire:loading.class="opacity-50 pointer-events-none" wire:target="startCancel"
                    class="w-full rounded-xl border border-red-200 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition min-h-12">
                    Cancel request
                </button>
            @endif
        @endif

    </div>
</div>
