@php
    $isDisabled = $isDisabled();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :valid="! $errors->has($statePath)"
        class="fi-fo-color-picker"
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
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    })"
            x-on:keydown.esc="isOpen() && $event.stopPropagation()"
            {{ $getExtraAlpineAttributeBag()->class(['flex']) }}
        >
            <x-filament::input
                x-on:focus="togglePanelVisibility()"
                x-on:keydown.enter.stop.prevent="togglePanelVisibility()"
                x-ref="input"
                :attributes="
                    \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                        ->merge([
                            'autocomplete' => 'off',
                            'disabled' => $isDisabled,
                            'id' => $getId(),
                            'inlinePrefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                            'inlineSuffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                            'placeholder' => $getPlaceholder(),
                            'required' => $isRequired() && (! $isConcealed()),
                            'type' => 'text',
                            'x-model' . ($isLiveDebounced() ? '.debounce.' . $getLiveDebounce() : null) => 'state',
                            'x-on:blur' => $isLiveOnBlur() ? '$wire.call(\'$refresh\')' : null,
                        ], escape: false)
                "
            />

            <div
                class="flex min-h-full items-center pe-3"
                x-on:click="$refs.input.focus()"
            >
                <div
                    x-bind:style="{ 'background-color': state }"
                    x-bind:class="{
                        'ring-1 ring-inset ring-gray-200 dark:ring-white/10': ! state,
                    }"
                    class="h-5 w-5 rounded-full"
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
    </x-filament::input.wrapper>
</x-dynamic-component>
