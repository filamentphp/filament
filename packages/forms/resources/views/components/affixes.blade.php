@props([
    'disabled' => false,
    'inlinePrefix' => false,
    'inlineSuffix' => false,
    'prefix' => null,
    'prefixActions' => [],
    'prefixIcon' => null,
    'prefixIconAlias' => null,
    'statePath' => null,
    'suffix' => null,
    'suffixActions' => [],
    'suffixIcon' => null,
    'suffixIconAlias' => null,
])

@php
    $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefix);
    $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffix);

    $ringClasses = \Illuminate\Support\Arr::toCssClasses([
        'ring-gray-950/10 dark:ring-white/20',
        'focus-within:ring-primary-600 dark:focus-within:ring-primary-600' => ! $disabled,
    ]);

    $errorRingClasses = \Illuminate\Support\Arr::toCssClasses([
        'ring-danger-600 dark:ring-danger-400',
        'focus-within:ring-danger-600 dark:focus-within:ring-danger-400' => ! $disabled,
    ]);

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
    @if ($statePath)
        x-bind:class="
            @js($statePath) in $wire.__instance.snapshot.memo.errors
                ? '{{ $errorRingClasses }}'
                : '{{ $ringClasses }}'
        "
    @endif
    {{
        $attributes
            ->except('wire:target')
            ->class([
                'fi-fo-affixes flex rounded-lg shadow-sm ring-1 transition duration-75',
                $ringClasses => ! $statePath,
                'bg-gray-50 dark:bg-gray-950' => $disabled,
                'bg-white focus-within:ring-2 dark:bg-gray-900' => ! $disabled,
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
                    :name="$prefixIcon"
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
                    :name="$suffixIcon"
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
