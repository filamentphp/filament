@props([
    'actions',
])

<div {{ $attributes->class(['filament-tables-actions-container flex flex-wrap items-center gap-4']) }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
