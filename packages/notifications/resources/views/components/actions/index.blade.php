@props([
    'actions',
])

<div {{ $attributes->class(['filament-notifications-actions mt-4 flex gap-3']) }}>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
