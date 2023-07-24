@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconSize' => null,
    'keyBindings' => null,
    'label' => null,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= match ($size) {
        'xs' => 'sm',
        'sm', 'md' => 'md',
        'lg', 'xl' => 'lg',
    };

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn relative flex items-center justify-center outline-none transition duration-75 disabled:pointer-events-none disabled:opacity-70 rounded-lg',
        match ($size) {
            'xs' => 'h-7 w-7',
            'sm' => 'h-8 w-8',
            'md' => 'h-9 w-9',
            'lg' => 'h-10 w-10',
            'xl' => 'h-11 w-11',
            default => $size,
        },
        match ($color) {
            'gray' => 'text-gray-400 hover:text-gray-500 focus:bg-gray-950/5 focus:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:focus:bg-white/5 dark:focus:text-gray-400',
            default => 'text-custom-500 hover:text-custom-600 focus:bg-custom-50 focus:text-custom-600 dark:text-custom-400 dark:hover:text-custom-300 dark:focus:bg-custom-400/10 dark:focus:text-custom-300',
        },
    ]);

    $buttonStyles = \Filament\Support\get_color_css_variables($color, shades: [50, 300, 400, 500, 600]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn-icon',
        match ($iconSize) {
            'sm' => 'h-4 w-4',
            'md' => 'h-5 w-5',
            'lg' => 'h-6 w-6',
            default => $iconSize,
        },
    ]);

    $badgeClasses = 'absolute start-full top-0 -translate-x-1/2 rounded-md bg-white dark:bg-gray-900';

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
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
                    'title' => $label,
                    'type' => $type,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :alias="$iconAlias"
            :icon="$icon"
            :wire:loading.remove.delay="$hasLoadingIndicator"
            :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
            :class="$iconClasses"
        />

        @if ($hasLoadingIndicator)
            <x-filament::loading-indicator
                wire:loading.delay=""
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses"
            />
        @endif

        @if ($badge)
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </button>
@elseif ($tag === 'a')
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
                ->merge([
                    'title' => $label,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :alias="$iconAlias"
            :icon="$icon"
            :class="$iconClasses"
        />

        @if ($badge)
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
@endif
