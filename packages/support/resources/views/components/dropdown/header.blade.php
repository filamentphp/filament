@props([
    'color' => 'primary',
    'darkMode' => false,
    'icon' => null,
    'tag' => 'div',
])

@php
    $headerClasses = 'filament-dropdown-header flex w-full p-3 text-sm';

    $iconSize = 'h-5 w-5';

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-header-icon mr-2 rtl:ml-2 rtl:mr-0',
        'text-primary-500' => $color === 'primary',
        'text-danger-500' => $color === 'danger',
        'text-gray-500' => $color === 'secondary',
        'text-success-500' => $color === 'success',
        'text-warning-500' => $color === 'warning',
    ]);

    $labelClasses = 'filament-dropdown-header-label';
@endphp

@if ($tag === 'div')
    <div {{ $attributes->class([$headerClasses]) }}>
        @if ($icon)
            <x-filament-support::icon
                :name="$icon"
                alias="support::dropdown.header"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>
    </div>
@elseif ($tag === 'a')
    <a {{ $attributes->class([$headerClasses]) }}>
        @if ($icon)
            <x-filament-support::icon
                :name="$icon"
                alias="support::dropdown.header"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>
    </a>
@endif
