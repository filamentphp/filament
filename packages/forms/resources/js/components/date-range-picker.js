import dayjs from 'dayjs/esm'
import {
    autoUpdate,
    computePosition,
    flip,
    offset,
    shift,
} from '@floating-ui/dom'
import advancedFormat from 'dayjs/plugin/advancedFormat'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localeData from 'dayjs/plugin/localeData'
import utc from 'dayjs/plugin/utc'
import {
    observeFieldAccessibility,
    setFieldAccessibilityAttributes,
} from '../utilities/field-accessibility.js'

dayjs.extend(advancedFormat)
dayjs.extend(customParseFormat)
dayjs.extend(localeData)
dayjs.extend(utc)

export default function dateRangePickerFormComponent({
    defaultFocusedDate,
    displayFormat,
    firstDayOfWeek,
    isAutofocused,
    locale,
    maxDays,
    shouldCloseOnDateSelection,
    state,
}) {
    const calendarLocale = (locales[locale] ?? locales['en']).name

    return {
        accessibilityObserver: null,

        dayLabels: [],

        defaultFocusedDate,

        displayText: '',

        focusedDate: null,

        focusedMonth: null,

        focusedMonths: [],

        focusedYear: null,

        hoveredDate: null,

        focusGeneration: 0,

        fullDayLabels: [],

        isDestroyed: false,

        isPanelOpen: false,

        isPanelReady: false,

        months: [],

        panelGeneration: 0,

        positionGeneration: 0,

        positioningCleanup: null,

        selectedStartDate: null,

        state,

        init() {
            dayjs.locale(locales[locale] ?? locales['en'])

            this.focusedDate =
                this.getSelectedStartDate() ??
                this.getDefaultFocusedDate() ??
                this.getToday()

            this.setDisplayText()
            this.setMonths()
            this.setDayLabels()
            this.setFocusedMonth(this.focusedDate)

            this.accessibilityObserver = observeFieldAccessibility(
                this.$el,
                (attributes) =>
                    setFieldAccessibilityAttributes(
                        this.$refs.button,
                        attributes,
                    ),
            )

            if (isAutofocused) {
                this.$nextTick(() => {
                    if (!this.isDestroyed) {
                        this.togglePanelVisibility()
                    }
                })
            }

            this.$watch('focusedMonth', () => {
                this.focusedMonth = +this.focusedMonth

                this.setupDaysGrid()
            })

            this.$watch('focusedYear', () => {
                if (this.focusedYear?.length > 4) {
                    this.focusedYear = this.focusedYear.substring(0, 4)
                }

                if (!this.focusedYear || this.focusedYear?.length !== 4) {
                    return
                }

                let year = +this.focusedYear

                if (!Number.isInteger(year)) {
                    year = this.getToday().year()

                    this.focusedYear = year
                }

                this.setupDaysGrid()
            })

            this.$watch('focusedDate', () => {
                const firstFocusedMonth = this.getFirstFocusedMonth()
                const lastFocusedMonth = firstFocusedMonth.add(
                    this.getVisibleMonthCount() - 1,
                    'month',
                )

                if (this.focusedDate.isBefore(firstFocusedMonth, 'month')) {
                    this.setFocusedMonth(this.focusedDate)
                } else if (
                    this.focusedDate.isAfter(lastFocusedMonth, 'month')
                ) {
                    this.setFocusedMonth(
                        this.focusedDate.subtract(
                            this.getVisibleMonthCount() - 1,
                            'month',
                        ),
                    )
                }
            })

            this.$watch('state', () => {
                if (this.state === undefined) {
                    return
                }

                this.setDisplayText()
            })

            this.$dispatch('async-component-ready')
        },

        clearState() {
            this.selectedStartDate = null

            this.setState(null)
        },

        dateIsDisabled(date) {
            date = dayjs.utc(date)

            if (
                this.getDisabledDates().some((disabledDate) =>
                    disabledDate.isSame(date, 'day'),
                )
            ) {
                return true
            }

            if (this.getMaxDate() && date.isAfter(this.getMaxDate(), 'day')) {
                return true
            }

            if (this.getMinDate() && date.isBefore(this.getMinDate(), 'day')) {
                return true
            }

            if (this.selectedStartDate === null) {
                return false
            }

            return date.isBefore(this.selectedStartDate)
                ? this.dateRangeIsDisabled(date, this.selectedStartDate)
                : this.dateRangeIsDisabled(this.selectedStartDate, date)
        },

        dateRangeIsDisabled(startDate, endDate) {
            if (
                this.getDisabledDates().some(
                    (disabledDate) =>
                        !disabledDate.isBefore(startDate, 'day') &&
                        !disabledDate.isAfter(endDate, 'day'),
                )
            ) {
                return true
            }

            return (
                maxDays !== null && endDate.diff(startDate, 'day') + 1 > maxDays
            )
        },

        dateIsFocused(date) {
            return this.focusedDate?.format('YYYY-MM-DD') === date
        },

        dateIsInRange(date) {
            const [startDate, endDate] = this.getFocusedRange()

            return startDate !== null && date >= startDate && date <= endDate
        },

        dateIsRangeEnd(date) {
            const [startDate, endDate] = this.getFocusedRange()

            return startDate !== endDate && date === endDate
        },

        dateIsRangeStart(date) {
            const [startDate, endDate] = this.getFocusedRange()

            return startDate !== endDate && date === startDate
        },

        dateIsSelected(date) {
            return this.getFocusedRange().includes(date)
        },

        dateIsToday(date) {
            return this.getToday().format('YYYY-MM-DD') === date
        },

        focusPreviousMonth() {
            this.setFocusedMonth(
                this.getFirstFocusedMonth().subtract(1, 'month'),
            )
        },

        focusNextMonth() {
            this.setFocusedMonth(this.getFirstFocusedMonth().add(1, 'month'))
        },

        handleTriggerKeydown(event) {
            if (
                event.altKey ||
                event.ctrlKey ||
                event.metaKey ||
                ![
                    'Enter',
                    ' ',
                    'ArrowLeft',
                    'ArrowRight',
                    'ArrowUp',
                    'ArrowDown',
                ].includes(event.key)
            ) {
                return
            }

            event.preventDefault()
            event.stopPropagation()

            if (this.isOpen()) {
                this.focusCalendarDate()
            } else {
                this.togglePanelVisibility()
            }
        },

        handleFocusOut(event) {
            const generation = this.panelGeneration

            if (
                event.relatedTarget &&
                !this.$refs.calendar?.contains(event.relatedTarget)
            ) {
                this.focusGeneration++
            }

            const focusGeneration = this.focusGeneration

            this.$nextTick(() =>
                requestAnimationFrame(() => {
                    if (
                        !this.isDestroyed &&
                        generation === this.panelGeneration &&
                        focusGeneration === this.focusGeneration &&
                        this.isOpen() &&
                        !this.$el.contains(document.activeElement)
                    ) {
                        this.closePanel()
                    }
                }),
            )
        },

        handleCalendarKeydown(event) {
            if (
                event.altKey ||
                event.ctrlKey ||
                event.metaKey ||
                (event.target !== this.$refs.calendar &&
                    !event.target.matches('[role="gridcell"]'))
            ) {
                return
            }

            const isRtl =
                getComputedStyle(this.$refs.calendar).direction === 'rtl'
            const weekDay = (this.focusedDate.day() - firstDayOfWeek + 7) % 7
            let focusedDate = this.focusedDate

            switch (event.key) {
                case 'ArrowLeft':
                    focusedDate = focusedDate.add(isRtl ? 1 : -1, 'day')
                    break
                case 'ArrowRight':
                    focusedDate = focusedDate.add(isRtl ? -1 : 1, 'day')
                    break
                case 'ArrowUp':
                    focusedDate = focusedDate.subtract(7, 'day')
                    break
                case 'ArrowDown':
                    focusedDate = focusedDate.add(7, 'day')
                    break
                case 'Home':
                    focusedDate = focusedDate.subtract(weekDay, 'day')
                    break
                case 'End':
                    focusedDate = focusedDate.add(6 - weekDay, 'day')
                    break
                case 'PageUp':
                case 'PageDown':
                    focusedDate = focusedDate.add(
                        event.key === 'PageUp' ? -1 : 1,
                        event.shiftKey ? 'year' : 'month',
                    )
                    break
                case 'Enter':
                case ' ':
                    this.selectDate()
                    event.preventDefault()
                    event.stopPropagation()

                    return
                default:
                    return
            }

            event.preventDefault()
            event.stopPropagation()

            const firstFocusedMonth = this.getFirstFocusedMonth()

            if (
                focusedDate.isBefore(firstFocusedMonth, 'month') ||
                focusedDate.isAfter(
                    firstFocusedMonth.add(
                        this.getVisibleMonthCount() - 1,
                        'month',
                    ),
                    'month',
                )
            ) {
                // Keep keyboard events in the calendar while Alpine replaces the focused month.
                this.$refs.calendar.focus({ preventScroll: true })
            }

            this.hoveredDate = null
            this.focusedDate = focusedDate
            this.focusCalendarDate()
        },

        focusCalendarDate(generation = ++this.focusGeneration) {
            const focusDate = () => {
                if (
                    this.isDestroyed ||
                    !this.$el.isConnected ||
                    !this.isOpen() ||
                    generation !== this.focusGeneration
                ) {
                    return
                }

                this.$refs.calendar
                    ?.querySelector(
                        `[data-date="${this.focusedDate.format('YYYY-MM-DD')}"]`,
                    )
                    ?.focus({ preventScroll: true })
            }

            this.$nextTick(() =>
                this.isPanelReady
                    ? focusDate()
                    : requestAnimationFrame(focusDate),
            )
        },

        getCalendarLabel() {
            return this.focusedMonths
                .slice(0, this.getVisibleMonthCount())
                .map((month) => month.label)
                .join(', ')
        },

        getDateLabel(date) {
            return dayjs
                .utc(date)
                .locale(calendarLocale)
                .format('dddd, D MMMM YYYY')
        },

        getDayLabels() {
            const labels = dayjs()
                .locale(calendarLocale)
                .localeData()
                .weekdaysShort()

            if (firstDayOfWeek === 0) {
                return labels
            }

            return [
                ...labels.slice(firstDayOfWeek),
                ...labels.slice(0, firstDayOfWeek),
            ]
        },

        getDateLimit(value) {
            const date = dayjs.utc(value)

            return date.isValid() ? date : null
        },

        getDefaultFocusedDate() {
            if (this.defaultFocusedDate === null) {
                return null
            }

            const defaultFocusedDate = dayjs.utc(this.defaultFocusedDate)

            if (!defaultFocusedDate.isValid()) {
                return null
            }

            return defaultFocusedDate
        },

        getDisabledDates() {
            return JSON.parse(this.$refs.disabledDates?.value ?? '[]')
                .map((disabledDate) =>
                    // Preserve browser-calendar projection for explicitly zoned and other non-canonical values.
                    typeof disabledDate === 'string' &&
                    /^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}(?::\d{2}(?:\.\d{1,6})?)?)?$/.test(
                        disabledDate,
                    )
                        ? dayjs.utc(disabledDate)
                        : dayjs.utc(dayjs(disabledDate).format('YYYY-MM-DD')),
                )
                .filter((disabledDate) => disabledDate.isValid())
        },

        getFirstFocusedMonth() {
            return dayjs
                .utc()
                .year(+this.focusedYear)
                .month(+this.focusedMonth)
                .startOf('month')
        },

        getFocusedRange() {
            if (this.selectedStartDate !== null) {
                return [
                    this.selectedStartDate.format('YYYY-MM-DD'),
                    this.hoveredDate ??
                        (this.focusedDate ?? this.selectedStartDate).format(
                            'YYYY-MM-DD',
                        ),
                ].sort()
            }

            return [
                this.getSelectedStartDate()?.format('YYYY-MM-DD') ?? null,
                this.getSelectedEndDate()?.format('YYYY-MM-DD') ?? null,
            ]
        },

        getMaxDate() {
            return this.getDateLimit(this.$refs.maxDate?.value)
        },

        getMinDate() {
            return this.getDateLimit(this.$refs.minDate?.value)
        },

        getPresetDates(preset) {
            if (Array.isArray(preset)) {
                return preset.map((date) => dayjs.utc(date))
            }

            const today = this.getToday()

            switch (preset) {
                case 'today':
                    return [today, today]
                case 'yesterday':
                    return [today.subtract(1, 'day'), today.subtract(1, 'day')]
                case 'week_to_date':
                    return [
                        today.subtract(
                            (today.day() - firstDayOfWeek + 7) % 7,
                            'day',
                        ),
                        today,
                    ]
                case 'past_7_days':
                    return [today.subtract(6, 'day'), today]
                case 'past_30_days':
                    return [today.subtract(29, 'day'), today]
                case 'month_to_date':
                    return [today.startOf('month'), today]
                case 'previous_month':
                    return [
                        today.subtract(1, 'month').startOf('month'),
                        today.subtract(1, 'month').endOf('month'),
                    ]
                case 'year_to_date':
                    return [today.startOf('year'), today]
            }
        },

        getSelectedDate(value) {
            if (value === undefined || value === null) {
                return null
            }

            const date = dayjs.utc(value)

            if (!date.isValid()) {
                return null
            }

            return date
        },

        getSelectedEndDate() {
            return this.getSelectedDate(this.state?.end)
        },

        getSelectedStartDate() {
            return this.getSelectedDate(this.state?.start)
        },

        getToday() {
            // Use UTC only as a neutral calendar, preserving the browser's local date.
            return dayjs.utc(dayjs().format('YYYY-MM-DD'))
        },

        getVisibleMonthCount() {
            const grids = this.$refs.calendar?.querySelectorAll('[role="grid"]')

            if (!grids?.length || !this.isPanelReady) {
                return this.focusedMonths.length || 1
            }

            return (
                Array.from(grids).filter((grid) => grid.offsetParent !== null)
                    .length || 1
            )
        },

        togglePanelVisibility(shouldFocusCalendar = true) {
            if (this.isDestroyed) {
                return
            }

            if (this.isOpen()) {
                this.closePanel(true)

                return
            }

            this.selectedStartDate = null

            this.focusedDate =
                this.getSelectedStartDate() ??
                this.focusedDate ??
                this.getMinDate() ??
                this.getToday()

            this.setFocusedMonth(this.focusedDate)

            this.isPanelOpen = true
            const generation = ++this.panelGeneration
            const focusGeneration = ++this.focusGeneration

            this.$nextTick(() =>
                requestAnimationFrame(() => {
                    if (
                        this.isDestroyed ||
                        !this.$el.isConnected ||
                        !this.isOpen() ||
                        generation !== this.panelGeneration
                    ) {
                        return
                    }

                    this.isPanelReady = true
                    this.positioningCleanup = autoUpdate(
                        this.$refs.button,
                        this.$refs.panel,
                        () => {
                            const positionGeneration = ++this.positionGeneration

                            computePosition(
                                this.$refs.button,
                                this.$refs.panel,
                                {
                                    placement: 'bottom-start',
                                    middleware: [offset(8), flip(), shift()],
                                },
                            ).then(({ x, y }) => {
                                if (
                                    this.isDestroyed ||
                                    !this.$el.isConnected ||
                                    !this.isOpen() ||
                                    generation !== this.panelGeneration ||
                                    positionGeneration !==
                                        this.positionGeneration
                                ) {
                                    return
                                }

                                Object.assign(this.$refs.panel.style, {
                                    left: `${x}px`,
                                    top: `${y}px`,
                                })
                            })
                        },
                    )

                    if (
                        shouldFocusCalendar &&
                        focusGeneration === this.focusGeneration
                    ) {
                        this.focusCalendarDate(focusGeneration)
                    }
                }),
            )
        },

        closePanel(shouldRestoreFocus = false) {
            this.hoveredDate = null
            this.selectedStartDate = null
            this.isPanelOpen = false
            this.isPanelReady = false
            this.panelGeneration++
            this.focusGeneration++
            this.positioningCleanup?.()
            this.positioningCleanup = null

            if (
                shouldRestoreFocus &&
                !this.isDestroyed &&
                this.$refs.button.isConnected
            ) {
                this.$refs.button.focus({ preventScroll: true })
            }
        },

        presetIsDisabled(preset) {
            const [startDate, endDate] = this.getPresetDates(preset)

            if (
                this.getMaxDate() &&
                endDate.isAfter(this.getMaxDate(), 'day')
            ) {
                return true
            }

            if (
                this.getMinDate() &&
                startDate.isBefore(this.getMinDate(), 'day')
            ) {
                return true
            }

            return this.dateRangeIsDisabled(startDate, endDate)
        },

        presetIsSelected(preset) {
            const [startDate, endDate] = this.getPresetDates(preset)

            return (
                (this.getSelectedStartDate()?.isSame(startDate, 'day') ??
                    false) &&
                (this.getSelectedEndDate()?.isSame(endDate, 'day') ?? false)
            )
        },

        selectDate(date = null) {
            if (date) {
                this.setFocusedDate(date)
            }

            this.focusedDate ??= this.getToday()

            if (this.dateIsDisabled(this.focusedDate)) {
                return
            }

            if (this.selectedStartDate === null) {
                this.selectedStartDate = this.focusedDate

                return
            }

            this.setState(
                this.focusedDate.isBefore(this.selectedStartDate)
                    ? [this.focusedDate, this.selectedStartDate]
                    : [this.selectedStartDate, this.focusedDate],
            )

            this.selectedStartDate = null

            if (shouldCloseOnDateSelection) {
                this.closePanel(true)
            }
        },

        selectPreset(preset) {
            if (this.presetIsDisabled(preset)) {
                return
            }

            this.selectedStartDate = null

            this.setState(this.getPresetDates(preset))

            if (shouldCloseOnDateSelection) {
                this.closePanel(true)
            }
        },

        setDayLabels() {
            this.dayLabels = this.getDayLabels()

            const labels = dayjs()
                .locale(calendarLocale)
                .localeData()
                .weekdays()

            this.fullDayLabels = [
                ...labels.slice(firstDayOfWeek % 7),
                ...labels.slice(0, firstDayOfWeek % 7),
            ]
        },

        setDisplayText() {
            const startDate = this.getSelectedStartDate()
            const endDate = this.getSelectedEndDate()

            if (!startDate || !endDate) {
                this.displayText = ''

                return
            }

            this.displayText = startDate.isSame(endDate, 'day')
                ? startDate.locale(calendarLocale).format(displayFormat)
                : `${startDate.locale(calendarLocale).format(displayFormat)} – ${endDate.locale(calendarLocale).format(displayFormat)}`
        },

        setFocusedDate(date) {
            if (this.dateIsFocused(date)) {
                return
            }

            this.focusedDate = dayjs.utc(date)
        },

        setFocusedMonth(date) {
            this.focusedMonth = date.month()
            this.focusedYear = date.year()

            this.setupDaysGrid()
        },

        setMonths() {
            this.months = dayjs().locale(calendarLocale).localeData().months()
        },

        setState(dates) {
            if (dates === null) {
                this.state = null
                this.setDisplayText()

                return
            }

            const [startDate, endDate] = dates

            this.state = {
                start: startDate.format('YYYY-MM-DD'),
                end: endDate.format('YYYY-MM-DD'),
            }

            this.setDisplayText()
        },

        setupDaysGrid() {
            this.focusedMonths = [0, 1].map((monthOffset) => {
                const month = this.getFirstFocusedMonth().add(
                    monthOffset,
                    'month',
                )
                const emptyDays =
                    (month.startOf('month').day() - firstDayOfWeek + 7) % 7
                const daysInMonth = month.daysInMonth()

                return {
                    key: month.format('YYYY-MM'),
                    label: month.locale(calendarLocale).format('MMMM YYYY'),
                    weeks: Array.from(
                        { length: Math.ceil((emptyDays + daysInMonth) / 7) },
                        (_, week) =>
                            Array.from({ length: 7 }, (_, column) => {
                                const day = week * 7 + column - emptyDays + 1

                                return day > 0 && day <= daysInMonth
                                    ? month.date(day).format('YYYY-MM-DD')
                                    : null
                            }),
                    ),
                }
            })
        },

        isOpen() {
            return this.isPanelOpen
        },

        destroy() {
            this.isDestroyed = true
            this.accessibilityObserver?.disconnect()

            this.closePanel()
        },
    }
}

