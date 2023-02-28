<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.affixes
        :state-path="$statePath"
        :prefix="$getPrefixLabel()"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$getPrefixIcon()"
        :suffix="$getSuffixLabel()"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$getSuffixIcon()"
        class="filament-forms-text-input-component"
        :attributes="$getExtraAttributeBag()"
    >
        @php
            $isDisabled = $isDisabled();
            $statePath = $getStatePath();
        @endphp

        <div {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['filament-forms-color-picker-component flex items-center space-x-1 rtl:space-x-reverse group']) }}>
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms') }}"
                x-data="colorPickerFormComponent({
                    isAutofocused: @js($isAutofocused()),
                    isDisabled: @js($isDisabled),
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }}
                })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{ $getExtraAlpineAttributeBag()->class(['relative flex-1']) }}
            >
                <input
                    x-ref="input"
                    x-model="state"
                    x-on:click="togglePanelVisibility()"
                    x-on:keydown.enter.stop.prevent="togglePanelVisibility()"
                    {{
                        $getExtraInputAttributeBag()
                            ->merge([
                                'autocomplete' => 'off',
                                'disabled' => $isDisabled,
                                'dusk' => "filament.forms.{$statePath}",
                                'id' => $getId(),
                                'placeholder' => $getPlaceholder(),
                                'required' => $isRequired() && (! $isConcealed()),
                                'type' => 'text',
                            ], escape: false)
                            ->class([
                                'text-gray-900 block w-full transition duration-75 shadow-sm outline-none sm:text-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500',
                                'border-gray-300 dark:border-gray-600' => ! $errors->has($statePath),
                                'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                                'rounded-l-lg' => ! ($getPrefixLabel() || $getPrefixIcon()),
                                'rounded-r-lg' => ! ($getSuffixLabel() || $getSuffixIcon()),
                            ])
                    }}
                />

                <span
                    x-cloak
                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none rtl:right-auto rtl:left-0 rtl:pl-2"
                >
                    <span
                        x-bind:style="{ 'background-color': state }"
                        class="filament-forms-color-picker-component-preview relative overflow-hidden rounded-md w-7 h-7"
                    ></span>
                </span>

                <div
                    x-cloak
                    x-ref="panel"
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore.self
                    wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.panel"
                    @class([
                        'hidden absolute z-10 shadow-lg',
                        'opacity-70 pointer-events-none' => $isDisabled,
                    ])
                >
                    @php
                        $tag = match ($getFormat()) {
                            'hsl' => 'hsl-string',
                            'rgb' => 'rgb-string',
                            'rgba' => 'rgba-string',
                            default => 'hex',
                        } . '-color-picker';
                    @endphp

                    <{{ $tag }} color="{{ $getState() }}" />
                </div>
            </div>

        </div>
    </x-filament::input.affixes>
</x-dynamic-component>
