<div class="safe-top">
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <h1 class="text-lg font-semibold text-ink">Pending Approvals</h1>
        <p class="text-sm text-ink-muted">{{ $pendingUsers->count() }} user(s) awaiting review</p>
    </div>

    <div class="px-4 py-4 space-y-3">
        @forelse($pendingUsers as $user)
            <div class="rounded-2xl bg-white border border-gray-200 shadow-card p-4">
                <div class="flex items-start gap-3">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="" class="h-11 w-11 rounded-full object-cover">
                        @else
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-ink truncate">{{ $user->name }}</p>
                        <p class="text-sm text-ink-muted truncate">{{ $user->email }}</p>
                        <p class="text-xs text-ink-muted mt-0.5">Registered {{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- Role selector + action buttons -->
                @if($rejectingUserId === $user->id)
                    <!-- Rejection reason input -->
                    <div class="mt-3 space-y-2">
                        <textarea
                            wire:model="rejectionReason"
                            rows="2"
                            placeholder="Reason for rejection (required)…"
                            class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary @error('rejectionReason') border-red-400 @enderror"
                        ></textarea>
                        @error('rejectionReason')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="flex gap-2">
                            <button wire:click="confirmReject"
                                wire:loading.attr="disabled" wire:target="confirmReject"
                                class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white min-h-11 transition active:scale-[0.98] disabled:opacity-60">
                                <span wire:loading.remove wire:target="confirmReject">Confirm Reject</span>
                                <span wire:loading wire:target="confirmReject" class="inline-flex items-center justify-center gap-1.5">
                                    <x-spinner class="h-3.5 w-3.5" /> Rejecting…
                                </span>
                            </button>
                            <button wire:click="cancelReject"
                                wire:loading.class="opacity-50 pointer-events-none" wire:target="cancelReject"
                                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-ink-muted min-h-11 transition active:scale-[0.98]">
                                Cancel
                            </button>
                        </div>
                    </div>
                @else
                    <div class="mt-3 space-y-2">
                        <!-- Role selector -->
                        <div class="flex gap-2">
                            @foreach($roles as $role)
                                <button
                                    wire:click="approve({{ $user->id }}, '{{ $role->value }}')"
                                    wire:loading.attr="disabled" wire:target="approve({{ $user->id }}, '{{ $role->value }}')"
                                    class="flex-1 rounded-xl bg-primary py-2.5 text-xs font-semibold text-white min-h-11 transition hover:bg-primary-dark active:scale-[0.98] disabled:opacity-60"
                                >
                                    <span wire:loading.remove wire:target="approve({{ $user->id }}, '{{ $role->value }}')">Approve as {{ $role->label() }}</span>
                                    <span wire:loading wire:target="approve({{ $user->id }}, '{{ $role->value }}')" class="inline-flex items-center justify-center gap-1.5">
                                        <x-spinner class="h-3.5 w-3.5" /> Approving…
                                    </span>
                                </button>
                            @endforeach
                        </div>
                        <button
                            wire:click="startReject({{ $user->id }})"
                            wire:loading.class="opacity-50 pointer-events-none" wire:target="startReject({{ $user->id }})"
                            class="w-full rounded-xl border border-red-200 py-2.5 text-sm font-medium text-red-600 min-h-11 transition hover:bg-red-50 active:scale-[0.98]"
                        >
                            Reject
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="mt-3 font-semibold text-ink">All caught up</p>
                <p class="mt-1 text-sm text-ink-muted">No pending registrations.</p>
            </div>
        @endforelse
    </div>
</div>
