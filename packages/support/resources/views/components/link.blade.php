@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
    'indicator' => null,
    'indicatorColor' => 'primary',
    'keyBindings' => null,
    'size' => 'md',
    'tag' => 'a',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= $size;

    $linkClasses = [
        'filament-link inline-flex items-center justify-center gap-0.5 font-medium relative outline-none hover:underline focus:underline disabled:opacity-70 disabled:pointer-events-none',
        'pr-4 rtl:pr-0 rtl:pl-4' => $indicator,
        'opacity-70 pointer-events-none' => $disabled,
        match ($color) {
            'danger' => 'text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400',
            'gray' => 'text-gray-600 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200',
            'primary' => 'text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400',
            'secondary' => 'text-secondary-600 hover:text-secondary-500 dark:text-secondary-500 dark:hover:text-secondary-400',
            'success' => 'text-success-600 hover:text-success-500 dark:text-success-500 dark:hover:text-success-400',
            'warning' => 'text-warning-600 hover:text-warning-500 dark:text-warning-500 dark:hover:text-warning-400',
            default => $color,
        },
        match ($size) {
            'sm' => 'text-sm',
            'md' => 'text-sm',
            'lg' => 'text-base',
        },
    ];

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-link-icon',
        'mr-1 rtl:ml-1' => $iconPosition === 'before',
        'ml-1 rtl:mr-1' => $iconPosition === 'after'
    ]);

    $indicatorClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-link-indicator absolute -top-1 -right-1 inline-flex items-center justify-center h-4 w-4 rounded-full text-[0.5rem] font-medium text-white',
        match ($indicatorColor) {
            'danger' => 'bg-danger-600',
            'gray' => 'bg-gray-600',
            'primary' => 'bg-primary-600',
            'secondary' => 'bg-secondary-600',
            'success' => 'bg-success-600',
            'warning' => 'bg-warning-600',
            default => $indicatorColor,
        },
    ]);

    $hasLoadingIndicator = filled($attributes->get('wire:target')) || filled($attributes->get('wire:click')) || (($type === 'submit') && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($attributes->get('wire:target', $attributes->get('wire:click', $form)), ENT_QUOTES);
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
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        {{ $attributes->class($linkClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-filament::icon
                :name="$icon"
                group="support::link.prefix"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        {{ $slot }}

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :name="$icon"
                group="support::link.suffix"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        @if ($indicator)
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </a>
@elseif ($tag === 'button')
    <button
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'type' => $type,
                ], escape: false)
                ->class($linkClasses)
        }}
    >
        @if ($iconPosition === 'before')
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    group="support::link.prefix"
                    :size="$iconSize"
                    :class="$iconClasses"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    x-cloak="x-cloak"
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
        @endif

        {{ $slot }}

        @if ($iconPosition === 'after')
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    group="support::link.suffix"
                    :size="$iconSize"
                    :class="$iconClasses"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    x-cloak="x-cloak"
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
        @endif

        @if ($indicator)
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </button>
@endif
