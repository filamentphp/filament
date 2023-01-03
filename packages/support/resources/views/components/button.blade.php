@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
    'keyBindings' => null,
    'labelSrOnly' => false,
    'outlined' => false,
    'size' => 'md',
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= $size;

    $buttonClasses = array_merge([
        "filament-button filament-button-size-{$size} inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 disabled:opacity-70 disabled:pointer-events-none",
        'opacity-70 pointer-events-none' => $disabled,
        match ($size) {
            'sm' => 'min-h-[2rem] px-3 text-sm',
            'md' => 'min-h-[2.25rem] px-4 text-sm',
            'lg' => 'min-h-[2.75rem] px-6 text-base',
        },
    ], $outlined ? [
        'shadow focus:ring-white' => $color !== 'gray',
        match ($color) {
            'danger' => 'text-danger-600 border-danger-600 hover:bg-danger-600/20 focus:bg-danger-700/20 focus:ring-offset-danger-700 dark:text-danger-500 dark:border-danger-500 dark:hover:bg-danger-500/20 dark:focus:bg-danger-600/20 dark:focus:ring-offset-danger-600',
            'gray' => 'text-gray-800 border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-500/20 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800/20',
            'primary' => 'text-primary-600 border-primary-600 hover:bg-primary-600/20 focus:bg-primary-700/20 focus:ring-offset-primary-700 dark:text-primary-500 dark:border-primary-500 dark:hover:bg-primary-500/20 dark:focus:bg-primary-600/20 dark:focus:ring-offset-primary-600',
            'secondary' => 'text-secondary-600 border-secondary-600 hover:bg-secondary-600/20 focus:bg-secondary-700/20 focus:ring-offset-secondary-700 dark:text-secondary-500 dark:border-secondary-500 dark:hover:bg-secondary-500/20 dark:focus:bg-secondary-600/20 dark:focus:ring-offset-secondary-600',
            'success' => 'text-success-600 border-success-600 hover:bg-success-600/20 focus:bg-success-700/20 focus:ring-offset-success-700 dark:text-success-500 dark:border-success-500 dark:hover:bg-success-500/20 dark:focus:bg-success-600/20 dark:focus:ring-offset-success-600',
            'warning' => 'text-warning-600 border-warning-600 hover:bg-warning-600/20 focus:bg-warning-700/20 focus:ring-offset-warning-700 dark:text-warning-500 dark:border-warning-500 dark:hover:bg-warning-500/20 dark:focus:bg-warning-600/20 dark:focus:ring-offset-warning-600',
            default => $color,
        },
    ] : [
        'text-white shadow focus:ring-white border-transparent' => $color !== 'gray',
        match ($color) {
            'danger' => 'bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700',
            'gray' => 'text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800',
            'primary' => 'bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700',
            'secondary' => 'bg-secondary-600 hover:bg-secondary-500 focus:bg-secondary-700 focus:ring-offset-secondary-700',
            'success' => 'bg-success-600 hover:bg-success-500 focus:bg-success-700 focus:ring-offset-success-700',
            'warning' => 'bg-warning-600 hover:bg-warning-500 focus:bg-warning-700 focus:ring-offset-warning-700',
            default => $color,
        },
    ]);

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-button-icon',
        'mr-1 -ml-2 rtl:ml-1 rtl:-mr-2' => ($iconPosition === 'before') && ($size === 'md') && (! $labelSrOnly),
        'mr-2 -ml-3 rtl:ml-2 rtl:-mr-3' => ($iconPosition === 'before') && ($size === 'lg') && (! $labelSrOnly),
        'mr-1 -ml-1.5 rtl:ml-1 rtl:-mr-1.5' => ($iconPosition === 'before') && ($size === 'sm') && (! $labelSrOnly),
        'ml-1 -mr-2 rtl:mr-1 rtl:-ml-2' => ($iconPosition === 'after') && ($size === 'md') && (! $labelSrOnly),
        'ml-2 -mr-3 rtl:mr-2 rtl:-ml-3' => ($iconPosition === 'after') && ($size === 'lg') && (! $labelSrOnly),
        'ml-1 -mr-1.5 rtl:mr-1 rtl:-ml-1.5' => ($iconPosition === 'after') && ($size === 'sm') && (! $labelSrOnly),
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
                    x-cloak=""
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
        @endif

        <span class="flex items-center gap-1">
            @if ($hasFileUploadLoadingIndicator)
                <x-filament::loading-indicator
                    x-show="isUploadingFile"
                    x-cloak=""
                    :class="$iconClasses . ' ' . $iconSize"
                />

                <span x-show="isUploadingFile" x-cloak>
                    {{ __('filament-support::components/button.messages.uploading_file') }}
                </span>

                <span x-show="! isUploadingFile" @class([
                    'sr-only' => $labelSrOnly,
                ])>
                    {{ $slot }}
                </span>
            @else
                <span @class([
                    'sr-only' => $labelSrOnly,
                ])>
                    {{ $slot }}
                </span>
            @endif
        </span>

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
                    x-cloak=""
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
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
                :class="$iconClasses"
            />
        @endif

        <span @class([
            'sr-only' => $labelSrOnly,
        ])>
            {{ $slot }}
        </span>

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :name="$icon"
                group="support::button.suffix"
                :class="$iconClasses"
            />
        @endif
    </a>
@endif
