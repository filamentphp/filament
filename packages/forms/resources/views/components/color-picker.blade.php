@php
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament-forms::affixes
        :state-path="$statePath"
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline()"
        :inline-suffix="$isSuffixInline()"
        :prefix="$getPrefixLabel()"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$getPrefixIcon()"
        :suffix="$getSuffixLabel()"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$getSuffixIcon()"
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
                        isLiveOnPickerClose: @js($isLiveOnBlur() || $isLiveDebounced()),
                        state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
                    })"
            x-on:keydown.esc="isOpen() && $event.stopPropagation()"
            {{ $getExtraAlpineAttributeBag()->class(['flex']) }}
        >
            <x-filament::input
                {{ 'x-model' . ($isLiveDebounced() ? ".debounce.{$getLiveDebounce()}" : null) }}="state"
                @if ($isLiveOnBlur())
                x-on:blur="$wire.call('$refresh')"
                @endif
                x-on:focus="togglePanelVisibility()"
                x-on:keydown.enter.stop.prevent="togglePanelVisibility()"
                x-ref="input"
                :attributes="
                    $getExtraInputAttributeBag()
                        ->merge([
                            'autocomplete' => 'off',
                            'disabled' => $isDisabled,
                            'id' => $getId(),
                            'placeholder' => $getPlaceholder(),
                            'required' => $isRequired() && (! $isConcealed()),
                            'type' => 'text',
                        ], escape: false)
                "
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
                wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.panel"
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
