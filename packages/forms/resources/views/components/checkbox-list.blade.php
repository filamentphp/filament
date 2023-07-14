<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $gridDirection = $getGridDirection() ?? 'column';
        $isBulkToggleable = $isBulkToggleable();
        $isDisabled = $isDisabled();
        $isSearchable = $isSearchable();
        $statePath = $getStatePath();
    @endphp

    <div
        x-data="{
            areAllCheckboxesChecked: false,

            checkboxListOptions: Array.from(
                $root.querySelectorAll('.fi-fo-checkbox-list-option-label'),
            ),

            search: '',

            visibleCheckboxListOptions: [],

            init: function () {
                this.updateVisibleCheckboxListOptions()
                this.checkIfAllCheckboxesAreChecked()

                Livewire.hook(
                    'commit',
                    ({ component, commit, succeed, fail, respond }) => {
                        succeed(({ snapshot, effect }) => {
                            if (component.id !== @js($this->getId())) {
                                return
                            }

                            this.updateVisibleCheckboxListOptions()

                            this.checkIfAllCheckboxesAreChecked()
                        })
                    },
                )

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
                            .querySelector('.fi-fo-checkbox-list-option-label-text')
                            .innerText.toLowerCase()
                            .includes(this.search.toLowerCase())
                    },
                )
            },
        }"
    >
        @if (! $isDisabled)
            @if ($isSearchable)
                <x-filament-forms::affixes
                    inline-prefix
                    prefix-icon="heroicon-m-magnifying-glass"
                    prefix-icon-alias="forms:components.checkbox-list.search-field"
                    class="mb-4"
                >
                    <x-filament::input
                        inline-prefix
                        :placeholder="$getSearchPrompt()"
                        type="search"
                        :attributes="
                            \Filament\Support\prepare_inherited_attributes(
                                new \Illuminate\View\ComponentAttributeBag([
                                    'x-model.debounce.' . $getSearchDebounce() => 'search',
                                ])
                            )
                        "
                    />
                </x-filament-forms::affixes>
            @endif

            @if ($isBulkToggleable && count($getOptions()))
                <div
                    x-cloak
                    class="mb-2"
                    wire:key="{{ $this->getId() }}.{{ $getStatePath() }}.{{ $field::class }}.actions"
                >
                    <span
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.select_all"
                    >
                        {{ $getAction('selectAll') }}
                    </span>

                    <span
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.actions.deselect_all"
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
            :direction="$gridDirection"
            :x-show="$isSearchable ? 'visibleCheckboxListOptions.length' : null"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($attributes)->class([
                    'fi-fo-checkbox-list gap-2',
                    '-mt-2' => $gridDirection === 'column',
                ])
            "
        >
            @forelse ($getOptions() as $optionValue => $optionLabel)
                <div
                    wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.options.{{ $optionValue }}"
                    @if ($isSearchable)
                        x-show="
                            $el.querySelector('.fi-fo-checkbox-list-option-label-text')
                                .innerText.toLowerCase()
                                .includes(search.toLowerCase())
                        "
                    @endif
                    @class([
                        'break-inside-avoid pt-2' => $gridDirection === 'column',
                    ])
                >
                    <label
                        class="fi-fo-checkbox-list-option-label flex items-center gap-x-3"
                    >
                        <x-filament::input.checkbox
                            :errors="$errors"
                            :state-path="$statePath"
                            :attributes="
                                $getExtraAttributeBag()
                                    ->merge([
                                        'disabled' => $isDisabled,
                                        'type' => 'checkbox',
                                        'value' => $optionValue,
                                        'wire:loading.attr' => 'disabled',
                                        $applyStateBindingModifiers('wire:model') => $statePath,
                                        'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                    ], escape: false)
                            "
                        />

                        <span
                            class="fi-fo-checkbox-list-option-label-text text-sm font-medium text-gray-950 dark:text-white"
                        >
                            {{ $optionLabel }}
                        </span>
                    </label>
                </div>
            @empty
                <div
                    wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.empty"
                ></div>
            @endforelse
        </x-filament::grid>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="! visibleCheckboxListOptions.length"
                class="fi-fo-checkbox-list-no-search-results-message text-sm text-gray-500 dark:text-gray-400"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
