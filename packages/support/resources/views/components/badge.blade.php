@props([
    'color' => 'primary',
    'icon' => null,
    'iconPosition' => null,
])

@php
    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-badge-icon h-4 w-4 flex items-center gap-x-1',
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);
@endphp

<div
    {{
        $attributes
            ->class([
                'fi-badge whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                match ($color) {
                    'gray' => 'bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
                    default => 'bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables($color, shades: [50, 400, 500, 600]) => $color !== 'gray',
            ])
    }}
>
    @if ($icon && $iconPosition === 'before')
        <x-filament::icon :name="$icon" :class="$iconClasses" />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if ($icon && $iconPosition === 'after')
        <x-filament::icon :name="$icon" :class="$iconClasses" />
    @endif
</div>
