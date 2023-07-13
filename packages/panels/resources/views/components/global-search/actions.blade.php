@props([
    'actions',
])

<div
    {{ $attributes->class('fi-global-search-actions mt-4 flex gap-3') }}
>
    @foreach ($actions as $action)
        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach
</div>
