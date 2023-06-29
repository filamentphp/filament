<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $gridDirection = $getGridDirection();
        $isBulkToggleable = $isBulkToggleable();
        $isDisabled = $isDisabled();
        $isSearchable = $isSearchable();
        $statePath = $getStatePath();
    @endphp

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
                this.checkIfAllCheckboxesAreChecked()

                Livewire.hook('message.processed', () => {
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
                state = ! this.areAllCheckboxesChecked

                this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                    checkbox = checkboxLabel.querySelector('input[type=checkbox]')

                    checkbox.checked = state
                    checkbox.dispatchEvent(new Event('change'))
                })

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
        @if (! $isDisabled)
            @if ($isSearchable)
                <input
                    x-model.debounce.{{ $getSearchDebounce() }}="search"
                    type="search"
                    placeholder="{{ $getSearchPrompt() }}"
                    class="mb-2 block h-7 w-full rounded-lg border-gray-300 px-2 text-sm text-gray-700 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-primary-500"
                />
            @endif

            @if ($isBulkToggleable && count($getOptions()))
                <div
                    x-cloak
                    class="mb-2"
                    wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.actions"
                >
                    <span
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.actions.select_all"
                    >
                        {{ $getAction('selectAll') }}
                    </span>

                    <span
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.actions.deselect_all"
                    >
                        {{ $getAction('deselectAll') }}
                    </span>
                </div>
            @endif
        @endif

        <x-filament::grid
            :default="$getColumns('default')"
            :sm="$getColumns('sm')"
            :md="$getColumns('md')"
            :lg="$getColumns('lg')"
            :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')"
            :direction="$gridDirection ?? 'column'"
            :x-show="$isSearchable ? 'visibleCheckboxListOptions.length' : null"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($attributes->class([
                    'filament-forms-checkbox-list-component gap-1',
                    'space-y-2' => $gridDirection !== 'row',
                ]))
            "
        >
            @forelse ($getOptions() as $optionValue => $optionLabel)
                <div
                    wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.options.{{ $optionValue }}"
                >
                    <label
                        class="filament-forms-checkbox-list-component-option-label flex items-center space-x-3 rtl:space-x-reverse"
                        @if ($isSearchable)
                            x-show="
                                $el.querySelector('.filament-forms-checkbox-list-component-option-label-text')
                                    .innerText.toLowerCase()
                                    .includes(search.toLowerCase())
                            "
                        @endif
                    >
                        <input
                            @if ($isBulkToggleable)
                                x-on:change="checkIfAllCheckboxesAreChecked()"
                            @endif
                            {{
                                $getExtraAttributeBag()
                                    ->merge([
                                        'disabled' => $isDisabled,
                                        'type' => 'checkbox',
                                        'value' => $optionValue,
                                        'wire:loading.attr' => 'disabled',
                                        $applyStateBindingModifiers('wire:model') => $statePath,
                                    ], escape: false)
                                    ->class([
                                        'rounded text-primary-600 shadow-sm transition duration-75 focus:ring-2 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                                        'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500' => ! $errors->has($statePath),
                                        'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                                    ])
                            }}
                        />

                        <span
                            class="filament-forms-checkbox-list-component-option-label-text text-sm text-gray-700 dark:text-gray-200"
                        >
                            {{ $optionLabel }}
                        </span>
                    </label>
                </div>
            @empty
                <div
                    wire:key="{{ $this->id }}.{{ $statePath }}.{{ $field::class }}.empty"
                ></div>
            @endforelse
        </x-filament::grid>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="! visibleCheckboxListOptions.length"
                class="filament-forms-checkbox-list-component-no-search-results-message text-sm text-gray-700 dark:text-gray-200"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
