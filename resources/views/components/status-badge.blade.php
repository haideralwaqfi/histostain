@props(['status'])

@php
$config = match($status->value) {
    'pending'     => ['bg-amber-100 text-amber-700',  $status->label()],
    'accepted'    => ['bg-blue-100 text-blue-700',    $status->label()],
    'in_progress' => ['bg-indigo-100 text-indigo-700','In Progress'],
    'on_hold'     => ['bg-orange-100 text-orange-700','On Hold'],
    'completed'   => ['bg-green-100 text-green-700',  $status->label()],
    'cancelled'   => ['bg-gray-100 text-gray-500',    $status->label()],
    default       => ['bg-gray-100 text-gray-500',    $status->label()],
};
@endphp

<span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $config[0] }}">
    {{ $config[1] }}
</span>
