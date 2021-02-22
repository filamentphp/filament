@php
    if (! $formComponent->time) {
        if ($formComponent->displayFormat === 'F j, Y H:i:s') $formComponent->displayFormat = 'F j, Y';
        if ($formComponent->format === 'F j, Y H:i:s') $formComponent->format = 'Y-m-d';
    }

    if ($formComponent->time && $formComponent->withoutSeconds) {
        if ($formComponent->displayFormat === 'F j, Y H:i:s') $formComponent->displayFormat = 'F j, Y H:i';
    }
@endphp

@pushonce('filament-scripts:date-time-picker-component')
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

                hours: null,

                maxDate: config.maxDate,

                minDate: config.minDate,

                minutes: null,

                open: false,

                required: config.required,

                seconds: null,

                time: config.time,

                value: config.value,

                withoutSeconds: config.withoutSeconds,

                clearValue: function () {
                    this.setValue(null)

                    this.closePicker()
                },

                closePicker: function () {
                    this.open = false
                },

                dateIsDisabled: function (date) {
                    if (this.maxDate && date.isAfter(this.maxDate)) return true
                    if (this.minDate && date.isBefore(this.minDate)) return true

                    return false
                },

                dayIsDisabled: function (day) {
                    return this.dateIsDisabled(this.focusedDate.date(day))
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

                evaluatePosition: function () {
                    let availableHeight = window.innerHeight - this.$refs.button.offsetHeight

                    let element = this.$refs.button

                    while (element) {
                        availableHeight -= element.offsetTop

                        element = element.offsetParent
                    }

                    if (this.$refs.picker.offsetHeight <= availableHeight) {
                        this.$refs.picker.style.bottom = 'auto'

                        return
                    }

                    this.$refs.picker.style.bottom = `${this.$refs.button.offsetHeight}px`
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
                    this.maxDate = dayjs(this.maxDate)
                    if (! this.maxDate.isValid()) this.maxDate = null

                    this.minDate = dayjs(this.minDate)
                    if (! this.minDate.isValid()) this.minDate = null

                    let date = this.getSelectedDate() ?? dayjs()

                    if (this.maxDate !== null && date.isAfter(this.maxDate)) date = this.required ? this.maxDate : null
                    if (this.minDate !== null && date.isBefore(this.minDate)) date = this.required ? this.minDate : null

                    this.hour = date.hour()
                    this.minute = date.minute()
                    this.second = date.second()

                    if (this.required && ! this.getSelectedDate()) this.setValue(date)

                    this.setDisplayValue()

                    if (this.autofocus) this.openPicker()

                    this.$watch('focusedMonth', ((value) => {
                        this.focusedMonth = +value

                        if (this.focusedDate.month() === this.focusedMonth) return

                        this.focusedDate = this.focusedDate.set('month', this.focusedMonth)
                    }))

                    this.$watch('focusedYear', ((value) => {
                        this.focusedYear = Number.isInteger(+value) ? +value : dayjs().year()

                        if (this.focusedDate.year() === this.focusedYear) return

                        this.focusedDate = this.focusedDate.set('year', this.focusedYear)
                    }))

                    this.$watch('focusedDate', ((value) => {
                        this.focusedMonth = value.month()
                        this.focusedYear = value.year()

                        this.setupDaysGrid()

                        this.$nextTick(() => {
                            this.evaluatePosition()
                        })
                    }))

                    this.$watch('hour', ((value) => {
                        this.hour = Number.isInteger(+value) && value >= 0 && value < 24 ? +value : dayjs().hour()

                        let date = this.getSelectedDate()

                        if (date === null) return

                        this.setValue(date.set('hour', value))
                    }))

                    this.$watch('minute', ((value) => {
                        this.minute = Number.isInteger(+value) && value >= 0 && value < 60 ? +value : dayjs().minute()

                        let date = this.getSelectedDate()

                        if (date === null) return

                        this.setValue(date.set('minute', value))
                    }))

                    this.$watch('second', ((value) => {
                        this.second = Number.isInteger(+value) && value >= 0 && value < 60 ? +value : dayjs().second()

                        let date = this.getSelectedDate()

                        if (date === null) return

                        this.setValue(date.set('second', value))
                    }))
                },

                openPicker: function () {
                    this.focusedDate = this.getSelectedDate() ?? dayjs()

                    this.setupDaysGrid()

                    this.open = true

                    this.$nextTick(() => {
                        this.evaluatePosition()
                    })
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
                    if (date === null) {
                        if (this.required) {
                            date = dayjs()

                            if (this.maxDate !== null && date.isAfter(this.maxDate)) date = this.maxDate
                            if (this.minDate !== null && date.isBefore(this.minDate)) date = this.minDate
                        } else {
                            this.value = null

                            this.setDisplayValue()

                            return
                        }
                    } else {
                        if (this.dateIsDisabled(date)) return
                    }

                    this.value = date
                        .set('hour', this.hour)
                        .set('minute', this.minute)
                        .set('second', this.second)
                        .format(this.format)

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
        x-data="dateTimePicker({
            autofocus: {{ $formComponent->autofocus ? 'true' : 'false' }},
            displayFormat: '{{ convert_date_format($formComponent->displayFormat)->to('day.js') }}',
            format: '{{ convert_date_format($formComponent->format)->to('day.js') }}',
            maxDate: '{{ $formComponent->maxDate }}',
            minDate: '{{ $formComponent->minDate }}',
            name: '{{ $formComponent->name }}',
            placeholder: '{{ __($formComponent->placeholder) }}',
            required: {{ $formComponent->required ? 'true' : 'false' }},
            time: {{ $formComponent->time ? 'true' : 'false' }},
            @if (Str::of($formComponent->nameAttribute)->startsWith('wire:model'))
                value: @entangle($formComponent->name){{ Str::of($formComponent->nameAttribute)->after('wire:model') }},
            @endif
            withoutSeconds: {{ $formComponent->withoutSeconds ? 'true' : 'false' }},
        })"
        x-init="init()"
        x-on:click.away="closePicker()"
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

        <button
            @unless($formComponent->disabled)
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
                aria-label="{{ __($formComponent->placeholder) }}"
            @endunless
            type="button"
            class="bg-white relative w-full border rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 {{ $formComponent->disabled ? 'text-gray-500' : '' }} {{ $errors->has($formComponent->name) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        >
            <input
                readonly
                placeholder="{{ __($formComponent->placeholder) }}"
                x-model="displayValue"
                class="w-full h-full border-0 p-0 placeholder-gray-400 focus:placeholder-gray-500 focus:ring-0 focus:outline-none"
            />

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <x-heroicon-s-calendar class="w-5 h-5 text-gray-400" />
            </span>
        </button>

        @unless ($formComponent->disabled)
            <div
                x-ref="picker"
                x-on:click.away="closePicker()"
                x-show.transition="open"
                aria-modal="true"
                role="dialog"
                x-cloak
                class="bg-white border border-gray-300 my-1 rounded shadow-sm p-4 absolute w-64 z-10"
            >
                <div class="space-y-3">
                    <div class="flex items-center justify-between space-x-1">
                        <select
                            x-model="focusedMonth"
                            class="flex-grow text-lg font-medium text-gray-800 cursor-pointer border-0 p-0 focus:ring-0 focus:outline-none"
                        >
                            <template x-for="(month, index) in [
                                '{{ __('forms::fields.dateTimePicker.months.january') }}',
                                '{{ __('forms::fields.dateTimePicker.months.february') }}',
                                '{{ __('forms::fields.dateTimePicker.months.march') }}',
                                '{{ __('forms::fields.dateTimePicker.months.april') }}',
                                '{{ __('forms::fields.dateTimePicker.months.may') }}',
                                '{{ __('forms::fields.dateTimePicker.months.june') }}',
                                '{{ __('forms::fields.dateTimePicker.months.july') }}',
                                '{{ __('forms::fields.dateTimePicker.months.august') }}',
                                '{{ __('forms::fields.dateTimePicker.months.september') }}',
                                '{{ __('forms::fields.dateTimePicker.months.october') }}',
                                '{{ __('forms::fields.dateTimePicker.months.november') }}',
                                '{{ __('forms::fields.dateTimePicker.months.december') }}',
                            ]">
                                <option x-bind:value="index" x-text="month"></option>
                            </template>
                        </select>

                        <input
                            type="number"
                            x-model.debounce="focusedYear"
                            class="text-right w-20 text-lg text-gray-600 border-0 p-0 focus:ring-0 focus:outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="(day, index) in [
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.sunday') }}',
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.monday') }}',
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.tuesday') }}',
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.wednesday') }}',
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.thursday') }}',
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.friday') }}',
                            '{{ __('forms::fields.dateTimePicker.abbreviatedDays.saturday') }}',
                        ]" :key="index">
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
                                x-on:click="dayIsDisabled(day) || selectDate(day)"
                                x-on:mouseenter="setFocusedDay(day)"
                                role="option"
                                x-bind:aria-selected="focusedDate.date() === day"
                                x-bind:class="{
                                    'bg-secondary-600 text-white': dayIsSelected(day),
                                    'text-gray-700': ! dayIsSelected(day),
                                    'bg-secondary-50': dayIsToday(day) && ! dayIsSelected(day) && focusedDate.date() !== day && ! dayIsDisabled(day),
                                    'bg-secondary-200': focusedDate.date() === day && ! dayIsSelected(day),
                                    'bg-gray-100': dayIsDisabled(day) && focusedDate.date() !== day,
                                    'cursor-pointer': ! dayIsDisabled(day),
                                    'cursor-not-allowed': dayIsDisabled(day),
                                }"
                                class="text-center text-sm leading-none rounded leading-loose transition ease-in-out duration-100"
                            ></div>
                        </template>
                    </div>

                    @if ($formComponent->time)
                        <div class="flex items-center justify-center bg-gray-100 rounded py-2">
                            <input
                                type="number"
                                x-model.debounce="hour"
                                class="text-center bg-gray-100 w-16 text-xl text-gray-700 border-0 p-0 pr-1 focus:ring-0 focus:outline-none"
                            />

                            <span class="text-xl bg-gray-100 font-medium text-gray-700">:</span>

                            <input
                                type="number"
                                x-model.debounce="minute"
                                class="text-center bg-gray-100 w-16 text-xl text-gray-700 border-0 p-0 pr-1 focus:ring-0 focus:outline-none"
                            />

                            @unless ($formComponent->withoutSeconds)
                                <span class="text-xl bg-gray-100 font-medium text-gray-700">:</span>

                                <input
                                    type="number"
                                    x-model.debounce="second"
                                    class="text-center bg-gray-100 w-16 text-xl text-gray-700 border-0 p-0 pr-1 focus:ring-0 focus:outline-none"
                                />
                            @endunless
                        </div>
                    @endif
                </div>
            </div>
        @endunless
    </div>
</x-forms::field-group>
