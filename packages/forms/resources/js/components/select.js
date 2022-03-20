export default (Alpine) => {
    Alpine.data('selectFormComponent', ({
        getOptionLabelUsing,
        getOptionsUsing,
        getSearchResultsUsing,
        isAutofocused,
        hasDynamicOptions,
        options,
        state,
    }) => {
        return {
            focusedOptionIndex: null,

            hasNoSearchResults: false,

            index: {},

            isLoading: false,

            isOpen: false,

            label: null,

            options,

            search: '',

            state,

            init: async function () {
                if (isAutofocused) {
                    this.openListbox(false)
                }

                this.addOptionsToIndex(this.options)

                this.label = await this.getOptionLabel()

                this.$watch('search', Alpine.debounce(async () => {
                    this.hasNoSearchResults = false

                    if (! this.isOpen || this.search === '' || this.search === null) {
                        this.options = options
                        this.focusedOptionIndex = 0

                        return
                    }

                    if (Object.keys(options).length) {
                        this.options = {}

                        let search = this.search.trim().toLowerCase()

                        for (let key in options) {
                            if (options[key].toString().trim().toLowerCase().includes(search)) {
                                this.options[key] = options[key]
                            }
                        }

                        this.focusedOptionIndex = 0
                    } else {
                        this.isLoading = true
                        this.options = await getSearchResultsUsing(this.search)
                        this.addOptionsToIndex(this.options)
                        this.focusedOptionIndex = 0
                        this.isLoading = false
                    }

                    if (! Object.keys(this.options).length) {
                        this.hasNoSearchResults = true
                    }
                }, 500))

                this.$watch('state', async () => {
                    this.label = await this.getOptionLabel()
                })
            },

            addOptionToIndex: function (key, label) {
                this.index[key] = label
            },

            addOptionsToIndex: function (options) {
                this.index = {
                    ...this.index,
                    ...options,
                }
            },

            clearState: function () {
                this.state = null
                this.label = null

                this.closeListbox()
            },

            closeListbox: function () {
                this.isOpen = false

                this.focusedOptionIndex = null

                this.search = ''
            },

            evaluatePosition: function () {
                let availableHeight = window.innerHeight - this.$refs.button.offsetHeight

                let element = this.$refs.button

                while (element) {
                    availableHeight -= element.offsetTop

                    element = element.offsetParent
                }

                if (this.$refs.listbox.offsetHeight <= availableHeight) {
                    this.$refs.listbox.style.bottom = 'auto'

                    return
                }

                this.$refs.listbox.style.bottom = `${this.$refs.button.offsetHeight}px`
            },

            focusNextOption: function () {
                if (this.focusedOptionIndex === null) {
                    this.focusedOptionIndex = Object.keys(this.options).length - 1

                    return
                }

                if (this.focusedOptionIndex + 1 >= Object.keys(this.options).length) {
                    return
                }

                this.focusedOptionIndex++

                this.$refs.listboxOptionsList.children[this.focusedOptionIndex].scrollIntoView({
                    block: 'center',
                })
            },

            focusPreviousOption: function () {
                if (this.focusedOptionIndex === null) {
                    this.focusedOptionIndex = 0

                    return
                }

                if (this.focusedOptionIndex <= 0) {
                    return
                }

                this.focusedOptionIndex--

                this.$refs.listboxOptionsList.children[this.focusedOptionIndex].scrollIntoView({
                    block: 'center',
                })
            },

            openListbox: async function (shouldLoadDynamicOptions = true) {
                if (hasDynamicOptions && shouldLoadDynamicOptions) {
                    this.isLoading = true

                    this.options = await getOptionsUsing()

                    this.isLoading = false
                }

                this.focusedOptionIndex = Object.keys(this.options).indexOf(this.state)

                if (this.focusedOptionIndex < 0) {
                    this.focusedOptionIndex = 0
                }

                this.isOpen = true

                this.$nextTick(() => {
                    this.$refs.search.focus()

                    this.evaluatePosition()

                    this.$refs.listboxOptionsList.children[this.focusedOptionIndex].scrollIntoView({
                        block: 'center'
                    })
                })
            },

            selectOption: function (index = null) {
                if (! this.isOpen) {
                    this.closeListbox()

                    return
                }

                this.state = Object.keys(this.options)[index ?? this.focusedOptionIndex]
                this.label = this.options[this.state]

                this.closeListbox()
            },

            toggleListboxVisibility: function () {
                if (this.isOpen) {
                    this.closeListbox()

                    return
                }

                this.openListbox()
            },

            getOptionLabel: async function () {
                let label = this.index[this.state] ?? null

                if (label !== null) {
                    return label
                }

                label = await getOptionLabelUsing(this.state)

                this.addOptionToIndex(this.state, label)

                return label
            },
        }
    })
}
