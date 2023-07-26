@props([
    'alpineDisabled' => null,
    'alpineValid' => null,
    'disabled' => false,
    'inlinePrefix' => false,
    'inlineSuffix' => false,
    'prefix' => null,
    'prefixActions' => [],
    'prefixIcon' => null,
    'prefixIconAlias' => null,
    'suffix' => null,
    'suffixActions' => [],
    'suffixIcon' => null,
    'suffixIconAlias' => null,
    'valid' => true,
])

@php
    $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefix);
    $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffix);

    $hasAlpineDisabledClasses = filled($alpineDisabled);
    $hasAlpineValidClasses = filled($alpineValid);
    $hasAlpineClasses = $hasAlpineDisabledClasses || $hasAlpineValidClasses;

    $enabledClasses = 'bg-white focus-within:ring-2 dark:bg-gray-900';
    $disabledClasses = 'bg-gray-50 dark:bg-gray-950';
    $validClasses = 'ring-gray-950/10 dark:ring-white/20';
    $invalidClasses = 'ring-danger-600 dark:ring-danger-400';
    $enabledValidClasses = 'focus-within:ring-primary-600 dark:focus-within:ring-primary-600';
    $enabledInvalidClasses = 'focus-within:ring-danger-600 dark:focus-within:ring-danger-400';

    $affixActionsClasses = '-mx-1.5 flex items-center';
    $affixIconClasses = 'fi-fo-affixes-icon h-5 w-5 text-gray-400 dark:text-gray-500';
    $affixLabelClasses = 'fi-fo-affixes-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400';

    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Forms\Components\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Forms\Components\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );

    $wireTarget = $attributes->whereStartsWith(['wire:target'])->first();

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }
@endphp

<div
    @if ($hasAlpineClasses)
        x-bind:class="{
            {{ $hasAlpineDisabledClasses ? "'{$enabledClasses}': ! ({$alpineDisabled})," : null }}
            {{ $hasAlpineDisabledClasses ? "'{$disabledClasses}': {$alpineDisabled}," : null }}
            {{ $hasAlpineValidClasses ? "'{$validClasses}': {$alpineValid}," : null }}
            {{ $hasAlpineValidClasses ? "'{$invalidClasses}': ! ({$alpineValid})," : null }}
            {{ ($hasAlpineDisabledClasses && $hasAlpineValidClasses) ? "'{$enabledValidClasses}': ! ({$alpineDisabled}) && {$alpineValid}," : null }}
            {{ ($hasAlpineDisabledClasses && $hasAlpineValidClasses) ? "'{$enabledInvalidClasses}': ! ({$alpineDisabled}) && ! ({$alpineValid})," : null }}
        }"
    @endif
    {{
        $attributes
            ->except(['wire:target'])
            ->class([
                'fi-fo-affixes flex rounded-lg shadow-sm ring-1 transition duration-75',
                $enabledClasses => (! $hasAlpineClasses) && (! $disabled),
                $disabledClasses => (! $hasAlpineClasses) && $disabled,
                $validClasses => (! $hasAlpineClasses) && $valid,
                $invalidClasses => (! $hasAlpineClasses) && (! $valid),
                $enabledValidClasses => (! $hasAlpineClasses) && (! $disabled) && $valid,
                $enabledInvalidClasses => (! $hasAlpineClasses) && (! $disabled) && (! $valid),
            ])
    }}
>
    @if ($hasPrefix || $hasLoadingIndicator)
        <div
            @if (! $hasPrefix)
                wire:loading.delay.flex
                wire:target="{{ $loadingIndicatorTarget }}"
                wire:key="{{ \Illuminate\Support\Str::random() }}" {{-- Makes sure the loading indicator gets hidden again. --}}
            @endif
            @class([
                'flex items-center gap-x-3 ps-3',
                'pe-1' => $inlinePrefix && filled($prefix),
                'pe-2' => $inlinePrefix && blank($prefix),
                'border-e border-gray-950/10 pe-3 ps-3 dark:border-white/20' => ! $inlinePrefix,
            ])
        >
            @if (count($prefixActions))
                <div class="{{ $affixActionsClasses }}">
                    @foreach ($prefixActions as $prefixAction)
                        {{ $prefixAction }}
                    @endforeach
                </div>
            @endif

            @if ($prefixIcon)
                <x-filament::icon
                    :alias="$prefixIconAlias"
                    :icon="$prefixIcon"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                    :class="$affixIconClasses"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new \Illuminate\View\ComponentAttributeBag([
                                'wire:loading.delay' => $hasPrefix,
                                'wire:target' => $hasPrefix ? $loadingIndicatorTarget : null,
                            ])
                        )
                    "
                    :class="$affixIconClasses"
                />
            @endif

            @if (filled($prefix))
                <span class="{{ $affixLabelClasses }}">
                    {{ $prefix }}
                </span>
            @endif
        </div>
    @endif

    <div
        @if ($hasLoadingIndicator && (! $hasPrefix))
            @if ($inlinePrefix)
                wire:loading.delay.class.remove="ps-3"
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
                'flex items-center gap-x-3 pe-3',
                'ps-1' => $inlineSuffix && filled($suffix),
                'ps-2' => $inlineSuffix && blank($suffix),
                'border-s border-gray-950/10 ps-3 dark:border-white/20' => ! $inlineSuffix,
            ])
        >
            @if (filled($suffix))
                <span class="{{ $affixLabelClasses }}">
                    {{ $suffix }}
                </span>
            @endif

            @if ($suffixIcon)
                <x-filament::icon
                    :alias="$suffixIconAlias"
                    :icon="$suffixIcon"
                    :class="$affixIconClasses"
                />
            @endif

            @if (count($suffixActions))
                <div class="{{ $affixActionsClasses }}">
                    @foreach ($suffixActions as $suffixAction)
                        {{ $suffixAction }}
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
