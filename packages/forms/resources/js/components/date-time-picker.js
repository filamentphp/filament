import dayjs from 'dayjs/esm'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localeData from 'dayjs/plugin/localeData'
import timezone from 'dayjs/plugin/timezone'
import utc from 'dayjs/plugin/utc'

dayjs.extend(customParseFormat)
dayjs.extend(localeData)
dayjs.extend(timezone)
dayjs.extend(utc)
dayjs.extend((option, Dayjs, dayjs) => {
    const listeners = []

    dayjs.addLocaleListeners = (listener) => listeners.push(listener)
    dayjs.onLocaleUpdated = () => { listeners.forEach((listener) => listener()) }
    dayjs.updateLocale = (locale) => {
        dayjs.locale(locale)

        // Emit the `localeUpdated` event that we can bind to later
        dayjs.onLocaleUpdated()
    }
})

window.dayjs = dayjs

export default (Alpine) => {
    Alpine.data('dateTimePickerFormComponent', ({
        displayFormat,
        firstDayOfWeek,
        format,
        isAutofocused,
        maxDate,
        minDate,
        state,
    }) => {
        const timezone = dayjs.tz.guess()

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

            dayLabels: [],

            months: [],

            init: function () {
                this.focusedDate = dayjs().tz(timezone)

                this.maxDate = dayjs(this.maxDate)
                if (! this.maxDate.isValid()) {
                    this.maxDate = null
                }

                this.minDate = dayjs(this.minDate)
                if (! this.minDate.isValid()) {
                    this.minDate = null
                }

                let date = this.getSelectedDate() ?? dayjs().tz(timezone)
                    .hour(0)
                    .minute(0)
                    .second(0)

                if (this.maxDate !== null && date.isAfter(this.maxDate)) {
                    date = null
                } else if (this.minDate !== null && date.isBefore(this.minDate)) {
                    date = null
                }

                this.hour = date?.hour() ?? 0
                this.minute = date?.minute() ?? 0
                this.second = date?.second() ?? 0

                this.setDisplayText()
                this.setMonths()
                this.setDayLabels()

                if (isAutofocused) {
                    this.openPicker()
                }

                dayjs.addLocaleListeners(() => {
                    this.setDisplayText()
                    this.setMonths()
                    this.setDayLabels()
                })

                this.$watch('focusedMonth', () => {
                    this.focusedMonth = +this.focusedMonth

                    if (this.focusedDate.month() === this.focusedMonth) {
                        return
                    }

                    this.focusedDate = this.focusedDate.month(this.focusedMonth)
                })

                this.$watch('focusedYear', () => {
                    if (this.focusedYear?.length > 4) {
                        this.focusedYear = this.focusedYear.substring(0, 4)
                    }

                    if ((! this.focusedYear) || (this.focusedYear?.length !== 4)) {
                        return
                    }

                    let year = +this.focusedYear

                    if (! Number.isInteger(year)) {
                        year = dayjs().tz(timezone).year()

                        this.focusedYear = year
                    }

                    if (this.focusedDate.year() === year) {
                        return
                    }

                    this.focusedDate = this.focusedDate.year(year)
                })

                this.$watch('focusedDate', () => {
                    let month = this.focusedDate.month()
                    let year = this.focusedDate.year()

                    if (this.focusedMonth !== month) {
                        this.focusedMonth = month
                    }

                    if (this.focusedYear !== year) {
                        this.focusedYear = year
                    }

                    this.setupDaysGrid()

                    this.$nextTick(() => {
                        this.evaluatePosition()
                    })
                })

                this.$watch('hour', () => {
                    let hour = +this.hour

                    if (! Number.isInteger(hour)) {
                        this.hour = 0
                    } else if (hour > 23) {
                        this.hour = 0
                    } else if (hour < 0) {
                        this.hour = 23
                    } else {
                        this.hour = hour
                    }

                    let date = this.getSelectedDate() ?? this.focusedDate

                    this.setState(date.hour(this.hour ?? 0))
                })

                this.$watch('minute', () => {
                    let minute = +this.minute

                    if (! Number.isInteger(minute)) {
                        this.minute = 0
                    } else if (minute > 59) {
                        this.minute = 0
                    } else if (minute < 0) {
                        this.minute = 59
                    } else {
                        this.minute = minute
                    }

                    let date = this.getSelectedDate() ?? this.focusedDate

                    this.setState(date.minute(this.minute ?? 0))
                })

                this.$watch('second', () => {
                    let second = +this.second

                    if (! Number.isInteger(second)) {
                        this.second = 0
                    } else if (second > 59) {
                        this.second = 0
                    } else if (second < 0) {
                        this.second = 59
                    } else {
                        this.second = second
                    }

                    let date = this.getSelectedDate() ?? this.focusedDate

                    this.setState(date.second(this.second ?? 0))
                })

                this.$watch('state', () => {
                    let date = this.getSelectedDate()

                    if (this.maxDate !== null && date.isAfter(this.maxDate)) {
                        date = null
                    }
                    if (this.minDate !== null && date.isBefore(this.minDate)) {
                        date = null
                    }

                    this.hour = date?.hour() ?? 0
                    this.minute = date?.minute() ?? 0
                    this.second = date?.second() ?? 0

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
                this.focusedDate ??= dayjs().tz(timezone)

                return this.dateIsDisabled(this.focusedDate.date(day))
            },

            dayIsSelected: function (day) {
                let selectedDate = this.getSelectedDate()

                if (selectedDate === null) {
                    return false
                }

                this.focusedDate ??= dayjs().tz(timezone)

                return selectedDate.date() === day &&
                    selectedDate.month() === this.focusedDate.month() &&
                    selectedDate.year() === this.focusedDate.year()
            },

            dayIsToday: function (day) {
                let date = dayjs().tz(timezone)
                this.focusedDate ??= date

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
                this.focusedDate ??= dayjs().tz(timezone)

                this.focusedDate = this.focusedDate.subtract(1, 'day')
            },

            focusPreviousWeek: function () {
                this.focusedDate ??= dayjs().tz(timezone)

                this.focusedDate = this.focusedDate.subtract(1, 'week')
            },

            focusNextDay: function () {
                this.focusedDate ??= dayjs().tz(timezone)

                this.focusedDate = this.focusedDate.add(1, 'day')
            },

            focusNextWeek: function () {
                this.focusedDate ??= dayjs().tz(timezone)

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
                this.focusedDate = this.getSelectedDate() ?? dayjs().tz(timezone)

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

                this.focusedDate ??= dayjs().tz(timezone)

                this.setState(this.focusedDate)
            },

            setDisplayText: function () {
                this.displayText = this.getSelectedDate() ? this.getSelectedDate().format(displayFormat) : ''
            },

            setMonths: function () {
                this.months = dayjs.months()
            },

            setDayLabels: function () {
                this.dayLabels = this.getDayLabels()
            },

            setupDaysGrid: function () {
                this.focusedDate ??= dayjs().tz(timezone)

                this.emptyDaysInFocusedMonth = Array.from({
                    length: this.focusedDate.date(8 - firstDayOfWeek).day(),
                }, (_, i) => i + 1)

                this.daysInFocusedMonth = Array.from({
                    length: this.focusedDate.daysInMonth(),
                }, (_, i) => i + 1)
            },

            setFocusedDay: function (day) {
                this.focusedDate = (this.focusedDate ?? dayjs().tz(timezone)).date(day)
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
                    .hour(this.hour ?? 0)
                    .minute(this.minute ?? 0)
                    .second(this.second ?? 0)
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
