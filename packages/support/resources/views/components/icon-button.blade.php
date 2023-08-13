@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconSize' => null,
    'keyBindings' => null,
    'label' => null,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall, 'xs' => IconSize::Small,
        ActionSize::Small, ActionSize::Medium, 'sm', 'md' => IconSize::Medium,
        ActionSize::Large, ActionSize::ExtraLarge, 'lg', 'xl' => IconSize::Large,
    };

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70',
        match ($size) {
            ActionSize::ExtraSmall, 'xs' => 'h-7 w-7',
            ActionSize::Small, 'sm' => 'h-8 w-8',
            ActionSize::Medium, 'md' => 'h-9 w-9',
            ActionSize::Large, 'lg' => 'h-10 w-10',
            ActionSize::ExtraLarge, 'xl' => 'h-11 w-11',
            default => $size,
        },
        match ($color) {
            'gray' => 'text-gray-400 hover:text-gray-500 focus:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus:ring-primary-500',
            default => 'text-custom-500 hover:text-custom-600 focus:ring-custom-600 dark:text-custom-400 dark:hover:text-custom-300 dark:focus:ring-custom-500',
        },
    ]);

    $buttonStyles = \Filament\Support\get_color_css_variables($color, shades: [300, 400, 500, 600]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn-icon',
        match ($iconSize) {
            IconSize::Small, 'sm' => 'h-4 w-4',
            IconSize::Medium, 'md' => 'h-5 w-5',
            IconSize::Large, 'lg' => 'h-6 w-6',
            default => $iconSize,
        },
    ]);

    $badgeClasses = 'absolute start-full top-0 z-[1] -ms-1 -translate-x-1/2 rounded-md bg-white rtl:translate-x-1/2 dark:bg-gray-900';

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first();

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
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
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
            :alias="$iconAlias"
            :icon="$icon"
            :wire:loading.remove.delay="$hasLoadingIndicator"
            :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
            :class="$iconClasses"
        />

        @if ($hasLoadingIndicator)
            <x-filament::loading-indicator
                wire:loading.delay=""
                :wire:target="$loadingIndicatorTarget"
                :class="$iconClasses"
            />
        @endif

        @if (filled($badge))
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
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
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
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
            :alias="$iconAlias"
            :icon="$icon"
            :class="$iconClasses"
        />

        @if (filled($badge))
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
@endif
