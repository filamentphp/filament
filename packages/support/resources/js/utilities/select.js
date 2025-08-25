import { computePosition, flip, shift, offset } from '@floating-ui/dom'

// Helper function to check if a value is null, undefined, or an empty string
function blank(value) {
    return (
        value === null ||
        value === undefined ||
        value === '' ||
        (typeof value === 'string' && value.trim() === '')
    )
}

// Helper function to check if a value is not null, not undefined, and not an empty string
function filled(value) {
    return !blank(value)
}

export class Select {
    constructor({
        element,
        options,
        placeholder,
        state,
        canOptionLabelsWrap = true,
        canSelectPlaceholder = true,
        initialOptionLabel = null,
        initialOptionLabels = null,
        initialState = null,
        isHtmlAllowed = false,
        isAutofocused = false,
        isDisabled = false,
        isMultiple = false,
        isSearchable = false,
        getOptionLabelUsing = null,
        getOptionLabelsUsing = null,
        getOptionsUsing = null,
        getSearchResultsUsing = null,
        hasDynamicOptions = false,
        hasDynamicSearchResults = true,
        searchPrompt = 'Search...',
        searchDebounce = 1000,
        loadingMessage = 'Loading...',
        searchingMessage = 'Searching...',
        noSearchResultsMessage = 'No results found',
        maxItems = null,
        maxItemsMessage = 'Maximum number of items selected',
        optionsLimit = null,
        position = null,
        searchableOptionFields = ['label'],
        livewireId = null,
        statePath = null,
        onStateChange = () => {},
    }) {
        this.element = element
        this.options = options
        this.originalOptions = JSON.parse(JSON.stringify(options)) // Keep a copy of original options
        this.placeholder = placeholder
        this.state = state
        this.canOptionLabelsWrap = canOptionLabelsWrap
        this.canSelectPlaceholder = canSelectPlaceholder
        this.initialOptionLabel = initialOptionLabel
        this.initialOptionLabels = initialOptionLabels
        this.initialState = initialState
        this.isHtmlAllowed = isHtmlAllowed
        this.isAutofocused = isAutofocused
        this.isDisabled = isDisabled
        this.isMultiple = isMultiple
        this.isSearchable = isSearchable
        this.getOptionLabelUsing = getOptionLabelUsing
        this.getOptionLabelsUsing = getOptionLabelsUsing
        this.getOptionsUsing = getOptionsUsing
        this.getSearchResultsUsing = getSearchResultsUsing
        this.hasDynamicOptions = hasDynamicOptions
        this.hasDynamicSearchResults = hasDynamicSearchResults
        this.searchPrompt = searchPrompt
        this.searchDebounce = searchDebounce
        this.loadingMessage = loadingMessage
        this.searchingMessage = searchingMessage
        this.noSearchResultsMessage = noSearchResultsMessage
        this.maxItems = maxItems
        this.maxItemsMessage = maxItemsMessage
        this.optionsLimit = optionsLimit
        this.position = position
        this.searchableOptionFields = Array.isArray(searchableOptionFields)
            ? searchableOptionFields
            : ['label']
        this.livewireId = livewireId
        this.statePath = statePath
        this.onStateChange = onStateChange

        // Central repository for option labels
        this.labelRepository = {}

        this.isOpen = false
        this.selectedIndex = -1
        this.searchQuery = ''
        this.searchTimeout = null

        this.render()
        this.setUpEventListeners()

        if (this.isAutofocused) {
            this.selectButton.focus()
        }
    }

    // Helper method to populate the label repository from options
    populateLabelRepositoryFromOptions(options) {
        if (!options || !Array.isArray(options)) {
            return
        }

        for (const option of options) {
            if (option.options && Array.isArray(option.options)) {
                // Handle option groups
                this.populateLabelRepositoryFromOptions(option.options)
            } else if (
                option.value !== undefined &&
                option.label !== undefined
            ) {
                // Store the label in the repository
                this.labelRepository[option.value] = option.label
            }
        }
    }

    render() {
        // Populate the label repository from initial options
        this.populateLabelRepositoryFromOptions(this.options)

        // Create the main container
        this.container = document.createElement('div')
        this.container.className = 'fi-select-input-ctn'

        if (!this.canOptionLabelsWrap) {
            this.container.classList.add(
                'fi-select-input-ctn-option-labels-not-wrapped',
            )
        }

        this.container.setAttribute('aria-haspopup', 'listbox')

        // Create the button that toggles the dropdown
        this.selectButton = document.createElement('button')
        this.selectButton.className = 'fi-select-input-btn'
        this.selectButton.type = 'button'
        this.selectButton.setAttribute('aria-expanded', 'false')

        // Create the selected value display
        this.selectedDisplay = document.createElement('div')
        this.selectedDisplay.className = 'fi-select-input-value-ctn'

        // Update the selected display based on current state
        this.updateSelectedDisplay()

        this.selectButton.appendChild(this.selectedDisplay)

        // Create the dropdown
        this.dropdown = document.createElement('div')
        this.dropdown.className = 'fi-dropdown-panel fi-scrollable'
        this.dropdown.setAttribute('role', 'listbox')
        this.dropdown.setAttribute('tabindex', '-1')
        this.dropdown.style.display = 'none'

        // Generate a unique ID for the dropdown
        this.dropdownId = `fi-select-input-dropdown-${Math.random().toString(36).substring(2, 11)}`
        this.dropdown.id = this.dropdownId

        // Set aria-multiselectable for multi-select
        if (this.isMultiple) {
            this.dropdown.setAttribute('aria-multiselectable', 'true')
        }

        // Add search input if searchable
        if (this.isSearchable) {
            this.searchContainer = document.createElement('div')
            this.searchContainer.className = 'fi-select-input-search-ctn'

            this.searchInput = document.createElement('input')
            this.searchInput.className = 'fi-input'
            this.searchInput.type = 'text'
            this.searchInput.placeholder = this.searchPrompt
            this.searchInput.setAttribute('aria-label', 'Search')

            this.searchContainer.appendChild(this.searchInput)
            this.dropdown.appendChild(this.searchContainer)

            // Add event listeners for search input
            this.searchInput.addEventListener('input', (event) => {
                // If the select is disabled, don't handle input events
                if (this.isDisabled) {
                    return
                }

                this.handleSearch(event)
            })

            // Handle Tab, Arrow Up, and Arrow Down in search input
            this.searchInput.addEventListener('keydown', (event) => {
                // If the select is disabled, don't handle keyboard events
                if (this.isDisabled) {
                    return
                }

                if (event.key === 'Tab') {
                    event.preventDefault()

                    const options = this.getVisibleOptions()
                    if (options.length === 0) return

                    // If Shift+Tab, focus the last option, otherwise focus the first option
                    if (event.shiftKey) {
                        this.selectedIndex = options.length - 1
                    } else {
                        this.selectedIndex = 0
                    }

                    // Remove focus from any previously focused option
                    options.forEach((option) => {
                        option.classList.remove('fi-selected')
                    })

                    options[this.selectedIndex].classList.add('fi-selected')
                    options[this.selectedIndex].focus()
                } else if (event.key === 'ArrowDown') {
                    event.preventDefault()
                    event.stopPropagation() // Prevent page scrolling

                    const options = this.getVisibleOptions()
                    if (options.length === 0) return

                    // Reset selectedIndex to -1 to ensure we focus the first option
                    this.selectedIndex = -1
                    // Blur the search input to allow arrow key navigation between options
                    this.searchInput.blur()
                    this.focusNextOption()
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault()
                    event.stopPropagation() // Prevent page scrolling

                    const options = this.getVisibleOptions()
                    if (options.length === 0) return

                    // Set selectedIndex to the last option
                    this.selectedIndex = options.length - 1
                    // Blur the search input to allow arrow key navigation between options
                    this.searchInput.blur()

                    // Focus the last option directly
                    options[this.selectedIndex].classList.add('fi-selected')
                    options[this.selectedIndex].focus()

                    // Set aria-activedescendant to the ID of the focused option
                    if (options[this.selectedIndex].id) {
                        this.dropdown.setAttribute(
                            'aria-activedescendant',
                            options[this.selectedIndex].id,
                        )
                    }

                    this.scrollOptionIntoView(options[this.selectedIndex])
                }
            })
        }

        // Create the options list
        this.optionsList = document.createElement('ul')

        // Render options
        this.renderOptions()

        // Append everything to the container
        this.container.appendChild(this.selectButton)
        this.container.appendChild(this.dropdown)

        // Append the container to the element
        this.element.appendChild(this.container)

        // Apply disabled state if needed
        this.applyDisabledState()
    }

