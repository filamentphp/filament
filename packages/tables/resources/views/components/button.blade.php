@props([
    'color' => 'primary',
    'icon' => null,
    'iconPosition' => 'before',
    'tag' => 'button',
    'type' => 'button',
    'size' => 'md',
])

@php
    $buttonClasses = [
        'inline-flex items-center justify-center font-medium tracking-tight transition rounded-lg focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset',
        'bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700' => $color === 'primary',
        'h-9 px-4' => $size === 'md',
        'text-white shadow focus:ring-white' => $color !== 'secondary',
        'bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700' => $color === 'danger',
        'text-gray-800 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600' => $color === 'secondary',
        'bg-success-600 hover:bg-success-500 focus:bg-success-700 focus:ring-offset-success-700' => $color === 'success',
        'bg-warning-600 hover:bg-warning-500 focus:bg-warning-700 focus:ring-offset-warning-700' => $color === 'warning',
        'h-8 px-3 text-sm' => $size === 'sm',
        'h-11 px-6 text-xl' => $size === 'lg',
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
    ]);
@endphp

@if ($tag === 'button')
    <button
        type="{{ $type }}"
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses"/>
        @endif

        <span>{{ $slot }}</span>

        @if ($icon && $iconPosition === 'after')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </button>
@elseif ($tag === 'a')
    <a {{ $attributes->class($buttonClasses) }}>
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif

        <span>{{ $slot }}</span>

        @if ($icon && $iconPosition === 'after')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </a>
@endif
