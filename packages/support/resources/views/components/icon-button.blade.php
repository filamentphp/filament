@props([
    'color' => 'primary',
    'darkMode' => false,
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'keyBindings' => null,
    'indicator' => null,
    'label' => null,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $buttonClasses = [
        'filament-icon-button flex items-center justify-center rounded-full relative outline-none hover:bg-gray-500/5 disabled:opacity-70 disabled:cursor-not-allowed disabled:pointer-events-none',
        'text-primary-500 focus:bg-primary-500/10' => $color === 'primary',
        'text-danger-500 focus:bg-danger-500/10' => $color === 'danger',
        'text-gray-500 focus:bg-gray-500/10' => $color === 'secondary',
        'text-success-500 focus:bg-success-500/10' => $color === 'success',
        'text-warning-500 focus:bg-warning-500/10' => $color === 'warning',
        'dark:hover:bg-gray-300/5' => $darkMode,
        'w-10 h-10' => $size === 'md',
        'w-8 h-8' => $size === 'sm',
        'w-8 h-8 md:w-10 md:h-10' => $size === 'sm md:md',
        'w-12 h-12' => $size === 'lg',
    ];

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-icon-button-icon',
        'w-5 h-5' => $size === 'md',
        'w-4 h-4' => $size === 'sm',
        'w-4 h-4 md:w-5 md:h-5' => $size === 'sm md:md',
        'w-6 h-6' => $size === 'lg',
    ]);

    $indicatorClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-icon-button-indicator absolute rounded-full text-xs inline-block w-4 h-4 -top-0.5 -right-0.5',
        'bg-primary-500/10' => $color === 'primary',
        'bg-danger-500/10' => $color === 'danger',
        'bg-gray-500/10' => $color === 'secondary',
        'bg-success-500/10' => $color === 'success',
        'bg-warning-500/10' => $color === 'warning',
    ]);

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
