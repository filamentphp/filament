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
                autofocus: config.autofocus,

                daysInFocusedMonth: [],

                displayFormat: config.displayFormat,

                displayValue: '',

                emptyDaysInFocusedMonth: [],

                focusedDate: null,

                focusedMonth: null,

                focusedYear: null,

                format: config.format,

                max: config.max,

                min: config.min,

                open: false,

                required: config.required,

                time: config.time,

                value: config.value,

                clearValue: function () {
                    this.setValue(null)
                },

                closePicker: function () {
                    this.open = false
                },

                dayIsSelected: function (day) {
                    let selectedDate = this.getSelectedDate()

                    if (selectedDate === null) return false

                    return selectedDate.date() === day &&
                        selectedDate.month() === this.focusedDate.month() &&
                        selectedDate.year() === this.focusedDate.year()
                },

                dayIsToday: function (day) {
                    let date = dayjs()

                    return date.date() === day &&
                        date.month() === this.focusedDate.month() &&
                        date.year() === this.focusedDate.year()
                },

                focusPreviousDay: function () {
                    this.focusedDate = this.focusedDate.subtract(1, 'day')
                },

                focusPreviousWeek: function () {
                    this.focusedDate = this.focusedDate.subtract(1, 'week')
                },

                focusNextDay: function () {
                    this.focusedDate = this.focusedDate.add(1, 'day')
                },

                focusNextWeek: function () {
                    this.focusedDate = this.focusedDate.add(1, 'week')
                },

                getSelectedDate: function () {
                    let date = dayjs(this.value, this.format)

                    if (! date.isValid()) return null

                    return date
                },

                init: function () {
                    this.$watch('focusedMonth', ((value) => {
                        this.focusedMonth = +value

                        this.setupDaysGrid()

                        if (this.focusedDate.month() === this.focusedMonth) return

                        this.focusedDate = this.focusedDate.month(this.focusedMonth)
                    }))

                    this.$watch('focusedYear', ((value) => {
                        this.focusedYear = Number.isInteger(value) ? +value : dayjs().year()

                        this.setupDaysGrid()

                        if (this.focusedDate.year() === this.focusedYear) return

                        this.focusedDate = this.focusedDate.year(this.focusedYear)
                    }))

                    this.$watch('focusedDate', ((value) => {
                        this.focusedMonth = value.month()
                        this.focusedYear = value.year()
                    }))

                    let date = this.getSelectedDate()

                    if (date === null && this.required) {
                        this.setValue(dayjs())
                    }

                    this.setDisplayValue()

                    if (this.autofocus) this.openPicker()
                },

                openPicker: function () {
                    this.focusedDate = this.getSelectedDate() ?? dayjs()

                    this.setupDaysGrid()

                    this.open = true
                },

                selectDate: function (day = null) {
                    if (day) this.setFocusedDay(day)

                    this.setValue(this.focusedDate)
                },

                setDisplayValue: function () {
                    this.displayValue = this.getSelectedDate() ? this.getSelectedDate().format(this.displayFormat) : ''
                },

                setupDaysGrid: function () {
                    this.emptyDaysInFocusedMonth = Array.from({
                        length: this.focusedDate.date(1).day(),
                    }, (_, i) => i + 1)

                    this.daysInFocusedMonth = Array.from({
                        length: this.focusedDate.daysInMonth(),
                    }, (_, i) => i + 1)
                },

                setFocusedDay: function (day) {
                    this.focusedDate = this.focusedDate.date(day)
                },

                setValue: function (date) {
                    if (date === null && this.required) date = dayjs()

                    this.value = date ? date.format(this.format) : null

                    this.setDisplayValue()
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
            x-on:keydown.arrow-up.stop.prevent="focusPreviousWeek()"
            x-on:keydown.arrow-down.stop.prevent="focusNextWeek()"
            x-on:keydown.backspace.stop.prevent="clearValue()"
            x-on:keydown.clear.stop.prevent="clearValue()"
            x-on:keydown.delete.stop.prevent="clearValue()"
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
                        x-model="focusedMonth"
                        class="flex-grow text-lg font-medium text-gray-800 cursor-pointer border-0 p-0 focus:ring-0 focus:outline-none"
                    >
                        <template x-for="(month, index) in ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']">
                            <option x-bind:value="index" x-text="month"></option>
                        </template>
                    </select>

                    <input
                        type="number"
                        x-model.debounce="focusedYear"
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
                    <template x-for="day in emptyDaysInFocusedMonth" x-bind:key="day">
                        <div class="text-center border border-transparent text-sm"></div>
                    </template>

                    <template x-for="day in daysInFocusedMonth" x-bind:key="day">
                        <div
                            x-text="day"
                            x-on:click="selectDate(day)"
                            x-on:mouseenter="setFocusedDay(day)"
                            role="option"
                            x-bind:aria-selected="focusedDate.date() === day"
                            x-bind:class="{
                                'bg-blue-600 text-white': dayIsSelected(day),
                                'text-gray-700': ! dayIsSelected(day),
                                'bg-blue-50': dayIsToday(day) && ! dayIsSelected(day) && focusedDate.date() !== day,
                                'bg-blue-200': focusedDate.date() === day && ! dayIsSelected(day),
                            }"
                            class="cursor-pointer text-center text-sm leading-none rounded leading-loose transition ease-in-out duration-100"
                        ></div>
                    </template>
                </div>
            </div>
        </div>
    @endunless
</div>
