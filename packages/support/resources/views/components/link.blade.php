@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
    'keyBindings' => null,
    'size' => 'md',
    'tag' => 'a',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= $size;

    $linkClasses = \Illuminate\Support\Arr::toCssClasses([
        "fi-link fi-link-size-{$size} relative inline-flex items-center justify-center font-semibold outline-none transition duration-75 hover:underline focus:underline disabled:pointer-events-none disabled:opacity-70",
        'pe-4' => $badge,
        'pointer-events-none opacity-70' => $disabled,
        match ($size) {
            'xs' => 'gap-1 text-xs',
            'sm' => 'gap-1 text-sm',
            'md' => 'gap-1.5 text-sm',
            'lg' => 'gap-1.5 text-sm',
            'xl' => 'gap-1.5 text-sm',
        },
        match ($color) {
            'gray' => 'text-gray-700 dark:text-gray-200',
            default => 'text-custom-600 dark:text-custom-400',
        },
    ]);

    $linkStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($color, shades: [300, 400, 500, 600]) => $color !== 'gray',
    ]);

    $iconSize ??= match ($size) {
        'xs', 'sm' => 'sm',
        default => 'md',
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-link-icon',
        match ($iconSize) {
            'sm' => 'h-4 w-4',
            'md' => 'h-5 w-5',
            'lg' => 'h-6 w-6',
            default => $iconSize,
        },
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($color, shades: [500]) => $color !== 'gray',
    ]);

    $badgeClasses = 'absolute -top-1 start-full -ms-1 -translate-x-1/2 rounded-md bg-white dark:bg-gray-900';

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
@endphp

@if ($tag === 'a')
    <a
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
        @endif
        {{
            $attributes
                ->class([$linkClasses])
                ->style([$linkStyles])
        }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
                :style="$iconStyles"
            />
        @endif

        {{ $slot }}

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
                :style="$iconStyles"
            />
        @endif

        @if ($badge)
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
@elseif ($tag === 'button')
    <button
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'type' => $type,
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->class([$linkClasses])
                ->style([$linkStyles])
        }}
    >
        @if ($iconPosition === 'before')
            @if ($icon)
                <x-filament::icon
                    :alias="$iconAlias"
                    :icon="$icon"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                    :class="$iconClasses"
                    :style="$iconStyles"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses"
                    :style="$iconStyles"
                />
            @endif
        @endif

        {{ $slot }}

        @if ($iconPosition === 'after')
            @if ($icon)
                <x-filament::icon
                    :alias="$iconAlias"
                    :icon="$icon"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                    :class="$iconClasses"
                    :style="$iconStyles"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses"
                    :style="$iconStyles"
                />
            @endif
        @endif

        @if ($badge)
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </button>
@endif