const locales = {
    am: require('dayjs/locale/am'),
    ar: require('dayjs/locale/ar'),
    bs: require('dayjs/locale/bs'),
    ca: require('dayjs/locale/ca'),
    ckb: require('dayjs/locale/ku'),
    cs: require('dayjs/locale/cs'),
    cy: require('dayjs/locale/cy'),
    da: require('dayjs/locale/da'),
    de: require('dayjs/locale/de'),
    el: require('dayjs/locale/el'),
    en: require('dayjs/locale/en'),
    es: require('dayjs/locale/es'),
    et: require('dayjs/locale/et'),
    fa: require('dayjs/locale/fa'),
    fi: require('dayjs/locale/fi'),
    fr: require('dayjs/locale/fr'),
    he: require('dayjs/locale/he'),
    hi: require('dayjs/locale/hi'),
    hr: require('dayjs/locale/hr'),
    hu: require('dayjs/locale/hu'),
    hy: require('dayjs/locale/hy-am'),
    id: require('dayjs/locale/id'),
    it: require('dayjs/locale/it'),
    ja: require('dayjs/locale/ja'),
    ka: require('dayjs/locale/ka'),
    km: require('dayjs/locale/km'),
    ko: require('dayjs/locale/ko'),
    ku: require('dayjs/locale/ku'),
    lt: require('dayjs/locale/lt'),
    lv: require('dayjs/locale/lv'),
    ms: require('dayjs/locale/ms'),
    my: require('dayjs/locale/my'),
    nb: require('dayjs/locale/nb'),
    nl: require('dayjs/locale/nl'),
    pl: require('dayjs/locale/pl'),
    pt: require('dayjs/locale/pt'),
    pt_BR: require('dayjs/locale/pt-br'),
    ro: require('dayjs/locale/ro'),
    ru: require('dayjs/locale/ru'),
    sl: require('dayjs/locale/sl'),
    sr_Cyrl: require('dayjs/locale/sr-cyrl'),
    sr_Latn: require('dayjs/locale/sr'),
    sv: require('dayjs/locale/sv'),
    th: require('dayjs/locale/th'),
    tr: require('dayjs/locale/tr'),
    uk: require('dayjs/locale/uk'),
    ur: require('dayjs/locale/ur'),
    vi: require('dayjs/locale/vi'),
    zh_CN: require('dayjs/locale/zh-cn'),
    zh_HK: require('dayjs/locale/zh-hk'),
    zh_TW: require('dayjs/locale/zh-tw'),
}
