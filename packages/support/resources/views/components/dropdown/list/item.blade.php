@props([
    'color' => 'primary',
    'darkMode' => false,
    'detail' => null,
    'icon' => null,
    'keyBindings' => null,
    'tag' => 'button',
    'type' => 'button',
])

@php
    $hasHoverAndFocusState = ($tag !== 'a' || filled($attributes->get('href')));

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item filament-dropdown-item group flex w-full items-center whitespace-nowrap rounded-md p-2 text-sm outline-none',
        'hover:text-white focus:text-white' => $hasHoverAndFocusState,
        'hover:bg-primary-500 focus:bg-primary-500' => ($color === 'primary' || $color === 'secondary') && $hasHoverAndFocusState,
        'hover:bg-danger-500 focus:bg-danger-500' => $color === 'danger' && $hasHoverAndFocusState,
        'hover:bg-success-500 focus:bg-success-500' => $color === 'success' && $hasHoverAndFocusState,
        'hover:bg-warning-500 focus:bg-warning-500' => $color === 'warning' && $hasHoverAndFocusState,
    ]);

    $detailClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item-detail ml-auto text-xs text-gray-500',
        'group-hover:text-primary-100 group-focus:text-primary-100' => ($color === 'primary' || $color === 'secondary') && $hasHoverAndFocusState,
        'group-hover:text-danger-100 group-focus:text-danger-100' => $color === 'danger' && $hasHoverAndFocusState,
        'group-hover:text-success-100 group-focus:text-success-100' => $color === 'success' && $hasHoverAndFocusState,
        'group-hover:text-warning-100 group-focus:text-warning-100' => $color === 'warning' && $hasHoverAndFocusState,
    ]);

    $labelClasses = 'filament-dropdown-list-item-label w-full truncate text-start';

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item-icon mr-2 h-5 w-5 rtl:ml-2 rtl:mr-0',
        'group-hover:text-white group-focus:text-white' => $hasHoverAndFocusState,
        'text-primary-500' => $color === 'primary',
        'text-danger-500' => $color === 'danger',
        'text-gray-500' => $color === 'secondary',
        'text-success-500' => $color === 'success',
        'text-warning-500' => $color === 'warning',
    ]);

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        type="{{ $type }}"
        wire:loading.attr="disabled"
        {!! $hasLoadingIndicator ? 'wire:loading.class.delay="opacity-70 cursor-wait"' : '' !!}
        {!! ($hasLoadingIndicator && $loadingIndicatorTarget) ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' !!}
        {{ $attributes->class([$buttonClasses]) }}
    >
        @if ($icon)
            <x-dynamic-component
                :component="$icon"
                :wire:loading.remove.delay="$hasLoadingIndicator"
                :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : false"
                :class="$iconClasses"
            />
        @endif

        @if ($hasLoadingIndicator)
            <x-filament-support::loading-indicator
                x-cloak
                wire:loading.delay
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses"
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if ($detail)
            <span class="{{ $detailClasses }}">
                {{ $detail }}
            </span>
        @endif
    </button>
@elseif ($tag === 'a')
    <a {{ $attributes->class([$buttonClasses]) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if ($detail)
            <span class="{{ $detailClasses }}">
                {{ $detail }}
            </span>
        @endif
    </a>
@elseif ($tag === 'form')
    <form
        {{ $attributes->only(['action', 'class', 'method', 'wire:submit.prevent']) }}
    >
        @csrf

        <button
            type="submit"
            {{ $attributes->except(['action', 'class', 'method', 'wire:submit.prevent'])->class([$buttonClasses]) }}
        >
            @if ($icon)
                <x-dynamic-component
                    :component="$icon"
                    :class="$iconClasses"
                />
            @endif

            <span class="{{ $labelClasses }}">
                {{ $slot }}
            </span>

            @if ($detail)
                <span class="{{ $detailClasses }}">
                    {{ $detail }}
                </span>
            @endif
        </button>
    </form>
@endif
