@props([
    'actions',
])

<div {{ $attributes->class(['flex items-center justify-center space-x-4']) }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
