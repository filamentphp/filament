@props([
    'actions',
])

<div
    {{ $attributes->class(['fi-no-actions mt-3 flex gap-3']) }}
>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
