@php
    $isDisabled = $isDisabled();

    $statePath = $getStatePath();

    $prefixLabel = $getPrefixLabel();
    $prefixIcon = $getPrefixIcon();
    $hasPrefix = $prefixLabel || $prefixIcon;

    $suffixLabel = $getSuffixLabel();
    $suffixIcon = $getSuffixIcon();
    $hasSuffix = $suffixLabel || $suffixIcon;
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament-forms::affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-select-component"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        @unless ($isSearchable() || $isMultiple())
            <x-filament::input.select
                :autofocus="$isAutofocused()"
                :disabled="$isDisabled"
                :id="$getId()"
                dusk="filament.forms.{{ $statePath }}"
                :required="$isRequired() && (! ! $isConcealed())"
                :attributes="\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag()->merge([
                    $applyStateBindingModifiers('wire:model') => $statePath,
                ], escape: false))"
                :error="$errors->has($statePath)"
                :prefix="$hasPrefix"
                :suffix="$hasSuffix"
                class="filament-forms-input w-full"
            >
                @php
                    $isHtmlAllowed = $isHtmlAllowed();
                @endphp

                @if ($canSelectPlaceholder())
                    <option value="">@if (! $isDisabled) {{ $getPlaceholder() }} @endif</option>
                @endif

                @foreach ($getOptions() as $value => $label)
                    <option
                        value="{{ $value }}"
                        @disabled($isOptionDisabled($value, $label))
                    >
                        @if ($isHtmlAllowed)
                            {!! $label !!}
                        @else
                            {{ $label }}
                        @endif
                    </option>
                @endforeach
            </x-filament::input.select>
        @else
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('select', 'filament/forms') }}"
                x-data="selectFormComponent({
                    canSelectPlaceholder: @js($canSelectPlaceholder()),
                    isHtmlAllowed: @js($isHtmlAllowed()),
                    getOptionLabelUsing: async () => {
                        return await $wire.getFormSelectOptionLabel(@js($statePath))
                    },
                    getOptionLabelsUsing: async () => {
                        return await $wire.getFormSelectOptionLabels(@js($statePath))
                    },
                    getOptionsUsing: async () => {
                        return await $wire.getFormSelectOptions(@js($statePath))
                    },
                    getSearchResultsUsing: async (search) => {
                        return await $wire.getFormSelectSearchResults(@js($statePath), search)
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
                    searchableOptionFields: @js($getSearchableOptionFields()),
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
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class([
                            'filament-forms-input',
                            'filament-select-input-with-prefix' => $hasPrefix,
                            'filament-select-input-with-suffix' => $hasSuffix,
                        ])
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
    </x-filament-forms::affixes>
</x-dynamic-component>
