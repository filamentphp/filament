@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconSize' => null,
    'indicator' => null,
    'indicatorColor' => 'primary',
    'inline' => false,
    'keyBindings' => null,
    'label' => null,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= $size;

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-icon-button relative flex items-center justify-center text-custom-500 outline-none transition disabled:pointer-events-none disabled:opacity-70',
        'rounded-full hover:bg-gray-500/5 focus:bg-custom-500/10 dark:hover:bg-gray-300/5' => ! $inline,
        match ($size) {
            'sm' => 'h-8 w-8',
            'sm md:md' => 'h-8 w-8 md:h-10 md:w-10',
            'md' => 'h-10 w-10',
            'lg' => 'h-12 w-12',
            default => $size,
        },
    ]);

    $buttonStyles = \Filament\Support\get_color_css_variables($color, shades: [500]);

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'sm md:md' => 'h-4 w-4 md:h-5 md:w-5',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

    $iconClasses = 'filament-icon-button-icon';

    $indicatorClasses = 'filament-icon-button-indicator absolute -end-0.5 -top-0.5 inline-flex h-4 w-4 items-center justify-center rounded-full bg-custom-600 text-[0.5rem] font-medium text-white';

    $indicatorStyles = \Filament\Support\get_color_css_variables($indicatorColor, shades: [600]);

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'title' => $label,
                    'type' => $type,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :name="$icon"
            :alias="$iconAlias"
            group="support::icon-button"
            :size="$iconSize"
            :class="$iconClasses"
            :wire:loading.remove.delay="$hasLoadingIndicator"
            :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
        />

        @if ($hasLoadingIndicator)
            <x-filament::loading-indicator
                wire:loading.delay=""
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses . ' ' . $iconSize"
            />
        @endif

        @if ($indicator)
            <span
                class="{{ $indicatorClasses }}"
                style="{{ $indicatorStyles }}"
            >
                {{ $indicator }}
            </span>
        @endif
    </button>
@elseif ($tag === 'a')
    <a
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        {{
            $attributes
                ->merge([
                    'title' => $label,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :name="$icon"
            alias="support::icon-button"
            :size="$iconSize"
            :class="$iconClasses"
        />

        @if ($indicator)
            <span
                class="{{ $indicatorClasses }}"
                style="{{ $indicatorStyles }}"
            >
                {{ $indicator }}
            </span>
        @endif
    </a>
@endif
