@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
    'indicator' => null,
    'indicatorColor' => 'primary',
    'keyBindings' => null,
    'labeledFrom' => null,
    'labelSrOnly' => false,
    'outlined' => false,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        ...[
            'filament-button relative grid-flow-col items-center justify-center rounded-lg border font-medium outline-none transition-colors focus:ring-2 disabled:pointer-events-none disabled:opacity-70',
            is_string($color) ? "filament-button-color-{$color}" : null,
            match ($size) {
                'xs' => 'filament-button-size-xs gap-1.5 px-[calc(theme(spacing.3)-1px)] py-[calc(theme(spacing.2)-1px)] text-xs',
                'sm' => 'filament-button-size-sm gap-1.5 px-[calc(theme(spacing.[3.5])-1px)] py-[calc(theme(spacing.2)-1px)] text-sm',
                'md' => 'filament-button-size-md gap-2 px-[calc(theme(spacing.4)-1px)] py-[calc(theme(spacing[2.5])-1px)] text-sm',
                'lg' => 'filament-button-size-lg gap-2 px-[calc(theme(spacing.5)-1px)] py-[calc(theme(spacing.3)-1px)] text-sm',
                'xl' => 'filament-button-size-xl gap-2 px-[calc(theme(spacing.6)-1px)] py-[calc(theme(spacing.3)-1px)] text-base',
            },
            'hidden' => $labeledFrom,
            match ($labeledFrom) {
                'sm' => 'sm:inline-grid',
                'md' => 'md:inline-grid',
                'lg' => 'lg:inline-grid',
                'xl' => 'xl:inline-grid',
                '2xl' => '2xl:inline-grid',
                default => 'inline-grid',
            },
        ],
        ...(
            $outlined
                ? [
                    'filament-button-outlined',
                    match ($color) {
                        'gray' => 'border-gray-300 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10 focus:ring-primary-500/50 dark:border-gray-600 dark:text-gray-200',
                        default => 'border-custom-600 text-custom-600 hover:bg-custom-500/10 focus:bg-custom-500/10 focus:ring-custom-500/50 dark:border-custom-400 dark:text-custom-400',
                    },
                ]
                : [
                    'shadow',
                    match ($color) {
                        'gray' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:border-transparent focus:bg-gray-50 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700',
                        default => 'border-transparent bg-custom-600 text-white hover:bg-custom-500 focus:bg-custom-500 focus:ring-custom-500/50',
                    },
                ]
        ),
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: $outlined ? [400, 500, 600] : [500, 600],
        ) => $color !== 'gray',
    ]);

    $iconSize ??= match ($size) {
        'xs', 'sm' => 'sm',
        'md', 'lg', 'xl' => 'md',
        default => 'md',
    };

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-icon',
        match ($iconPosition) {
            'before' => match ($size) {
                'xs' => '-ms-0.5',
                'sm' => '-ms-0.5',
                'md' => '-ms-1',
                'lg' => '-ms-1',
                'xl' => '-ms-1',
                default => null,
            },
            'after' => match ($size) {
                'xs' => '-me-0.5',
                'sm' => '-me-0.5',
                'md' => '-me-1',
                'lg' => '-me-1',
                'xl' => '-me-1',
                default => null,
            },
            default => null,
        } => ! $labelSrOnly,
    ]);

    $indicatorClasses = 'filament-button-indicator absolute -end-1 -top-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-custom-600 text-[0.5rem] font-medium text-white';

    $indicatorStyles = \Filament\Support\get_color_css_variables($indicatorColor, shades: [600]);

    $labelClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-label truncate',
        'sr-only' => $labelSrOnly,
    ]);

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasFileUploadLoadingIndicator = $type === 'submit' && filled($form);
    $hasLoadingIndicator = filled($wireTarget) || $hasFileUploadLoadingIndicator;

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
@endphp

@if ($labeledFrom)
    <x-filament::icon-button
        :color="$color"
        :disabled="$disabled"
        :form="$form"
        :icon="$icon"
        :icon-size="$iconSize"
        :indicator="$indicator"
        :indicator-color="$indicatorColor"
        :key-bindings="$keyBindings"
        :label="$slot"
        :size="$size"
        :tag="$tag"
        :tooltip="$tooltip"
        :type="$type"
        :class="match ($labeledFrom) {
            'sm' => 'sm:hidden',
            'md' => 'md:hidden',
            'lg' => 'lg:hidden',
            'xl' => 'xl:hidden',
            '2xl' => '2xl:hidden',
            default => 'hidden',
        }"
        :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    />
@endif

@if ($tag === 'button')
    <button
        @if (($keyBindings || $tooltip) && (! $hasFileUploadLoadingIndicator))
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        @if ($hasFileUploadLoadingIndicator)
            x-data="{
                form: null,
                isUploadingFile: false,
            }"
            x-init="
                form = $el.closest('form')

                form?.addEventListener('file-upload-started', () => {
                    isUploadingFile = true
                })

                form?.addEventListener('file-upload-finished', () => {
                    isUploadingFile = false
                })
            "
            x-bind:class="{ 'enabled:opacity-70 enabled:cursor-wait': isUploadingFile }"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'type' => $type,
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                    'x-bind:disabled' => $hasFileUploadLoadingIndicator ? 'isUploadingFile' : false,
                ], escape: false)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($iconPosition === 'before')
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    group="support::button.prefix"
                    :size="$iconSize"
                    :class="$iconClasses"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif

            @if ($hasFileUploadLoadingIndicator)
                <x-filament::loading-indicator
                    x-show="isUploadingFile"
                    x-cloak="x-cloak"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
        @endif

        <span
            @if ($hasFileUploadLoadingIndicator)
                x-show="! isUploadingFile"
            @endif
            class="{{ $labelClasses }}"
        >
            {{ $slot }}
        </span>

        @if ($hasFileUploadLoadingIndicator)
            <span x-show="isUploadingFile" x-cloak>
                {{ __('filament-support::components/button.messages.uploading_file') }}
            </span>
        @endif

        @if ($iconPosition === 'after')
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    group="support::button.suffix"
                    :size="$iconSize"
                    :class="$iconClasses"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif

            @if ($hasFileUploadLoadingIndicator)
                <x-filament::loading-indicator
                    x-show="isUploadingFile"
                    x-cloak="x-cloak"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
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
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-filament::icon
                :name="$icon"
                group="support::button.prefix"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :name="$icon"
                group="support::button.suffix"
                :size="$iconSize"
                :class="$iconClasses"
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
    </a>
@endif
