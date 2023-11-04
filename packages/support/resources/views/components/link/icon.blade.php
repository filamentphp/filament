@php
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'alias' => null,
    'color',
    'hasLoadingIndicator' => false,
    'icon' => null,
    'isLoadingIndicator' => false,
    'loadingIndicatorTarget',
    'size',
])

<x-filament::icon
    :alias="$alias"
    :icon="$icon"
    :wire:target="$loadingIndicatorTarget"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => $isLoadingIndicator ? '' : null,
                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
            ], escape: false)
            ->class([
                'fi-link-icon',
                'animate-spin' => $isLoadingIndicator, // TODO: fix spin animation not centered
                match ($size) {
                    IconSize::Small => 'h-4 w-4',
                    IconSize::Medium => 'h-5 w-5',
                    IconSize::Large => 'h-6 w-6',
                    default => $size,
                },
                match ($color) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'text-custom-600 dark:text-custom-400',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [500],
                ) => $color !== 'gray',
            ])
    "
>
    {{ $slot }}
</x-filament::icon>
