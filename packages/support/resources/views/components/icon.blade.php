@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
])

@if (is_string($icon))
    @svg(
        ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: $icon,
        $class,
        array_filter($attributes->getAttributes()),
    )
@else
    <div {{ $attributes->class($class) }}>
        {{ $icon ?? $slot }}
    </div>
@endif
