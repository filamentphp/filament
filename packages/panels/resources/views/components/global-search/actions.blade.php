@props([
    'actions',
])

<div
    {{ $attributes->class('fi-global-search-result-actions mt-3 flex gap-x-3 px-4 pb-4') }}
>
    @foreach ($actions as $action)
        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach
</div>
