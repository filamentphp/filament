import { computePosition, flip, shift, offset } from '@floating-ui/dom'
import Sortable from 'sortablejs'

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
        accessibilityAttributes = {},
        ariaLabel = null,
        canOptionLabelsWrap = true,
        canSelectPlaceholder = true,
        clearButtonLabel = 'Clear selection',
        element,
        errorMessage = 'The options could not be loaded. Please try again.',
        getOptionLabelUsing = null,
        getOptionLabelsUsing = null,
        getOptionsUsing = null,
        getSearchResultsUsing = null,
        hasDynamicOptions = false,
        hasDynamicSearchResults = true,
        hasInitialNoOptionsMessage = false,
        id = null,
        initialOptionLabel = null,
        initialOptionLabels = null,
        initialState = null,
        isAutofocused = false,
        isDisabled = false,
        isHtmlAllowed = false,
        isMultiple = false,
        isReorderable = false,
        isSearchable = false,
        livewireId = null,
        loadingMessage = 'Loading...',
        maxItems = null,
        maxItemsMessage = 'Maximum number of items selected',
        noOptionsMessage = 'No options available',
        noSearchResultsMessage = 'No results found',
        onStateChange = () => {},
        options,
        optionsLimit = null,
        placeholder,
        position = null,
        removeButtonLabel = 'Remove :label',
        searchableOptionFields = ['label'],
        searchDebounce = 1000,
        searchingMessage = 'Searching...',
        searchLabel = 'Search',
        searchPrompt = 'Search...',
        state,
        statePath = null,
    }) {
        this.accessibilityAttributes = accessibilityAttributes
        this.ariaLabel = ariaLabel
        this.canOptionLabelsWrap = canOptionLabelsWrap
        this.canSelectPlaceholder = canSelectPlaceholder
        this.clearButtonLabel = clearButtonLabel
        this.element = element
        this.errorMessage = errorMessage
        this.getOptionLabelUsing = getOptionLabelUsing
        this.getOptionLabelsUsing = getOptionLabelsUsing
        this.getOptionsUsing = getOptionsUsing
        this.getSearchResultsUsing = getSearchResultsUsing
        this.hasDynamicOptions = hasDynamicOptions
        this.hasDynamicSearchResults =
            hasDynamicSearchResults &&
            typeof getSearchResultsUsing === 'function'
        this.hasInitialNoOptionsMessage = hasInitialNoOptionsMessage
        this.id = id
        this.initialOptionLabel = initialOptionLabel
        this.initialOptionLabels = initialOptionLabels
        this.initialState = initialState
        this.isAutofocused = isAutofocused
        this.isDisabled = isDisabled
        this.isHtmlAllowed = isHtmlAllowed
        this.isMultiple = isMultiple
        this.isReorderable = isReorderable
        this.isSearchable = isSearchable
        this.livewireId = livewireId
        this.loadingMessage = loadingMessage
        this.maxItems = maxItems
        this.maxItemsMessage = maxItemsMessage
        this.noOptionsMessage = noOptionsMessage
        this.noSearchResultsMessage = noSearchResultsMessage
        this.onStateChange = onStateChange
        this.options = options
        this.optionsLimit = optionsLimit
        this.originalOptions = JSON.parse(JSON.stringify(options))
        this.placeholder = placeholder
        this.position = position
        this.removeButtonLabel = removeButtonLabel
        this.searchableOptionFields = Array.isArray(searchableOptionFields)
            ? searchableOptionFields
            : ['label']
        this.searchDebounce = searchDebounce
        this.searchingMessage = searchingMessage
        this.searchLabel = searchLabel
        this.searchPrompt = searchPrompt
        this.state = state
        this.statePath = statePath

        // Invalidate work when its query, opening session, or instance expires.
        this.activeSearchId = 0
        this.openId = 0
        this.isDestroyed = false

        // Central repository for option labels
        this.labelRepository = {}

        this.isOpen = false
        this.activeValue = null
        this.typeahead = ''
        this.typeaheadTime = 0
        this.searchQuery = ''
        this.searchTimeout = null
        this.isSearching = false
        this.isLoadingOptions = false
        this.hasOptionsError = false
        this.maxItemsMessageElement = null
        this.badgesSortable = null
        // Version token to prevent race conditions when updating the selected display
        this.selectedDisplayVersion = 0

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

        // Create the button that toggles the dropdown
        this.selectButton = document.createElement('button')
        this.selectButton.className = 'fi-select-input-btn'
        this.selectButton.type = 'button'
        if (!this.isSearchable) {
            this.selectButton.setAttribute('role', 'combobox')
        }
        this.selectButton.setAttribute('aria-haspopup', 'listbox')
        this.selectButton.setAttribute('aria-expanded', 'false')

        // Associate the button with the field's `<label>`, or name it directly
        if (filled(this.id)) {
            this.selectButton.id = this.id
        }

        if (filled(this.ariaLabel)) {
            this.selectButton.setAttribute('aria-label', this.ariaLabel)
        }

        // Create the selected value display
        this.selectedDisplay = document.createElement('div')
        this.selectedDisplay.className = 'fi-select-input-value-ctn'

        if (this.isMultiple) {
            this.container.classList.add('fi-select-input-ctn-multiple')
            if (this.isReorderable) {
                this.container.classList.add('fi-select-input-ctn-reorderable')
            }
            this.selectionSummary = document.createElement('span')
            this.selectionSummary.className = 'fi-sr-only'
            this.selectButton.appendChild(this.selectionSummary)
        } else {
            this.selectButton.appendChild(this.selectedDisplay)
        }

        // Create the dropdown
        this.dropdown = document.createElement('div')
        this.dropdown.className = 'fi-dropdown-panel fi-scrollable'
        this.dropdown.style.display = 'none'

        // Generate a unique ID for the dropdown
        this.dropdownId = `fi-select-input-dropdown-${Math.random().toString(36).substring(2, 11)}`
        this.selectButton.setAttribute('aria-controls', this.dropdownId)
        if (!this.selectionSummary) {
            this.selectionSummary = document.createElement('span')
            this.selectionSummary.className = 'fi-sr-only'
            this.container.appendChild(this.selectionSummary)
        }
        this.selectionSummary.id = `${this.dropdownId}-value`

        // Add search input if searchable
        if (this.isSearchable) {
            this.searchContainer = document.createElement('div')
            this.searchContainer.className = 'fi-select-input-search-ctn'

            this.searchInput = document.createElement('input')
            this.searchInput.className = 'fi-input'
            this.searchInput.type = 'text'
            this.searchInput.placeholder = this.searchPrompt
            this.searchInput.setAttribute('role', 'combobox')
            this.searchInput.setAttribute('aria-autocomplete', 'list')
            this.searchInput.setAttribute('aria-expanded', 'false')
            this.searchInput.setAttribute('aria-controls', this.dropdownId)

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
        }

        // Create the options list
        this.optionsList = document.createElement('ul')
        this.optionsList.id = this.dropdownId
        this.optionsList.setAttribute('role', 'listbox')
        if (this.isMultiple) {
            this.optionsList.setAttribute('aria-multiselectable', 'true')
        }
        this.optionsList.addEventListener('mousedown', (event) =>
            event.preventDefault(),
        )

        // Create a visually hidden live region to announce loading / empty / limit messages,
        // before `renderOptions()` since it may announce a "no options" message
        this.statusRegion = document.createElement('div')
        this.statusRegion.className = 'fi-sr-only'
        this.statusRegion.setAttribute('role', 'status')
        this.statusRegion.setAttribute('aria-live', 'polite')
        this.statusRegion.setAttribute('aria-atomic', 'true')

        // Render options
        this.renderOptions()

        // Append everything to the container
        this.container.appendChild(this.selectButton)
        if (this.isMultiple) {
            this.container.appendChild(this.selectedDisplay)
        }
        this.container.appendChild(this.dropdown)
        this.container.appendChild(this.statusRegion)

        // Append the container to the element
        this.element.appendChild(this.container)

        this.setAccessibilityAttributes(this.accessibilityAttributes)
        this.updateSelectedDisplay()

        // Apply disabled state if needed
        this.applyDisabledState()
    }

    renderOptions() {
        // Selection-only updates must not revive candidates for an obsolete query.
        if (
            this.isSearching ||
            this.hasOptionsError ||
            (this.isLoadingOptions &&
                (!this.searchQuery || !this.hasDynamicSearchResults))
        )
            return

        const activeValue = this.activeValue
        this.setActiveOption(null)
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
            if (this.optionsLimit > 0 && renderedCount >= this.optionsLimit) {
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
                    if (this.optionsLimit > 0) {
                        const remainingSlots = this.optionsLimit - renderedCount
                        if (remainingSlots < groupOptions.length) {
                            groupOptions = groupOptions.slice(0, remainingSlots)
                        }
                    }

                    this.renderOptionGroup(option.label, groupOptions)
                    ungroupedList = null
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
                    ungroupedList.setAttribute('role', 'presentation')
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
            // Show a message if:
            // - There is an active search query (show "no search results" message), or
            // - The field has `hasInitialNoOptionsMessage` enabled (show "no options" message), or
            // - The field has dynamic options and no options were returned (show "no options" message)
            if (this.searchQuery) {
                this.showNoResultsMessage()
            } else if (
                this.hasInitialNoOptionsMessage ||
                this.hasDynamicOptions
            ) {
                this.showNoOptionsMessage()
            }

            this.optionsList.hidden = true
        } else {
            this.optionsList.hidden = false
            // Hide any existing messages (like "No results")
            this.hideLoadingState()

            // Append the options list to the dropdown if it's not already there
            if (this.optionsList.parentNode !== this.dropdown) {
                this.dropdown.appendChild(this.optionsList)
            }
        }

        // Keep the controlled element connected even when there are no candidates.
        if (this.optionsList.parentNode !== this.dropdown) {
            this.dropdown.appendChild(this.optionsList)
        }

        if (this.isOpen) {
            const options = this.getEnabledOptions()
            this.setActiveOption(
                options.find(
                    (option) => option.dataset.value === activeValue,
                ) ??
                    (activeValue !== null || !this.isSearchable
                        ? (options.find(
                              (option) =>
                                  this.typeahead &&
                                  option.textContent
                                      .trim()
                                      .toLowerCase()
                                      .startsWith(this.typeahead),
                          ) ??
                          options.find(
                              (option) => option.dataset.value === this.state,
                          ) ??
                          options[0])
                        : null),
            )
        }
    }

    renderOptionGroup(label, options) {
        // Don't render if there are no options
        if (options.length === 0) {
            return
        }

        const optionGroup = document.createElement('li')
        optionGroup.className = 'fi-select-input-option-group'
        optionGroup.setAttribute('role', 'presentation')

        const optionGroupLabel = document.createElement('div')
        optionGroupLabel.className = 'fi-dropdown-header'
        optionGroupLabel.textContent = label

        const groupOptionsList = document.createElement('ul')
        groupOptionsList.className = 'fi-dropdown-list'
        groupOptionsList.setAttribute('role', 'group')
        groupOptionsList.setAttribute('aria-label', label)

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
            })
        }

        return option
    }

    async updateSelectedDisplay() {
        if (this.isDestroyed) return
        // Increment version to invalidate any in-flight renders
        this.selectedDisplayVersion = this.selectedDisplayVersion + 1
        const renderVersion = this.selectedDisplayVersion

        // Stage all DOM updates in a fragment to avoid intermediate states
        const fragment = document.createDocumentFragment()

        if (this.isMultiple) {
            if (!Array.isArray(this.state) || this.state.length === 0) {
                const placeholderSpan = document.createElement('span')
                placeholderSpan.textContent = this.placeholder
                placeholderSpan.classList.add('fi-select-input-placeholder')
                fragment.appendChild(placeholderSpan)
            } else {
                let selectedLabels = await this.getLabelsForMultipleSelection()
                // Check version before committing
                if (renderVersion !== this.selectedDisplayVersion) return
                this.addBadgesForSelectedOptions(selectedLabels, fragment)
            }

            // Commit if still current
            if (renderVersion === this.selectedDisplayVersion) {
                if (
                    !fragment.querySelector('.fi-select-input-value-badges-ctn')
                ) {
                    this.destroyBadgesSortable()
                }

                this.commitSelectedDisplay(fragment)
                if (this.isOpen) {
                    this.positionDropdown()
                }
            }
            return
        }

        // Single selection
        if (this.state === null || this.state === '') {
            const placeholderSpan = document.createElement('span')
            placeholderSpan.textContent = this.placeholder
            placeholderSpan.classList.add('fi-select-input-placeholder')
            fragment.appendChild(placeholderSpan)

            if (renderVersion === this.selectedDisplayVersion) {
                this.destroyBadgesSortable()
                this.commitSelectedDisplay(fragment)

                // Remove the remove button since there's no selection
                const existingRemoveButton = this.container.querySelector(
                    '.fi-select-input-value-remove-btn',
                )
                if (existingRemoveButton) {
                    existingRemoveButton.remove()
                }
                this.container.classList.remove('fi-select-input-ctn-clearable')
            }
            return
        }

        const selectedLabel = await this.getLabelForSingleSelection()
        if (renderVersion !== this.selectedDisplayVersion) return

        this.addSingleSelectionDisplay(selectedLabel, fragment)

        if (renderVersion === this.selectedDisplayVersion) {
            this.destroyBadgesSortable()
            this.commitSelectedDisplay(fragment)
        }
    }

    commitSelectedDisplay(fragment) {
        const focusedButton = this.selectedDisplay.contains(
            document.activeElement,
        )
            ? document.activeElement
            : null
        const focusedValue =
            focusedButton?.closest('[data-value]')?.dataset.value
        this.selectedDisplay.replaceChildren(fragment)
        this.updateSelectionSummary()
        if (focusedButton) {
            const target = this.isOpen
                ? this.getFocusOwner()
                : (this.selectedDisplay.querySelector(
                      `[data-value="${CSS.escape(focusedValue ?? '')}"] button`,
                  ) ?? this.selectButton)
            target.focus()
        }
    }

    updateSelectionSummary() {
        this.selectionSummary.textContent = Array.from(
            this.selectedDisplay.querySelectorAll(
                '.fi-badge-label, .fi-select-input-value-label, .fi-select-input-placeholder',
            ),
            (label) => label.textContent,
        ).join(', ')
    }

    // Helper method to get labels for multiple selection
    async getLabelsForMultipleSelection() {
        const renderVersion = this.selectedDisplayVersion
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
                missingValues.push(value.toString())
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
                if (
                    this.isDestroyed ||
                    renderVersion !== this.selectedDisplayVersion
                )
                    return []

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
            'fi-badge fi-size-md fi-color fi-color-primary fi-text-color-700 dark:fi-text-color-200'

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
        removeButton.setAttribute(
            'aria-label',
            this.removeButtonLabel.replace(
                ':label',
                this.isHtmlAllowed ? label.replace(/<[^>]*>/g, '') : label,
            ),
        )

        const removeButtonIcon = document.createElement('span')
        removeButtonIcon.className = 'fi-badge-delete-btn-icon'
        removeButtonIcon.setAttribute('aria-hidden', 'true')
        removeButton.appendChild(removeButtonIcon)

        if (this.isDisabled) {
            removeButton.setAttribute('disabled', 'disabled')
            removeButton.classList.add('fi-disabled')
        }

        removeButton.addEventListener('click', (event) => {
            event.stopPropagation() // Prevent dropdown from toggling
            if (filled(value)) {
                const buttons = Array.from(
                    this.selectedDisplay.querySelectorAll(
                        '.fi-badge-delete-btn',
                    ),
                )
                const index = buttons.indexOf(removeButton)
                const focusTarget = this.isOpen
                    ? this.getFocusOwner()
                    : (buttons[index + 1] ??
                      buttons[index - 1] ??
                      this.selectButton)
                this.selectOption(value) // This will remove the value since it's already selected
                if (!this.isDisabled) focusTarget.focus()
            }
        })

        // Add keydown event listener to handle space key
        removeButton.addEventListener('keydown', (event) => {
            if (event.key === ' ' || event.key === 'Enter') {
                event.preventDefault()
                event.stopPropagation() // Prevent event from bubbling up to selectButton
                removeButton.click()
            }
        })

        return removeButton
    }

    // Helper method to add badges for selected options
    addBadgesForSelectedOptions(selectedLabels, target = this.selectedDisplay) {
        // Create a container for the badges
        const badgesContainer = document.createElement('div')
        badgesContainer.className = 'fi-select-input-value-badges-ctn'

        // Add badges for each selected option
        selectedLabels.forEach((label, index) => {
            const value = Array.isArray(this.state) ? this.state[index] : null
            const badge = this.createBadgeElement(value, label)
            badgesContainer.appendChild(badge)
        })

        target.appendChild(badgesContainer)

        if (this.isReorderable) {
            badgesContainer.addEventListener('click', (event) => {
                event.stopPropagation()
            })

            badgesContainer.addEventListener('mousedown', (event) => {
                event.stopPropagation()
            })

            this.destroyBadgesSortable()

            this.badgesSortable = new Sortable(badgesContainer, {
                animation: 150,
                disabled: this.isDisabled,
                onEnd: () => {
                    const newState = []

                    badgesContainer
                        .querySelectorAll('[data-value]')
                        .forEach((badge) => {
                            newState.push(badge.getAttribute('data-value'))
                        })

                    this.state = newState
                    this.updateSelectionSummary()
                    this.onStateChange(this.state)
                },
            })
        }
    }

    destroyBadgesSortable() {
        this.badgesSortable?.destroy()
        this.badgesSortable = null
    }

    // Helper method to get label for single selection
    async getLabelForSingleSelection() {
        const value = this.state
        const renderVersion = this.selectedDisplayVersion
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
                if (
                    this.isDestroyed ||
                    renderVersion !== this.selectedDisplayVersion ||
                    value !== this.state
                )
                    return null

                // Store the fetched label in the repository
                if (filled(selectedLabel) && filled(this.state)) {
                    this.labelRepository[value] = selectedLabel
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
    addSingleSelectionDisplay(selectedLabel, target = this.selectedDisplay) {
        // Create a container for the label
        const labelContainer = document.createElement('span')
        labelContainer.className = 'fi-select-input-value-label'

        if (this.isHtmlAllowed) {
            labelContainer.innerHTML = selectedLabel
        } else {
            labelContainer.textContent = selectedLabel
        }

        target.appendChild(labelContainer)

        // Add a cross button to clear the selection if canSelectPlaceholder is true
        if (!this.canSelectPlaceholder) {
            return
        }

        // Only add the remove button if one doesn't already exist
        if (this.container.querySelector('.fi-select-input-value-remove-btn')) {
            return
        }

        const removeButton = document.createElement('button')
        removeButton.type = 'button'
        removeButton.className = 'fi-select-input-value-remove-btn'
        removeButton.setAttribute('aria-label', this.clearButtonLabel)

        if (this.isDisabled) {
            removeButton.setAttribute('disabled', 'disabled')
            removeButton.classList.add('fi-disabled')
        }

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

        this.container.appendChild(removeButton)
        this.container.classList.add('fi-select-input-ctn-clearable')
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

            this.handleDropdownKeydown(event)
        }

        this.focusinListener = () => this.syncActiveDescendant()
        this.focusoutListener = () => {
            this.selectButton.removeAttribute('aria-activedescendant')
            this.searchInput?.removeAttribute('aria-activedescendant')
            queueMicrotask(() => {
                if (this.isDestroyed || !this.isOpen) return
                if (
                    this.isTabbing ||
                    !this.container.contains(document.activeElement)
                ) {
                    this.isTabbing = false
                    this.closeDropdown()
                }
            })
        }

        this.dropdownEscapeListener = (event) => {
            if (!this.isOpen) {
                return
            }

            this.closeDropdown()
            this.selectButton.focus()
            event.preventDefault()
            event.stopPropagation()
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
        this.container.addEventListener('focusin', this.focusinListener)
        this.container.addEventListener('focusout', this.focusoutListener)
        this.element.addEventListener(
            'dropdown-escape',
            this.dropdownEscapeListener,
        )

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
                            const value = this.state
                            const renderVersion = ++this.selectedDisplayVersion
                            delete this.labelRepository[this.state]

                            // Get the new label
                            const newLabel = await this.getOptionLabelUsing()
                            if (
                                this.isDestroyed ||
                                value !== this.state ||
                                renderVersion !== this.selectedDisplayVersion
                            )
                                return

                            this.updateOptionLabelInList(this.state, newLabel)
                            this.updateSelectedDisplay()
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
        if (event.isComposing) return

        if (
            (!this.isOpen || this.isSearchable) &&
            ['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(event.key)
        ) {
            event.preventDefault()
            event.stopPropagation()
            this.openDropdown()
            return
        }

        this.handleDropdownKeydown(event)
    }

    // Handle keyboard events within the dropdown
    handleDropdownKeydown(event) {
        if (event.isComposing) return

        const isEditing = event.target === this.searchInput
        const options = this.getEnabledOptions()

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
                if (isEditing) return
            // Fall through to commit a select-only candidate.
            case 'Enter':
                if (!this.isOpen) return
                event.preventDefault()
                event.stopPropagation()
                const option =
                    options.find(
                        (option) => option.dataset.value === this.activeValue,
                    ) ?? (isEditing ? options[0] : null)
                if (option) {
                    this.selectOption(option.dataset.value)
                }
                break
            case 'Escape':
                if (!this.isOpen) return
                event.preventDefault()
                event.stopPropagation()
                this.closeDropdown()
                this.selectButton.focus()
                break
            case 'Tab':
                this.isTabbing = this.isOpen
                break
            case 'Home':
            case 'End':
                if (isEditing || !this.isOpen) return
                event.preventDefault()
                event.stopPropagation()
                this.setActiveOption(
                    event.key === 'Home' ? options[0] : options.at(-1),
                )
                break
            default:
                if (
                    !isEditing &&
                    !event.ctrlKey &&
                    !event.metaKey &&
                    !event.altKey &&
                    typeof event.key === 'string' &&
                    event.key.length === 1
                ) {
                    event.preventDefault()
                    if (!this.isOpen) this.openDropdown()

                    if (this.isSearchable) {
                        this.searchInput.value =
                            (this.searchInput.value || '') + event.key
                        this.searchInput.dispatchEvent(
                            new Event('input', { bubbles: true }),
                        )
                    } else {
                        const now = Date.now()
                        this.typeahead =
                            (now - this.typeaheadTime < 1000
                                ? this.typeahead
                                : '') + event.key.toLowerCase()
                        this.typeaheadTime = now
                        const matchingOption = this.getEnabledOptions().find(
                            (option) =>
                                option.textContent
                                    .trim()
                                    .toLowerCase()
                                    .startsWith(this.typeahead),
                        )
                        if (matchingOption) {
                            this.setActiveOption(matchingOption)
                        }
                    }
                }
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

        // Open the dropdown
        this.openDropdown()
    }

    async openDropdown() {
        if (this.isDestroyed || this.isDisabled) return
        if (this.isOpen) {
            this.getFocusOwner().focus()
            return
        }

        const openId = ++this.openId
        const searchId = ++this.activeSearchId
        this.typeahead = ''
        this.hasOptionsError = false
        // Make dropdown visible but with position absolute by default, or fixed in containers with .fi-fixed-positioning-context class, and opacity 0 for measurement
        this.dropdown.style.display = 'block'
        this.dropdown.style.opacity = '0'

        // Check if the select is inside a container that opts in to fixed positioning
        const useFixedPositioning =
            this.selectButton.closest('.fi-fixed-positioning-context') !==
                null &&
            this.selectButton.closest('.fi-absolute-positioning-context') ===
                null
        this.dropdown.style.position = useFixedPositioning
            ? 'fixed'
            : 'absolute'
        // Set width immediately to match the select button
        this.dropdown.style.width = `${this.selectButton.offsetWidth}px`
        this.selectButton.setAttribute('aria-expanded', 'true')
        this.searchInput?.setAttribute('aria-expanded', 'true')
        this.isOpen = true
        this.isTabbing = false
        this.getFocusOwner().focus()

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

        // On every fresh open, clear any previous search query so reopening starts clean.
        if (this.isSearchable && this.searchInput) {
            this.searchInput.value = ''
            this.searchQuery = ''
        }

        // Restore static candidates and empty messages on every fresh open.
        if (!this.hasDynamicOptions) {
            this.options = JSON.parse(JSON.stringify(this.originalOptions))
            this.renderOptions()
        }

        // If hasDynamicOptions is true, fetch options
        if (this.hasDynamicOptions && this.getOptionsUsing) {
            // Show loading message
            this.isLoadingOptions = true
            this.showLoadingState(false)

            try {
                // Fetch options
                const fetchedOptions = await this.getOptionsUsing()
                if (this.isDestroyed || !this.isOpen || openId !== this.openId)
                    return
                this.isLoadingOptions = false

                // Normalize fetched options to an array
                const normalizedFetched = Array.isArray(fetchedOptions)
                    ? fetchedOptions
                    : fetchedOptions && Array.isArray(fetchedOptions.options)
                      ? fetchedOptions.options
                      : []

                this.originalOptions = JSON.parse(
                    JSON.stringify(normalizedFetched),
                )

                // A newer remote query owns its candidates and their labels.
                if (!this.searchQuery || !this.hasDynamicSearchResults) {
                    this.populateLabelRepositoryFromOptions(normalizedFetched)
                    if (this.searchQuery) {
                        this.filterOptions(this.searchQuery)
                    } else {
                        this.options = normalizedFetched
                        this.renderOptions()
                        this.positionDropdown()
                    }
                }
            } catch (error) {
                if (this.isDestroyed || !this.isOpen || openId !== this.openId)
                    return
                this.isLoadingOptions = false
                if (
                    this.searchQuery &&
                    this.hasDynamicSearchResults &&
                    searchId !== this.activeSearchId
                )
                    return
                console.error('Error fetching options:', error)
                this.showErrorMessage()
            }
        } else if (!this.hasInitialNoOptionsMessage || this.searchQuery) {
            this.hideLoadingState()
        }
    }

    positionDropdown() {
        if (this.isDestroyed || !this.isOpen) return
        const openId = this.openId
        const placement = this.position === 'top' ? 'top-start' : 'bottom-start'
        const middleware = [
            offset(4), // Add some space between button and dropdown
            shift({ padding: 5 }), // Keep within viewport with some padding
        ]

        // Only use flip middleware if position is not explicitly set to 'top' or 'bottom'
        if (this.position !== 'top' && this.position !== 'bottom') {
            middleware.push(flip()) // Flip to top if not enough space at bottom
        }

        // Check if the select is inside a container that opts in to fixed positioning
        const useFixedPositioning =
            this.selectButton.closest('.fi-fixed-positioning-context') !==
                null &&
            this.selectButton.closest('.fi-absolute-positioning-context') ===
                null

        computePosition(this.selectButton, this.dropdown, {
            placement: placement,
            middleware: middleware,
            strategy: useFixedPositioning ? 'fixed' : 'absolute',
        }).then(({ x, y }) => {
            if (this.isDestroyed || !this.isOpen || openId !== this.openId)
                return
            Object.assign(this.dropdown.style, {
                left: `${x}px`,
                top: `${y}px`,
            })
        })
    }

    closeDropdown() {
        this.dropdown.style.display = 'none'
        this.selectButton.setAttribute('aria-expanded', 'false')
        this.searchInput?.setAttribute('aria-expanded', 'false')
        this.isOpen = false
        this.openId++

        // Cancel any pending debounced search
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout)
            this.searchTimeout = null
        }

        // Invalidate any in-flight async searches and reset searching state
        this.activeSearchId++
        this.isSearching = false
        this.isLoadingOptions = false

        // Remove any loading / no-results messages
        this.hideLoadingState()
        this.hideMaxItemsMessage()

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

        this.setActiveOption(null)
    }

    focusNextOption() {
        const options = this.getEnabledOptions()
        if (options.length === 0) return
        const index = options.findIndex(
            (option) => option.dataset.value === this.activeValue,
        )
        this.setActiveOption(options[Math.min(index + 1, options.length - 1)])
    }

    focusPreviousOption() {
        const options = this.getEnabledOptions()
        if (options.length === 0) return
        const index = options.findIndex(
            (option) => option.dataset.value === this.activeValue,
        )
        this.setActiveOption(
            options[index < 0 ? options.length - 1 : Math.max(index - 1, 0)],
        )
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
        return Array.from(this.optionsList.querySelectorAll('[role="option"]'))
    }

    getEnabledOptions() {
        if (
            this.isSearching ||
            this.optionsList.hidden ||
            !this.optionsList.isConnected
        )
            return []
        return this.getVisibleOptions().filter(
            (option) => option.getAttribute('aria-disabled') !== 'true',
        )
    }

    getFocusOwner() {
        return this.searchInput ?? this.selectButton
    }

    setActiveOption(option) {
        this.activeValue = option?.dataset.value ?? null
        this.getVisibleOptions().forEach((candidate) =>
            candidate.classList.toggle('fi-selected', candidate === option),
        )
        this.syncActiveDescendant()
        if (option && this.isOpen) this.scrollOptionIntoView(option)
    }

    syncActiveDescendant() {
        this.selectButton.removeAttribute('aria-activedescendant')
        this.searchInput?.removeAttribute('aria-activedescendant')
        const owner = this.getFocusOwner()
        if (!this.isOpen || document.activeElement !== owner) return
        const option = this.getEnabledOptions().find(
            (option) => option.dataset.value === this.activeValue,
        )
        if (option) owner.setAttribute('aria-activedescendant', option.id)
    }

    setAccessibilityAttributes(attributes) {
        this.accessibilityAttributes = attributes
        for (const name of [
            'aria-label',
            'aria-labelledby',
            'aria-describedby',
            'aria-invalid',
            'aria-busy',
            'aria-required',
        ]) {
            const value = attributes[name]
            for (const control of [this.selectButton, this.searchInput].filter(
                Boolean,
            )) {
                if (filled(value)) control.setAttribute(name, value)
                else control.removeAttribute(name)
            }
        }

        if (
            !this.selectButton.hasAttribute('aria-label') &&
            !this.selectButton.hasAttribute('aria-labelledby') &&
            filled(this.ariaLabel)
        ) {
            this.selectButton.setAttribute('aria-label', this.ariaLabel)
        }
        const label =
            (this.selectButton.getAttribute('aria-labelledby') ?? '')
                .split(/\s+/)
                .map((id) => document.getElementById(id)?.textContent ?? '')
                .join(' ')
                .trim() ||
            this.selectButton.getAttribute('aria-label') ||
            Array.from(
                this.selectButton.labels ?? [],
                (label) => label.textContent,
            )
                .join(' ')
                .trim()
        this.optionsList.setAttribute('aria-label', label)
        if (this.isSearchable) {
            this.searchInput.removeAttribute('aria-labelledby')
            this.searchInput.setAttribute(
                'aria-label',
                `${label}: ${this.searchLabel}`,
            )
            this.selectButton.removeAttribute('aria-required')
        }
        const descriptions = [
            attributes['aria-describedby'],
            this.selectionSummary.id,
        ]
            .filter(Boolean)
            .join(' ')
            .split(/\s+/)
        this.selectButton.setAttribute(
            'aria-describedby',
            [...new Set(descriptions)].join(' '),
        )
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
                    break
                }
            }

            // If not found, don't add a fallback
            // This allows the caller to know which labels are missing
        }

        return labels
    }

    handleSearch(event) {
        if (this.isDestroyed || !this.isOpen) return
        const query = event.target.value.trim()
        this.searchQuery = query
        this.hasOptionsError = false
        const searchId = ++this.activeSearchId
        this.setActiveOption(null)

        // Clear any existing timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout)
        }

        // If query is empty, restore original options and exit early
        if (query === '') {
            this.isSearching = false
            if (this.isLoadingOptions) {
                this.showLoadingState()
                return
            }
            this.hideLoadingState()
            this.options = JSON.parse(JSON.stringify(this.originalOptions))
            this.renderOptions()
            this.positionDropdown()
            return
        }

        // If we don't have dynamic search results or no search function, filter locally and exit early
        if (
            !this.getSearchResultsUsing ||
            typeof this.getSearchResultsUsing !== 'function' ||
            !this.hasDynamicSearchResults
        ) {
            this.isSearching = false
            this.filterOptions(query)
            return
        }

        // Old results are not candidates for the new query, including during debounce.
        this.isSearching = true
        this.showLoadingState(true)
        this.searchTimeout = setTimeout(async () => {
            // Clear the timeout handle immediately to avoid stale truthy checks
            this.searchTimeout = null

            try {
                // Get search results from backend
                const results = await this.getSearchResultsUsing(query)

                // If this search is no longer the latest or the dropdown is closed, ignore the results
                if (
                    this.isDestroyed ||
                    searchId !== this.activeSearchId ||
                    !this.isOpen
                ) {
                    return
                }

                // Normalize results to an array
                const normalizedResults = Array.isArray(results)
                    ? results
                    : results && Array.isArray(results.options)
                      ? results.options
                      : []

                // Update options with search results
                this.options = normalizedResults

                // Update the label repository with the search results
                this.populateLabelRepositoryFromOptions(normalizedResults)

                // Hide loading state and render options
                this.isSearching = false
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
                // If this search is obsolete, silence errors to avoid noisy logs on cancellation
                if (
                    !this.isDestroyed &&
                    this.isOpen &&
                    searchId === this.activeSearchId
                ) {
                    console.error('Error fetching search results:', error)

                    this.showErrorMessage()
                }
            } finally {
                if (searchId === this.activeSearchId) {
                    this.isSearching = false
                }
            }
        }, this.searchDebounce)
    }

    showLoadingState(isSearching = false) {
        this.setActiveOption(null)
        this.optionsList.hidden = true

        // Remove any existing message
        this.hideLoadingState()
        this.optionsList.setAttribute('aria-busy', 'true')

        // Add loading message
        const loadingItem = document.createElement('div')
        loadingItem.className = 'fi-select-input-message'
        loadingItem.textContent = isSearching
            ? this.searchingMessage
            : this.loadingMessage
        this.dropdown.appendChild(loadingItem)

        // Announce the message to screen readers, unless the dropdown is closed,
        // such as when rendering the initial options on page load
        if (this.isOpen) {
            this.statusRegion.textContent = loadingItem.textContent
        }

        this.positionDropdown()
    }

    hideLoadingState() {
        this.optionsList.removeAttribute('aria-busy')
        // Remove loading message
        const loadingItem = this.dropdown.querySelector(
            '.fi-select-input-message',
        )
        if (loadingItem) {
            loadingItem.remove()

            this.statusRegion.textContent = ''
        }
    }

    showNoOptionsMessage() {
        this.setActiveOption(null)
        this.optionsList.hidden = true

        // Remove any existing message
        this.hideLoadingState()

        // Add "No options" message
        const noOptionsItem = document.createElement('div')
        noOptionsItem.className = 'fi-select-input-message'
        noOptionsItem.textContent = this.noOptionsMessage
        this.dropdown.appendChild(noOptionsItem)

        // Announce the message to screen readers, unless the dropdown is closed,
        // such as when rendering the initial options on page load
        if (this.isOpen) {
            this.statusRegion.textContent = this.noOptionsMessage
        }
    }

    showNoResultsMessage() {
        this.setActiveOption(null)
        this.optionsList.hidden = true

        // Remove any existing message
        this.hideLoadingState()

        // Add "No results" message
        const noResultsItem = document.createElement('div')
        noResultsItem.className = 'fi-select-input-message'
        noResultsItem.textContent = this.noSearchResultsMessage
        this.dropdown.appendChild(noResultsItem)

        // Announce the message to screen readers, unless the dropdown is closed,
        // such as when rendering the initial options on page load
        if (this.isOpen) {
            this.statusRegion.textContent = this.noSearchResultsMessage
        }
    }

    showErrorMessage() {
        this.hasOptionsError = true
        this.showLoadingState()
        this.optionsList.removeAttribute('aria-busy')
        this.dropdown.querySelector('.fi-select-input-message').textContent =
            this.errorMessage
        this.statusRegion.textContent = this.errorMessage
        this.positionDropdown()
    }

    showMaxItemsMessage() {
        // Remove any existing message so it is re-inserted in the correct position
        this.hideMaxItemsMessage()

        this.maxItemsMessageElement = document.createElement('div')
        this.maxItemsMessageElement.className =
            'fi-select-input-max-items-message'
        this.maxItemsMessageElement.textContent = this.maxItemsMessage

        // Insert the message above the options list so it is visible without scrolling
        if (this.optionsList.parentNode === this.dropdown) {
            this.dropdown.insertBefore(
                this.maxItemsMessageElement,
                this.optionsList,
            )
        } else {
            this.dropdown.appendChild(this.maxItemsMessageElement)
        }

        // Announce the message to screen readers
        this.statusRegion.textContent = this.maxItemsMessage
    }

    hideMaxItemsMessage() {
        if (!this.maxItemsMessageElement) {
            return
        }

        this.maxItemsMessageElement.remove()
        this.maxItemsMessageElement = null

        // Clear the announcement so reaching the limit again re-announces the same text
        if (this.statusRegion.textContent === this.maxItemsMessage) {
            this.statusRegion.textContent = ''
        }
    }

    filterOptions(query) {
        const searchInLabel = this.searchableOptionFields.includes('label')
        const searchInValue = this.searchableOptionFields.includes('value')

        query = query.toLowerCase()

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
            const shouldRestoreFocus = this.container.contains(
                document.activeElement,
            )
            // For single selection - simpler case, handle first
            this.state = value
            this.updateSelectedDisplay()
            this.renderOptions()
            this.closeDropdown()
            if (shouldRestoreFocus) this.selectButton.focus()
            this.onStateChange(this.state)
            return
        }

        // For multiple selection
        let newState = Array.isArray(this.state) ? [...this.state] : []

        // If already selected, remove the value
        if (newState.includes(value)) {
            this.state = newState.filter(
                (selectedValue) => selectedValue !== value,
            )
            this.updateSelectedDisplay()

            // An item was deselected, so any previous limit message is stale
            this.hideMaxItemsMessage()

            this.renderOptions()

            // Reevaluate dropdown position after options are removed
            if (this.isOpen) {
                this.positionDropdown()
            }

            this.maintainFocusInMultipleMode()
            this.updateSelectionSummary()
            this.onStateChange(this.state)
            return
        }

        // Check if maxItems limit has been reached
        if (this.maxItems && newState.length >= this.maxItems) {
            // Show and announce a message about reaching the limit, without a blocking `alert()`
            if (this.maxItemsMessage) {
                this.showMaxItemsMessage()
            }
            return // Don't add more items
        }

        // A new item can be selected, so any previous limit message is stale
        this.hideMaxItemsMessage()

        // Add the new value
        newState.push(value)
        this.state = newState

        this.updateSelectedDisplay()

        this.renderOptions()

        // Reevaluate dropdown position after options are added
        if (this.isOpen) {
            this.positionDropdown()
        }

        this.maintainFocusInMultipleMode()
        this.onStateChange(this.state)
    }

    // Helper method to maintain focus in multiple selection mode
    maintainFocusInMultipleMode() {
        if (this.isOpen && this.container.contains(document.activeElement)) {
            this.getFocusOwner().focus()
        }
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
        this.badgesSortable?.option('disabled', this.isDisabled)

        if (this.isDisabled) {
            // Add disabled attribute and class to the select button
            this.selectButton.setAttribute('disabled', 'disabled')
            this.selectButton.setAttribute('aria-disabled', 'true')
            this.selectButton.classList.add('fi-disabled')

            // If there are remove buttons in multiple mode, disable them
            if (this.isMultiple) {
                const removeButtons = this.container.querySelectorAll(
                    '.fi-badge-delete-btn',
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
                    '.fi-badge-delete-btn',
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
                    removeButton.classList.remove('fi-disabled')
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
        this.isDestroyed = true
        this.openId++
        this.activeSearchId++
        this.selectedDisplayVersion++
        this.destroyBadgesSortable()

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

        if (this.dropdownEscapeListener) {
            this.element.removeEventListener(
                'dropdown-escape',
                this.dropdownEscapeListener,
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

export function createSelectOptionRequestHandler() {
    let isDestroyed = false
    const pendingRequests = new Set()

    const request = (livewire, method, parameters, argumentsObject = {}) => {
        // A fresh arguments object identifies this invocation, even for identical queries.
        const requestParameters = [...parameters, { ...argumentsObject }]
        let unsubscribe = null
        let cancel

        return new Promise((resolve, reject) => {
            let isSettled = false

            const finish = (callback, value) => {
                if (isSettled) return
                isSettled = true
                unsubscribe?.()
                unsubscribe = null
                pendingRequests.delete(cancel)
                callback(value)
            }

            cancel = () =>
                finish(
                    reject,
                    new Error('Select option request was cancelled.'),
                )
            pendingRequests.add(cancel)

            if (isDestroyed) {
                cancel()
                return
            }

            unsubscribe = Livewire.hook(
                'commit',
                ({ component, commit, fail }) => {
                    if (
                        component.id !== livewire.$id ||
                        !commit.calls.some(
                            (call) =>
                                call.method === method &&
                                call.params.length ===
                                    requestParameters.length &&
                                call.params.every(
                                    (parameter, index) =>
                                        parameter === requestParameters[index],
                                ),
                        )
                    )
                        return

                    unsubscribe?.()
                    unsubscribe = null
                    fail(() =>
                        finish(
                            reject,
                            new Error('Select option request failed.'),
                        ),
                    )
                },
            )

            try {
                Promise.resolve(livewire[method](...requestParameters)).then(
                    (value) => finish(resolve, value),
                    (error) => finish(reject, error),
                )
            } catch (error) {
                finish(reject, error)
            }
        })
    }

    return {
        wrap: (callback) =>
            typeof callback === 'function'
                ? (...callbackArguments) =>
                      callback(...callbackArguments, request)
                : callback,
        destroy() {
            isDestroyed = true
            for (const cancel of pendingRequests) cancel()
        },
    }
}
