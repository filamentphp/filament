@props([
    'color' => 'primary',
    'darkMode' => false,
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'indicator' => null,
    'keyBindings' => null,
    'label' => null,
    'size' => 'md',
    'solid' => false,
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $buttonClasses = array_merge([
        'filament-icon-button relative flex items-center justify-center rounded-full outline-none hover:bg-gray-500/5 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-70',
        'text-primary-500 focus:bg-primary-500/10' => $color === 'primary',
        'text-danger-500 focus:bg-danger-500/10' => $color === 'danger',
        'text-gray-500 focus:bg-gray-500/10' => $color === 'secondary',
        'dark:text-gray-400' => $color === 'secondary' && $darkMode,
        'text-success-500 focus:bg-success-500/10' => $color === 'success',
        'text-warning-500 focus:bg-warning-500/10' => $color === 'warning',
        'dark:hover:bg-gray-300/5' => $darkMode,
        'h-10 w-10' => $size === 'md',
        'h-8 w-8' => $size === 'sm',
        'h-8 w-8 md:h-10 md:w-10' => $size === 'sm md:md',
        'h-12 w-12' => $size === 'lg',
    ], $solid ? [
        'text-white',
        'bg-danger-600 hover:bg-danger-600/20 hover:text-danger-500 focus:bg-danger-700/20 focus:text-danger-500 focus:ring-offset-danger-700' => $color === 'danger',
        'bg-gray-300 hover:bg-gray-50 hover:text-gray-500 focus:bg-primary-50 focus:text-primary-600 focus:ring-primary-600' => $color === 'secondary',
        'bg-gray-600 hover:bg-gray-600/20 hover:text-gray-500 focus:bg-gray-700/20 focus:text-gray-500 focus:ring-offset-gray-700' => $color === 'gray',
        'bg-primary-600 hover:bg-primary-600/20 hover:text-primary-500 focus:bg-primary-700/20 focus:text-primary-500 focus:ring-offset-primary-700' => $color === 'primary',
        'bg-success-600 hover:bg-success-600/20 hover:text-success-500 focus:bg-success-700/20 focus:text-success-500 focus:ring-offset-success-700' => $color === 'success',
        'bg-warning-600 hover:bg-warning-600/20 hover:text-warning-500 focus:bg-warning-700/20 focus:text-danger-500 focus:ring-offset-warning-700' => $color === 'warning',
        'dark:bg-danger-500 dark:hover:bg-danger-500/20 dark:focus:bg-danger-600/20 dark:focus:ring-offset-danger-600' => $color === 'danger' && $darkMode,
        'dark:bg-gray-400 dark:hover:bg-gray-400/20 dark:focus:bg-gray-600/20 dark:focus:ring-offset-gray-600' => $color === 'gray' && $darkMode,
        'dark:bg-gray-600 dark:hover:bg-gray-500/20 dark:text-gray-200 dark:focus:bg-gray-800/20' => $color === 'secondary' && $darkMode,
        'dark:bg-primary-500 dark:hover:bg-primary-500/20 dark:focus:bg-primary-600/20 dark:focus:ring-offset-primary-600' => $color === 'primary' && $darkMode,
        'dark:bg-success-500 dark:hover:bg-success-500/20 dark:focus:bg-success-600/20 dark:focus:ring-offset-success-600' => $color === 'success' && $darkMode,
        'dark:bg-warning-500 dark:hover:bg-warning-500/20 dark:focus:bg-warning-600/20 dark:focus:ring-offset-warning-600' => $color === 'warning' && $darkMode,
    ] : []);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-icon-button-icon',
        'w-5 h-5' => $size === 'md',
        'w-4 h-4' => $size === 'sm',
        'w-4 h-4 md:w-5 md:h-5' => $size === 'sm md:md',
        'w-6 h-6' => $size === 'lg',
    ]);

    $indicatorClasses = \Illuminate\Support\Arr::toCssClasses(array_merge([
        'filament-icon-button-indicator absolute rounded-full text-xs inline-block w-4 h-4 -top-0.5 -right-0.5'
        ], $solid ? [
            'text-white',
            'bg-danger-600' => $color === 'danger',
            'bg-gray-500' => $color === 'secondary',
            'bg-gray-600' => $color === 'gray',
            'bg-primary-600' => $color === 'primary',
            'bg-success-600' => $color === 'success',
            'bg-warning-600' => $color === 'warning',
        ] : [
            'bg-danger-500/10' => $color === 'danger',
            'bg-gray-500/10' => $color === 'secondary',
            'bg-gray-600/10' => $color === 'gray',
            'bg-primary-500/10' => $color === 'primary',
            'bg-success-500/10' => $color === 'success',
            'bg-warning-500/10' => $color === 'warning'
        ]
    ));

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($label)
            title="{{ $label }}"
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        type="{{ $type }}"
        {!! $disabled ? 'disabled' : '' !!}
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-dynamic-component
            :component="$icon"
            :wire:loading.remove.delay="$hasLoadingIndicator"
            :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : false"
            :class="$iconClasses"
        />

        @if ($hasLoadingIndicator)
            <x-filament-support::loading-indicator
                x-cloak
                wire:loading.delay
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses"
            />
        @endif

        @if ($indicator)
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </button>
@elseif ($tag === 'a')
    <a
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($label)
            title="{{ $label }}"
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-dynamic-component :component="$icon" :class="$iconClasses" />

        @if ($indicator)
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </a>
@endif
