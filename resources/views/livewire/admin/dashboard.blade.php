<div class="safe-top" wire:poll.30s>
    <div class="px-4 py-5 space-y-5">

        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-ink">Dashboard</h1>
            <span class="text-xs text-ink-muted">Updates every 30s</span>
        </div>

        {{-- STAT alert (only shown when active STAT requests exist) --}}
        @if($stats['stat_active'] > 0)
            <a href="{{ route('admin.requests') }}?priority=stat" wire:navigate
               class="flex items-center gap-3 rounded-2xl bg-red-600 px-4 py-4 stat-ring">
                <svg class="h-6 w-6 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-bold text-white">{{ $stats['stat_active'] }} STAT {{ Str::plural('request', $stats['stat_active']) }} active</p>
                    <p class="text-xs text-red-100">Tap to view immediately</p>
                </div>
                <svg class="h-5 w-5 text-red-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
        @endif

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 gap-3">
            <x-stat-card
                label="Pending"
                :value="$stats['pending']"
                color="amber"
                href="{{ route('admin.requests') }}?status=pending"
            />
            <x-stat-card
                label="In Progress"
                :value="$stats['in_progress']"
                color="indigo"
                href="{{ route('admin.requests') }}?status=in_progress"
            />
            <x-stat-card
                label="On Hold"
                :value="$stats['on_hold']"
                color="orange"
                href="{{ route('admin.requests') }}?status=on_hold"
            />
            <x-stat-card
                label="Done Today"
                :value="$stats['completed_today']"
                color="green"
                href="{{ route('admin.requests') }}?status=completed"
            />
        </div>

        {{-- Pending approvals banner --}}
        @if($stats['pending_users'] > 0)
            <a href="{{ route('admin.approvals') }}" wire:navigate
               class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-amber-800">{{ $stats['pending_users'] }} pending {{ Str::plural('registration', $stats['pending_users']) }}</p>
                    <p class="text-xs text-amber-600">Tap to review</p>
                </div>
                <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
        @endif

        {{-- Recent requests --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-ink">Recent Requests</h2>
                <a href="{{ route('admin.requests') }}" wire:navigate class="text-xs text-primary hover:underline">View all</a>
            </div>
            <div class="space-y-2">
                @forelse($recentRequests as $req)
                    <a href="{{ route('admin.requests.show', $req->ulid) }}" wire:navigate
                       class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-card hover:shadow-md transition">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-ink truncate">{{ $req->type->shortLabel() }}</span>
                                @if($req->isStat())
                                    <span class="shrink-0 rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-bold text-red-700">STAT</span>
                                @endif
                            </div>
                            <p class="text-xs text-ink-muted truncate">Case {{ $req->case_number }} · Dr. {{ $req->doctor->name }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <x-status-badge :status="$req->status" />
                            <span class="text-xs text-ink-muted">{{ $req->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <p class="py-4 text-center text-sm text-ink-muted">No requests yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
