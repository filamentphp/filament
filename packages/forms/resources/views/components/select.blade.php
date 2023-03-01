@php
    $inputClasses = [
        'filament-select-input-with-prefix' => ($hasPrefix = $getPrefixLabel() || $getPrefixIcon()),
        'filament-select-input-with-suffix' => ($hasSuffix = $getSuffixLabel() || $getSuffixIcon()),
    ];

    $isDisabled = $isDisabled();

    $statePath = $getStatePath();
@endphp

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
        class="filament-forms-select-component"
        :attributes="$getExtraAttributeBag()"
    >
        @unless ($isSearchable() || $isMultiple())
            <x-filament::input.select
                :autofocus="$isAutofocused()"
                :disabled="$isDisabled"
                :id="$getId()"
                dusk="filament.forms.{{ $statePath }}"
                :required="$isRequired() && (! ! $isConcealed())"
                :attributes="$getExtraInputAttributeBag()->merge([
                    $applyStateBindingModifiers('wire:model') => $statePath,
                ], escape: false)"
                :prefix="$hasPrefix"
                :suffix="$hasSuffix"
                class="w-full"
            >
                @if ($canSelectPlaceholder())
                    <option value="">@if (! $isDisabled) {{ $getPlaceholder() }} @endif</option>
                @endif

                @foreach ($getOptions() as $value => $label)
                    <option
                        value="{{ $value }}"
                        @disabled($isOptionDisabled($value, $label))
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </x-filament::input.select>
        @else
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('select', 'filament/forms') }}"
                x-data="selectFormComponent({
                    isHtmlAllowed: @js($isHtmlAllowed()),
                    getOptionLabelUsing: async () => {
                        return await $wire.getSelectOptionLabel(@js($statePath))
                    },
                    getOptionLabelsUsing: async () => {
                        return await $wire.getSelectOptionLabels(@js($statePath))
                    },
                    getOptionsUsing: async () => {
                        return await $wire.getSelectOptions(@js($statePath))
                    },
                    getSearchResultsUsing: async (search) => {
                        return await $wire.getSelectSearchResults(@js($statePath), search)
                    },
                    isAutofocused: @js($isAutofocused()),
                    isDisabled: @js($isDisabled),
                    isMultiple: @js($isMultiple()),
                    livewireId: @js($this->id),
                    hasDynamicOptions: @js($hasDynamicOptions()),
                    hasDynamicSearchResults: @js($hasDynamicSearchResults()),
                    loadingMessage: @js($getLoadingMessage()),
                    maxItems: @js($getMaxItems()),
                    maxItemsMessage: @js($getMaxItemsMessage()),
                    noSearchResultsMessage: @js($getNoSearchResultsMessage()),
                    options: @js($getOptionsForJs()),
                    optionsLimit: @js($getOptionsLimit()),
                    placeholder: @js($getPlaceholder()),
                    position: @js($getPosition()),
                    searchDebounce: @js($getSearchDebounce()),
                    searchingMessage: @js($getSearchingMessage()),
                    searchPrompt: @js($getSearchPrompt()),
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }},
                    statePath: @js($statePath),
                })"
                x-on:keydown.esc="select.dropdown.isActive && $event.stopPropagation()"
                wire:ignore
                x-bind:class="{
                    'choices--error': (@js($statePath) in $wire.__instance.serverMemo.errors),
                }"
                {{
                    $attributes
                        ->merge($getExtraAttributes(), escape: false)
                        ->merge($getExtraAlpineAttributes())
                        ->class($inputClasses)
                }}
            >
                <select
                    x-ref="input"
                    {{
                        $getExtraInputAttributeBag()
                            ->merge([
                                'disabled' => $isDisabled,
                                'id' => $getId(),
                                'multiple' => $isMultiple(),
                            ], escape: false)
                    }}
                ></select>
            </div>
        @endif
    </x-filament::input.affixes>
</x-dynamic-component>
