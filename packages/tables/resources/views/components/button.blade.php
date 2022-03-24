@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'outlined' => false,
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
    'size' => 'md',
])

@php
    $buttonClasses = array_merge([
        'inline-flex items-center justify-center font-medium tracking-tight rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button',
        'dark:focus:ring-offset-0' => config('tables.dark_mode'),
        'opacity-70 cursor-not-allowed' => $disabled,
        'h-9 px-4' => $size === 'md',
        'h-8 px-3 text-sm' => $size === 'sm',
        'h-11 px-6 text-xl' => $size === 'lg',
    ], $outlined ? [
        'shadow focus:ring-white' => $color !== 'secondary',
        'text-primary-600 border-primary-600 hover:bg-primary-600/20 focus:bg-primary-700/20 focus:ring-offset-primary-700' => $color === 'primary',
        'text-success-600 border-success-600 hover:bg-success-600/20 focus:bg-success-700/20 focus:ring-offset-success-700' => $color === 'success',
        'text-danger-600 border-danger-600 hover:bg-danger-600/20 focus:bg-danger-700/20 focus:ring-offset-danger-700' => $color === 'danger',
        'text-warning-600 border-warning-600 hover:bg-warning-600/20 focus:bg-warning-700/20 focus:ring-offset-warning-700' => $color === 'warning',
        'text-gray-600 border-gray-600 hover:bg-gray-600/20 focus:bg-gray-700/20 focus:ring-offset-gray-700' => $color === 'gray',
        'text-gray-800 border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600' => $color === 'secondary',
        'dark:text-primary-500 dark:border-primary-500 dark:hover:bg-primary-500/20 dark:focus:bg-primary-600/20 dark:focus:ring-offset-primary-600' => $color === 'primary' && config('tables.dark_mode'),
        'dark:text-success-500 dark:border-success-500 dark:hover:bg-success-500/20 dark:focus:bg-success-600/20 dark:focus:ring-offset-success-600' => $color === 'success' && config('tables.dark_mode'),
        'dark:text-danger-500 dark:border-danger-500 dark:hover:bg-danger-500/20 dark:focus:bg-danger-600/20 dark:focus:ring-offset-danger-600' => $color === 'danger' && config('tables.dark_mode'),
        'dark:text-warning-500 dark:border-warning-500 dark:hover:bg-warning-500/20 dark:focus:bg-warning-600/20 dark:focus:ring-offset-warning-600' => $color === 'warning' && config('tables.dark_mode'),
        'dark:text-gray-400 dark:border-gray-400 dark:hover:bg-gray-400/20 dark:focus:bg-gray-600/20 dark:focus:ring-offset-gray-600' => $color === 'gray' && config('tables.dark_mode'),
        'dark:border-gray-600 dark:hover:border-gray-500 dark:hover:hover:bg-gray-500/20 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800/20' => $color === 'secondary' && config('tables.dark_mode'),
    ] : [
        'text-white shadow focus:ring-white border-transparent' => $color !== 'secondary',
        'bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700' => $color === 'primary',
        'bg-success-600 hover:bg-success-500 focus:bg-success-700 focus:ring-offset-success-700' => $color === 'success',
        'bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700' => $color === 'danger',
        'bg-warning-600 hover:bg-warning-500 focus:bg-warning-700 focus:ring-offset-warning-700' => $color === 'warning',
        'bg-gray-600 hover:bg-gray-500 focus:bg-gray-700 focus:ring-offset-gray-700' => $color === 'gray',
        'text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600' => $color === 'secondary',
        'dark:bg-gray-800 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800' => $color === 'secondary' && config('tables.dark_mode'),
    ]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-icon',
        'w-6 h-6' => $size === 'md',
        'w-7 h-7' => $size === 'lg',
        'w-5 h-5' => $size === 'sm',
        'mr-1 -ml-2 rtl:ml-1 rtl:-mr-2' => ($iconPosition === 'before') && ($size === 'md'),
        'mr-2 -ml-3 rtl:ml-2 rtl:-mr-3' => ($iconPosition === 'before') && ($size === 'lg'),
        'mr-1 -ml-1.5 rtl:ml-1 rtl:-mr-1.5' => ($iconPosition === 'before') && ($size === 'sm'),
        'ml-1 -mr-2 rtl:mr-1 rtl:-ml-2' => ($iconPosition === 'after') && ($size === 'md'),
        'ml-2 -mr-3 rtl:mr-2 rtl:-ml-3' => ($iconPosition === 'after') && ($size === 'lg'),
        'ml-1 -mr-1.5 rtl:mr-1 rtl:-ml-1.5' => ($iconPosition === 'after') && ($size === 'sm'),
    ]);

    $hasLoadingIndicator = filled($attributes->get('wire:click')) || (($type === 'submit') && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($attributes->get('wire:click', $form), ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        @if ($tooltip)
            x-data="{}"
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        type="{{ $type }}"
        wire:loading.attr="disabled"
        {!! $hasLoadingIndicator ? 'wire:loading.class="opacity-70 cursor-wait"' : '' !!}
        {!! ($hasLoadingIndicator && $loadingIndicatorTarget) ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' !!}
        {!! $disabled ? 'disabled' : '' !!}
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses"/>
        @elseif ($hasLoadingIndicator)
            <svg
                wire:loading
                {!! $loadingIndicatorTarget ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' !!}
                @class([$iconClasses, 'animate-spin'])
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
            >
                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" />
            </svg>
        @endif

        <span>{{ $slot }}</span>

        @if ($icon && $iconPosition === 'after')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </button>
@elseif ($tag === 'a')
    <a
        @if ($tooltip)
            x-data="{}"
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        wire:loading.attr="disabled"
        {!! $hasLoadingIndicator ? 'wire:loading.class="opacity-70 cursor-wait"' : '' !!}
        {!! ($hasLoadingIndicator && $loadingIndicatorTarget) ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' !!}
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @elseif ($hasLoadingIndicator)
            <svg
                wire:loading
                {!! $loadingIndicatorTarget ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' !!}
                @class([$iconClasses, 'animate-spin'])
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
            >
                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" />
            </svg>
        @endif

        <span>{{ $slot }}</span>

        @if ($icon && $iconPosition === 'after')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </a>
@endif
