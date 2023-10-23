@php
    $gridDirection = $getGridDirection();
@endphp

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
    <div
        x-data="{
            areAllCheckboxesChecked: false,

            checkboxListOptions: Array.from(
                $root.querySelectorAll(
                    '.filament-forms-checkbox-list-component-option-label',
                ),
            ),

            search: '',

            visibleCheckboxListOptions: [],

            init: function () {
                this.updateVisibleCheckboxListOptions()

                $nextTick(() => {
                    this.checkIfAllCheckboxesAreChecked()
                })

                Livewire.hook('message.processed', () => {
                    this.updateVisibleCheckboxListOptions()

                    this.checkIfAllCheckboxesAreChecked()
                })

                $watch('search', () => {
                    this.updateVisibleCheckboxListOptions()
                    this.checkIfAllCheckboxesAreChecked()
                })
            },

            checkIfAllCheckboxesAreChecked: function () {
                this.areAllCheckboxesChecked =
                    this.visibleCheckboxListOptions.length ===
                    this.visibleCheckboxListOptions.filter((checkboxLabel) =>
                        checkboxLabel.querySelector('input[type=checkbox]:checked'),
                    ).length
            },

            toggleAllCheckboxes: function () {
                updatedState = []

                state = ! this.areAllCheckboxesChecked

                this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                    checkbox = checkboxLabel.querySelector('input[type=checkbox]')

                    checkbox.checked = state

                    if (state) {
                        updatedState.push(checkbox.value)
                    }
                })

                this.checkIfAllCheckboxesAreChecked()

                $wire.set(@js($getStatePath()), updatedState)

                this.areAllCheckboxesChecked = state
            },

            updateVisibleCheckboxListOptions: function () {
                this.visibleCheckboxListOptions = this.checkboxListOptions.filter(
                    (checkboxListItem) => {
                        return checkboxListItem
                            .querySelector(
                                '.filament-forms-checkbox-list-component-option-label-text',
                            )
                            .innerText.toLowerCase()
                            .includes(this.search.toLowerCase())
                    },
                )
            },
        }"
    >
        @if (! $isDisabled())
            @if ($isSearchable())
                <input
                    x-model.debounce.{{ $getSearchDebounce() }}="search"
                    type="search"
                    placeholder="{{ $getSearchPrompt() }}"
                    @class([
                        'mb-2 block h-7 w-full rounded-lg border-gray-300 px-2 text-sm text-gray-700 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500',
                        'dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200' => config('forms.dark_mode'),
                    ])
                />
            @endif

            @if ($isBulkToggleable() && count($getOptions()))
                <div
                    x-cloak
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
        @endif

        <x-filament-support::grid
            :default="$getColumns('default')"
            :sm="$getColumns('sm')"
            :md="$getColumns('md')"
            :lg="$getColumns('lg')"
            :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')"
            :direction="$gridDirection ?? 'column'"
            :x-show="$isSearchable() ? 'visibleCheckboxListOptions.length' : null"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($attributes->class([
                    'filament-forms-checkbox-list-component gap-1',
                    'space-y-2' => $gridDirection !== 'row',
                ]))
            "
        >
            @forelse ($getOptions() as $optionValue => $optionLabel)
                <div
                    wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.options.{{ $optionValue }}"
                >
                    <label
                        class="filament-forms-checkbox-list-component-option-label flex items-center space-x-3 rtl:space-x-reverse"
                        @if ($isSearchable())
                            x-show="
                                $el.querySelector('.filament-forms-checkbox-list-component-option-label-text')
                                    .innerText.toLowerCase()
                                    .includes(search.toLowerCase())
                            "
                        @endif
                    >
                        <input
                            wire:loading.attr="disabled"
                            type="checkbox"
                            value="{{ $optionValue }}"
                            dusk="filament.forms.{{ $getStatePath() }}"
                            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                            {{
                                $getExtraAttributeBag()
                                    ->class([
                                        'text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70',
                                        'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                                        'border-gray-300' => ! $errors->has($getStatePath()),
                                        'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                                        'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                                        'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                                    ])
                                    ->merge([
                                        'disabled' => $isDisabled(),
                                    ])
                            }}
                        />

                        <span
                            @class([
                                'filament-forms-checkbox-list-component-option-label-text text-sm font-medium text-gray-700',
                                'dark:text-gray-200' => config('forms.dark_mode'),
                            ])
                        >
                            {{ $optionLabel }}
                        </span>
                    </label>
                </div>
            @empty
                <div
                    wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.empty"
                ></div>
            @endforelse
        </x-filament-support::grid>

        @if ($isSearchable())
            <div
                x-cloak
                x-show="! visibleCheckboxListOptions.length"
                @class([
                    'filament-forms-checkbox-list-component-no-search-results-message text-sm text-gray-700',
                    'dark:text-gray-200' => config('forms.dark_mode'),
                ])
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
