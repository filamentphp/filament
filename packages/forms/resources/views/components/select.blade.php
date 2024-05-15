@php
    use Filament\Support\Facades\FilamentView;

    $canSelectPlaceholder = $canSelectPlaceholder();
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
                ->class(['fi-fo-select'])
        "
    >
        @if ((! ($isSearchable() || $isMultiple()) && $isNative()))
            <x-filament::input.select
                :autofocus="$isAutofocused()"
                :disabled="$isDisabled"
                :id="$getId()"
                :inline-prefix="$isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel))"
                :inline-suffix="$isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel))"
                :required="$isRequired() && (! $isConcealed())"
                :attributes="
                    $getExtraInputAttributeBag()
                        ->merge([
                            $applyStateBindingModifiers('wire:model') => $statePath,
                        ], escape: false)
                "
            >
                @php
                    $isHtmlAllowed = $isHtmlAllowed();
                @endphp

                @if ($canSelectPlaceholder)
                    <option value="">
                        @if (! $isDisabled)
                            {{ $getPlaceholder() }}
                        @endif
                    </option>
                @endif

                @foreach ($getOptions() as $value => $label)
                    @if (is_array($label))
                        <optgroup label="{{ $value }}">
                            @foreach ($label as $groupedValue => $groupedLabel)
                                <option
                                    @disabled($isOptionDisabled($groupedValue, $groupedLabel))
                                    value="{{ $groupedValue }}"
                                >
                                    @if ($isHtmlAllowed)
                                        {!! $groupedLabel !!}
                                    @else
                                        {{ $groupedLabel }}
                                    @endif
                                </option>
                            @endforeach
                        </optgroup>
                    @else
                        <option
                            @disabled($isOptionDisabled($value, $label))
                            value="{{ $value }}"
                        >
                            @if ($isHtmlAllowed)
                                {!! $label !!}
                            @else
                                {{ $label }}
                            @endif
                        </option>
                    @endif
                @endforeach
            </x-filament::input.select>
        @else
            <div
                x-ignore
                @if (FilamentView::hasSpaMode())
                    ax-load="visible"
                @else
                    ax-load
                @endif
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('select', 'filament/forms') }}"
                x-data="selectFormComponent({
                            canSelectPlaceholder: @js($canSelectPlaceholder),
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
                            isMultiple: @js($isMultiple()),
                            isSearchable: @js($isSearchable()),
                            livewireId: @js($this->getId()),
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
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                            statePath: @js($statePath),
                        })"
                wire:ignore
                x-on:keydown.esc="select.dropdown.isActive && $event.stopPropagation()"
                {{
                    $attributes
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class([
                            '[&_.choices\_\_inner]:ps-0' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
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
                            ->class([
                                'h-9 w-full rounded-lg border-none bg-transparent !bg-none',
                            ])
                    }}
                ></select>
            </div>
        @endif
    </x-filament::input.wrapper>
</x-dynamic-component>
