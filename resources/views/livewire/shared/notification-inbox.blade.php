<div class="safe-top">
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-ink">Notifications</h1>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-sm text-primary font-medium hover:underline">
                    Mark all read
                </button>
            @endif
        </div>
        @if($unreadCount > 0)
            <p class="text-sm text-ink-muted mt-0.5">{{ $unreadCount }} unread</p>
        @endif
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($notifications as $notification)
            @php $data = $notification->data; $isUnread = is_null($notification->read_at); @endphp
            <div class="flex items-start gap-3 px-4 py-4 {{ $isUnread ? 'bg-primary/3' : 'bg-white' }}">
                {{-- Unread dot --}}
                <div class="mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center">
                    @if($isUnread)
                        <div class="h-2.5 w-2.5 rounded-full bg-primary"></div>
                    @else
                        <div class="h-2.5 w-2.5 rounded-full bg-transparent"></div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-{{ $isUnread ? 'semibold' : 'medium' }} text-ink">
                        {{ $data['title'] ?? 'Notification' }}
                    </p>
                    @if(!empty($data['body']))
                        <p class="text-sm text-ink-muted mt-0.5">{{ $data['body'] }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1.5">
                        <time class="text-xs text-ink-muted">{{ $notification->created_at->diffForHumans() }}</time>
                        @if(!empty($data['action_url']))
                            <a href="{{ $data['action_url'] }}" wire:navigate class="text-xs text-primary hover:underline font-medium">View →</a>
                        @endif
                        @if($isUnread)
                            <button wire:click="markRead('{{ $notification->id }}')" class="text-xs text-ink-muted hover:text-ink">Mark read</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                </div>
                <p class="mt-3 font-semibold text-ink">No notifications yet</p>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $notifications->links() }}</div>
</div>
