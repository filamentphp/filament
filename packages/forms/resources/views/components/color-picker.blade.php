@php
    use Filament\Support\Facades\FilamentView;

    $isDisabled = $isDisabled();
    $isLiveOnBlur = $isLiveOnBlur();
    $isLiveDebounced = $isLiveDebounced();
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

<x-dynamic-component
    :component="$getFieldWrapperView()"
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
        :prefix-icon-color="$getPrefixIconColor()"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$getSuffixIconColor()"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class('fi-fo-color-picker')
        "
    >
        <div
            x-ignore
            @if (FilamentView::hasSpaMode())
                ax-load="visible"
            @else
                ax-load
            @endif
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms') }}"
            x-data="colorPickerFormComponent({
                        isAutofocused: @js($isAutofocused()),
                        isDisabled: @js($isDisabled),
                        isLiveDebounced: @js($isLiveDebounced),
                        isLiveOnBlur: @js($isLiveOnBlur),
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                    })"
            x-on:keydown.esc="isOpen() && $event.stopPropagation()"
            {{ $getExtraAlpineAttributeBag()->class(['flex']) }}
        >
            <x-filament::input
                x-on:focus="$refs.panel.open($refs.input)"
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
                            'x-model' . ($isLiveDebounced ? '.debounce.' . $getLiveDebounce() : null) => 'state',
                            'x-on:blur' => $isLiveOnBlur ? 'isOpen() ? null : $wire.call(\'$refresh\')' : null,
                        ], escape: false)
                "
            />

            <div
                class="flex min-h-full items-center pe-3"
                x-on:click="togglePanelVisibility()"
            >
                <div
                    class="h-5 w-5 select-none rounded-full"
                    x-bind:class="{
                        'ring-1 ring-inset ring-gray-200 dark:ring-white/10': ! state,
                    }"
                    x-bind:style="{ 'background-color': state }"
                ></div>
            </div>

            <div
                wire:ignore.self
                wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.panel"
                x-cloak
                x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                x-ref="panel"
                class="fi-fo-color-picker-panel absolute z-10 hidden rounded-lg shadow-lg"
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
