@props([
    'alpineDisabled' => null,
    'alpineValid' => null,
    'disabled' => false,
    'inlinePrefix' => false,
    'inlineSuffix' => false,
    'prefix' => null,
    'prefixActions' => [],
    'prefixIcon' => null,
    'prefixIconColor' => 'gray',
    'prefixIconAlias' => null,
    'suffix' => null,
    'suffixActions' => [],
    'suffixIcon' => null,
    'suffixIconColor' => 'gray',
    'suffixIconAlias' => null,
    'valid' => true,
])

@php
    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );

    $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefix);
    $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffix);

    $hasAlpineDisabledClasses = filled($alpineDisabled);
    $hasAlpineValidClasses = filled($alpineValid);
    $hasAlpineClasses = $hasAlpineDisabledClasses || $hasAlpineValidClasses;

    $actionsClasses = 'flex items-center gap-3';
    $labelClasses = 'fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400';

    $getIconClasses = fn (string | array $color = 'gray'): string => \Illuminate\Support\Arr::toCssClasses([
        'fi-input-wrp-icon size-5',
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $getIconStyles = fn (string | array $color = 'gray'): string => \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [500],
            alias: 'input-wrapper.icon',
        ) => $color !== 'gray',
    ]);

    $wireTarget = $attributes->whereStartsWith(['wire:target'])->first();

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }
@endphp

<div
    @if ($hasAlpineClasses)
        x-bind:class="{
            {{ $hasAlpineDisabledClasses ? "'fi-disabled': {$alpineDisabled}," : null }}
            {{ $hasAlpineValidClasses ? "'fi-invalid': ! ({$alpineValid})," : null }}
        }"
    @endif
    {{
        $attributes
            ->except(['wire:target'])
            ->class([
                'fi-input-wrp',
                'fi-disabled' => (! $hasAlpineClasses) && $disabled,
                'fi-invalid' => (! $hasAlpineClasses) && (! $valid),
            ])
    }}
>
    @if ($hasPrefix || $hasLoadingIndicator)
        <div
            @if (! $hasPrefix)
                wire:loading.delay.{{ config('filament.livewire_loading_delay', 'default') }}.flex
                wire:target="{{ $loadingIndicatorTarget }}"
                wire:key="{{ \Illuminate\Support\Str::random() }}" {{-- Makes sure the loading indicator gets hidden again. --}}
            @endif
            @class([
                'fi-input-wrp-prefix',
                'flex' => $hasPrefix,
                'hidden' => ! $hasPrefix,
                'pe-1' => $inlinePrefix && filled($prefix),
                'pe-2' => $inlinePrefix && blank($prefix),
                'border-e border-gray-200 pe-3 ps-3 dark:border-white/10' => ! $inlinePrefix,
            ])
        >
            @if (count($prefixActions))
                <div class="{{ $actionsClasses }}">
                    @foreach ($prefixActions as $prefixAction)
                        {{ $prefixAction }}
                    @endforeach
                </div>
            @endif

            @if ($prefixIcon)
                <x-filament::icon
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'alias' => $prefixIconAlias,
                                'icon' => $prefixIcon,
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                            ])
                        )
                            ->class([$getIconClasses($prefixIconColor)])
                            ->style([$getIconStyles($prefixIconColor)])
                    "
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => $hasPrefix,
                                'wire:target' => $hasPrefix ? $loadingIndicatorTarget : null,
                            ])
                        )->class([$getIconClasses()])
                    "
                />
            @endif

            @if (filled($prefix))
                <span class="{{ $labelClasses }}">
                    {{ $prefix }}
                </span>
            @endif
        </div>
    @endif

    <div
        @if ($hasLoadingIndicator && (! $hasPrefix))
            @if ($inlinePrefix)
                wire:loading.delay.{{ config('filament.livewire_loading_delay', 'default') }}.class.remove="ps-3"
            @endif

            wire:target="{{ $loadingIndicatorTarget }}"
        @endif
        @class([
            'min-w-0 flex-1',
            'ps-3' => $hasLoadingIndicator && (! $hasPrefix) && $inlinePrefix,
        ])
    >
        {{ $slot }}
    </div>

    @if ($hasSuffix)
        <div
            @class([
                'fi-input-wrp-suffix',
                'ps-1' => $inlineSuffix && filled($suffix),
                'ps-2' => $inlineSuffix && blank($suffix),
                'border-s border-gray-200 ps-3 dark:border-white/10' => ! $inlineSuffix,
            ])
        >
            @if (filled($suffix))
                <span class="{{ $labelClasses }}">
                    {{ $suffix }}
                </span>
            @endif

            @if ($suffixIcon)
                <x-filament::icon
                    :alias="$suffixIconAlias"
                    :icon="$suffixIcon"
                    :class="$getIconClasses($suffixIconColor)"
                    :style="$getIconStyles($suffixIconColor)"
                />
            @endif

            @if (count($suffixActions))
                <div class="{{ $actionsClasses }}">
                    @foreach ($suffixActions as $suffixAction)
                        {{ $suffixAction }}
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
