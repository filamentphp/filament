@props([
    'actions',
])

<div {{ $attributes->class(['flex flex-wrap items-center gap-4', 'filament-tables-components-actions']) }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
