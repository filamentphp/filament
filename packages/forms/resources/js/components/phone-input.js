import * as flags from 'country-flag-icons/string/3x2'
import {
    autoUpdate,
    computePosition,
    flip,
    offset,
    shift,
} from '@floating-ui/dom'
import {
    AsYouType,
    getCountries,
    getCountryCallingCode,
    parsePhoneNumberFromString,
    validatePhoneNumberLength,
} from 'libphonenumber-js/min'
import normalizeSearchString from '../../../../support/resources/js/utilities/normalize-search-string.js'
import {
    observeFieldAccessibility,
    setFieldAccessibilityAttributes,
} from '../utilities/field-accessibility.js'

export default function phoneInputFormComponent({
    countries,
    countryButtonLabel,
    defaultCountry,
    isAutofocused,
    locale,
    preferredCountries,
    state,
}) {
    return {
        accessibilityObserver: null,

        country: null,

        countryOptions: [],

        focusedCountry: null,

        isDestroyed: false,

        isPanelOpen: false,

        panelGeneration: 0,

        positionGeneration: 0,

        positioningCleanup: null,

        search: '',

        state,

        init() {
            this.setCountryOptions()

            this.country = defaultCountry
            this.setDisplayText()

            this.accessibilityObserver = observeFieldAccessibility(
                this.$el,
                (attributes) =>
                    setFieldAccessibilityAttributes(
                        this.$refs.input,
                        attributes,
                    ),
            )

            this.$watch('state', () => {
                if (this.state === undefined) {
                    return
                }

                if (this.state === this.getNumber(this.$refs.input.value)) {
                    return
                }

                this.setDisplayText()
            })

            this.$watch('search', () => {
                this.focusedCountry =
                    this.filteredCountryOptions[0]?.code ?? null
            })

            this.$dispatch('async-component-ready')

            if (isAutofocused) {
                this.$nextTick(() => {
                    if (!this.isDestroyed) {
                        this.$refs.input.focus()
                    }
                })
            }
        },

        get filteredCountryOptions() {
            const search = normalizeSearchString(this.search).trim()

            if (search === '') {
                return this.countryOptions
            }

            return this.countryOptions.filter(
                (option) =>
                    normalizeSearchString(option.name).includes(search) ||
                    option.callingCode.includes(search) ||
                    option.code.toLowerCase() === search,
            )
        },

        handleSearchKeydown(event) {
            if (event.isComposing || event.keyCode === 229) {
                return
            }

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault()
                    event.stopPropagation()
                    this.focusCountry(1)
                    break
                case 'ArrowUp':
                    event.preventDefault()
                    event.stopPropagation()
                    this.focusCountry(-1)
                    break
                case 'Enter':
                    event.preventDefault()
                    event.stopPropagation()
                    this.selectCountry()
                    break
            }
        },

        handleFocusOut(event) {
            const generation = this.panelGeneration

            this.$nextTick(() =>
                requestAnimationFrame(() => {
                    if (
                        !this.isDestroyed &&
                        generation === this.panelGeneration &&
                        this.isOpen() &&
                        !this.$el.contains(document.activeElement)
                    ) {
                        this.closePanel()
                    }
                }),
            )
        },

        focusCountry(offset) {
            const options = this.filteredCountryOptions

            if (!options.length) {
                return
            }

            const index = options.findIndex(
                (option) => option.code === this.focusedCountry,
            )

            this.focusedCountry =
                options[
                    Math.min(Math.max(index + offset, 0), options.length - 1)
                ].code

            this.scrollFocusedCountryIntoView()
        },

        getCountryButtonLabel() {
            const option = this.getCountryOption(this.country)

            if (!option) {
                return countryButtonLabel
            }

            return `${countryButtonLabel}: ${option.name} (${option.callingCode})`
        },

        getCountryOption(country) {
            return (
                this.countryOptions.find((option) => option.code === country) ??
                this.getCountryOptions([country])[0] ??
                null
            )
        },

        getCountryOptionId(country) {
            return `${this.$refs.panel.id}-${country}`
        },

        getCountryOptions(countries) {
            let labels

            try {
                labels = new Intl.DisplayNames([locale.replace('_', '-')], {
                    type: 'region',
                })
            } catch {
                labels = new Intl.DisplayNames(['en'], { type: 'region' })
            }

            return countries
                .filter((country) => getCountries().includes(country))
                .map((country) => ({
                    code: country,
                    name: labels.of(country) ?? country,
                    callingCode: `+${getCountryCallingCode(country)}`,
                    flag: flags[country]
                        ? `data:image/svg+xml,${encodeURIComponent(flags[country])}`
                        : null,
                }))
        },

        getFocusedCountryOptionId() {
            if (!this.isPanelOpen || this.focusedCountry === null) {
                return null
            }

            if (
                !this.filteredCountryOptions.some(
                    (option) => option.code === this.focusedCountry,
                )
            ) {
                return null
            }

            return this.getCountryOptionId(this.focusedCountry)
        },

        getNumber(value) {
            if (value.trim() === '') {
                return null
            }

            const formatter = new AsYouType(this.country ?? undefined)

            formatter.input(value)

            const number = formatter.getNumber()

            return number?.isValid() ? number.number : value
        },

        scrollFocusedCountryIntoView() {
            this.$nextTick(() =>
                this.$refs.panel
                    .querySelector('.fi-selected')
                    ?.scrollIntoView({ block: 'nearest' }),
            )
        },

        togglePanelVisibility() {
            if (this.isDestroyed) {
                return
            }

            if (this.isOpen()) {
                this.closePanel(true)

                return
            }

            this.search = ''
            this.focusedCountry = this.country

            this.isPanelOpen = true
            const generation = ++this.panelGeneration

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

                    this.$refs.search.focus({ preventScroll: true })
                    this.scrollFocusedCountryIntoView()
                }),
            )
        },

        closePanel(shouldRestoreFocus = false) {
            this.isPanelOpen = false
            this.panelGeneration++
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

        selectCountry(country = null) {
            country ??= this.focusedCountry

            if (country === null) {
                return
            }

            const formatter = new AsYouType(this.country ?? undefined)

            formatter.input(this.$refs.input.value)

            this.country = country

            this.$refs.input.value = ''
            this.setNumber(formatter.getNationalNumber())

            this.closePanel()
            this.$refs.input.focus()
        },

        setCountryOptions() {
            const options = this.getCountryOptions(countries ?? getCountries())

            const collator = new Intl.Collator(locale.replace('_', '-'))

            this.countryOptions = [
                ...preferredCountries
                    .map((country) =>
                        options.find((option) => option.code === country),
                    )
                    .filter((option) => option !== undefined),
                ...options
                    .filter(
                        (option) => !preferredCountries.includes(option.code),
                    )
                    .sort((a, b) => collator.compare(a.name, b.name)),
            ]
        },

        setDisplayText() {
            const number = parsePhoneNumberFromString(
                this.state ?? '',
                this.country ?? undefined,
            )

            if (!number?.isValid()) {
                this.$refs.input.value =
                    new AsYouType(this.country ?? undefined).input(
                        this.state ?? '',
                    ) ||
                    (this.state ?? '')

                return
            }

            this.country = number.country ?? this.country

            this.$refs.input.value = number.formatNational()
        },

        setNumber(value) {
            let number = value.replace(/[^\d+]/g, '').replace(/(?!^)\+/g, '')

            while (
                number.replace('+', '').length > 15 ||
                validatePhoneNumberLength(number, this.country ?? undefined) ===
                    'TOO_LONG'
            ) {
                number = number.slice(0, -1)
            }

            const formatter = new AsYouType(this.country ?? undefined)
            const displayText = formatter.input(number)

            if (formatter.getCountry()) {
                this.country = formatter.getCountry()
            }

            if (
                this.$refs.input.selectionStart ===
                    this.$refs.input.value.length ||
                number !== value.replace(/[\s().-]/g, '')
            ) {
                this.$refs.input.value = displayText
            }

            this.state = this.getNumber(number)
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
