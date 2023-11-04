@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
])

@php
    $icon = ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: $icon;
@endphp

@if ($icon instanceof \Illuminate\Contracts\Support\Htmlable || (! \Filament\Support\is_slot_empty($slot)))
    <span {{ $attributes->class([$class]) }}>
        {{ $icon ?? $slot }}
    </span>
@elseif (str_contains($icon, '/'))
    <img
        {{
            $attributes
                ->merge(['src' => $icon])
                ->class([$class])
        }}
    />
@else
    @svg(
        $icon,
        $class,
        array_filter($attributes->getAttributes(), fn (mixed $attribute): bool => $attribute !== null && $attribute !== false),
    )
@endif
