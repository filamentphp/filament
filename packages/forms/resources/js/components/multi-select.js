export default (Alpine) => {
    Alpine.data('multiSelectFormComponent', ({
        getOptionLabelsUsing,
        getSearchResultsUsing,
        isAutofocused,
        options,
        state,
    }) => {
        return {
            focusedOptionIndex: null,

            index: {},

            isLoading: false,

            isOpen: false,

            labels: [],

            options,

            search: '',

            state,

            init: async function () {
                if (isAutofocused) {
                    this.openListbox()
                }

                if (! this.state) {
                    this.state = []
                }

                this.addOptionsToIndex(this.options)

                this.labels = await this.getOptionLabels()

                this.$watch('search', async () => {
                    if (! this.isOpen || this.search === '' || this.search === null) {
                        this.options = options
                        this.focusedOptionIndex = 0

                        return
                    }

                    if (Object.keys(options).length) {
                        this.options = {}

                        let search = this.search.trim().toLowerCase()

                        for (let key in options) {
                            if (options[key].trim().toLowerCase().includes(search)) {
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
                })

                this.$watch('state', async () => {
                    this.labels = await this.getOptionLabels()
                })
            },

            addOptionsToIndex: function (options) {
                this.index = {
                    ...this.index,
                    ...options,
                }
            },

            clearState: function () {
                this.state = []
                this.labels = []

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

            openListbox: function () {
                this.focusedOptionIndex = 0

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

                let value = Object.keys(this.options)[index ?? this.focusedOptionIndex]

                if (this.state.indexOf(value) < 0) {
                    this.state.push(value)
                } else {
                    this.deselectOption(value)
                }

                this.closeListbox()
            },

            deselectOption: function (optionToDeselect) {
                this.state = this.state.filter((option) => option !== optionToDeselect)
            },

            toggleListboxVisibility: function () {
                if (this.isOpen) {
                    this.closeListbox()

                    return
                }

                this.openListbox()
            },

            getOptionLabels: async function () {
                let labels = {}
                let areAllLabelsIndexed = true

                for (let key of this.state) {
                    let label = this.index[key] ?? null

                    if (label === null) {
                        areAllLabelsIndexed = false

                        break
                    }

                    labels[key] = label
                }

                if (areAllLabelsIndexed) {
                    return labels
                }

                labels = await getOptionLabelsUsing()

                this.addOptionsToIndex(labels)

                return labels
            },
        }
    })
}
