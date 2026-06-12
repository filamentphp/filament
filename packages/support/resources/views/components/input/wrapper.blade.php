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
    use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
    use Illuminate\View\ComponentAttributeBag;

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

    $wireTarget = $attributes->whereStartsWith(['wire:target'])->first();

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }

    $hasFocusInputListener = $attributes->has('x-on:focus-input.stop');
    $canClickPrefixAffix = $hasFocusInputListener && ($prefixIcon || filled($prefix));
    $canClickSuffixAffix = $hasFocusInputListener && ($suffixIcon || filled($suffix));
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
            ->except(['wire:target', 'tabindex'])
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
            @if ($canClickPrefixAffix)
                x-on:click="$dispatch('focus-input')"
            @endif
            @class([
                'fi-input-wrp-prefix',
                'fi-input-wrp-prefix-has-content' => $hasPrefix,
                'fi-inline' => $inlinePrefix,
                'fi-input-wrp-prefix-has-label' => filled($prefix),
            ])
        >
            @if (count($prefixActions))
                <div
                    @class(['fi-input-wrp-actions'])
                    @if ($canClickPrefixAffix) x-on:click.stop @endif
                >
                    @foreach ($prefixActions as $prefixAction)
                        {{ $prefixAction }}
                    @endforeach
                </div>
            @endif

            {{
                \Filament\Support\generate_icon_html($prefixIcon, $prefixIconAlias, (new \Illuminate\View\ComponentAttributeBag)
                    ->merge([
                        'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                        'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                    ], escape: false)
                    ->color(IconComponent::class, $prefixIconColor))
            }}

            @if ($hasLoadingIndicator)
                {{
                    \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                        'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => $hasPrefix,
                        'wire:target' => $hasPrefix ? $loadingIndicatorTarget : null,
                    ]))->color(IconComponent::class, 'gray'))
                }}
            @endif

            @if (filled($prefix))
                <span class="fi-input-wrp-label">
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
            'fi-input-wrp-content-ctn',
            'fi-input-wrp-content-ctn-ps' => $hasLoadingIndicator && (! $hasPrefix) && $inlinePrefix,
        ])
    >
        {{ $slot }}
    </div>

    @if ($hasSuffix)
        <div
            @if ($canClickSuffixAffix)
                x-on:click="$dispatch('focus-input')"
            @endif
            @class([
                'fi-input-wrp-suffix',
                'fi-inline' => $inlineSuffix,
                'fi-input-wrp-suffix-has-label' => filled($suffix),
            ])
        >
            @if (filled($suffix))
                <span class="fi-input-wrp-label">
                    {{ $suffix }}
                </span>
            @endif

            {{
                \Filament\Support\generate_icon_html($suffixIcon, $suffixIconAlias, (new \Illuminate\View\ComponentAttributeBag)
                    ->merge([
                        'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                        'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                    ], escape: false)
                    ->color(IconComponent::class, $suffixIconColor))
            }}

            @if (count($suffixActions))
                <div
                    @class(['fi-input-wrp-actions'])
                    @if ($canClickSuffixAffix) x-on:click.stop @endif
                >
                    @foreach ($suffixActions as $suffixAction)
                        {{ $suffixAction }}
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
