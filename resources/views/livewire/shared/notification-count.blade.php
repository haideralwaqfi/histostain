<span
    x-on:push-received.window="$wire.onPushReceived()"
    @class([
        'hidden' => $count === 0,
        'absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white leading-none' => $count > 0,
    ])
>{{ $count > 9 ? '9+' : $count }}</span>
