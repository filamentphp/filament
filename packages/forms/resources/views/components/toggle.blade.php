@php
    $offColor = $getOffColor() ?? 'gray';
    $onColor = $getOnColor() ?? 'primary';
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    @capture($content)
        <button
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            }"
            x-bind:aria-checked="state?.toString()"
            x-on:click="state = ! state"
            x-bind:class="
                state
                    ? '{{
                        \Illuminate\Support\Arr::toCssClasses([
                            match ($onColor) {
                                'gray' => 'bg-gray-200 dark:bg-gray-700',
                                default => 'fi-color-custom bg-custom-600',
                            },
                            is_string($onColor) ? "fi-color-{$onColor}" : null,
                        ])
                    }}'
                    : '{{
                        \Illuminate\Support\Arr::toCssClasses([
                            match ($offColor) {
                                'gray' => 'bg-gray-200 dark:bg-gray-700',
                                default => 'fi-color-custom bg-custom-600',
                            },
                            is_string($offColor) ? "fi-color-{$offColor}" : null,
                        ])
                    }}'
            "
            x-bind:style="
                state
                    ? '{{
                        \Filament\Support\get_color_css_variables(
                            $onColor,
                            shades: [600],
                            alias: 'forms::components.toggle.on',
                        )
                    }}'
                    : '{{
                        \Filament\Support\get_color_css_variables(
                            $offColor,
                            shades: [600],
                            alias: 'forms::components.toggle.off',
                        )
                    }}'
            "
            {{
                $attributes
                    ->merge([
                        'aria-checked' => 'false',
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'id' => $getId(),
                        'role' => 'switch',
                        'type' => 'button',
                        'wire:loading.attr' => 'disabled',
                        'wire:target' => $statePath,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->class(['fi-fo-toggle relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent outline-none transition-colors duration-200 ease-in-out focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-70 dark:focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900'])
            }}
        >
            <span
                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                x-bind:class="{
                    'translate-x-5 rtl:-translate-x-5': state,
                    'translate-x-0': ! state,
                }"
            >
                <span
                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-0 ease-out duration-100': state,
                        'opacity-100 ease-in duration-200': ! state,
                    }"
                >
                    @if ($hasOffIcon())
                        <x-filament::icon
                            :icon="$getOffIcon()"
                            @class([
                                'fi-fo-toggle-off-icon h-3 w-3',
                                match ($offColor) {
                                    'gray' => 'text-gray-400 dark:text-gray-700',
                                    default => 'text-custom-600',
                                },
                            ])
                        />
                    @endif
                </span>

                <span
                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-100 ease-in duration-200': state,
                        'opacity-0 ease-out duration-100': ! state,
                    }"
                >
                    @if ($hasOnIcon())
                        <x-filament::icon
                            :icon="$getOnIcon()"
                            x-cloak="x-cloak"
                            @class([
                                'fi-fo-toggle-on-icon h-3 w-3',
                                match ($onColor) {
                                    'gray' => 'text-gray-400 dark:text-gray-700',
                                    default => 'text-custom-600',
                                },
                            ])
                        />
                    @endif
                </span>
            </span>
        </button>
    @endcapture

    @if ($isInline())
        <x-slot name="labelPrefix">
            {{ $content() }}
        </x-slot>
    @else
        {{ $content() }}
    @endif
</x-dynamic-component>
