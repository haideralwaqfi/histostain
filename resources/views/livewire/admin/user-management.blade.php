<div class="safe-top">
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200">
        <div class="px-4 py-4">
            <h1 class="text-lg font-semibold text-ink">User Management</h1>
        </div>
        <div class="px-4 pb-3 space-y-2">
            {{-- Search --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-ink-muted pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search name or email…"
                    class="block w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            {{-- Filter chips --}}
            <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
                <button wire:click="$set('filterStatus', '')" class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterStatus === '' ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted' }}">All</button>
                @foreach($statuses as $s)
                    <button wire:click="$set('filterStatus', '{{ $s->value }}')" class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterStatus === $s->value ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted' }}">{{ $s->label() }}</button>
                @endforeach
                @foreach($roles as $r)
                    <button wire:click="$set('filterRole', '{{ $r->value }}')" class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterRole === $r->value ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted' }}">{{ $r->label() }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="px-4 py-3 space-y-3">
        @forelse($users as $user)
            <div class="rounded-2xl bg-white border border-gray-200 shadow-card p-4">
                <div class="flex items-start gap-3">
                    {{-- Avatar --}}
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="" class="h-11 w-11 rounded-full object-cover shrink-0">
                    @else
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-ink text-sm">{{ $user->name }}</p>
                            @if($user->role)
                                <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">{{ $user->role->label() }}</span>
                            @endif
                            @if($user->status->value === 'rejected')
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600">Rejected</span>
                            @elseif($user->status->value === 'pending')
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-600">Pending</span>
                            @endif
                        </div>
                        <p class="text-xs text-ink-muted truncate">{{ $user->email }}</p>
                        <p class="text-xs text-ink-muted">Joined {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Role edit --}}
                @if($editingRoleId === $user->id)
                    <div class="mt-3 flex gap-2">
                        <x-form.select wire:model="newRole" class="flex-1 text-sm">
                            @foreach($roles as $r)
                                <option value="{{ $r->value }}">{{ $r->label() }}</option>
                            @endforeach
                        </x-form.select>
                        <button wire:click="saveRole" class="rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white min-h-10">Save</button>
                        <button wire:click="cancelEditRole" class="rounded-xl border border-gray-300 px-3 py-2 text-xs text-ink-muted min-h-10">✕</button>
                    </div>
                @elseif($rejectingId === $user->id)
                    <div class="mt-3 space-y-2">
                        <textarea wire:model="rejectReason" rows="2" placeholder="Rejection reason…"
                            class="block w-full rounded-xl border border-gray-300 px-3 py-2 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary resize-none"></textarea>
                        @error('rejectReason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        <div class="flex gap-2">
                            <button wire:click="confirmReject" class="flex-1 rounded-xl bg-red-600 py-2 text-xs font-semibold text-white min-h-10">Confirm</button>
                            <button wire:click="cancelReject" class="rounded-xl border border-gray-300 px-3 py-2 text-xs text-ink-muted min-h-10">Cancel</button>
                        </div>
                    </div>
                @else
                    <div class="mt-3 flex gap-2 flex-wrap">
                        @if($user->role)
                            <button wire:click="startEditRole({{ $user->id }}, '{{ $user->role->value }}')"
                                class="rounded-xl border border-gray-300 px-3 py-2 text-xs font-medium text-ink-muted hover:bg-gray-50 min-h-10">
                                Change role
                            </button>
                        @endif
                        @if($user->status->value === 'approved')
                            <button wire:click="deactivate({{ $user->id }})"
                                class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-500 hover:bg-red-50 min-h-10">
                                Deactivate
                            </button>
                        @elseif($user->status->value === 'rejected')
                            <button wire:click="reactivate({{ $user->id }})"
                                class="rounded-xl border border-green-200 px-3 py-2 text-xs font-medium text-green-600 hover:bg-green-50 min-h-10">
                                Reactivate
                            </button>
                        @elseif($user->status->value === 'pending')
                            <button wire:click="startReject({{ $user->id }})"
                                class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-500 hover:bg-red-50 min-h-10">
                                Reject
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="py-20 text-center">
                <p class="font-semibold text-ink">No users found</p>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $users->links() }}</div>
</div>
