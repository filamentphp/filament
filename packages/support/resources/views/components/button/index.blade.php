@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'grouped' => false,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
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
            "fi-btn fi-btn-size-{$size} relative grid-flow-col items-center justify-center font-medium outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70",
            'flex-1' => $grouped,
            'rounded-lg' => ! $grouped,
            is_string($color) ? "fi-btn-color-{$color}" : null,
            match ($size) {
                'xs' => 'gap-1 px-2 py-1.5 text-xs',
                'sm' => 'gap-1 px-2.5 py-1.5 text-sm',
                'md' => 'gap-1.5 px-3 py-2 text-sm',
                'lg' => 'gap-1.5 px-3.5 py-2.5 text-sm',
                'xl' => 'gap-1.5 px-4 py-3 text-sm',
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
            $outlined ?
                [
                    'fi-btn-outlined ring-1',
                    match ($color) {
                        'gray' => 'ring-gray-300 text-gray-950 hover:bg-gray-400/10 focus:bg-gray-400/10 focus:ring-gray-400/40 dark:ring-gray-700 dark:text-white',
                        default => 'ring-custom-600 text-custom-600 hover:bg-custom-400/10 focus:bg-custom-400/10 focus:ring-custom-500/50 dark:ring-custom-400 dark:text-custom-400 dark:focus:ring-custom-400/70',
                    },
                ] :
                [
                    'shadow' => ! $grouped,
                    ...match ($color) {
                        'gray' => [
                            'bg-white text-gray-950 hover:bg-gray-50 focus:bg-gray-50 dark:bg-white/10 dark:text-white dark:hover:bg-white/20 dark:focus:bg-white/20',
                            'ring-gray-950/10 ring-1 dark:ring-white/20 dark:hover:ring-white/30 dark:focus:ring-white/30' => ! $grouped,
                        ],
                        default => [
                            'bg-custom-600 text-white hover:bg-custom-500 focus:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus:bg-custom-400',
                            'focus:ring-custom-400/50' => ! $grouped,
                        ],
                    },
                ]
        ),
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($color, shades: [400, 500, 600]) => $color !== 'gray',
    ]);

    $iconSize ??= match ($size) {
        'xs', 'sm' => 'sm',
        default => 'md',
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-btn-icon',
        match ($iconSize) {
            'sm' => 'h-4 w-4',
            'md' => 'h-5 w-5',
            'lg' => 'h-6 w-6',
            default => $iconSize,
        },
    ]);

    $badgeClasses = 'absolute -top-1 start-full -ms-1 -translate-x-1/2 rounded-md bg-white dark:bg-gray-900';

    $labelClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-btn-label',
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
        :badge="$badge"
        :badge-color="$badgeColor"
        :color="$color"
        :disabled="$disabled"
        :form="$form"
        :icon="$icon"
        :icon-alias="$iconAlias"
        :icon-size="$iconSize"
        :key-bindings="$keyBindings"
        :label="$slot"
        :size="$size"
        :tag="$tag"
        :tooltip="$tooltip"
        :type="$type"
        :class="
            match ($labeledFrom) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
                default => 'hidden',
            }
        "
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
                    :alias="$iconAlias"
                    :name="$icon"
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
                    :alias="$iconAlias"
                    :name="$icon"
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

        @if ($badge)
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
                :alias="$iconAlias"
                :name="$icon"
                :class="$iconClasses"
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :alias="$iconAlias"
                :name="$icon"
                :class="$iconClasses"
            />
        @endif

        @if ($badge)
            <div class="{{ $badgeClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
@endif
