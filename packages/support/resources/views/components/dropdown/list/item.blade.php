@props([
    'color' => 'primary',
    'detail' => null,
    'icon' => null,
    'image' => null,
    'keyBindings' => null,
    'tag' => 'button',
])

@php
    $hasHoverAndFocusState = ($tag !== 'a' || filled($attributes->get('href')));

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item group flex w-full items-center whitespace-nowrap rounded-md p-2 text-sm text-gray-900 dark:text-gray-100',
        'focus:outline-none hover:text-white focus:text-white' => $hasHoverAndFocusState,
        'hover:bg-primary-500 focus:bg-primary-500' => ($color === 'primary' || $color === 'gray') && $hasHoverAndFocusState,
        'hover:bg-danger-500 focus:bg-danger-500' => $color === 'danger' && $hasHoverAndFocusState,
        'hover:bg-secondary-500 focus:bg-secondary-500' => $color === 'secondary' && $hasHoverAndFocusState,
        'hover:bg-success-500 focus:bg-success-500' => $color === 'success' && $hasHoverAndFocusState,
        'hover:bg-warning-500 focus:bg-warning-500' => $color === 'warning' && $hasHoverAndFocusState,
    ]);

    $detailClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item-detail ml-auto text-xs text-gray-500',
        'group-hover:text-primary-100 group-focus:text-primary-100' => ($color === 'primary' || $color === 'gray') && $hasHoverAndFocusState,
        'group-hover:text-danger-100 group-focus:text-danger-100' => $color === 'danger' && $hasHoverAndFocusState,
        'group-hover:text-secondary-100 group-focus:text-secondary-100' => $color === 'secondary' && $hasHoverAndFocusState,
        'group-hover:text-success-100 group-focus:text-success-100' => $color === 'success' && $hasHoverAndFocusState,
        'group-hover:text-warning-100 group-focus:text-warning-100' => $color === 'warning' && $hasHoverAndFocusState,
    ]);

    $labelClasses = 'filament-dropdown-list-item-label truncate w-full text-start';

    $iconColor = match ($color) {
        'danger' => 'text-danger-500',
        'gray' => 'text-gray-500',
        'primary' => 'text-primary-500',
        'secondary' => 'text-secondary-500',
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
    };

    $iconSize = 'h-5 w-5';

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item-icon flex-shrink-0 mr-2 rtl:ml-2 rtl:mr-0',
        'group-hover:text-white group-focus:text-white' => $hasHoverAndFocusState,
    ]);

    $imageClasses = 'filament-dropdown-list-item-image flex-shrink-0 h-5 w-5 rounded-full bg-gray-200 bg-cover bg-center mr-2 dark:bg-gray-900 rtl:ml-2 rtl:mr-0';

    $hasLoadingIndicator = filled($attributes->get('wire:target')) || filled($attributes->get('wire:click'));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($attributes->get('wire:target', $attributes->get('wire:click')), ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        type="button"
        wire:loading.attr="disabled"
        {!! $hasLoadingIndicator ? 'wire:loading.class.delay="opacity-70 cursor-wait"' : '' !!}
        {!! ($hasLoadingIndicator && $loadingIndicatorTarget) ? "wire:target=\"{$loadingIndicatorTarget}\"" : '' !!}
        {{ $attributes->class([$buttonClasses]) }}
    >
        @if ($icon)
            <x-filament::icon
                :name="$icon"
                alias="support::dropdown.list.item"
                :color="$iconColor"
                :size="$iconSize"
                :class="$iconClasses"
                :wire:loading.remove.delay="$hasLoadingIndicator"
                :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : false"
            />
        @endif

        @if ($image)
            <div
                class="{{ $imageClasses }}"
                style="background-image: url('{{ $image }}')"
            ></div>
        @endif

        @if ($hasLoadingIndicator)
            <x-filament::loading-indicator
                x-cloak
                wire:loading.delay
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses . ' ' . $iconColor . ' ' . $iconSize"
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
            <x-filament::icon
                :name="$icon"
                alias="support::dropdown.list.item"
                :color="$iconColor"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        @if ($image)
            <div
                class="{{ $imageClasses }}"
                style="background-image: url('{{ $image }}')"
            ></div>
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
    <form {{ $attributes->only(['action', 'class', 'method', 'wire:submit.prevent']) }}>
        @csrf

        <button
            type="submit"
            {{ $attributes->except(['action', 'class', 'method', 'wire:submit.prevent'])->class([$buttonClasses]) }}
        >
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    alias="support::dropdown.list.item"
                    :color="$iconColor"
                    :size="$iconSize"
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
