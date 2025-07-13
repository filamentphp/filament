@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributeBag = $getExtraAttributeBag();
    $isDisabled = $isDisabled();
    $isLive = $isLive();
    $isLiveOnBlur = $isLiveOnBlur();
    $isLiveDebounced = $isLiveDebounced();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $liveDebounce = $getLiveDebounce();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixIconColor = $getPrefixIconColor();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixIconColor = $getSuffixIconColor();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$prefixIconColor"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$suffixIconColor"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
                ->class('fi-fo-color-picker')
        "
    >
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms') }}"
            x-data="colorPickerFormComponent({
                        isAutofocused: @js($isAutofocused()),
                        isDisabled: @js($isDisabled),
                        isLive: @js($isLive),
                        isLiveDebounced: @js($isLiveDebounced),
                        isLiveOnBlur: @js($isLiveOnBlur),
                        liveDebounce: @js($liveDebounce),
                        state: $wire.$entangle('{{ $statePath }}'),
                    })"
            x-on:keydown.esc="isOpen() && $event.stopPropagation()"
            {{ $getExtraAlpineAttributeBag()->class(['fi-input-wrp-content']) }}
        >
            <input
                x-on:focus="$refs.panel.open($refs.input)"
                x-on:keydown.enter.prevent.stop="togglePanelVisibility()"
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
                            'x-model' . ($isLiveDebounced ? '.debounce.' . $liveDebounce : null) => 'state',
                            'x-on:blur' => $isLiveOnBlur ? 'isOpen() ? null : commitState()' : null,
                        ], escape: false)
                        ->class([
                            'fi-input',
                            'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                            'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                        ])
                }}
            />

            <div
                class="fi-fo-color-picker-preview my-auto me-3 size-5 shrink-0 rounded-full select-none"
                x-on:click="togglePanelVisibility()"
                x-bind:class="{
                    'fi-empty': ! state,
                }"
                x-bind:style="{ 'background-color': state }"
            ></div>

            <div
                wire:ignore.self
                wire:key="{{ $getLivewireKey() }}.panel"
                x-cloak
                x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                x-ref="panel"
                class="fi-fo-color-picker-panel"
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
