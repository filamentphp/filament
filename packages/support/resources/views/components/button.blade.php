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
    'labelSrOnly' => false,
    'outlined' => false,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $buttonClasses = array_merge(
        [
            'filament-button inline-grid grid-flow-col items-center justify-center rounded-lg border font-medium relative outline-none transition-colors focus:ring-2 disabled:pointer-events-none disabled:opacity-70',
            match ($size) {
                'xs' => 'filament-button-size-xs gap-1.5 py-[calc(theme(spacing.2)-1px)] px-[calc(theme(spacing.3)-1px)] text-xs',
                'sm' => 'filament-button-size-sm gap-1.5 py-[calc(theme(spacing.2)-1px)] px-[calc(theme(spacing.[3.5])-1px)] text-sm',
                'md' => 'filament-button-size-md gap-2 py-[calc(theme(spacing[2.5])-1px)] px-[calc(theme(spacing.4)-1px)] text-sm',
                'lg' => 'filament-button-size-lg gap-2 py-[calc(theme(spacing.3)-1px)] px-[calc(theme(spacing.5)-1px)] text-sm',
                'xl' => 'filament-button-size-xl gap-2 py-[calc(theme(spacing.3)-1px)] px-[calc(theme(spacing.6)-1px)] text-base',
            },
        ],
        $outlined
            ? [
                'filament-button-outlined',
                match ($color) {
                    'danger' => 'filament-button-color-danger border-danger-600 text-danger-600 hover:bg-danger-500/10 focus:bg-danger-500/10 focus:ring-danger-500/50 dark:border-danger-400 dark:text-danger-400',
                    'gray' => 'filament-button-color-gray border-gray-300 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10 focus:ring-primary-500/50 dark:border-gray-600 dark:text-gray-200',
                    'primary' => 'filament-button-color-primary border-primary-600 text-primary-600 hover:bg-primary-500/10 focus:bg-primary-500/10 focus:ring-primary-500/50 dark:border-primary-400 dark:text-primary-400',
                    'secondary' => 'filament-button-color-secondary border-secondary-600 text-secondary-600 hover:bg-secondary-500/10 focus:bg-secondary-500/10 focus:ring-secondary-500/50 dark:border-secondary-400 dark:text-secondary-400',
                    'success' => 'filament-button-color-success border-success-600 text-success-600 hover:bg-success-500/10 focus:bg-success-500/10 focus:ring-success-500/50 dark:border-success-400 dark:text-success-400',
                    'warning' => 'filament-button-color-warning border-warning-600 text-warning-600 hover:bg-warning-500/10 focus:bg-warning-500/10 focus:ring-warning-500/50 dark:border-warning-400 dark:text-warning-400',
                    default => $color,
                },
            ]
            : [
                'shadow',
                match ($color) {
                    'danger' => 'filament-button-color-danger border-transparent bg-danger-600 text-white hover:bg-danger-500 focus:bg-danger-500 focus:ring-danger-500/50',
                    'gray' => 'filament-button-color-gray border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:ring-primary-500 focus:border-transparent dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700',
                    'primary' => 'filament-button-color-primary border-transparent bg-primary-600 text-white hover:bg-primary-500 focus:bg-primary-500 focus:ring-primary-500/50',
                    'secondary' => 'filament-button-color-secondary border-transparent bg-secondary-600 text-white hover:bg-secondary-500 focus:bg-secondary-500 focus:ring-secondary-500/50',
                    'success' => 'filament-button-color-success border-transparent bg-success-600 text-white hover:bg-success-500 focus:bg-success-500 focus:ring-success-500/50',
                    'warning' => 'filament-button-color-warning border-transparent bg-warning-600 text-white hover:bg-warning-500 focus:bg-warning-500 focus:ring-warning-500/50',
                    default => $color,
                },
            ],
    );

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
                'xs' => '-ml-0.5 rtl:ml-0 rtl:-mr-0.5',
                'sm' => '-ml-0.5 rtl:ml-0 rtl:-mr-0.5',
                'md' => '-ml-1 rtl:ml-0 rtl:-mr-1',
                'lg' => '-ml-1 rtl:ml-0 rtl:-mr-1',
                'xl' => '-ml-1 rtl:ml-0 rtl:-mr-1',
                default => null,
            },
            'after' => match ($size) {
                'xs' => '-mr-0.5 rtl:-ml-0.5 rtl:mr-0',
                'sm' => '-mr-0.5 rtl:-ml-0.5 rtl:mr-0',
                'md' => '-mr-1 rtl:-ml-1 rtl:mr-0',
                'lg' => '-mr-1 rtl:-ml-1 rtl:mr-0',
                'xl' => '-mr-1 rtl:-ml-1 rtl:mr-0',
                default => null,
            },
            default => null,
        } => ! $labelSrOnly,
    ]);

    $indicatorClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-indicator absolute -top-1 -right-1 inline-flex items-center justify-center h-4 w-4 rounded-full text-[0.5rem] font-medium text-white',
        match ($indicatorColor) {
            'danger' => 'bg-danger-600',
            'gray' => 'bg-gray-600',
            'primary' => 'bg-primary-600',
            'secondary' => 'bg-secondary-600',
            'success' => 'bg-success-600',
            'warning' => 'bg-warning-600',
            default => $indicatorColor,
        },
    ]);

    $labelClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-label truncate',
        'sr-only' => $labelSrOnly,
    ]);

    $hasFileUploadLoadingIndicator = ($type === 'submit') && filled($form);
    $hasLoadingIndicator = filled($attributes->get('wire:target')) || filled($attributes->get('wire:click')) || $hasFileUploadLoadingIndicator;

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($attributes->get('wire:target', $attributes->get('wire:click', $form)), ENT_QUOTES);
    }
@endphp

@if ($tag === 'button')
    <button
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
            @unless ($disabled)
                x-bind:class="{ 'opacity-70 cursor-wait': isUploadingFile }"
            @endunless
            x-init="
                form = $el.closest('form')

                form?.addEventListener('file-upload-started', () => {
                    isUploadingFile = true
                })

                form?.addEventListener('file-upload-finished', () => {
                    isUploadingFile = false
                })
            "
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
                ->class($buttonClasses)
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
                    x-cloak="x-cloak"
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
                x-show="!isUploadingFile"
            @endif
            class="{{ $labelClasses }}"
        >
            {{ $slot }}
        </span>

        @if ($hasFileUploadLoadingIndicator)
            <span
                x-show="isUploadingFile"
                x-cloak
            >
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
                    x-cloak="x-cloak"
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
            <span class="{{ $indicatorClasses }}">
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
        {{ $attributes->class($buttonClasses) }}
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
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </a>
@endif
