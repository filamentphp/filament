@php
    $isDisabled = $isDisabled();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $statePath = $getStatePath();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament-forms::affixes
        :state-path="$statePath"
        :disabled="$isDisabled"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-color-picker-component"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        <div
            x-ignore
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms') }}"
            x-data="colorPickerFormComponent({
                        isAutofocused: @js($isAutofocused()),
                        isDisabled: @js($isDisabled),
                        state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
                    })"
            x-on:keydown.esc="isOpen() && $event.stopPropagation()"
            {{ $getExtraAlpineAttributeBag()->class(['flex']) }}
        >
            <input
                x-model="state"
                x-on:focus="togglePanelVisibility()"
                x-on:keydown.enter.stop.prevent="togglePanelVisibility()"
                x-ref="input"
                {{
                    $getExtraInputAttributeBag()
                        ->merge([
                            'autocomplete' => 'off',
                            'disabled' => $isDisabled,
                            'id' => $getId(),
                            'placeholder' => $getPlaceholder(),
                            'required' => $isRequired() && (! $isConcealed()),
                            'type' => 'text',
                        ], escape: false)
                        ->class([
                            'filament-forms-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6',
                        ])
                }}
            />

            <div
                class="flex min-h-full items-center pe-3"
                x-on:click="$refs.input.focus()"
            >
                <div
                    x-bind:style="{ 'background-color': state }"
                    class="h-5 w-5 rounded-full ring-1 ring-inset ring-gray-950/10 dark:ring-white/20"
                ></div>
            </div>

            <div
                wire:ignore.self
                wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.panel"
                x-cloak
                x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                x-ref="panel"
                class="absolute z-10 hidden rounded-lg shadow-lg"
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
    </x-filament-forms::affixes>
</x-dynamic-component>
