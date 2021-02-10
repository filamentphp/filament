@props([
    'autofocus' => false,
    'disabled' => false,
    'displayFormat' => 'MMM D, YYYY',
    'errorKey' => null,
    'extraAttributes' => [],
    'format' => 'YYYY-MM-DD',
    'max' => null,
    'min' => null,
    'name',
    'nameAttribute' => 'name',
    'placeholder' => null,
    'required' => false,
    'time' => false,
])

@php
    if ($time) {
        if ($displayFormat === 'MMM D, YYYY') $format = 'M j, Y H:mm';
        if ($format === 'YYYY-MM-DD') $format = 'YYYY-MM-DD HH:mm';
    }
@endphp

@pushonce('js:date-time-picker-component')
    <script src="https://unpkg.com/dayjs@1.8.21/dayjs.min.js"></script>
    <script src="https://unpkg.com/dayjs@1.8.21/plugin/customParseFormat.js"></script>

    <script>
        dayjs.extend(window.dayjs_plugin_customParseFormat)

        function dateTimePicker(config) {
            return {
                activeMonth: null,

                activeYear: null,

                autofocus: config.autofocus,

                days: [],

                displayFormat: config.displayFormat,

                displayValue: '',

                emptyDays: [],

                focusedDay: null,

                format: config.format,

                max: config.max,

                min: config.min,

                open: false,

                required: config.required,

                time: config.time,

                value: config.value,

                clearValue: function () {
                    this.displayValue = null
                    this.value = null
                },

                closePicker: function () {
                    this.open = false

                    this.focusedDay = null
                },

                dayIsSelected: function (day) {
                    let selectedDate = this.getSelectedDate()

                    if (selectedDate === null) return false

                    return selectedDate.date() === day &&
                        selectedDate.month() === this.activeMonth &&
                        selectedDate.year() === this.activeYear
                },

                dayIsToday: function (day) {
                    let date = dayjs()

                    return date.date() === day &&
                        date.month() === this.activeMonth &&
                        date.year() === this.activeYear
                },

                decrementActiveMonth: function () {
                    if (this.activeMonth === 0) {
                        this.activeMonth = 12
                        this.activeYear--
                    }

                    this.activeMonth--
                },

                focusNextDay: function () {
                    if (this.focusedDay === null) {
                        let selectedDate = this.getSelectedDate()

                        if (selectedDate === null) {
                            this.focusedDay = this.days.length

                            return
                        }

                        this.focusedDay = selectedDate.date()
                    }

                    if (this.focusedDay >= this.days.length) {
                        this.incrementActiveMonth()
                        this.focusedDay = 1

                        return
                    }

                    this.focusedDay++
                },

                focusPreviousDay: function () {
                    if (this.focusedDay === null) {
                        let selectedDate = this.getSelectedDate()

                        if (selectedDate === null) {
                            this.focusedDay = 1

                            return
                        }

                        this.focusedDay = selectedDate.date()
                    }

                    if (this.focusedDay <= 1) {
                        this.decrementActiveMonth()
                        this.focusedDay = this.days.length

                        return
                    }

                    this.focusedDay--
                },

                getDaysInMonth: function () {
                    let date = dayjs(new Date(
                        this.activeYear,
                        this.activeMonth,
                    ))

                    return date.daysInMonth()
                },

                getFirstDayOfMonth: function () {
                    let date = dayjs(new Date(
                        this.activeYear,
                        this.activeMonth,
                        1,
                    ))

                    return date.day()
                },

                getSelectedDate: function () {
                    let date = dayjs(this.value, this.format)

                    if (! date.isValid()) return

                    return date
                },

                incrementActiveMonth: function () {
                    if (this.activeMonth === 11) {
                        this.activeMonth = -1
                        this.activeYear++
                    }

                    this.activeMonth++
                },

                init: function () {
                    this.resetActiveMonth()

                    if (this.autofocus) this.openPicker()

                    this.$watch('activeMonth', ((value) => {
                        this.activeMonth = +value

                        this.focusedDay = null

                        this.renderDays()
                    }))

                    this.$watch('activeYear', ((value) => {
                        if (! Number.isInteger(+value)) this.activeYear = dayjs().year()

                        this.activeYear = +value

                        this.focusedDay = null

                        this.renderDays()
                    }))
                },

                openPicker: function () {
                    this.open = true

                    this.resetActiveMonth()
                },

                renderDays: function () {
                    let emptyDays = []
                    for (let i = 1; i <= this.getFirstDayOfMonth(); i++) {
                        emptyDays.push(i)
                    }
                    this.emptyDays = emptyDays

                    let days = []
                    for (let i = 1; i <= this.getDaysInMonth(); i++) {
                        days.push(i)
                    }
                    this.days = days
                },

                resetActiveMonth: function () {
                    let date = this.getSelectedDate()

                    if (date) {
                        this.setDisplayValue(date)
                    } else {
                        date = dayjs()
                    }

                    this.activeMonth = date.month()
                    this.activeYear = date.year()

                    this.renderDays()
                },

                selectDate: function (day = null) {
                    if (day) this.focusedDay = day

                    let date = dayjs(new Date(
                        this.activeYear,
                        this.activeMonth,
                        this.focusedDay,
                    ))

                    this.value = date.format(this.format)

                    this.setDisplayValue(date)
                },

                setDisplayValue: function (date) {
                    this.displayValue = date.format(this.displayFormat)
                },

                togglePickerVisibility: function () {
                    if (this.open) {
                        this.closePicker()

                        return
                    }

                    this.openPicker()
                },
            }
        }
    </script>
