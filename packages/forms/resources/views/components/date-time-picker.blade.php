@php
    if (! $formComponent->hasTime()) {
        if ($formComponent->getDisplayFormat() === 'F j, Y H:i:s') $formComponent->displayFormat('F j, Y');
        if ($formComponent->getFormat() === 'F j, Y H:i:s') $formComponent->format('Y-m-d');
    }

    if ($formComponent->hasTime() && ! $formComponent->hasSeconds()) {
        if ($formComponent->getDisplayFormat() === 'F j, Y H:i:s') $formComponent->displayFormat('F j, Y H:i');
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

                    this.$watch('focusedMonth', () => {
                        this.focusedMonth = +this.focusedMonth

                        if (this.focusedDate.month() === this.focusedMonth) return

                        this.focusedDate = this.focusedDate.set('month', this.focusedMonth)
                    })

                    this.$watch('focusedYear', () => {
                        this.focusedYear = Number.isInteger(+this.focusedYear) ? +this.focusedYear : dayjs().year()

                        if (this.focusedDate.year() === this.focusedYear) return

                        this.focusedDate = this.focusedDate.set('year', this.focusedYear)
                    })

                    this.$watch('focusedDate', () => {
                        this.focusedMonth = this.focusedDate.month()
                        this.focusedYear = this.focusedDate.year()

                        this.setupDaysGrid()

                        this.$nextTick(() => {
                            this.evaluatePosition()
                        })
                    })

                    this.$watch('hour', () => {
                        this.hour = Number.isInteger(+this.hour) && this.hour >= 0 && this.hour < 24 ? +this.hour : dayjs().hour()

                        let date = this.getSelectedDate()

                        if (date === null) return

                        this.setValue(date.set('hour', this.hour))
                    })

                    this.$watch('minute', () => {
                        this.minute = Number.isInteger(+this.minute) && this.minute >= 0 && this.minute < 60 ? +this.minute : dayjs().minute()

                        let date = this.getSelectedDate()

                        if (date === null) return

                        this.setValue(date.set('minute', this.minute))
                    })

                    this.$watch('second', () => {
                        this.second = Number.isInteger(+this.second) && this.second >= 0 && this.second < 60 ? +this.second : dayjs().second()

                        let date = this.getSelectedDate()

                        if (date === null) return

                        this.setValue(date.set('second', this.second))
                    })

                    this.$watch('value', () => {
                        let date = this.getSelectedDate() ?? dayjs()

                        if (this.maxDate !== null && date.isAfter(this.maxDate)) date = this.required ? this.maxDate : null
                        if (this.minDate !== null && date.isBefore(this.minDate)) date = this.required ? this.minDate : null

                        this.hour = date.hour()
                        this.minute = date.minute()
                        this.second = date.second()

                        if (this.required && ! this.getSelectedDate()) this.setValue(date)

                        this.setDisplayValue()
                    })
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
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <div
        x-data="dateTimePicker({
            autofocus: {{ $formComponent->isAutofocused() ? 'true' : 'false' }},
            displayFormat: '{{ convert_date_format($formComponent->getDisplayFormat())->to('day.js') }}',
            format: '{{ convert_date_format($formComponent->getFormat())->to('day.js') }}',
            maxDate: '{{ $formComponent->getMaxDate() }}',
            minDate: '{{ $formComponent->getMinDate() }}',
            name: '{{ $formComponent->getName() }}',
            placeholder: '{{ __($formComponent->getPlaceholder()) }}',
            required: {{ $formComponent->isRequired() ? 'true' : 'false' }},
            time: {{ $formComponent->hasTime() ? 'true' : 'false' }},
            @if (Str::of($formComponent->getBindingAttribute())->startsWith('wire:model'))
                value: @entangle($formComponent->getName()){{ Str::of($formComponent->getBindingAttribute())->after('wire:model') }},
            @endif
        })"
        x-init="init()"
        x-on:click.away="closePicker()"
        {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
        class="relative"
        {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
    >
        @unless (Str::of($formComponent->getBindingAttribute())->startsWith(['wire:model', 'x-model']))
            <input
                x-model="value"
                {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
                type="hidden"
            />
        @endif

        <button
            @unless($formComponent->isDisabled())
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
                aria-label="{{ __($formComponent->getPlaceholder()) }}"
            @endunless
            type="button"
            class="bg-white relative w-full border rounded shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $formComponent->isDisabled() ? 'text-gray-500' : '' }} {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        >
            <input
                readonly
                placeholder="{{ __($formComponent->getPlaceholder()) }}"
                x-model="displayValue"
                class="w-full h-full p-0 placeholder-gray-400 border-0 focus:placeholder-gray-500 focus:ring-0 focus:outline-none"
            />

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <x-heroicon-o-calendar class="w-5 h-5 text-gray-400" />
            </span>
        </button>

        @unless ($formComponent->isDisabled())
            <div
                x-ref="picker"
                x-on:click.away="closePicker()"
                x-show.transition="open"
                aria-modal="true"
                role="dialog"
                x-cloak
                class="absolute z-10 w-64 p-4 my-1 bg-white border border-gray-300 rounded shadow-sm"
            >
                <div class="space-y-3">
                    <div class="flex items-center justify-between space-x-1">
                        <select
                            x-model="focusedMonth"
                            class="flex-grow p-0 text-lg font-medium text-gray-800 border-0 cursor-pointer focus:ring-0 focus:outline-none"
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
                            class="w-20 p-0 text-lg text-right text-gray-600 border-0 focus:ring-0 focus:outline-none"
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
                                class="text-xs font-medium text-center text-gray-800"
                            ></div>
                        </template>
                    </div>

                    <div role="grid" class="grid grid-cols-7 gap-1">
                        <template x-for="day in emptyDaysInFocusedMonth" x-bind:key="day">
                            <div class="text-sm text-center border border-transparent"></div>
                        </template>

                        <template x-for="day in daysInFocusedMonth" x-bind:key="day">
                            <div
                                x-text="day"
                                x-on:click="dayIsDisabled(day) || selectDate(day)"
                                x-on:mouseenter="setFocusedDay(day)"
                                role="option"
                                x-bind:aria-selected="focusedDate.date() === day"
                                x-bind:class="{
                                    'bg-blue-600 text-white': dayIsSelected(day),
                                    'text-gray-700': ! dayIsSelected(day),
                                    'bg-blue-50': dayIsToday(day) && ! dayIsSelected(day) && focusedDate.date() !== day && ! dayIsDisabled(day),
                                    'bg-blue-200': focusedDate.date() === day && ! dayIsSelected(day),
                                    'bg-gray-100': dayIsDisabled(day) && focusedDate.date() !== day,
                                    'cursor-pointer': ! dayIsDisabled(day),
                                    'cursor-not-allowed': dayIsDisabled(day),
                                }"
                                class="text-sm leading-none leading-loose text-center transition duration-100 ease-in-out rounded"
                            ></div>
                        </template>
                    </div>

                    @if ($formComponent->hasTime())
                        <div class="flex items-center justify-center py-2 bg-gray-100 rounded">
                            <input
                                type="number"
                                x-model.debounce="hour"
                                class="w-16 p-0 pr-1 text-xl text-center text-gray-700 bg-gray-100 border-0 focus:ring-0 focus:outline-none"
                            />

                            <span class="text-xl font-medium text-gray-700 bg-gray-100">:</span>

                            <input
                                type="number"
                                x-model.debounce="minute"
                                class="w-16 p-0 pr-1 text-xl text-center text-gray-700 bg-gray-100 border-0 focus:ring-0 focus:outline-none"
                            />

                            @if ($formComponent->hasSeconds())
                                <span class="text-xl font-medium text-gray-700 bg-gray-100">:</span>

                                <input
                                    type="number"
                                    x-model.debounce="second"
                                    class="w-16 p-0 pr-1 text-xl text-center text-gray-700 bg-gray-100 border-0 focus:ring-0 focus:outline-none"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endunless
    </div>
</x-forms::field-group>
