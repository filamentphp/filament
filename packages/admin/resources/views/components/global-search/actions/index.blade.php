@props([
'actions',
])

<div {{ $attributes->class('filament-global-search-actions mt-4 flex gap-3 justify-end') }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