@endpushonce

<div
    x-data="dateTimePicker({
        autofocus: {{ $autofocus ? 'true' : 'false' }},
        displayFormat: '{{ $displayFormat }}',
        format: '{{ $format }}',
        name: '{{ $name }}',
        placeholder: '{{ $placeholder }}',
        required: {{ $required ? 'true' : 'false' }},
        @if (Str::of($nameAttribute)->startsWith('wire:model')) value: @entangle($name){{ Str::of($nameAttribute)->after('wire:model') }}, @endif
    })"
    x-init="init()"
    x-on:click.away="closePicker()"
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
            x-on:click="togglePickerVisibility()"
            x-on:keydown.enter.stop.prevent="open ? selectDate() : openPicker()"
            x-on:keydown.arrow-left.stop.prevent="focusPreviousDay()"
            x-on:keydown.arrow-right.stop.prevent="focusNextDay()"
            x-on:keydown.arrow-up.stop.prevent="decrementActiveMonth()"
            x-on:keydown.arrow-down.stop.prevent="incrementActiveMonth()"
            x-on:keydown.escape.stop="closePicker()"
            x-bind:aria-expanded="open"
            aria-label="{{ $placeholder }}"
        @endunless
        type="button"
        class="bg-white relative w-full border border-gray-300 rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $disabled ? 'text-gray-500' : '' }}"
    >
        <input
            readonly
            placeholder="{{ $placeholder }}"
            x-model="displayValue"
            class="w-full h-full border-0 p-0 focus:ring-0 focus:outline-none"
        />

        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <x-heroicon-s-calendar class="w-5 h-5 text-gray-400" />
        </span>
    </button>

    @unless ($disabled)
        <div
            x-on:click.away="closePicker()"
            x-show.transition="open"
            aria-modal="true"
            role="dialog"
            x-cloak
            class="bg-white border border-gray-300 mt-1 rounded shadow-sm p-4 absolute w-64 z-10"
        >
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <select
                        x-model="activeMonth"
                        class="flex-grow text-lg font-medium text-gray-800 cursor-pointer border-0 p-0 focus:ring-0 focus:outline-none"
                    >
                        <template x-for="(month, index) in ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']">
                            <option x-bind:value="index" x-text="month"></option>
                        </template>
                    </select>

                    <input
                        type="number"
                        x-model.debounce="activeYear"
                        class="ml-1 text-right w-20 text-lg text-gray-600 font-normal border-0 p-0 pr-1 focus:ring-0 focus:outline-none"
                    />
                </div>

                <div class="grid grid-cols-7 gap-1">
                    <template x-for="(day, index) in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="index">
                        <div
                            x-text="day"
                            class="text-gray-800 font-medium text-center text-xs"
                        ></div>
                    </template>
                </div>

                <div role="grid" class="grid grid-cols-7 gap-1">
                    <template x-for="day in emptyDays" x-bind:key="day">
                        <div class="text-center border border-transparent text-sm"></div>
                    </template>

                    <template x-for="day in days" x-bind:key="day">
                        <div
                            x-text="day"
                            x-on:click="selectDate(day)"
                            x-on:mouseenter="focusedDay = day"
                            x-on:mouseleave="focusedDay = null"
                            role="option"
                            x-bind:aria-selected="focusedDay === day"
                            x-bind:class="{
                                'bg-blue-600 text-white': dayIsSelected(day),
                                'text-gray-700': ! dayIsSelected(day),
                                'bg-blue-50': dayIsToday(day) && ! dayIsSelected(day) && day !== focusedDay,
                                'bg-blue-200': day === focusedDay && ! dayIsSelected(day),
                            }"
                            class="cursor-pointer text-center text-sm leading-none rounded leading-loose transition ease-in-out duration-100"
                        ></div>
                    </template>
                </div>

                <div class="flex">
                    <button
                        x-on:click="decrementActiveMonth()"
                        type="button"
                        class="focus:outline-none focus:shadow-outline transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded"
                    >
                        <x-heroicon-o-chevron-left class="h-6 w-6 text-gray-500 inline-flex" />
                    </button>

                    <div class="flex-grow text-center">
                        @unless ($required)
                            <button
                                type="button"
                                x-on:click="clearValue()"
                                x-show="displayValue"
                                class="focus:outline-none focus:shadow-outline transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded"
                            >
                                <x-heroicon-o-x class="h-6 w-6 text-gray-500 inline-flex" />
                            </button>
                        @endunless
                    </div>

                    <button
                        type="button"
                        x-on:click="incrementActiveMonth()"
                        class="focus:outline-none focus:shadow-outline transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded"
                    >
                        <x-heroicon-o-chevron-right class="h-6 w-6 text-gray-500 inline-flex" />
                    </button>
                </div>
            </div>
        </div>
    @endunless
</div>
