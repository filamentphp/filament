@props([
    'actions',
])

<div {{ $attributes->class('flex items-center gap-4 -my-2') }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
