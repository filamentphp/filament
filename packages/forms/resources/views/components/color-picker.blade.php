@php
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();

    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament-forms::affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-text-input-component"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        <div
            {{
                $attributes
                    ->merge($getExtraAttributes(), escape: false)
                    ->class(['filament-forms-color-picker-component group flex items-center space-x-1 rtl:space-x-reverse'])
            }}
        >
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms') }}"
                x-data="colorPickerFormComponent({
                    isAutofocused: @js($isAutofocused()),
                    isDisabled: @js($isDisabled),
                    state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }}
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
                                'filament-forms-input block w-full text-gray-900 shadow-sm outline-none transition duration-75 focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white sm:text-sm',
                                'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500' => ! $errors->has($statePath),
                                'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                                'rounded-s-lg' => ! ($prefixLabel || $prefixIcon),
                                'rounded-e-lg' => ! ($suffixLabel || $suffixIcon),
                            ])
                    }}
                />

                <span
                    x-cloak
                    class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-2"
                >
                    <span
                        x-bind:style="{ 'background-color': state, ...(state ? { 'background-image': 'none' } : {}) }"
                        class="filament-forms-color-picker-component-preview relative h-7 w-7 overflow-hidden rounded-md"
                    ></span>
                </span>

                <div
                    x-cloak
                    x-ref="panel"
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore.self
                    wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.panel"
                    @class([
                        'absolute z-10 hidden shadow-lg',
                        'pointer-events-none opacity-70' => $isDisabled,
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
    </x-filament-forms::affixes>
</x-dynamic-component>