    renderOptions() {
        this.optionsList.innerHTML = ''

        // Placeholder option removed as there are X buttons in the main part

        // Process and add options
        let totalRenderedCount = 0

        // Apply options limit if specified
        let optionsToRender = this.options
        let optionsCount = 0

        // Check if we have any grouped options
        let hasGroupedOptions = false

        this.options.forEach((option) => {
            if (option.options && Array.isArray(option.options)) {
                // Count options in groups
                optionsCount += option.options.length
                hasGroupedOptions = true
            } else {
                // Count regular options
                optionsCount++
            }
        })

        // Set the appropriate class based on whether we have grouped options
        if (hasGroupedOptions) {
            this.optionsList.className = 'fi-select-input-options-ctn'
        } else if (optionsCount > 0) {
            // Only set fi-dropdown-list class if there are options to render
            this.optionsList.className = 'fi-dropdown-list'
        }

        // Create a list for ungrouped options only if we have grouped options
        let ungroupedList = hasGroupedOptions ? null : this.optionsList

        // Render options with limit in mind
        let renderedCount = 0

        for (const option of optionsToRender) {
            if (this.optionsLimit && renderedCount >= this.optionsLimit) {
                break
            }

            if (option.options && Array.isArray(option.options)) {
                // This is an option group
                // If in multiple mode, filter out selected options from the group
                let groupOptions = option.options

                if (
                    this.isMultiple &&
                    Array.isArray(this.state) &&
                    this.state.length > 0
                ) {
                    groupOptions = option.options.filter(
                        (groupOption) =>
                            !this.state.includes(groupOption.value),
                    )
                }

                if (groupOptions.length > 0) {
                    // Apply limit to group options if needed
                    if (this.optionsLimit) {
                        const remainingSlots = this.optionsLimit - renderedCount
                        if (remainingSlots < groupOptions.length) {
                            groupOptions = groupOptions.slice(0, remainingSlots)
                        }
                    }

                    this.renderOptionGroup(option.label, groupOptions)
                    renderedCount += groupOptions.length
                    totalRenderedCount += groupOptions.length
                }
            } else {
                // This is a regular option
                // If in multiple mode, skip already selected options
                if (
                    this.isMultiple &&
                    Array.isArray(this.state) &&
                    this.state.includes(option.value)
                ) {
                    continue
                }

                // Create ungrouped list if it doesn't exist yet and we have grouped options
                if (!ungroupedList && hasGroupedOptions) {
                    // Check if there are any ungrouped options to render
                    // We know there's at least one (the current option), so create the list
                    ungroupedList = document.createElement('ul')
                    ungroupedList.className = 'fi-dropdown-list'
                    this.optionsList.appendChild(ungroupedList)
                }

                const optionElement = this.createOptionElement(
                    option.value,
                    option,
                )
                ungroupedList.appendChild(optionElement)
                renderedCount++
                totalRenderedCount++
            }
        }

        // If no options were rendered
        if (totalRenderedCount === 0) {
            // If there's a search query, show "No results" message
            if (this.searchQuery) {
                this.showNoResultsMessage()
            }
            // If in multiple mode and no search query, hide the dropdown
            else if (this.isMultiple && this.isOpen && !this.isSearchable) {
                this.closeDropdown()
            }

            // Remove the options list from the DOM if it's already there
            if (this.optionsList.parentNode === this.dropdown) {
                this.dropdown.removeChild(this.optionsList)
            }
        } else {
            // Hide any existing messages (like "No results")
            this.hideLoadingState()

            // Append the options list to the dropdown if it's not already there
            if (this.optionsList.parentNode !== this.dropdown) {
                this.dropdown.appendChild(this.optionsList)
            }
        }
    }

    renderOptionGroup(label, options) {
        // Don't render if there are no options
        if (options.length === 0) {
            return
        }

        const optionGroup = document.createElement('li')
        optionGroup.className = 'fi-select-input-option-group'

        const optionGroupLabel = document.createElement('div')
        optionGroupLabel.className = 'fi-dropdown-header'
        optionGroupLabel.textContent = label

        const groupOptionsList = document.createElement('ul')
        groupOptionsList.className = 'fi-dropdown-list'

        options.forEach((option) => {
            const optionElement = this.createOptionElement(option.value, option)
            groupOptionsList.appendChild(optionElement)
        })

        optionGroup.appendChild(optionGroupLabel)
        optionGroup.appendChild(groupOptionsList)
        this.optionsList.appendChild(optionGroup)
    }

