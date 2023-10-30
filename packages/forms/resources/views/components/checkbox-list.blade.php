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

                $nextTick(() => {
                    this.checkIfAllCheckboxesAreChecked()
                })

                Livewire.hook(
                    'commit',
                    ({ component, commit, succeed, fail, respond }) => {
                        succeed(({ snapshot, effect }) => {
                            $nextTick(() => {
                                if (component.id !== @js($this->getId())) {
                                    return
                                }

                                this.checkboxListOptions = Array.from(
                                    $root.querySelectorAll(
                                        '.fi-fo-checkbox-list-option-label',
                                    ),
                                )

                                this.updateVisibleCheckboxListOptions()

                                this.checkIfAllCheckboxesAreChecked()
                            })
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
                        if (
                            checkboxListItem
                                .querySelector('.fi-fo-checkbox-list-option-label')
                                ?.innerText.toLowerCase()
                                .includes(this.search.toLowerCase())
                        ) {
                            return true
                        }

                        return checkboxListItem
                            .querySelector('.fi-fo-checkbox-list-option-description')
                            ?.innerText.toLowerCase()
                            .includes(this.search.toLowerCase())
                    },
                )
            },
        }"
    >
        @if (! $isDisabled)
            @if ($isSearchable)
                <x-filament::input.wrapper
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
                </x-filament::input.wrapper>
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
                \Filament\Support\prepare_inherited_attributes($attributes)
                    ->merge($getExtraAttributes(), escape: false)
                    ->class([
                        'fi-fo-checkbox-list gap-4',
                        '-mt-4' => $gridDirection === 'column',
                    ])
            "
        >
            @forelse ($getOptions() as $value => $label)
                <div
                    wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.options.{{ $value }}"
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
                    @class([
                        'break-inside-avoid pt-4' => $gridDirection === 'column',
                    ])
                >
                    <label
                        class="fi-fo-checkbox-list-option-label flex gap-x-3"
                    >
                        <x-filament::input.checkbox
                            :error="$errors->has($statePath)"
                            :attributes="
                                \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                                    ->merge([
                                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                        'value' => $value,
                                        'wire:loading.attr' => 'disabled',
                                        $applyStateBindingModifiers('wire:model') => $statePath,
                                        'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                    ], escape: false)
                                    ->class(['mt-1'])
                            "
                        />

                        <div class="grid text-sm leading-6">
                            <span
                                class="fi-fo-checkbox-list-option-label font-medium text-gray-950 dark:text-white"
                            >
                                {{ $label }}
                            </span>

                            @if ($hasDescription($value))
                                <p
                                    class="fi-fo-checkbox-list-option-description text-gray-500 dark:text-gray-400"
                                >
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
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
                x-show="search && ! visibleCheckboxListOptions.length"
                class="fi-fo-checkbox-list-no-search-results-message text-sm text-gray-500 dark:text-gray-400"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
