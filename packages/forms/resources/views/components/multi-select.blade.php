@pushonce('filament-scripts:multi-select-component')
    <script>
        function multiSelect(config) {
            return {
                autofocus: config.autofocus,

                emptyOptionsMessage: config.emptyOptionsMessage,

                focusedOptionIndex: null,

                initialOptions: config.initialOptions,

                name: config.name,

                noSearchResultsMessage: config.noSearchResultsMessage,

                open: false,

                options: {},

                required: config.required,

                search: '',

                value: config.value,

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
                    if (! this.value) {
                        this.value = []
                    }

                    this.setOptions()

                    if (this.autofocus) this.openListbox()

                    this.$watch('search', (() => {
                        if (! this.open || this.search === '' || this.search === null) {
                            this.setOptions()
                            this.focusedOptionIndex = 0

                            return
                        }

                        this.options = {}

                        for (const [key, label] of Object.entries(this.initialOptions)) {
                            if (
                                ! this.value.includes(key) &&
                                label.toLowerCase().includes(this.search.toLowerCase())
                            ) {
                                this.options[key] = label
                            }
                        }

                        this.focusedOptionIndex = 0
                    }))

                    this.$watch('value', (() => {
                        if (this.value) return

                        this.value = []
                    }))
                },

                openListbox: function () {
                    this.setOptions()

                    this.focusedOptionIndex = 0

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
                    if (! this.open) {
                        this.closeListbox()

                        return
                    }

                    this.value.push(Object.keys(this.options)[index ?? this.focusedOptionIndex])

                    this.closeListbox()
                },

                setOptions: function () {
                    this.options = {}

                    for (const [key, label] of Object.entries(this.initialOptions)) {
                        if (! this.value.includes(key)) {
                            this.options[key] = label
                        }
                    }
                },

                toggleListboxVisibility: function () {
                    if (this.open) {
                        this.closeListbox()

                        return
                    }

                    this.openListbox()
                },

                unselectOption: function (keyToUnselect) {
                    this.value = this.value.filter((key) => {
                        return key !== keyToUnselect
                    })
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
        x-data="multiSelect({
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

        @unless($formComponent->disabled)
            <div
                x-ref="button"
                x-on:click="toggleListboxVisibility()"
                x-on:keydown.enter.stop.prevent="open ? selectOption() : openListbox()"
                x-on:keydown.space="if (! open) openListbox()"
                x-bind:aria-expanded="open"
                aria-haspopup="listbox"
                tabindex="1"
                class="bg-white relative w-full border rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 {{ $formComponent->disabled ? 'text-gray-500' : '' }} {{ $errors->has($formComponent->name) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
            >
                <span
                    x-show="! open"
                    class="block truncate text-gray-400"
                >
                    {{ __($formComponent->placeholder) }}
                </span>

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
                    <x-heroicon-s-selector x-cloak class="w-5 h-5 text-gray-400" />
                </span>
            </div>

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
                        </li>
                    </template>

                    <div
                        x-show="! Object.keys(options).length"
                        x-text="! search ? emptyOptionsMessage : noSearchResultsMessage"
                        class="px-3 py-2 text-gray-900 cursor-default select-none text-sm"
                    ></div>
                </ul>
            </div>
        @endunless

        <div>
            <template x-for="key in value">
                <button
                    @unless($formComponent->disabled)
                        x-on:click="unselectOption(key)"
                    @endunless
                    type="button"
                    class="my-1 w-full flex justify-between space-x-2 items-center font-mono text-xs py-2 px-3 border border-gray-300 bg-gray-100 text-gray-800 rounded shadow-sm inline-block relative @unless($formComponent->disabled) cursor-pointer transition duration-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 hover:bg-gray-200 transition-colors duration-200 @else cursor-default @endunless"
                >
                    <span x-text="initialOptions[key] ?? key"></span>

                    @unless($formComponent->disabled)
                        <x-heroicon-s-x class="h-3 w-3 text-gray-500" />
                    @endunless
                </button>
            </template>
        </div>
    </div>
</x-forms::field-group>
