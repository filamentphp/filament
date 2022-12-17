@props([
    'actions',
])

<div {{ $attributes->class('filament-global-search-actions mt-4 flex gap-3') }}>
    @foreach ($actions as $action)
        @if(! $action->isHidden())
            {{ $action }}
        @endif
    @endforeach
</div>
