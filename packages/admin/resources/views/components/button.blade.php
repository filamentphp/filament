@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'tag' => 'button',
    'type' => 'button',
    'size' => 'md',
])

@php
    $buttonClasses = [
        'inline-flex items-center justify-center font-medium tracking-tight rounded-lg focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset',
        'bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700' => $color === 'primary',
        'h-9 px-4' => $size === 'md',
        'text-white shadow focus:ring-white' => $color !== 'secondary',
        'bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700' => $color === 'danger',
        'text-gray-800 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600' => $color === 'secondary',
        'bg-success-600 hover:bg-success-500 focus:bg-success-700 focus:ring-offset-success-700' => $color === 'success',
        'bg-warning-600 hover:bg-warning-500 focus:bg-warning-700 focus:ring-offset-warning-700' => $color === 'warning',
        'opacity-75 cursor-not-allowed' => $disabled,
        'h-8 px-3 text-sm' => $size === 'sm',
        'h-11 px-6 text-xl' => $size === 'lg',
        'filament-button',
    ];

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'w-6 h-6' => $size === 'md',
        'w-7 h-7' => $size === 'lg',
        'w-5 h-5' => $size === 'sm',
        'mr-1 -ml-2 rtl:ml-1 rtl:-mr-2' => ($iconPosition === 'before') && ($size === 'md'),
        'mr-2 -ml-3 rtl:ml-2 rtl:-mr-3' => ($iconPosition === 'before') && ($size === 'lg'),
        'mr-1 -ml-1.5 rtl:ml-1 rtl:-mr-1.5' => ($iconPosition === 'before') && ($size === 'sm'),
        'ml-1 -mr-2 rtl:mr-1 rtl:-ml-2' => ($iconPosition === 'after') && ($size === 'md'),
        'ml-2 -mr-3 rtl:mr-2 rtl:-ml-3' => ($iconPosition === 'after') && ($size === 'lg'),
        'ml-1 -mr-1.5 rtl:mr-1 rtl:-ml-1.5' => ($iconPosition === 'after') && ($size === 'sm'),
        'filament-button-icon',
    ]);

    $hasLoadingIndicator = filled($attributes->get('wire:click')) || (($type === 'submit') && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($attributes->get('wire:click', $form), ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        type="{{ $type }}"
        wire:loading.attr="disabled"
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses"/>
        @elseif ($hasLoadingIndicator)
            <svg
                wire:loading
                {{ $loadingIndicatorTarget ? "wire:target={$loadingIndicatorTarget}" : '' }}
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
        wire:loading.attr="disabled"
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @elseif ($hasLoadingIndicator)
            <svg
                wire:loading
                {{ $loadingIndicatorTarget ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' }}
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