    createOptionElement(value, label) {
        // Check if this is an object with label, value, and isDisabled properties
        let optionValue = value
        let optionLabel = label
        let isDisabled = false

        if (
            typeof label === 'object' &&
            label !== null &&
            'label' in label &&
            'value' in label
        ) {
            optionValue = label.value
            optionLabel = label.label
            isDisabled = label.isDisabled || false
        }

        const option = document.createElement('li')
        option.className = 'fi-dropdown-list-item fi-select-input-option'

        if (isDisabled) {
            option.classList.add('fi-disabled')
        }

        // Generate a unique ID for the option
        const optionId = `fi-select-input-option-${Math.random().toString(36).substring(2, 11)}`
        option.id = optionId

        option.setAttribute('role', 'option')
        option.setAttribute('data-value', optionValue)
        option.setAttribute('tabindex', '0') // Make the option focusable

        if (isDisabled) {
            option.setAttribute('aria-disabled', 'true')
        }

        // Store the plain text version of the label for aria-label if HTML is allowed
        if (this.isHtmlAllowed && typeof optionLabel === 'string') {
            // Create a temporary div to extract text content from HTML
            const tempDiv = document.createElement('div')
            tempDiv.innerHTML = optionLabel
            const plainText =
                tempDiv.textContent || tempDiv.innerText || optionLabel
            option.setAttribute('aria-label', plainText)
        }

        // Check if this option is selected
        const isSelected = this.isMultiple
            ? Array.isArray(this.state) && this.state.includes(optionValue)
            : this.state === optionValue

        option.setAttribute('aria-selected', isSelected ? 'true' : 'false')

        if (isSelected) {
            option.classList.add('fi-selected')
        }

        const labelSpan = document.createElement('span')

        // Handle HTML content if allowed
        if (this.isHtmlAllowed) {
            labelSpan.innerHTML = optionLabel
        } else {
            labelSpan.textContent = optionLabel
        }

        option.appendChild(labelSpan)

        // Add click event only if not disabled
        if (!isDisabled) {
            option.addEventListener('click', (event) => {
                event.preventDefault()
                event.stopPropagation()
                this.selectOption(optionValue)

                // Prevent the dropdown from losing focus
                if (this.isMultiple) {
                    // For multiple selection, maintain focus within the dropdown
                    if (this.isSearchable && this.searchInput) {
                        setTimeout(() => {
                            this.searchInput.focus()
                        }, 0)
                    } else {
                        // Keep focus on the option
                        setTimeout(() => {
                            option.focus()
                        }, 0)
                    }
                }
            })
        }

        return option
    }

    async updateSelectedDisplay() {
        // Clear the current content
        this.selectedDisplay.innerHTML = ''

        // Handle multiple selection
        if (this.isMultiple) {
            // If no items selected, show placeholder
            if (!Array.isArray(this.state) || this.state.length === 0) {
                const placeholderSpan = document.createElement('span')
                placeholderSpan.textContent = this.placeholder

                this.selectedDisplay.appendChild(placeholderSpan)

                return
            }

            // For multiple selection, get labels for selected options
            let selectedLabels = await this.getLabelsForMultipleSelection()

            // Create and add badges for selected options
            this.addBadgesForSelectedOptions(selectedLabels)

            // Reevaluate dropdown position after badges are added
            if (this.isOpen) {
                this.positionDropdown()
            }
            return
        }

        // Handle single selection

        // If no value selected, show placeholder
        if (this.state === null || this.state === '') {
            const placeholderSpan = document.createElement('span')
            placeholderSpan.textContent = this.placeholder

            this.selectedDisplay.appendChild(placeholderSpan)

            return
        }

        // Get label for the selected value
        const selectedLabel = await this.getLabelForSingleSelection()

        // Add the label and remove button
        this.addSingleSelectionDisplay(selectedLabel)
    }

    // Helper method to get labels for multiple selection
    async getLabelsForMultipleSelection() {
        let selectedLabels = this.getSelectedOptionLabels()

        // Check for values that are not in the repository or options
        const missingValues = []
        if (Array.isArray(this.state)) {
            for (const value of this.state) {
                // Check if we have the label in the repository
                if (filled(this.labelRepository[value])) {
                    continue
                }

                // Check if we have the label in the options
                if (filled(selectedLabels[value])) {
                    // Store the label in the repository
                    this.labelRepository[value] = selectedLabels[value]
                    continue
                }

                // If not found, add to missing values
                missingValues.push(value)
            }
        }

        // If we have missing values and current state matches initialState, use initialOptionLabels
        if (
            missingValues.length > 0 &&
            filled(this.initialOptionLabels) &&
            JSON.stringify(this.state) === JSON.stringify(this.initialState)
        ) {
            // Use initialOptionLabels and store them in the repository
            if (Array.isArray(this.initialOptionLabels)) {
                // initialOptionLabels is an array of objects with label and value properties
                for (const initialOption of this.initialOptionLabels) {
                    if (
                        filled(initialOption) &&
                        initialOption.value !== undefined &&
                        initialOption.label !== undefined &&
                        missingValues.includes(initialOption.value)
                    ) {
                        // Store the label in the repository
                        this.labelRepository[initialOption.value] =
                            initialOption.label
                    }
                }
            }
        }
        // If we still have missing values and getOptionLabelsUsing is available, fetch them
        else if (missingValues.length > 0 && this.getOptionLabelsUsing) {
            try {
                // Fetch labels for missing values - returns array of {label, value} objects
                const fetchedOptionsArray = await this.getOptionLabelsUsing()

                // Store fetched labels in the repository
                for (const option of fetchedOptionsArray) {
                    if (
                        filled(option) &&
                        option.value !== undefined &&
                        option.label !== undefined
                    ) {
                        this.labelRepository[option.value] = option.label
                    }
                }
            } catch (error) {
                console.error('Error fetching option labels:', error)
            }
        }

        // Create a result array with all labels in the same order as this.state
        const result = []
        if (Array.isArray(this.state)) {
            for (const value of this.state) {
                // First check if we have a label in the repository
                if (filled(this.labelRepository[value])) {
                    result.push(this.labelRepository[value])
                }
                // Then check if we have a label from options
                else if (filled(selectedLabels[value])) {
                    result.push(selectedLabels[value])
                }
                // If no label is found, use the value as fallback
                else {
                    result.push(value)
                }
            }
        }

        return result
    }

    // Helper method to create a badge element
    createBadgeElement(value, label) {
        const badge = document.createElement('span')
        badge.className =
            'fi-badge fi-size-md fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-200'

        // Add a data attribute to identify this badge by its value
        if (filled(value)) {
            badge.setAttribute('data-value', value)
        }

        // Create a container for the label text
        const labelContainer = document.createElement('span')
        labelContainer.className = 'fi-badge-label-ctn'

        // Create an element for the label text
        const labelElement = document.createElement('span')
        labelElement.className = 'fi-badge-label'

        if (this.canOptionLabelsWrap) {
            labelElement.classList.add('fi-wrapped')
        }

        if (this.isHtmlAllowed) {
            labelElement.innerHTML = label
        } else {
            labelElement.textContent = label
        }

        labelContainer.appendChild(labelElement)
        badge.appendChild(labelContainer)

        // Add a cross button to remove the selection
        const removeButton = this.createRemoveButton(value, label)
        badge.appendChild(removeButton)

        return badge
    }

    // Helper method to create a remove button
    createRemoveButton(value, label) {
        const removeButton = document.createElement('button')
        removeButton.type = 'button'
        removeButton.className = 'fi-badge-delete-btn'
        removeButton.innerHTML =
            '<svg class="fi-icon fi-size-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z"></path></svg>'
        removeButton.setAttribute(
            'aria-label',
            'Remove ' +
                (this.isHtmlAllowed ? label.replace(/<[^>]*>/g, '') : label),
        )

        removeButton.addEventListener('click', (event) => {
            event.stopPropagation() // Prevent dropdown from toggling
            if (filled(value)) {
                this.selectOption(value) // This will remove the value since it's already selected
            }
        })

        // Add keydown event listener to handle space key
        removeButton.addEventListener('keydown', (event) => {
            if (event.key === ' ' || event.key === 'Enter') {
                event.preventDefault()
                event.stopPropagation() // Prevent event from bubbling up to selectButton
                if (filled(value)) {
                    this.selectOption(value)
                }
            }
        })

        return removeButton
    }

