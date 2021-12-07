@props([
    'actions',
])

<div {{ $attributes->class(['flex flex-wrap items-center gap-4']) }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
