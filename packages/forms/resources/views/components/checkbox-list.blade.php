<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    has-nested-recursive-validation-rules
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{
        areAllCheckboxesChecked: false,

        init: function () {
            this.checkIfAllCheckboxesAreChecked()

            Livewire.hook('message.processed', () => {
                this.checkIfAllCheckboxesAreChecked()
            })

            this.updateVisibleCheckboxListItems()

            $watch('search', () => this.updateVisibleCheckboxListItems())
        },

        checkIfAllCheckboxesAreChecked: function () {
            this.areAllCheckboxesChecked = this.$root.querySelectorAll('input[type=checkbox]').length === this.$root.querySelectorAll('input[type=checkbox]:checked').length
        },

        toggleAllCheckboxes: function () {
            state = ! this.areAllCheckboxesChecked

            this.$root.querySelectorAll('input[type=checkbox]').forEach((checkbox) => {
                checkbox.checked = state
                checkbox.dispatchEvent(new Event('change'))
            })
        },

        search: '',

        checkboxListItems: Array.from($root.querySelectorAll('label')),

        visibleCheckboxListItems: [],

        updateVisibleCheckboxListItems: function () {
            this.visibleCheckboxListItems = this.checkboxListItems.filter((checkboxListItem) => {
                return checkboxListItem.querySelector('span').innerText.toLowerCase().includes(this.search.toLowerCase())
            })
        }

    }">
        <div wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.wrapper">
            @if ($isBulkToggleable() && count($getOptions()))
                <div
                    x-cloak
                    x-show="search == ''"
                    class="mb-2"
                    wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.buttons"
                >
                    <x-forms::link
                        tag="button"
                        size="sm"
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.buttons.select_all"
                    >
                        {{ __('forms::components.checkbox_list.buttons.select_all.label') }}
                    </x-forms::link>

                    <x-forms::link
                        tag="button"
                        size="sm"
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.buttons.deselect_all"
                    >
                        {{ __('forms::components.checkbox_list.buttons.deselect_all.label') }}
                    </x-forms::link>
                </div>
            @endif
        </div>

        @if ($isSearchable())
            <input
                {!! $isDisabled() ? 'disabled' : null !!}
                class="focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 block w-full transition duration-75 border-gray-300 rounded-lg shadow-sm mb-2"
                type="search"
                placeholder="{{ $getSearchPrompt() }}"
                x-model.debounce.{{ $getSearchDebounce() }}="search"
            >
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
            @forelse ($getOptions() as $optionValue => $optionLabel)
                <div wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.options.{{ $optionValue }}">
                    <label
                        class="rtl:space-x-reverse flex items-center space-x-3"
                        @if ($isSearchable())
                            x-show="$el.querySelector('span').innerText.toLowerCase().includes(search.toLowerCase())"
                        @endif
                    >
                        <input
                            @if ($isBulkToggleable())
                                x-on:change="checkIfAllCheckboxesAreChecked()"
                            @endif
                            wire:loading.attr="disabled"
                            type="checkbox"
                            value="{{ $optionValue }}"
                            dusk="filament.forms.{{ $getStatePath() }}"
                            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                            {{ $getExtraAttributeBag()->class([
                                'text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70',
                                'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                                'border-gray-300' => ! $errors->has($getStatePath()),
                                'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                                'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                                'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                            ])->merge([
                                'disabled' => $isDisabled(),
                            ]) }}
                        />

                        <span @class([
                            'text-sm font-medium text-gray-700',
                            'dark:text-gray-200' => config('forms.dark_mode'),
                        ])>
                            {{ $optionLabel }}
                        </span>
                    </label>
                </div>
            @empty
                <div wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.empty"></div>
            @endforelse
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

    </div>
</x-dynamic-component>
