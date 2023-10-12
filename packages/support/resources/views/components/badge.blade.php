@props([
    'color' => 'primary',
    'closeIcon' => null,
    'deleteButton' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'size' => 'md',
])

@php
    use Filament\Support\Enums\IconPosition;

    $isDeletable = count($deleteButton?->attributes->getAttributes() ?? []) > 0;

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-badge-icon h-4 w-4',
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
                'fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset',
                match ($size) {
                    'xs' => 'px-0.5 min-w-[theme(spacing.4)] tracking-tighter',
                    'sm' => 'px-1.5 min-w-[theme(spacing.5)] py-0.5 tracking-tight',
                    'md' => 'px-2 min-w-[theme(spacing.6)] py-1',
                },
                match ($color) {
                    'gray' => 'fi-color-gray bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
                    default => 'fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [
                        50,
                        400,
                        600,
                        ...$icon ? [500] : [],
                        ...$isDeletable ? [300, 700] : [],
                    ],
                    alias: 'badge',
                ) => $color !== 'gray',
            ])
    }}
>
    @if ($icon && in_array($iconPosition, [IconPosition::Before, 'before']))
        <x-filament::icon :icon="$icon" :class="$iconClasses" />
    @endif

    <span class="grid">
        <span class="truncate">
            {{ $slot }}
        </span>
    </span>

    @if ($isDeletable)
        <button
            type="button"
            {{
                $deleteButton
                    ->attributes
                    ->except(['label'])
                    ->class([
                        '-my-1 -me-2 -ms-1 flex items-center justify-center p-1 outline-none transition duration-75',
                        match ($color) {
                            'gray' => 'text-gray-700/50 hover:text-gray-700/75 focus:text-gray-700/75 dark:text-gray-300/50 dark:hover:text-gray-300/75 dark:focus:text-gray-300/75',
                            default => 'text-custom-700/50 hover:text-custom-700/75 focus:text-custom-700/75 dark:text-custom-300/50 dark:hover:text-custom-300/75 dark:focus:text-custom-300/75',
                        },
                    ])
            }}
        >
            <x-filament::icon
                alias="badge.delete-button"
                icon="heroicon-m-x-mark"
                class="h-3.5 w-3.5"
            />

            @if (filled($label = $deleteButton->attributes->get('label')))
                <span class="sr-only">
                    {{ $label }}
                </span>
            @endif
        </button>
    @elseif ($icon && in_array($iconPosition, [IconPosition::After, 'after']))
        <x-filament::icon :icon="$icon" :class="$iconClasses" />
    @endif
</div>
