@php
    $state = $getState();
@endphp

<div
    wire:key="{{ $this->getId() }}.table.record.{{ $recordKey }}.column.{{ $getName() }}.toggle-column.{{ $state ? 'true' : 'false' }}"
>
    <div
        x-data="{
            error: undefined,
            state: @js((bool) $state),
            isLoading: false,
        }"
        wire:ignore
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-ta-toggle',
                    'px-3 py-4' => ! $isInline(),
                ])
        }}
    >
        @php
            $offColor = $getOffColor() ?? 'gray';
            $onColor = $getOnColor() ?? 'primary';
        @endphp

        <button
            role="switch"
            aria-checked="false"
            x-bind:aria-checked="state.toString()"
            x-on:click="
                if (isLoading) {
                    return
                }

                const updatedState = ! state

                // Only update the state if the toggle is being turned off,
                // otherwise it will flicker on twice when Livewire replaces
                // the element.
                if (state) {
                    state = false
                }

                isLoading = true

                const response = await $wire.updateTableColumnState(
                    @js($getName()),
                    @js($recordKey),
                    updatedState,
                )

                error = response?.error ?? undefined

                // The state is only updated on the frontend if the toggle is
                // being turned off, so we only need to reset it then.
                if (! state && error) {
                    state = ! state
                }

                isLoading = false
            "
            x-tooltip="
                error === undefined
                    ? false
                    : {
                          content: error,
                          theme: $store.theme,
                      }
            "
            x-bind:class="
                (state
                    ? '{{
                        match ($onColor) {
                            'gray' => 'fi-color-gray bg-gray-200 dark:bg-gray-700',
                            default => 'fi-color-custom bg-custom-600',
                        }
                    }}'
                    : '{{
                        match ($offColor) {
                            'gray' => 'fi-color-gray bg-gray-200 dark:bg-gray-700',
                            default => 'fi-color-custom bg-custom-600',
                        }
                    }}') +
                    (isLoading ? ' opacity-70 pointer-events-none' : '')
            "
            x-bind:style="
                state
                    ? '{{
                        \Filament\Support\get_color_css_variables(
                            $onColor,
                            shades: [600],
                            alias: 'tables::columns.toggle-column.on',
                        )
                    }}'
                    : '{{
                        \Filament\Support\get_color_css_variables(
                            $offColor,
                            shades: [600],
                            alias: 'tables::columns.toggle-column.off',
                        )
                    }}'
            "
            @disabled($isDisabled())
            type="button"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent outline-none transition-colors duration-200 ease-in-out disabled:pointer-events-none disabled:opacity-70"
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
                                'fi-ta-toggle-off-icon h-3 w-3',
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
                                'fi-ta-toggle-on-icon h-3 w-3',
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
    </div>
</div>
