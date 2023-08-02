@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
])

@php
    $icon = ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: $icon;
@endphp

@if ($icon instanceof \Illuminate\Contracts\Support\Htmlable)
    <div {{ $attributes->class($class) }}>
        {{ $icon ?? $slot }}
    </div>
@elseif (str_contains($icon, '/'))
    <img
        {{
            $attributes
                ->merge(['src' => $icon])
                ->class($class)
        }}
    />
@else
    @svg(
        $icon,
        $class,
        array_filter($attributes->getAttributes()),
    )
@endif
