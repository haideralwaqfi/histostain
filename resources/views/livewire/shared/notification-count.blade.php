@if($count > 0)
    <span
        x-on:push-received.window="$wire.onPushReceived()"
        class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white leading-none"
    >{{ $count > 9 ? '9+' : $count }}</span>
@else
    {{-- Even at zero we need the listener mounted so arriving pushes trigger a refresh --}}
    <span x-on:push-received.window="$wire.onPushReceived()" class="hidden"></span>
@endif
