@props([
    'color' => 'gray',
    'detail' => null,
    'disabled' => false,
    'icon' => null,
    'iconSize' => 'md',
    'image' => null,
    'keyBindings' => null,
    'tag' => 'button',
])

@php
    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-dropdown-list-item flex w-full items-center gap-2 whitespace-nowrap rounded-md p-2 text-sm transition-colors outline-none disabled:pointer-events-none disabled:opacity-70',
        is_string($color) ? "filament-dropdown-list-item-color-{$color}" : null,
        match ($color) {
            'gray' => 'filament-dropdown-list-item-color-gray text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10 dark:text-gray-200',
            default => 'filament-dropdown-list-item-color-custom text-custom-600 hover:bg-custom-500/10 focus:bg-custom-500/10 dark:text-custom-400',
        },
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($color, shades: [400, 500, 600]) => $color !== 'gray',
    ]);

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

    $iconClasses = 'filament-dropdown-list-item-icon shrink-0';

    $imageClasses = 'filament-dropdown-list-item-image h-5 w-5 shrink-0 rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900';

    $labelClasses = 'filament-dropdown-list-item-label w-full truncate text-start';

    $detailClasses = 'filament-dropdown-list-item-detail ms-auto text-xs';

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        @if ($keyBindings)
            x-data="{}"
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'type' => 'button',
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($icon)
            <x-filament::icon
                :name="$icon"
                alias="support::dropdown.list.item"
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
                wire:loading.delay=""
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses . ' ' . $iconSize"
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
    <a
        @if ($keyBindings)
            x-data="{}"
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        {{
            $attributes
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($icon)
            <x-filament::icon
                :name="$icon"
                alias="support::dropdown.list.item"
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
    <form
        {{ $attributes->only(['action', 'class', 'method', 'wire:submit.prevent']) }}
    >
        @csrf

        <button
            @if ($keyBindings)
                x-data="{}"
                x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
            @endif
            type="submit"
            {{
                $attributes
                    ->except(['action', 'class', 'method', 'wire:submit.prevent'])
                    ->class([$buttonClasses])
                    ->style([$buttonStyles])
            }}
        >
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    alias="support::dropdown.list.item"
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
