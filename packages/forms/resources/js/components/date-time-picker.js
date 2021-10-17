import dayjs from 'dayjs/esm/index'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localeData from 'dayjs/plugin/localeData'

dayjs.extend(customParseFormat)
dayjs.extend(localeData)

window.dayjs = dayjs

export default (Alpine) => {
    Alpine.data('dateTimePickerFormComponent', ({
        displayFormat,
        firstDayOfWeek,
        format,
        isAutofocused,
        locale,
        maxDate,
        minDate,
        state,
    }) => {
        dayjs.locale(locale)

        return {
            daysInFocusedMonth: [],

            displayText: '',

            emptyDaysInFocusedMonth: [],

            focusedDate: null,

            focusedMonth: null,

            focusedYear: null,

            hour: null,

            maxDate,

            minDate,

            minute: null,

            open: false,

            second: null,

            state,

            init: function () {
                this.maxDate = dayjs(this.maxDate)
                if (! this.maxDate.isValid()) {
                    this.maxDate = null
                }

                this.minDate = dayjs(this.minDate)
                if (! this.minDate.isValid()) {
                    this.minDate = null
                }

                let date = this.getSelectedDate() ?? dayjs()
                    .set('hour', 0)
                    .set('minute', 0)
                    .set('second', 0)

                if (this.maxDate !== null && date.isAfter(this.maxDate)) {
                    date = null
                }

                if (this.minDate !== null && date.isBefore(this.minDate)) {
                    date = null
                }

                this.hour = date.hour()
                this.minute = date.minute()
                this.second = date.second()

                this.setDisplayText()

                if (isAutofocused) {
                    this.openPicker()
                }

                this.$watch('focusedMonth', () => {
                    this.focusedMonth = +this.focusedMonth

                    if (this.focusedDate.month() === this.focusedMonth) {
                        return
                    }

                    this.focusedDate = this.focusedDate.set('month', this.focusedMonth)
                })

                this.$watch('focusedYear', () => {
                    this.focusedYear = Number.isInteger(+this.focusedYear) ? +this.focusedYear : dayjs().year()

                    if (this.focusedDate.year() === this.focusedYear) {
                        return
                    }

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
                    let hour = +this.hour

                    if (! Number.isInteger(hour)) {
                        this.hour = dayjs().hour()
                    } else if (hour > 23) {
                        this.hour = 0
                    } else if (hour < 0) {
                        this.hour = 23
                    } else {
                        this.hour = hour
                    }

                    let date = this.getSelectedDate() ?? this.focusedDate

                    this.setState(date.set('hour', this.hour))
                })

                this.$watch('minute', () => {
                    let minute = +this.minute

                    if (! Number.isInteger(minute)) {
                        this.minute = dayjs().minute()
                    } else if (minute > 59) {
                        this.minute = 0
                    } else if (minute < 0) {
                        this.minute = 59
                    } else {
                        this.minute = minute
                    }

                    let date = this.getSelectedDate() ?? this.focusedDate

                    this.setState(date.set('minute', this.minute))
                })

                this.$watch('second', () => {
                    let second = +this.second

                    if (! Number.isInteger(second)) {
                        this.second = dayjs().second()
                    } else if (second > 59) {
                        this.second = 0
                    } else if (second < 0) {
                        this.second = 59
                    } else {
                        this.second = second
                    }

                    let date = this.getSelectedDate() ?? this.focusedDate

                    this.setState(date.set('second', this.second))
                })

                this.$watch('state', () => {
                    let date = this.getSelectedDate() ?? dayjs()

                    if (this.maxDate !== null && date.isAfter(this.maxDate)) {
                        date = null
                    }
                    if (this.minDate !== null && date.isBefore(this.minDate)) {
                        date = null
                    }

                    this.hour = date.hour()
                    this.minute = date.minute()
                    this.second = date.second()

                    this.setDisplayText()
                })
            },

            clearState: function () {
                this.setState(null)

                this.closePicker()
            },

            closePicker: function () {
                this.open = false
            },

            dateIsDisabled: function (date) {
                if (this.maxDate && date.isAfter(this.maxDate)) {
                    return true
                }
                if (this.minDate && date.isBefore(this.minDate)) {
                    return true
                }

                return false
            },

            dayIsDisabled: function (day) {
                return this.dateIsDisabled(this.focusedDate.date(day))
            },

            dayIsSelected: function (day) {
                let selectedDate = this.getSelectedDate()

                if (selectedDate === null) {
                    return false
                }

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

            getDayLabels: function () {
                const labels = dayjs.weekdaysShort()

                if (firstDayOfWeek === 0) {
                    return labels
                }

                return [
                    ...labels.slice(firstDayOfWeek),
                    ...labels.slice(0, firstDayOfWeek),
                ]
            },

            getSelectedDate: function () {
                let date = dayjs(this.state, format)

                if (! date.isValid()) {
                    return null
                }

                return date
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
                if (day) {
                    this.setFocusedDay(day)
                }

                this.setState(this.focusedDate)
            },

            setDisplayText: function () {
                this.displayText = this.getSelectedDate() ? this.getSelectedDate().format(displayFormat) : ''
            },

            setupDaysGrid: function () {
                this.emptyDaysInFocusedMonth = Array.from({
                    length: this.focusedDate.date(8 - firstDayOfWeek).day(),
                }, (_, i) => i + 1)

                this.daysInFocusedMonth = Array.from({
                    length: this.focusedDate.daysInMonth(),
                }, (_, i) => i + 1)
            },

            setFocusedDay: function (day) {
                this.focusedDate = this.focusedDate.date(day)
            },

            setState: function (date) {
                if (date === null) {
                    this.state = null

                    this.setDisplayText()

                    return
                } else {
                    if (this.dateIsDisabled(date)) {
                        return
                    }
                }

                this.state = date
                    .set('hour', this.hour)
                    .set('minute', this.minute)
                    .set('second', this.second)
                    .format(format)

                this.setDisplayText()
            },

            togglePickerVisibility: function () {
                if (this.open) {
                    this.closePicker()

                    return
                }

                this.openPicker()
            },
        }
    })
}
