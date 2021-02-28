@pushonce('filament-scripts:select-component')
    <script>
        function select(config) {
            return {
                autofocus: config.autofocus,

                displayValue: null,

                emptyOptionsMessage: config.emptyOptionsMessage,

                focusedOptionIndex: null,

                initialOptions: config.initialOptions,

                loading: false,

                name: config.name,

                noSearchResultsMessage: config.noSearchResultsMessage,

                open: false,

                options: {},

                required: config.required,

                search: '',

                value: config.value,

                clearValue: function () {
                    this.value = null
                    this.displayValue = null

                    this.closeListbox()
                },

                closeListbox: function () {
                    this.open = false

                    this.focusedOptionIndex = null

                    this.search = ''
                },

                evaluatePosition: function () {
                    let availableHeight = window.innerHeight - this.$refs.button.offsetHeight

                    let element = this.$refs.button

                    while (element) {
                        availableHeight -= element.offsetTop

                        element = element.offsetParent
                    }

                    if (this.$refs.listbox.offsetHeight <= availableHeight) {
                        this.$refs.listbox.style.bottom = 'auto'

                        return
                    }

                    this.$refs.listbox.style.bottom = `${this.$refs.button.offsetHeight}px`
                },

                focusNextOption: function () {
                    if (this.focusedOptionIndex === null) {
                        this.focusedOptionIndex = Object.keys(this.options).length - 1

                        return
                    }

                    if (this.focusedOptionIndex + 1 >= Object.keys(this.options).length) return

                    this.focusedOptionIndex++

                    this.$refs.listboxOptionsList.children[this.focusedOptionIndex].scrollIntoView({
                        block: 'center',
                    })
                },

                focusPreviousOption: function () {
                    if (this.focusedOptionIndex === null) {
                        this.focusedOptionIndex = 0

                        return
                    }

                    if (this.focusedOptionIndex <= 0) return

                    this.focusedOptionIndex--

                    this.$refs.listboxOptionsList.children[this.focusedOptionIndex].scrollIntoView({
                        block: 'center',
                    })
                },

                init: function () {
                    this.options = this.initialOptions

                    if (this.value in this.options) {
                        this.displayValue = this.options[this.value]
                    } else {
                        this.loading = true;

                        this.$wire.getSelectFieldDisplayValue(this.name, this.value).then((displayValue) => {
                            this.displayValue = displayValue

                            if (this.displayValue === null) {
                                this.value = null
                            }

                            this.loading = false
                        })
                    }

                    if (this.autofocus) this.openListbox()

                    this.$watch('search', (() => {
                        if (!this.open || this.search === '' || this.search === null) {
                            this.options = this.initialOptions
                            this.focusedOptionIndex = 0

                            return
                        }

                        this.loading = true

                        this.$wire.getSelectFieldOptionSearchResults(this.name, this.search).then((options) => {
                            this.options = options
                            this.focusedOptionIndex = 0
                            this.loading = false
                        })
                    }))

                    this.$watch('value', (() => {
                        if (this.value in this.options) {
                            this.displayValue = this.options[this.value]
                        } else {
                            this.loading = true;

                            this.$wire.getSelectFieldDisplayValue(this.name, this.value).then((displayValue) => {
                                this.displayValue = displayValue

                                if (this.displayValue === null) {
                                    this.value = null
                                }

                                this.loading = false
                            })
                        }
                    }))
                },

                openListbox: function () {
                    this.focusedOptionIndex = Object.keys(this.options).indexOf(this.value)

                    if (this.focusedOptionIndex < 0) this.focusedOptionIndex = 0

                    this.open = true

                    this.$nextTick(() => {
                        this.$refs.search.focus()

                        this.evaluatePosition()

                        this.$refs.listboxOptionsList.children[this.focusedOptionIndex].scrollIntoView({
                            block: 'center'
                        })
                    })
                },

                selectOption: function (index = null) {
                    if (!this.open) {
                        this.closeListbox()

                        return
                    }

                    this.value = Object.keys(this.options)[index ?? this.focusedOptionIndex]
                    this.displayValue = this.options[this.value]

                    this.closeListbox()
                },

                toggleListboxVisibility: function () {
                    if (this.open) {
                        this.closeListbox()

                        return
                    }

                    this.openListbox()
                },
            }
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->id"
    :help-message="__($formComponent->helpMessage)"
    :hint="__($formComponent->hint)"
    :label="__($formComponent->label)"
    :required="$formComponent->required"
>
    <div
        x-data="select({
            autofocus: {{ $formComponent->autofocus ? 'true' : 'false' }},
            emptyOptionsMessage: '{{ __($formComponent->emptyOptionsMessage) }}',
            initialOptions: {{ json_encode($formComponent->options) }},
            name: '{{ $formComponent->name }}',
            noSearchResultsMessage: '{{ __($formComponent->noSearchResultsMessage) }}',
            required: {{ $formComponent->required ? 'true' : 'false' }},
            @if (Str::of($formComponent->nameAttribute)->startsWith('wire:model'))
            value: @entangle($formComponent->name){{ Str::of($formComponent->nameAttribute)->after('wire:model') }},
            @endif
            })"
        x-init="init()"
        x-on:click.away="closeListbox()"
        x-on:keydown.escape.stop="closeListbox()"
        {!! $formComponent->id ? "id=\"{$formComponent->id}\"" : null !!}
        class="relative"
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    >
        @unless (Str::of($formComponent->nameAttribute)->startsWith(['wire:model', 'x-model']))
            <input
                x-model="value"
                {!! $formComponent->name ? "{$formComponent->nameAttribute}=\"{$formComponent->name}\"" : null !!}
                type="hidden"
            />
        @endif

        <div
            @unless($formComponent->disabled)
            x-ref="button"
            x-on:click="toggleListboxVisibility()"
            x-on:keydown.enter.stop.prevent="open ? selectOption() : openListbox()"
            x-on:keydown.space="if (! open) openListbox()"
            x-on:keydown.backspace="if (! search) clearValue()"
            x-on:keydown.clear="if (! search) clearValue()"
            x-on:keydown.delete="if (! search) clearValue()"
            x-bind:aria-expanded="open"
            aria-haspopup="listbox"
            tabindex="1"
            @endunless
            class="bg-white relative w-full border rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 {{ $formComponent->disabled ? 'text-gray-500' : '' }} {{ $errors->has($formComponent->name) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        >
            <span
                x-show="! open"
                x-text="displayValue ?? '{{ __($formComponent->placeholder) }}'"
                x-bind:class="{
                    'text-gray-400': displayValue === null,
                }"
                class="block truncate"
            ></span>

            @unless ($formComponent->disabled)
                <input
                    x-ref="search"
                    x-show="open"
                    x-model.debounce.500="search"
                    x-on:keydown.enter.stop.prevent="selectOption()"
                    x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
                    x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
                    type="search"
                    class="w-full h-full border-0 p-0 focus:ring-0 focus:outline-none"
                />

                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <x-heroicon-s-selector x-show="! loading" x-cloak class="w-5 h-5 text-gray-400" />

                    <svg x-show="loading" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor"
                         class="w-5 h-5 text-gray-400">
                        <path
                            d="M6.306 28.014c1.72 10.174 11.362 17.027 21.536 15.307C38.016 41.6 44.87 31.958 43.15 21.784l-4.011.678c1.345 7.958-4.015 15.502-11.974 16.847-7.959 1.346-15.501-4.014-16.847-11.973l-4.011.678z">
                        <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25"
                                          to="360 25 25" dur=".7s" repeatCount="indefinite" /></path>
                    </svg>
                </span>
            @endunless
        </div>

        @unless($formComponent->disabled)
            <div
                x-ref="listbox"
                x-show="open"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="listbox"
                x-bind:aria-activedescendant="focusedOptionIndex ? '{{ $formComponent->name }}' + 'Option' + focusedOptionIndex : null"
                tabindex="-1"
                x-cloak
                class="absolute z-10 w-full my-1 bg-white rounded shadow-sm border border-gray-300"
            >
                <ul
                    x-ref="listboxOptionsList"
                    class="py-1 overflow-auto text-base leading-6 rounded shadow-sm max-h-60 focus:outline-none"
                >
                    <template x-for="(key, index) in Object.keys(options)" :key="index">
                        <li
                            x-bind:id="'{{ $formComponent->name }}' + 'Option' + focusedOptionIndex"
                            x-on:click="selectOption(index)"
                            x-on:mouseenter="focusedOptionIndex = index"
                            x-on:mouseleave="focusedOptionIndex = null"
                            role="option"
                            x-bind:aria-selected="focusedOptionIndex === index"
                            x-bind:class="{
                                'text-white bg-secondary-600': index === focusedOptionIndex,
                                'text-gray-900': index !== focusedOptionIndex,
                            }"
                            class="relative py-2 pl-3 text-gray-900 cursor-default select-none pr-9"
                        >
                                <span
                                    x-text="Object.values(options)[index]"
                                    x-bind:class="{
                                        'font-medium': index === focusedOptionIndex,
                                        'font-normal': index !== focusedOptionIndex,
                                    }"
                                    class="block font-normal truncate"
                                ></span>

                            <span
                                x-show="key === value"
                                x-bind:class="{
                                    'text-white': index === focusedOptionIndex,
                                    'text-secondary-600': index !== focusedOptionIndex,
                                }"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-secondary-600"
                            >
                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </span>
                        </li>
                    </template>

                    <div
                        x-show="! Object.keys(options).length"
                        x-text="! search || loading ? emptyOptionsMessage : noSearchResultsMessage"
                        class="px-3 py-2 text-gray-900 cursor-default select-none text-sm"
                    ></div>
                </ul>
            </div>
        @endunless
    </div>
</x-forms::field-group>
