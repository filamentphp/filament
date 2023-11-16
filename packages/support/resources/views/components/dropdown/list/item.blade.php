@php
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'badge' => null,
    'badgeColor' => null,
    'color' => 'gray',
    'disabled' => false,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconSize' => IconSize::Medium,
    'image' => null,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
])

@php
    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-dropdown-list-item flex w-full items-center gap-2 whitespace-nowrap rounded-md p-2 text-sm transition-colors duration-75 outline-none disabled:pointer-events-none disabled:opacity-70',
        'pointer-events-none opacity-70' => $disabled,
        match ($color) {
            'gray' => 'fi-color-gray',
            default => 'fi-color-custom',
        },
        // @deprecated `fi-dropdown-list-item-color-*` has been replaced by `fi-color-gray` and `fi-color-custom`.
        is_string($color) ? "fi-dropdown-list-item-color-{$color}" : null,
        match ($color) {
            'gray' => 'hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5',
            default => 'hover:bg-custom-50 focus-visible:bg-custom-50 dark:hover:bg-custom-400/10 dark:focus-visible:bg-custom-400/10',
        },
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [50, 400, 500, 600],
        ) => $color !== 'gray',
    ]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-dropdown-list-item-icon',
        match ($iconSize) {
            IconSize::Small, 'sm' => 'h-4 w-4',
            IconSize::Medium, 'md' => 'h-5 w-5',
            IconSize::Large, 'lg' => 'h-6 w-6',
            default => $iconSize,
        },
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500 dark:text-custom-400',
        },
    ]);

    $imageClasses = 'fi-dropdown-list-item-image h-5 w-5 rounded-full bg-cover bg-center';

    $labelClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-dropdown-list-item-label flex-1 truncate text-start',
        match ($color) {
            'gray' => 'text-gray-700 dark:text-gray-200',
            default => 'text-custom-600 dark:text-custom-400 ',
        },
    ]);

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

@if ($tag === 'button')
    <button
        @if ($keyBindings || $hasTooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($hasTooltip)
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'type' => 'button',
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($icon)
            <x-filament::icon
                :attributes="
                    \Filament\Support\prepare_inherited_attributes(
                        new \Illuminate\View\ComponentAttributeBag([
                            'alias' => $iconAlias,
                            'icon' => $icon,
                            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : null,
                        ])
                    )->class([$iconClasses])
                "
            />
        @endif

        @if ($image)
            <div
                class="{{ $imageClasses }}"
                style="background-image: url('{{ $image }}')"
            ></div>
        @endif

        @if ($hasLoadingIndicator)
            <x-filament::loading-indicator
                :attributes="
                    \Filament\Support\prepare_inherited_attributes(
                        new \Illuminate\View\ComponentAttributeBag([
                            'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                            'wire:target' => $loadingIndicatorTarget,
                        ])
                    )->class([$iconClasses])
                "
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if (filled($badge))
            <x-filament::badge :color="$badgeColor" size="sm">
                {{ $badge }}
            </x-filament::badge>
        @endif
    </button>
@elseif ($tag === 'a')
    <a
        {{ \Filament\Support\generate_href_html($href, $target === '_blank') }}
        @if ($keyBindings || $hasTooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($hasTooltip)
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
        @endif
        {{
            $attributes
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($icon)
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
            />
        @endif

        @if ($image)
            <div
                class="{{ $imageClasses }}"
                style="background-image: url('{{ $image }}')"
            ></div>
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if (filled($badge))
            <x-filament::badge :color="$badgeColor" size="sm">
                {{ $badge }}
            </x-filament::badge>
        @endif
    </a>
@elseif ($tag === 'form')
    <form
        {{ $attributes->only(['action', 'class', 'method', 'wire:submit']) }}
    >
        @csrf

        <button
            @if ($keyBindings || $hasTooltip)
                x-data="{}"
            @endif
            @if ($keyBindings)
                x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
            @endif
            @if ($hasTooltip)
                x-tooltip="{
                    content: @js($tooltip),
                    theme: $store.theme,
                }"
            @endif
            type="submit"
            {{
                $attributes
                    ->except(['action', 'class', 'method', 'wire:submit'])
                    ->class([$buttonClasses])
                    ->style([$buttonStyles])
            }}
        >
            @if ($icon)
                <x-filament::icon
                    :alias="$iconAlias"
                    :icon="$icon"
                    :class="$iconClasses"
                />
            @endif

            <span class="{{ $labelClasses }}">
                {{ $slot }}
            </span>

            @if (filled($badge))
                <x-filament::badge :color="$badgeColor" size="sm">
                    {{ $badge }}
                </x-filament::badge>
            @endif
        </button>
    </form>
@endif
