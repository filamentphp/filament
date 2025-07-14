@php
    use Filament\Support\Enums\GridDirection;

    $fieldWrapperView = $getFieldWrapperView();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $isHtmlAllowed = $isHtmlAllowed();
    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();
    $options = $getOptions();
    $livewireKey = $getLivewireKey();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('checkbox-list', 'filament/forms') }}"
        x-data="checkboxListFormComponent({
                    livewireId: @js($this->getId()),
                })"
        {{ $getExtraAlpineAttributeBag()->class(['fi-fo-checkbox-list']) }}
    >
        @if (! $isDisabled)
            @if ($isSearchable)
                <x-filament::input.wrapper
                    inline-prefix
                    :prefix-icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass"
                    :prefix-icon-alias="\Filament\Forms\View\FormsIconAlias::COMPONENTS_CHECKBOX_LIST_SEARCH_FIELD"
                    class="fi-fo-checkbox-list-search-input-wrp"
                >
                    <input
                        placeholder="{{ $getSearchPrompt() }}"
                        type="search"
                        x-model.debounce.{{ $getSearchDebounce() }}="search"
                        class="fi-input fi-input-has-inline-prefix"
                    />
                </x-filament::input.wrapper>
            @endif

            @if ($isBulkToggleable && count($options))
                <div
                    x-cloak
                    class="fi-fo-checkbox-list-actions"
                    wire:key="{{ $livewireKey }}.actions"
                >
                    <span
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.select-all"
                    >
                        {{ $getAction('selectAll') }}
                    </span>

                    <span
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.deselect-all"
                    >
                        {{ $getAction('deselectAll') }}
                    </span>
                </div>
            @endif
        @endif

        <div
            {{
                $getExtraAttributeBag()
                    ->grid($getColumns(), $gridDirection)
                    ->merge([
                        'x-show' => $isSearchable ? 'visibleCheckboxListOptions.length' : null,
                    ], escape: false)
                    ->class([
                        'fi-fo-checkbox-list-options',
                    ])
            }}
        >
            @forelse ($options as $value => $label)
                <div
                    wire:key="{{ $livewireKey }}.options.{{ $value }}"
                    @if ($isSearchable)
                        x-show="
                            $el
                                .querySelector('.fi-fo-checkbox-list-option-label')
                                ?.innerText.toLowerCase()
                                .includes(search.toLowerCase()) ||
                                $el
                                    .querySelector('.fi-fo-checkbox-list-option-description')
                                    ?.innerText.toLowerCase()
                                    .includes(search.toLowerCase())
                        "
                    @endif
                    class="fi-fo-checkbox-list-option-ctn"
                >
                    <label class="fi-fo-checkbox-list-option">
                        <input
                            type="checkbox"
                            {{
                                $extraInputAttributeBag
                                    ->merge([
                                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                        'value' => $value,
                                        'wire:loading.attr' => 'disabled',
                                        $wireModelAttribute => $statePath,
                                        'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                    ], escape: false)
                                    ->class([
                                        'fi-checkbox-input',
                                        'fi-valid' => ! $errors->has($statePath),
                                        'fi-invalid' => $errors->has($statePath),
                                    ])
                            }}
                        />

                        <div class="fi-fo-checkbox-list-option-text">
                            <span class="fi-fo-checkbox-list-option-label">
                                @if ($isHtmlAllowed)
                                    {!! $label !!}
                                @else
                                    {{ $label }}
                                @endif
                            </span>

                            @if ($hasDescription($value))
                                <p
                                    class="fi-fo-checkbox-list-option-description"
                                >
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
                    </label>
                </div>
            @empty
                <div wire:key="{{ $livewireKey }}.empty"></div>
            @endforelse
        </div>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="search && ! visibleCheckboxListOptions.length"
                class="fi-fo-checkbox-list-no-search-results-message"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
