@props([
    'actions',
])

<div {{ $attributes->class(['flex items-center gap-4']) }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
