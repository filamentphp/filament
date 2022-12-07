<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @if ($isBulkToggleable() || $isSearchable())
    <div
        class="gap-y-2 grid"
        x-data="{
            options: @js($getOptions()),

            checkboxListItems: Array.from($root.querySelectorAll('label')),

            visibleCheckboxListItems: [],

            isAllVisibleSelected: false,

            visibleCheckboxInputs: [],

            init: function () {
                this.updateVisibleCheckboxListItems()
                this.updateIsAllVisibleSelected()

                $watch('search', () => this.updateVisibleCheckboxListItems())
            },

            updateVisibleCheckboxListItems: function () {
                this.visibleCheckboxListItems = this.checkboxListItems.filter((checkboxListItem) => {
                    return checkboxListItem.querySelector('span').innerText.toLowerCase().includes(this.search.toLowerCase())
                })

                this.visibleCheckboxInputs = this.visibleCheckboxListItems.map((checkboxListItem) => {
                    return checkboxListItem.querySelector('input[type=checkbox]')
                })
            },

            updateIsAllVisibleSelected: function () {
                let checkedVisibleCheckboxInputs = this.visibleCheckboxInputs.filter((checkboxInput) => {
                    return checkboxInput.checked
                })

                this.isAllVisibleSelected = this.visibleCheckboxInputs.every((item) => checkedVisibleCheckboxInputs.includes(item))
            },

            toggleAll: function () {
                state = !this.isAllVisibleSelected

                let checkboxListItems = this.visibleCheckboxListItems.length !== this.checkboxListItems.length ? this.visibleCheckboxListItems : this.checkboxListItems

                Array.from(checkboxListItems).forEach((checkboxListItem) => {
                    checkboxListItem.querySelector('input[type=checkbox]').checked = state
                    checkboxListItem.querySelector('input[type=checkbox]').dispatchEvent(new Event('change'))
                })
            },

            search: '',
        }"
    >
    @endif

        @if ($isSearchable())
            <input
                {!! $isDisabled() ? 'disabled' : null !!}
                class="focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 block w-full transition duration-75 border-gray-300 rounded-lg shadow-sm"
                type="search"
                placeholder="{{ $getSearchPrompt() }}"
                x-model.debounce.{{ $getSearchDebounce() }}="search"
            >
        @endif

        @if ($isBulkToggleable() && ! $isDisabled())
            <div>
                <x-forms::link
                    tag="button"
                    size="sm"
                    x-show="visibleCheckboxListItems.length && !isAllVisibleSelected"
                    x-on:click="toggleAll"
                >
                    {{ __('forms::components.checkbox_list.buttons.select_all.label') }}
                </x-forms::link>

                <x-forms::link
                    tag="button"
                    size="sm"
                    x-show="visibleCheckboxListItems.length && isAllVisibleSelected"
                    x-on:click="toggleAll"
                >
                    {{ __('forms::components.checkbox_list.buttons.deselect_all.label') }}
                </x-forms::link>
            </div>
        @endif

        <x-filament-support::grid
            :default="$getColumns('default')"
            :sm="$getColumns('sm')"
            :md="$getColumns('md')"
            :lg="$getColumns('lg')"
            :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')"
            direction="column"
            :attributes="$attributes->class(['filament-forms-checkbox-list-component gap-1 space-y-2'])"
        >
            @php
                $isDisabled = $isDisabled();
            @endphp

            @foreach ($getOptions() as $optionValue => $optionLabel)
                <label
                    class="rtl:space-x-reverse flex items-center space-x-3"
                    @if ($isSearchable())
                        x-show="$el.querySelector('span').innerText.toLowerCase().includes(search.toLowerCase())"
                    @endif
                >
                    <input
                        {!! $isDisabled ? 'disabled' : null !!}
                        wire:loading.attr="disabled"
                        type="checkbox"
                        value="{{ $optionValue }}"
                        dusk="filament.forms.{{ $getStatePath() }}"
                        @if ($isBulkToggleable())
                            x-on:change="updateIsAllVisibleSelected"
                        @endif
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                        {{ $getExtraAttributeBag()->class([
                            'text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70',
                            'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                            'border-gray-300' => ! $errors->has($getStatePath()),
                            'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                            'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                            'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                        ]) }}
                    />

                    <span @class([
                        'text-sm font-medium text-gray-700',
                        'dark:text-gray-200' => config('forms.dark_mode'),
                    ])>
                        {{ $optionLabel }}
                    </span>
                </label>
            @endforeach
        </x-filament-support::grid>

        @if ($isSearchable())
            <div
                x-cloak
                x-show="visibleCheckboxListItems.length === 0"
                class="filament-forms-checkbox-list-component-no-search-results-message text-sm"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif

    @if ($isBulkToggleable() || $isSearchable())
    </div>
    @endif
</x-dynamic-component>
