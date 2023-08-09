@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'grouped' => false,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => IconPosition::Before,
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
    $stringSize = match ($size) {
        ActionSize::ExtraSmall => 'xs',
        ActionSize::Small => 'sm',
        ActionSize::Medium => 'md',
        ActionSize::Large => 'lg',
        ActionSize::ExtraLarge => 'xl',
        default => $size,
    };

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        ...[
            "fi-btn fi-btn-size-{$stringSize} relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70",
            'flex-1' => $grouped,
            'rounded-lg' => ! $grouped,
            is_string($color) ? "fi-btn-color-{$color}" : null,
            match ($size) {
                ActionSize::ExtraSmall, 'xs' => 'gap-1 px-2 py-1.5 text-xs',
                ActionSize::Small, 'sm' => 'gap-1 px-2.5 py-1.5 text-sm',
                ActionSize::Medium, 'md' => 'gap-1.5 px-3 py-2 text-sm',
                ActionSize::Large, 'lg' => 'gap-1.5 px-3.5 py-2.5 text-sm',
                ActionSize::ExtraLarge, 'xl' => 'gap-1.5 px-4 py-3 text-sm',
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
                        'gray' => 'text-gray-950 ring-gray-300 hover:bg-gray-400/10 focus:ring-gray-400/40 dark:text-white dark:ring-gray-700',
                        default => 'text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500',
                    },
                ] :
                [
                    'shadow-sm' => ! $grouped,
                    ...match ($color) {
                        'gray' => [
                            'bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10',
                            'ring-1 ring-gray-950/10 dark:ring-white/20' => ! $grouped,
                        ],
                        default => [
                            'bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400',
                            'focus:ring-custom-500/50 dark:focus:ring-custom-400/50' => ! $grouped,
                        ],
                    },
                ]
        ),
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($color, shades: [400, 500, 600]) => $color !== 'gray',
    ]);

    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall, ActionSize::Small, 'xs', 'sm' => IconSize::Small,
        default => IconSize::Medium,
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-btn-icon',
        match ($iconSize) {
            IconSize::Small, 'sm' => 'h-4 w-4',
            IconSize::Medium, 'md' => 'h-5 w-5',
            IconSize::Large, 'lg' => 'h-6 w-6',
            default => $iconSize,
        },
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => null,
        },
    ]);

    $stringIconSize = match ($iconSize) {
        IconSize::Small => 'sm',
        IconSize::Medium => 'md',
        IconSize::Large => 'lg',
        default => $iconSize,
    };

    $badgeClasses = 'absolute -top-1 start-full z-[1] -ms-1 -translate-x-1/2 rounded-md bg-white rtl:translate-x-1/2 dark:bg-gray-900';

    $labelClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-btn-label',
        'sr-only' => $labelSrOnly,
    ]);

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first();

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
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
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
        @if (in_array($iconPosition, [IconPosition::Before, 'before']))
            @if ($icon)
                <x-filament::icon
                    :alias="$iconAlias"
                    :icon="$icon"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                    :class="$iconClasses"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $stringIconSize"
                />
            @endif

            @if ($hasFileUploadLoadingIndicator)
                <x-filament::loading-indicator
                    x-show="isUploadingFile"
                    x-cloak="x-cloak"
                    :class="$iconClasses . ' ' . $stringIconSize"
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
                {{ __('filament::components/button.messages.uploading_file') }}
            </span>
        @endif

        @if (in_array($iconPosition, [IconPosition::After, 'after']))
            @if ($icon)
                <x-filament::icon
                    :alias="$iconAlias"
                    :icon="$icon"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                    :class="$iconClasses"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $stringIconSize"
                />
            @endif

            @if ($hasFileUploadLoadingIndicator)
                <x-filament::loading-indicator
                    x-show="isUploadingFile"
                    x-cloak="x-cloak"
                    :class="$iconClasses . ' ' . $stringIconSize"
                />
            @endif
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
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($icon && in_array($iconPosition, [IconPosition::Before, 'before']))
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
                :class="$iconClasses"
            />
        @endif

        <span class="{{ $labelClasses }}">
            {{ $slot }}
        </span>

        @if ($icon && in_array($iconPosition, [IconPosition::After, 'after']))
            <x-filament::icon
                :alias="$iconAlias"
                :icon="$icon"
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
    </a>
@endif
