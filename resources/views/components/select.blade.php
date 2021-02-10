@props([
    'autofocus' => false,
    'disabled' => false,
    'emptyOptionLabel' => null,
    'emptyOptionsMessage' => null,
    'errorKey' => null,
    'extraAttributes' => [],
    'name',
    'nameAttribute' => 'name',
    'options' => [],
    'placeholder' => null,
    'required' => false,
])

@pushonce('js:select-component')
    <script>
        function select(config) {
            return {
                autofocus: config.autofocus,

                data: config.data,

                emptyOptionLabel: config.emptyOptionLabel,

                emptyOptionsMessage: config.emptyOptionsMessage,

                focusedOptionIndex: null,

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

                getOptions: function () {
                    let options = this.data

                    if (! this.required) options = Object.assign({ null: this.emptyOptionLabel }, options)

                    return options
                },

                focusNextOption: function () {
                    if (this.focusedOptionIndex === null) {
                        this.focusedOptionIndex = Object.keys(this.options).length - 1

                        return
                    }

                    if (this.focusedOptionIndex + 1 >= Object.keys(this.options).length) return

                    this.focusedOptionIndex++

                    this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
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

                    this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                        block: 'center',
                    })
                },

                init: function () {
                    this.options = this.getOptions()

                    if (! (this.value in this.options)) this.value = Object.keys(this.options)[0]

                    if (this.autofocus) this.openListbox()

                    this.$watch('search', ((value) => {
                        if (! this.open || ! value) {
                            this.options = this.getOptions()

                            return
                        }

                        this.options = Object.keys(this.getOptions())
                            .filter((key) => this.getOptions()[key].toLowerCase().includes(value.toLowerCase()))
                            .reduce((options, key) => {
                                options[key] = this.getOptions()[key]
                                return options
                            }, {})
                    }))

                    this.$watch('value', ((value) => {
                        if (value in this.options) return

                        this.value = Object.keys(this.options)[0]
                    }))
                },

                openListbox: function () {
                    this.focusedOptionIndex = Object.keys(this.options).indexOf(this.value)

                    if (this.focusedOptionIndex < 0) this.focusedOptionIndex = 0

                    this.open = true
                },

                selectOption: function (index = null) {
                    if (! this.open) {
                        this.closeListbox()

                        return
                    }

                    this.value = Object.keys(this.options)[index ?? this.focusedOptionIndex]

                    this.closeListbox()
                },

                toggleListboxVisibility: function () {
                    if (this.open) {
                        this.closeListbox()

                        return
                    }

                    this.openListbox()

                    this.$nextTick(() => {
                        this.$refs.search.focus()

                        this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                            block: 'center'
                        })
                    })
                },
            }
        }
    </script>
@endpushonce

<div
    x-data="select({
        autofocus: {{ $autofocus ? 'true' : 'false' }},
        data: {{ json_encode($options) }},
        emptyOptionLabel: '{{ $emptyOptionLabel }}',
        emptyOptionsMessage: '{{ $emptyOptionsMessage }}',
        required: {{ $required ? 'true' : 'false' }},
        @if (Str::of($nameAttribute)->startsWith('wire:model')) value: @entangle($name){{ Str::of($nameAttribute)->after('wire:model') }}, @endif
    })"
    x-init="init()"
    x-on:click.away="closeListbox()"
    x-on:keydown.escape.stop="closeListbox()"
    {{ $attributes->merge(array_merge([
        'class' => 'relative',
    ], $extraAttributes)) }}
>
    @unless (Str::of($nameAttribute)->startsWith(['wire:model', 'x-model']))
        <input
            x-model="value"
            @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
            type="hidden"
        />
    @endif

    <button
        @unless($disabled)
            x-ref="button"
            x-on:click="toggleListboxVisibility()"
            x-bind:aria-expanded="open"
            aria-haspopup="listbox"
        @endunless
        type="button"
        class="bg-white relative w-full border border-gray-300 rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $disabled ? 'text-gray-500' : '' }}"
    >
        <span
            x-show="! open"
            x-text="value in options ? options[value] : '{{ $placeholder }}'"
            x-bind:class="{
                'text-gray-500': ! (value in options),
            }"
            class="block truncate"
        ></span>

        @unless ($disabled)
            <input
                x-ref="search"
                x-show="open"
                x-model="search"
                x-on:keydown.enter.stop.prevent="selectOption()"
                x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
                x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
                type="search"
                class="w-full h-full border-0 p-0 focus:ring-0 focus:outline-none"
            />

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <x-heroicon-s-selector class="w-5 h-5 text-gray-400" />
            </span>
        @endunless
    </button>

    @unless($disabled)
        <div
            x-show="open"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="absolute z-10 w-full mt-1 bg-white rounded shadow-sm border border-gray-300"
        >
            <ul
                x-ref="listbox"
                x-on:keydown.enter.stop.prevent="selectOption()"
                x-on:keydown.arrow-up.stop.prevent="focusPreviousOption()"
                x-on:keydown.arrow-down.stop.prevent="focusNextOption()"
                role="listbox"
                x-bind:aria-activedescendant="focusedOptionIndex ? '{{ $name }}' + 'Option' + focusedOptionIndex : null"
                tabindex="-1"
                class="py-1 overflow-auto text-base leading-6 rounded shadow-sm max-h-60 focus:outline-none"
            >
                <template x-for="(key, index) in Object.keys(options)" :key="index">
                    <li
                        x-bind:id="'{{ $name }}' + 'Option' + focusedOptionIndex"
                        x-on:click="selectOption(index)"
                        x-on:mouseenter="focusedOptionIndex = index"
                        x-on:mouseleave="focusedOptionIndex = null"
                        role="option"
                        x-bind:aria-selected="focusedOptionIndex === index"
                        x-bind:class="{
                            'text-white bg-blue-600': index === focusedOptionIndex,
                            'text-gray-900': index !== focusedOptionIndex,
                        }"
                        class="relative py-2 pl-3 text-gray-900 cursor-default select-none pr-9"
                    >
                            <span
                                x-text="Object.values(options)[index]"
                                x-bind:class="{
                                    'font-semibold': index === focusedOptionIndex,
                                    'font-normal': index !== focusedOptionIndex,
                                }"
                                class="block font-normal truncate"
                            ></span>

                        <span
                            x-show="key === value"
                            x-bind:class="{
                                'text-white': index === focusedOptionIndex,
                                'text-blue-600': index !== focusedOptionIndex,
                            }"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600"
                        >
                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </span>
                    </li>
                </template>

                <div
                    x-show="! Object.keys(options).length"
                    x-text="emptyOptionsMessage"
                    class="px-3 py-2 text-gray-900 cursor-default select-none"
                ></div>
            </ul>
        </div>
    @endunless
</div>