    // Helper method to add badges for selected options
    addBadgesForSelectedOptions(selectedLabels) {
        // Create a container for the badges
        const badgesContainer = document.createElement('div')
        badgesContainer.className = 'fi-select-input-value-badges-ctn'

        // Add badges for each selected option
        selectedLabels.forEach((label, index) => {
            const value = Array.isArray(this.state) ? this.state[index] : null
            const badge = this.createBadgeElement(value, label)
            badgesContainer.appendChild(badge)
        })

        this.selectedDisplay.appendChild(badgesContainer)
    }

    // Helper method to get label for single selection
    async getLabelForSingleSelection() {
        // First check if we have the label in the repository
        let selectedLabel = this.labelRepository[this.state]

        // If not in repository, try to find it in the options
        if (blank(selectedLabel)) {
            selectedLabel = this.getSelectedOptionLabel(this.state)
        }

        // If label not found and current state matches initialState, use initialOptionLabel
        if (
            blank(selectedLabel) &&
            filled(this.initialOptionLabel) &&
            this.state === this.initialState
        ) {
            selectedLabel = this.initialOptionLabel

            // Store the label in the repository for future use
            if (filled(this.state)) {
                this.labelRepository[this.state] = selectedLabel
            }
        }
        // If label still not found and getOptionLabelUsing is available, fetch it
        else if (blank(selectedLabel) && this.getOptionLabelUsing) {
            try {
                selectedLabel = await this.getOptionLabelUsing()

                // Store the fetched label in the repository
                if (filled(selectedLabel) && filled(this.state)) {
                    this.labelRepository[this.state] = selectedLabel
                }
            } catch (error) {
                console.error('Error fetching option label:', error)
                selectedLabel = this.state // Fallback to using the value as the label
            }
        } else if (blank(selectedLabel)) {
            // If still no label and no getOptionLabelUsing, use the value as the label
            selectedLabel = this.state
        }

        return selectedLabel
    }

    // Helper method to add single selection display
    addSingleSelectionDisplay(selectedLabel) {
        // Create a container for the label
        const labelContainer = document.createElement('span')
        labelContainer.className = 'fi-select-input-value-label'

        if (this.isHtmlAllowed) {
            labelContainer.innerHTML = selectedLabel
        } else {
            labelContainer.textContent = selectedLabel
        }

        this.selectedDisplay.appendChild(labelContainer)

        // Add a cross button to clear the selection if canSelectPlaceholder is true
        if (!this.canSelectPlaceholder) {
            return
        }

        const removeButton = document.createElement('button')
        removeButton.type = 'button'
        removeButton.className = 'fi-select-input-value-remove-btn'
        removeButton.innerHTML =
            '<svg class="fi-icon fi-size-sm" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>'
        removeButton.setAttribute('aria-label', 'Clear selection')

        removeButton.addEventListener('click', (event) => {
            event.stopPropagation() // Prevent dropdown from toggling
            this.selectOption('') // Select empty value to clear
        })

        // Add keydown event listener to handle space key
        removeButton.addEventListener('keydown', (event) => {
            if (event.key === ' ' || event.key === 'Enter') {
                event.preventDefault()
                event.stopPropagation() // Prevent event from bubbling up to selectButton
                this.selectOption('') // Select empty value to clear
            }
        })

        this.selectedDisplay.appendChild(removeButton)
    }

    getSelectedOptionLabel(value) {
        // First check if we have the label in the repository
        if (filled(this.labelRepository[value])) {
            return this.labelRepository[value]
        }

        // If not in repository, search in options
        let selectedLabel = ''

        for (const option of this.options) {
            if (option.options && Array.isArray(option.options)) {
                // Search in option group
                for (const groupOption of option.options) {
                    if (groupOption.value === value) {
                        selectedLabel = groupOption.label
                        // Store the label in the repository for future use
                        this.labelRepository[value] = selectedLabel
                        break
                    }
                }
            } else if (option.value === value) {
                selectedLabel = option.label
                // Store the label in the repository for future use
                this.labelRepository[value] = selectedLabel
                break
            }
        }

        return selectedLabel
    }

    setUpEventListeners() {
        // Store event listener references for later cleanup
        this.buttonClickListener = () => {
            this.toggleDropdown()
        }

        this.documentClickListener = (event) => {
            if (!this.container.contains(event.target) && this.isOpen) {
                this.closeDropdown()
            }
        }

        this.buttonKeydownListener = (event) => {
            // If the select is disabled, don't handle keyboard events
            if (this.isDisabled) {
                return
            }

            this.handleSelectButtonKeydown(event)
        }

        this.dropdownKeydownListener = (event) => {
            // If the select is disabled, don't handle keyboard events
            if (this.isDisabled) {
                return
            }

            // Skip navigation if search input is focused and it's not Tab or Escape
            if (
                this.isSearchable &&
                document.activeElement === this.searchInput &&
                !['Tab', 'Escape'].includes(event.key)
            ) {
                return
            }

            this.handleDropdownKeydown(event)
        }

        // Toggle dropdown when button is clicked
        this.selectButton.addEventListener('click', this.buttonClickListener)

        // Close dropdown when clicking outside
        document.addEventListener('click', this.documentClickListener)

        // Keyboard navigation for the select button
        this.selectButton.addEventListener(
            'keydown',
            this.buttonKeydownListener,
        )

        // Keyboard navigation within dropdown
        this.dropdown.addEventListener('keydown', this.dropdownKeydownListener)

        // Add event listener for refreshing selected option labels (only for non-multiple selects)
        if (
            !this.isMultiple &&
            this.livewireId &&
            this.statePath &&
            this.getOptionLabelUsing
        ) {
            this.refreshOptionLabelListener = async (event) => {
                // Check if the event is for this select
                if (
                    event.detail.livewireId === this.livewireId &&
                    event.detail.statePath === this.statePath
                ) {
                    // Refresh the selected option label
                    if (filled(this.state)) {
                        try {
                            // Clear the label from the repository so it can be fetched again
                            delete this.labelRepository[this.state]

                            // Get the new label
                            const newLabel = await this.getOptionLabelUsing()

                            // Store the new label in the repository
                            if (filled(newLabel)) {
                                this.labelRepository[this.state] = newLabel
                            }

                            // Update the displayed label
                            const labelContainer =
                                this.selectedDisplay.querySelector(
                                    '.fi-select-input-value-label',
                                )
                            if (filled(labelContainer)) {
                                if (this.isHtmlAllowed) {
                                    labelContainer.innerHTML = newLabel
                                } else {
                                    labelContainer.textContent = newLabel
                                }
                            }

                            // Update the label in the options list
                            this.updateOptionLabelInList(this.state, newLabel)
                        } catch (error) {
                            console.error(
                                'Error refreshing option label:',
                                error,
                            )
                        }
                    }
                }
            }

            window.addEventListener(
                'filament-forms::select.refreshSelectedOptionLabel',
                this.refreshOptionLabelListener,
            )
        }
    }

