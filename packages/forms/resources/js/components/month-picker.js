import dayjs from 'dayjs/esm'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localeData from 'dayjs/plugin/localeData'
import timezone from 'dayjs/plugin/timezone'
import utc from 'dayjs/plugin/utc'

dayjs.extend(customParseFormat)
dayjs.extend(localeData)
dayjs.extend(timezone)
dayjs.extend(utc)

window.dayjs = dayjs

export default (Alpine) => {
    Alpine.data('MonthPickerFormComponent', ({
        displayFormat,
        firstDayOfWeek,
        format,
        isAutofocused,
        maxDate,
        minDate,
        state,
    }) => {
        const timezone = dayjs.tz.guess()

        dayjs.locale(window.dayjs_locale)

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
                }

                if (this.minDate !== null && date.isBefore(this.minDate)) {
                    date = null
                }

                this.hour = date?.hour() ?? 0
                this.minute = date?.minute() ?? 0
                this.second = date?.second() ?? 0

                this.setDisplayText()

                if (isAutofocused) {
                    this.openPicker()
                }

                this.$watch('focusedMonth', () => {
                    this.focusedMonth = +this.focusedMonth

                    if (this.focusedDate.month() === this.focusedMonth) {
                        return
                    }

                    this.focusedDate = this.focusedDate.month(this.focusedMonth)
                })

                this.$watch('focusedYear', () => {
                    if (! this.focusedYear) {
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

                    this.setupMonthsGrid()

                    this.$nextTick(() => {
                        //this.evaluatePosition()
                    })
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

            monthIsDisabled: function (month) {
                this.focusedDate ??= dayjs().tz(timezone)

                return this.dateIsDisabled(this.focusedDate.date(month))
            },

            monthIsSelected: function (month) {
                let selectedDate = this.getSelectedDate()

                if (selectedDate === null) {
                    return false
                }

                this.focusedDate ??= dayjs().tz(timezone)

                return selectedDate.month() === month &&
                    selectedDate.year() === this.focusedDate.year()
            },

            monthIsThisMonth: function (month) {
                let date = dayjs().tz(timezone)
                this.focusedDate ??= date

                return date.month()  === month &&
                    date.year() === this.focusedDate.year()
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

                this.setupMonthsGrid()

                this.open = true

                this.$nextTick(() => {
                    //this.evaluatePosition()
                })
            },

            selectDate: function (month = null) {
                if (month) {
                    this.setFocusedMonth(month)
                }

                this.focusedDate ??= dayjs().tz(timezone)
                this.setState(this.focusedDate)
            },

            setDisplayText: function () {
                this.displayText = this.getSelectedDate() ? this.getSelectedDate().format(displayFormat) : ''
            },

            setupMonthsGrid: function () {
                this.focusedDate ??= dayjs().tz(timezone)
            },

            setFocusedMonth: function (month) {
                this.focusedDate = (this.focusedDate ?? dayjs().tz(timezone)).month(month)
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
