@props([
    'color' => 'primary',
    'detail' => null,
    'disabled' => false,
    'icon' => null,
    'iconSize' => 'md',
    'image' => null,
    'keyBindings' => null,
    'tag' => 'button',
])

@php
    $hasHoverAndFocusState = ($tag !== 'a' || filled($attributes->get('href')));

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item group flex w-full items-center whitespace-nowrap rounded-md p-2 text-sm text-gray-900 dark:text-gray-100 disabled:opacity-70 disabled:pointer-events-none',
        'opacity-70 pointer-events-none' => $disabled,
        ...$hasHoverAndFocusState
            ? [
                'focus:outline-none hover:text-white focus:text-white',
                match ($color) {
                    'danger' => 'hover:bg-danger-500 focus:bg-danger-500',
                    'gray', null => 'hover:bg-gray-500 focus:bg-gray-500',
                    'primary' => 'hover:bg-primary-500 focus:bg-primary-500',
                    'secondary' => 'hover:bg-secondary-500 focus:bg-secondary-500',
                    'success' => 'hover:bg-success-500 focus:bg-success-500',
                    'warning' => 'hover:bg-warning-500 focus:bg-warning-500',
                    default => $color,
                },
            ]
            : [],
    ]);

    $detailClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item-detail ml-auto text-xs text-gray-500',
        match ($hasHoverAndFocusState) {
            true => match ($color) {
                'danger' => 'group-hover:text-danger-100 group-focus:text-danger-100',
                'gray', null => 'group-hover:text-gray-100 group-focus:text-gray-100',
                'primary' => 'group-hover:text-primary-100 group-focus:text-primary-100',
                'secondary' => 'group-hover:text-secondary-100 group-focus:text-secondary-100',
                'success' => 'group-hover:text-success-100 group-focus:text-success-100',
                'warning' => 'group-hover:text-warning-100 group-focus:text-warning-100',
                default => $color,
            },
            false => null,
        },
    ]);

    $labelClasses = 'filament-dropdown-list-item-label truncate w-full text-start';

    $iconColor = match ($color) {
        'danger' => 'text-danger-500',
        'gray' => 'text-gray-500',
        'primary' => 'text-primary-500',
        'secondary' => 'text-secondary-500',
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
        default => $color,
    };

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

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
    <button {{ $attributes
        ->merge([
            'disabled' => $disabled,
            'type' => 'button',
            'wire:loading.attr' => 'disabled',
            'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
        ], escape: false)
        ->class([$buttonClasses])
    }}>
        @if ($icon)
            <x-filament::icon
                :name="$icon"
                alias="support::dropdown.list.item"
                :color="$iconColor"
                :size="$iconSize"
                :class="$iconClasses"
                :wire:loading.remove.delay="$hasLoadingIndicator"
                :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
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
                x-cloak=""
                wire:loading.delay=""
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