    // Helper method to update an option's label in the options list
    updateOptionLabelInList(value, newLabel) {
        // Update the label in the repository
        this.labelRepository[value] = newLabel

        // Find the option in the list
        const options = this.getVisibleOptions()
        for (const option of options) {
            if (option.getAttribute('data-value') === String(value)) {
                // Clear the option content
                option.innerHTML = ''

                // Add the new label
                if (this.isHtmlAllowed) {
                    const labelSpan = document.createElement('span')
                    labelSpan.innerHTML = newLabel
                    option.appendChild(labelSpan)
                } else {
                    option.appendChild(document.createTextNode(newLabel))
                }

                break
            }
        }

        // Also update the option in the original options array
        for (const option of this.options) {
            if (option.options && Array.isArray(option.options)) {
                // Search in option group
                for (const groupOption of option.options) {
                    if (groupOption.value === value) {
                        groupOption.label = newLabel
                        break
                    }
                }
            } else if (option.value === value) {
                option.label = newLabel
                break
            }
        }

        // Update the original options as well
        for (const option of this.originalOptions) {
            if (option.options && Array.isArray(option.options)) {
                // Search in option group
                for (const groupOption of option.options) {
                    if (groupOption.value === value) {
                        groupOption.label = newLabel
                        break
                    }
                }
            } else if (option.value === value) {
                option.label = newLabel
                break
            }
        }
    }

