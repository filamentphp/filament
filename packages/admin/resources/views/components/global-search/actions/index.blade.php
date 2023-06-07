@props([
    'actions',
])

<div
    {{ $attributes->class('filament-global-search-actions mt-4 flex gap-3') }}
>
    @foreach ($actions as $action)
        @unless ($action->isHidden())
            {{ $action }}
        @endunless
    @endforeach
</div>
