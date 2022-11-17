@php
    $inputClasses = [
        'filament-select-input-with-prefix' => ($hasPrefix = $getPrefixLabel() || $getPrefixIcon()),
        'filament-select-input-with-suffix' => ($hasSuffix = $getSuffixLabel() || $getSuffixIcon()),
    ];
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.affixes
        :state-path="$statePath"
        :prefix="$getPrefixLabel()"
        :prefix-action="$getPrefixAction()"
        :prefix-icon="$getPrefixIcon()"
        :suffix="$getSuffixLabel()"
        :suffix-action="$getSuffixAction()"
        :suffix-icon="$getSuffixIcon()"
        class="filament-forms-select-component"
        :attributes="$getExtraAttributeBag()"
    >
        @unless ($isSearchable() || $isMultiple())
            <x-filament::input.select
                :autofocus="$isAutofocused()"
                :disabled="$isDisabled()"
                :id="$getId()"
                dusk="filament.forms.{{ $getStatePath() }}"
                :required="$isRequired() && (! ! $isConcealed())"
                :attributes="$getExtraInputAttributeBag()->merge([
                    $applyStateBindingModifiers('wire:model') => $getStatePath(),
                ], escape: false)"
                :prefix="$hasPrefix"
                :suffix="$hasSuffix"
                class="w-full"
            >
                @unless ($isPlaceholderSelectionDisabled())
                    <option value="">{{ $getPlaceholder() }}</option>
                @endif

                @foreach ($getOptions() as $value => $label)
                    <option
                        value="{{ $value }}"
                        @if ($isOptionDisabled($value, $label)) disabled @endif
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </x-filament::input.select>
        @else
            <div
                x-ignore
                ax-load
                ax-load-src="/js/filament/forms/components/select.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
                x-data="selectFormComponent({
                    isHtmlAllowed: @js($isHtmlAllowed()),
                    getOptionLabelUsing: async () => {
                        return await $wire.getSelectOptionLabel(@js($getStatePath()))
                    },
                    getOptionLabelsUsing: async () => {
                        return await $wire.getSelectOptionLabels(@js($getStatePath()))
                    },
                    getOptionsUsing: async () => {
                        return await $wire.getSelectOptions(@js($getStatePath()))
                    },
                    getSearchResultsUsing: async (search) => {
                        return await $wire.getSelectSearchResults(@js($getStatePath()), search)
                    },
                    isAutofocused: @js($isAutofocused()),
                    isMultiple: @js($isMultiple()),
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
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
                })"
                x-on:keydown.esc="select.dropdown.isActive && $event.stopPropagation()"
                wire:ignore
                x-bind:class="{
                    'choices--error': (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
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
                                'disabled' => $isDisabled(),
                                'id' => $getId(),
                                'multiple' => $isMultiple(),
                            ], escape: false)
                    }}
                ></select>
            </div>
        @endif
    </x-filament::input.affixes>
</x-dynamic-component>