    // Handle keyboard events for the select button
    handleSelectButtonKeydown(event) {
        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault()
                event.stopPropagation() // Prevent page scrolling
                if (!this.isOpen) {
                    this.openDropdown()
                } else {
                    this.focusNextOption()
                }
                break
            case 'ArrowUp':
                event.preventDefault()
                event.stopPropagation() // Prevent page scrolling
                if (!this.isOpen) {
                    this.openDropdown()
                } else {
                    this.focusPreviousOption()
                }
                break
            case ' ':
                event.preventDefault()
                if (this.isOpen) {
                    if (this.selectedIndex >= 0) {
                        const focusedOption =
                            this.getVisibleOptions()[this.selectedIndex]
                        if (focusedOption) {
                            focusedOption.click()
                        }
                    }
                } else {
                    this.openDropdown()
                }
                break
            case 'Enter':
                // Do nothing for Enter key, allow it to submit the form
                break
            case 'Escape':
                if (this.isOpen) {
                    event.preventDefault()
                    this.closeDropdown()
                }
                break
            case 'Tab':
                if (this.isOpen) {
                    this.closeDropdown()
                }
                break
        }
    }

    // Handle keyboard events within the dropdown
    handleDropdownKeydown(event) {
        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault()
                event.stopPropagation() // Prevent page scrolling
                this.focusNextOption()
                break
            case 'ArrowUp':
                event.preventDefault()
                event.stopPropagation() // Prevent page scrolling
                this.focusPreviousOption()
                break
            case ' ':
                event.preventDefault()
                if (this.selectedIndex >= 0) {
                    const focusedOption =
                        this.getVisibleOptions()[this.selectedIndex]
                    if (focusedOption) {
                        focusedOption.click()
                    }
                }
                break
            case 'Enter':
                event.preventDefault()
                if (this.selectedIndex >= 0) {
                    const focusedOption =
                        this.getVisibleOptions()[this.selectedIndex]
                    if (focusedOption) {
                        focusedOption.click()
                    }
                } else {
                    // If no option is focused, submit the form
                    const form = this.element.closest('form')
                    if (form) {
                        form.submit()
                    }
                }
                break
            case 'Escape':
                event.preventDefault()
                this.closeDropdown()
                this.selectButton.focus()
                break
            case 'Tab':
                this.closeDropdown()
                break
        }
    }

    toggleDropdown() {
        // If the select is disabled, don't allow toggling the dropdown
        if (this.isDisabled) {
            return
        }

        // If dropdown is already open, close it and exit
        if (this.isOpen) {
            this.closeDropdown()
            return
        }

        // In multiple selection mode with no search, check if there are any available options
        if (
            this.isMultiple &&
            !this.isSearchable &&
            !this.hasAvailableOptions()
        ) {
            return // No available options, don't open dropdown
        }

        // Open the dropdown
        this.openDropdown()
    }

    // Helper method to check if there are any available options
    hasAvailableOptions() {
        // For multiple selection, we need to check if there are any options that aren't already selected

        for (const option of this.options) {
            if (option.options && Array.isArray(option.options)) {
                // This is an option group
                for (const groupOption of option.options) {
                    if (
                        !Array.isArray(this.state) ||
                        !this.state.includes(groupOption.value)
                    ) {
                        return true // At least one option is available
                    }
                }
            } else if (
                !Array.isArray(this.state) ||
                !this.state.includes(option.value)
            ) {
                return true // At least one option is available
            }
        }

        // No available options found
        return false
    }

    async openDropdown() {
        // Make dropdown visible but with position fixed (or absolute in containers with .fi-absolute-positioning-context class) and opacity 0 for measurement
        this.dropdown.style.display = 'block'
        this.dropdown.style.opacity = '0'

        // Check if the select is inside a container that requires absolute positioning
        const useAbsolutePositioning =
            this.selectButton.closest('.fi-absolute-positioning-context') !==
            null
        this.dropdown.style.position = useAbsolutePositioning
            ? 'absolute'
            : 'fixed'
        // Set width immediately to match the select button
        this.dropdown.style.width = `${this.selectButton.offsetWidth}px`
        this.selectButton.setAttribute('aria-expanded', 'true')
        this.isOpen = true

        // Position the dropdown using Floating UI
        this.positionDropdown()

        // Add resize listener to update width and position when window is resized
        if (!this.resizeListener) {
            this.resizeListener = () => {
                // Update width to match the select button
                this.dropdown.style.width = `${this.selectButton.offsetWidth}px`
                this.positionDropdown()
            }
            window.addEventListener('resize', this.resizeListener)
        }

        // Add scroll listener to update position when page is scrolled
        if (!this.scrollListener) {
            this.scrollListener = () => this.positionDropdown()
            window.addEventListener('scroll', this.scrollListener, true)
        }

        // Make dropdown visible
        this.dropdown.style.opacity = '1'

        // If hasDynamicOptions is true, fetch options
        if (this.hasDynamicOptions && this.getOptionsUsing) {
            // Show loading message
            this.showLoadingState(false)

            try {
                // Fetch options
                const fetchedOptions = await this.getOptionsUsing()

                // Update options
                this.options = fetchedOptions
                this.originalOptions = JSON.parse(
                    JSON.stringify(fetchedOptions),
                )

                // Populate the label repository with the fetched options
                this.populateLabelRepositoryFromOptions(fetchedOptions)

                // Render options
                this.renderOptions()
            } catch (error) {
                console.error('Error fetching options:', error)

                // Hide loading state
                this.hideLoadingState()
            }
        }

        // Hide any existing messages (like "No results")
        this.hideLoadingState()

        // If searchable, focus the search input
        if (this.isSearchable && this.searchInput) {
            this.searchInput.value = ''
            this.searchInput.focus()

            // Always reset search query and options when reopening
            this.searchQuery = ''
            this.options = JSON.parse(JSON.stringify(this.originalOptions))
            this.renderOptions()
        } else {
            // Focus the first option or the selected option
            this.selectedIndex = -1

            // Find the index of the selected option
            const options = this.getVisibleOptions()
            if (this.isMultiple) {
                if (Array.isArray(this.state) && this.state.length > 0) {
                    for (let i = 0; i < options.length; i++) {
                        if (
                            this.state.includes(
                                options[i].getAttribute('data-value'),
                            )
                        ) {
                            this.selectedIndex = i
                            break
                        }
                    }
                }
            } else {
                for (let i = 0; i < options.length; i++) {
                    if (options[i].getAttribute('data-value') === this.state) {
                        this.selectedIndex = i
                        break
                    }
                }
            }

            // If no option is selected, focus the first option
            if (this.selectedIndex === -1 && options.length > 0) {
                this.selectedIndex = 0
            }

            // Focus the selected option
            if (this.selectedIndex >= 0) {
                options[this.selectedIndex].classList.add('fi-selected')
                options[this.selectedIndex].focus()
            }
        }
    }

    positionDropdown() {
        const placement = this.position === 'top' ? 'top-start' : 'bottom-start'
        const middleware = [
            offset(4), // Add some space between button and dropdown
            shift({ padding: 5 }), // Keep within viewport with some padding
        ]

        // Only use flip middleware if position is not explicitly set to 'top' or 'bottom'
        if (this.position !== 'top' && this.position !== 'bottom') {
            middleware.push(flip()) // Flip to top if not enough space at bottom
        }

        // Check if the select is inside a container that requires absolute positioning
        const useAbsolutePositioning =
            this.selectButton.closest('.fi-absolute-positioning-context') !==
            null

        computePosition(this.selectButton, this.dropdown, {
            placement: placement,
            middleware: middleware,
            strategy: useAbsolutePositioning ? 'absolute' : 'fixed',
        }).then(({ x, y }) => {
            Object.assign(this.dropdown.style, {
                left: `${x}px`,
                top: `${y}px`,
            })
        })
    }

    closeDropdown() {
        this.dropdown.style.display = 'none'
        this.selectButton.setAttribute('aria-expanded', 'false')
        this.isOpen = false

        // Remove resize listener
        if (this.resizeListener) {
            window.removeEventListener('resize', this.resizeListener)
            this.resizeListener = null
        }

        // Remove scroll listener
        if (this.scrollListener) {
            window.removeEventListener('scroll', this.scrollListener, true)
            this.scrollListener = null
        }

        // Remove focus from all options
        const options = this.getVisibleOptions()
        options.forEach((option) => {
            option.classList.remove('fi-selected')
        })
    }

    focusNextOption() {
        const options = this.getVisibleOptions()
        if (options.length === 0) return

        // Remove focus from current option
        if (this.selectedIndex >= 0 && this.selectedIndex < options.length) {
            options[this.selectedIndex].classList.remove('fi-selected')
        }

        // If we're at the last option and search input is available, focus the search input
        if (
            this.selectedIndex === options.length - 1 &&
            this.isSearchable &&
            this.searchInput
        ) {
            this.selectedIndex = -1
            this.searchInput.focus()
            // Clear aria-activedescendant when focus moves to search input
            this.dropdown.removeAttribute('aria-activedescendant')
            return
        }

        // Focus next option (wrap around to the first option if at the end)
        this.selectedIndex = (this.selectedIndex + 1) % options.length
        options[this.selectedIndex].classList.add('fi-selected')
        options[this.selectedIndex].focus()

        // Set aria-activedescendant to the ID of the focused option
        if (options[this.selectedIndex].id) {
            this.dropdown.setAttribute(
                'aria-activedescendant',
                options[this.selectedIndex].id,
            )
        }

        this.scrollOptionIntoView(options[this.selectedIndex])
    }

    focusPreviousOption() {
        const options = this.getVisibleOptions()
        if (options.length === 0) return

        // Remove focus from current option
        if (this.selectedIndex >= 0 && this.selectedIndex < options.length) {
            options[this.selectedIndex].classList.remove('fi-selected')
        }

        // If we're at the first option or haven't selected an option yet, focus the search input if available
        if (
            (this.selectedIndex === 0 || this.selectedIndex === -1) &&
            this.isSearchable &&
            this.searchInput
        ) {
            this.selectedIndex = -1
            this.searchInput.focus()
            // Clear aria-activedescendant when focus moves to search input
            this.dropdown.removeAttribute('aria-activedescendant')
            return
        }

        // Focus previous option (wrap around to the last option if at the beginning)
        this.selectedIndex =
            (this.selectedIndex - 1 + options.length) % options.length
        options[this.selectedIndex].classList.add('fi-selected')
        options[this.selectedIndex].focus()

        // Set aria-activedescendant to the ID of the focused option
        if (options[this.selectedIndex].id) {
            this.dropdown.setAttribute(
                'aria-activedescendant',
                options[this.selectedIndex].id,
            )
        }

        this.scrollOptionIntoView(options[this.selectedIndex])
    }

    scrollOptionIntoView(option) {
        if (!option) return

        const dropdownRect = this.dropdown.getBoundingClientRect()
        const optionRect = option.getBoundingClientRect()

        if (optionRect.bottom > dropdownRect.bottom) {
            this.dropdown.scrollTop += optionRect.bottom - dropdownRect.bottom
        } else if (optionRect.top < dropdownRect.top) {
            this.dropdown.scrollTop -= dropdownRect.top - optionRect.top
        }
    }

    getVisibleOptions() {
        let ungroupedOptions = []

        // Check if optionsList itself has the fi-dropdown-list class (no grouped options case)
        if (this.optionsList.classList.contains('fi-dropdown-list')) {
            // Get direct child options when there are no groups
            ungroupedOptions = Array.from(
                this.optionsList.querySelectorAll(':scope > li[role="option"]'),
            )
        } else {
            // Get options from nested ungrouped list when there are groups
            ungroupedOptions = Array.from(
                this.optionsList.querySelectorAll(
                    ':scope > ul.fi-dropdown-list > li[role="option"]',
                ),
            )
        }

        // Get all option elements that are in option groups
        const groupOptions = Array.from(
            this.optionsList.querySelectorAll(
                'li.fi-select-input-option-group > ul > li[role="option"]',
            ),
        )

        // Combine and return all options
        return [...ungroupedOptions, ...groupOptions]
    }

    getSelectedOptionLabels() {
        if (!Array.isArray(this.state) || this.state.length === 0) {
            return {}
        }

        const labels = {}

        for (const value of this.state) {
            // Search in flat options
            let found = false
            for (const option of this.options) {
                if (option.options && Array.isArray(option.options)) {
                    // Search in option group
                    for (const groupOption of option.options) {
                        if (groupOption.value === value) {
                            labels[value] = groupOption.label
                            found = true
                            break
                        }
                    }
                    if (found) break
                } else if (option.value === value) {
                    labels[value] = option.label
                    found = true
                    break
                }
            }

            // If not found, don't add a fallback
            // This allows the caller to know which labels are missing
        }

        return labels
    }

    handleSearch(event) {
        const query = event.target.value.trim().toLowerCase()
        this.searchQuery = query

        // Clear any existing timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout)
        }

        // If query is empty, restore original options and exit early
        if (query === '') {
            this.options = JSON.parse(JSON.stringify(this.originalOptions))
            this.renderOptions()
            return
        }

        // If we don't have dynamic search results or no search function, filter locally and exit early
        if (
            !this.getSearchResultsUsing ||
            typeof this.getSearchResultsUsing !== 'function' ||
            !this.hasDynamicSearchResults
        ) {
            this.filterOptions(query)
            return
        }

        // Handle server-side search with debounce
        this.searchTimeout = setTimeout(async () => {
            try {
                // Show searching state
                this.showLoadingState(true)

                // Get search results from backend
                const results = await this.getSearchResultsUsing(query)

                // Update options with search results
                this.options = results

                // Update the label repository with the search results
                this.populateLabelRepositoryFromOptions(results)

                // Hide loading state and render options
                this.hideLoadingState()
                this.renderOptions()

                // Reevaluate dropdown position after search results are updated
                if (this.isOpen) {
                    this.positionDropdown()
                }

                // If no results found, show "No results" message
                if (this.options.length === 0) {
                    this.showNoResultsMessage()
                }
            } catch (error) {
                console.error('Error fetching search results:', error)

                // Hide loading state and restore original options
                this.hideLoadingState()
                this.options = JSON.parse(JSON.stringify(this.originalOptions))
                this.renderOptions()
            }
        }, this.searchDebounce)
    }

    showLoadingState(isSearching = false) {
        // Clear options list if it's in the DOM
        if (this.optionsList.parentNode === this.dropdown) {
            this.optionsList.innerHTML = ''
        }

        // Remove any existing message
        this.hideLoadingState()

        // Add loading message
        const loadingItem = document.createElement('div')
        loadingItem.className = 'fi-select-input-message'
        loadingItem.textContent = isSearching
            ? this.searchingMessage
            : this.loadingMessage
        this.dropdown.appendChild(loadingItem)
    }

    hideLoadingState() {
        // Remove loading message
        const loadingItem = this.dropdown.querySelector(
            '.fi-select-input-message',
        )
        if (loadingItem) {
            loadingItem.remove()
        }
    }

    showNoResultsMessage() {
        // Clear options list if it's in the DOM and not already empty
        if (
            this.optionsList.parentNode === this.dropdown &&
            this.optionsList.children.length > 0
        ) {
            this.optionsList.innerHTML = ''
        }

        // Remove any existing message
        this.hideLoadingState()

        // Add "No results" message
        const noResultsItem = document.createElement('div')
        noResultsItem.className = 'fi-select-input-message'
        noResultsItem.textContent = this.noSearchResultsMessage
        this.dropdown.appendChild(noResultsItem)
    }

    filterOptions(query) {
        const searchInLabel = this.searchableOptionFields.includes('label')
        const searchInValue = this.searchableOptionFields.includes('value')

        const filteredOptions = []

        for (const option of this.originalOptions) {
            if (option.options && Array.isArray(option.options)) {
                // This is an option group
                const filteredGroupOptions = option.options.filter(
                    (groupOption) => {
                        // Check if the option matches the search query in any of the specified fields
                        return (
                            (searchInLabel &&
                                groupOption.label
                                    .toLowerCase()
                                    .includes(query)) ||
                            (searchInValue &&
                                String(groupOption.value)
                                    .toLowerCase()
                                    .includes(query))
                        )
                    },
                )

                if (filteredGroupOptions.length > 0) {
                    filteredOptions.push({
                        label: option.label,
                        options: filteredGroupOptions,
                    })
                }
            } else if (
                (searchInLabel && option.label.toLowerCase().includes(query)) ||
                (searchInValue &&
                    String(option.value).toLowerCase().includes(query))
            ) {
                // This is a regular option
                filteredOptions.push(option)
            }
        }

        this.options = filteredOptions

        // Render filtered options
        this.renderOptions()

        // If no options found, show "No results" message
        if (this.options.length === 0) {
            this.showNoResultsMessage()
        }

        // Reevaluate dropdown position after search results are updated
        if (this.isOpen) {
            this.positionDropdown()
        }
    }

    selectOption(value) {
        // If the select is disabled, don't allow selection
        if (this.isDisabled) {
            return
        }

        if (!this.isMultiple) {
            // For single selection - simpler case, handle first
            this.state = value
            this.updateSelectedDisplay()
            this.renderOptions()
            this.closeDropdown()
            this.selectButton.focus()
            this.onStateChange(this.state)
            return
        }

        // For multiple selection
        let newState = Array.isArray(this.state) ? [...this.state] : []

        // If already selected, remove the value
        if (newState.includes(value)) {
            // Find and remove the badge directly from the DOM
            const badgeToRemove = this.selectedDisplay.querySelector(
                `[data-value="${value}"]`,
            )
            if (filled(badgeToRemove)) {
                // Check if this is the last badge
                const badgesContainer = badgeToRemove.parentElement
                if (
                    filled(badgesContainer) &&
                    badgesContainer.children.length === 1
                ) {
                    // If this is the last badge, we need to update the display to show the placeholder
                    newState = newState.filter((v) => v !== value)
                    this.state = newState
                    this.updateSelectedDisplay()
                } else {
                    // Otherwise, just remove this badge
                    badgeToRemove.remove()

                    // Update the state
                    newState = newState.filter((v) => v !== value)
                    this.state = newState
                }
            } else {
                // If we couldn't find the badge, fall back to full update
                newState = newState.filter((v) => v !== value)
                this.state = newState
                this.updateSelectedDisplay()
            }

            this.renderOptions()

            // Reevaluate dropdown position after options are removed
            if (this.isOpen) {
                this.positionDropdown()
            }

            this.maintainFocusInMultipleMode()
            this.onStateChange(this.state)
            return
        }

        // Check if maxItems limit has been reached
        if (this.maxItems && newState.length >= this.maxItems) {
            // Show a message or alert about reaching the limit
            if (this.maxItemsMessage) {
                alert(this.maxItemsMessage)
            }
            return // Don't add more items
        }

        // Add the new value
        newState.push(value)
        this.state = newState

        // Check if we already have a badges container
        const existingBadgesContainer = this.selectedDisplay.querySelector(
            '.fi-select-input-value-badges-ctn',
        )

        if (blank(existingBadgesContainer)) {
            // If no badges container exists, we need to do a full update
            this.updateSelectedDisplay()
        } else {
            // Otherwise, just add a new badge to the existing container
            this.addSingleBadge(value, existingBadgesContainer)
        }

        this.renderOptions()

        // Reevaluate dropdown position after options are added
        if (this.isOpen) {
            this.positionDropdown()
        }

        this.maintainFocusInMultipleMode()
        this.onStateChange(this.state)
    }

    // Helper method to add a single badge for a value
    async addSingleBadge(value, badgesContainer) {
        // First check if we have the label in the repository
        let label = this.labelRepository[value]

        // If not in repository, try to find it in the options
        if (blank(label)) {
            label = this.getSelectedOptionLabel(value)

            // If found in options, store it in the repository
            if (filled(label)) {
                this.labelRepository[value] = label
            }
        }

        // If label not found and getOptionLabelsUsing is available, fetch it
        if (blank(label) && this.getOptionLabelsUsing) {
            try {
                // Fetch labels for this value - returns array of {label, value} objects
                const fetchedOptionsArray = await this.getOptionLabelsUsing()

                // Find the matching option
                for (const option of fetchedOptionsArray) {
                    if (
                        filled(option) &&
                        option.value === value &&
                        option.label !== undefined
                    ) {
                        label = option.label
                        // Store the fetched label in the repository
                        this.labelRepository[value] = label
                        break
                    }
                }
            } catch (error) {
                console.error('Error fetching option label:', error)
            }
        }

        // If still no label, use the value as fallback
        if (blank(label)) {
            label = value
        }

        // Create and add the badge
        const badge = this.createBadgeElement(value, label)
        badgesContainer.appendChild(badge)
    }

    // Helper method to maintain focus in multiple selection mode
    maintainFocusInMultipleMode() {
        if (this.isSearchable && this.searchInput) {
            // If searchable, focus the search input
            this.searchInput.focus()
            return
        }

        // Otherwise, focus the first option or the selected option
        const options = this.getVisibleOptions()
        if (options.length === 0) {
            return
        }

        // Find the index of the selected option
        this.selectedIndex = -1
        if (Array.isArray(this.state) && this.state.length > 0) {
            for (let i = 0; i < options.length; i++) {
                if (
                    this.state.includes(options[i].getAttribute('data-value'))
                ) {
                    this.selectedIndex = i
                    break
                }
            }
        }

        // If no option is selected, focus the first option
        if (this.selectedIndex === -1) {
            this.selectedIndex = 0
        }

        // Focus the selected option
        options[this.selectedIndex].classList.add('fi-selected')
        options[this.selectedIndex].focus()
    }

    disable() {
        if (this.isDisabled) return // Already disabled

        this.isDisabled = true
        this.applyDisabledState()

        // Close dropdown if it's open
        if (this.isOpen) {
            this.closeDropdown()
        }
    }

    enable() {
        if (!this.isDisabled) return // Already enabled

        this.isDisabled = false
        this.applyDisabledState()
    }

    applyDisabledState() {
        if (this.isDisabled) {
            // Add disabled attribute and class to the select button
            this.selectButton.setAttribute('disabled', 'disabled')
            this.selectButton.setAttribute('aria-disabled', 'true')
            this.selectButton.classList.add('fi-disabled')

            // If there are remove buttons in multiple mode, disable them
            if (this.isMultiple) {
                const removeButtons = this.container.querySelectorAll(
                    '.fi-select-input-badge-remove',
                )
                removeButtons.forEach((button) => {
                    button.setAttribute('disabled', 'disabled')
                    button.classList.add('fi-disabled')
                })
            }

            // If there's a remove button in single mode, disable it
            if (!this.isMultiple && this.canSelectPlaceholder) {
                const removeButton = this.container.querySelector(
                    '.fi-select-input-value-remove-btn',
                )
                if (removeButton) {
                    removeButton.setAttribute('disabled', 'disabled')
                    removeButton.classList.add('fi-disabled')
                }
            }

            // If there's a search input, disable it
            if (this.isSearchable && this.searchInput) {
                this.searchInput.setAttribute('disabled', 'disabled')
                this.searchInput.classList.add('fi-disabled')
            }
        } else {
            // Remove disabled attribute and class from the select button
            this.selectButton.removeAttribute('disabled')
            this.selectButton.removeAttribute('aria-disabled')
            this.selectButton.classList.remove('fi-disabled')

            // If there are remove buttons in multiple mode, enable them
            if (this.isMultiple) {
                const removeButtons = this.container.querySelectorAll(
                    '.fi-select-input-badge-remove',
                )
                removeButtons.forEach((button) => {
                    button.removeAttribute('disabled')
                    button.classList.remove('fi-disabled')
                })
            }

            // If there's a remove button in single mode, enable it
            if (!this.isMultiple && this.canSelectPlaceholder) {
                const removeButton = this.container.querySelector(
                    '.fi-select-input-value-remove-btn',
                )
                if (removeButton) {
                    removeButton.removeAttribute('disabled')
                    removeButton.classList.add('fi-disabled')
                }
            }

            // If there's a search input, enable it
            if (this.isSearchable && this.searchInput) {
                this.searchInput.removeAttribute('disabled')
                this.searchInput.classList.remove('fi-disabled')
            }
        }
    }

    destroy() {
        // Remove button click event listener
        if (this.selectButton && this.buttonClickListener) {
            this.selectButton.removeEventListener(
                'click',
                this.buttonClickListener,
            )
        }

        // Remove document click event listener
        if (this.documentClickListener) {
            document.removeEventListener('click', this.documentClickListener)
        }

        // Remove button keydown event listener
        if (this.selectButton && this.buttonKeydownListener) {
            this.selectButton.removeEventListener(
                'keydown',
                this.buttonKeydownListener,
            )
        }

        // Remove dropdown keydown event listener
        if (this.dropdown && this.dropdownKeydownListener) {
            this.dropdown.removeEventListener(
                'keydown',
                this.dropdownKeydownListener,
            )
        }

        // Remove resize event listener if it exists
        if (this.resizeListener) {
            window.removeEventListener('resize', this.resizeListener)
            this.resizeListener = null
        }

        // Remove scroll event listener if it exists
        if (this.scrollListener) {
            window.removeEventListener('scroll', this.scrollListener, true)
            this.scrollListener = null
        }

        // Remove the event listener for refreshing selected option labels if it was added
        if (this.refreshOptionLabelListener) {
            window.removeEventListener(
                'filament-forms::select.refreshSelectedOptionLabel',
                this.refreshOptionLabelListener,
            )
        }

        // Close dropdown if it's open
        if (this.isOpen) {
            this.closeDropdown()
        }

        // Clear any pending search timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout)
            this.searchTimeout = null
        }

        // Remove the container element from the DOM
        if (this.container) {
            this.container.remove()
        }
    }
}
