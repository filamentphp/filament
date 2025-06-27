export default ({
    canSelectMultipleRecords,
    canTrackDeselectedRecords,
    currentSelectionLivewireProperty,
    areAllGroupsCollapsed = false,
    $wire,
}) => ({
    checkboxClickController: null,

    collapsedGroups: areAllGroupsCollapsed ? [] : [],

    isLoading: false,

    selectedRecords: new Set(),

    deselectedRecords: new Set(),

    isTrackingDeselectedRecords: false,

    shouldCheckUniqueSelection: true,

    lastCheckedRecord: null,

    livewireId: null,

    entangledSelectedRecords: currentSelectionLivewireProperty
        ? $wire.$entangle(currentSelectionLivewireProperty)
        : null,

    init() {
        this.livewireId =
            this.$root.closest('[wire\\:id]').attributes['wire:id'].value

        $wire.$on('deselectAllTableRecords', () => this.deselectAllRecords())

        if (currentSelectionLivewireProperty) {
            if (canSelectMultipleRecords) {
                this.selectedRecords = new Set(this.entangledSelectedRecords)
            } else {
                this.selectedRecords = new Set(
                    this.entangledSelectedRecords
                        ? [this.entangledSelectedRecords]
                        : [],
                )
            }
        }

        this.$nextTick(() => {
            this.watchForCheckboxClicks()
            
            if (areAllGroupsCollapsed) {
                this.collapsedGroups = this.getAllGroupTitles()
            }
        })

        Livewire.hook('element.init', ({ component }) => {
            if (component.id === this.livewireId) {
                this.watchForCheckboxClicks()
            }
        })
    },

    mountAction(...args) {
        $wire.set(
            'isTrackingDeselectedTableRecords',
            this.isTrackingDeselectedRecords,
            false,
        )
        $wire.set('selectedTableRecords', [...this.selectedRecords], false)
        $wire.set('deselectedTableRecords', [...this.deselectedRecords], false)

        $wire.mountAction(...args)
    },

    toggleSelectRecordsOnPage() {
        const keys = this.getRecordsOnPage()

        if (this.areRecordsSelected(keys)) {
            this.deselectRecords(keys)

            return
        }

        this.selectRecords(keys)
    },

    async toggleSelectRecordsInGroup(group) {
        this.isLoading = true

        if (this.areRecordsSelected(this.getRecordsInGroupOnPage(group))) {
            this.deselectRecords(
                await $wire.getGroupedSelectableTableRecordKeys(group),
            )
        } else {
            this.selectRecords(
                await $wire.getGroupedSelectableTableRecordKeys(group),
            )
        }

        this.isLoading = false
    },

    getRecordsInGroupOnPage(group) {
        const keys = []

        for (let checkbox of this.$root?.getElementsByClassName(
            'fi-ta-record-checkbox',
        ) ?? []) {
            if (checkbox.dataset.group !== group) {
                continue
            }

            keys.push(checkbox.value)
        }

        return keys
    },

    getSelectedRecordsCount() {
        if (this.isTrackingDeselectedRecords) {
            return (
                (this.$refs.allSelectableRecordsCount?.value ??
                    this.deselectedRecords.size) - this.deselectedRecords.size
            )
        }

        return this.selectedRecords.size
    },

    getRecordsOnPage() {
        const keys = []

        for (let checkbox of this.$root?.getElementsByClassName(
            'fi-ta-record-checkbox',
        ) ?? []) {
            keys.push(checkbox.value)
        }

        return keys
    },

    selectRecords(keys) {
        if (!canSelectMultipleRecords) {
            this.deselectAllRecords()

            keys = keys.slice(0, 1)
        }

        for (let key of keys) {
            if (this.isRecordSelected(key)) {
                continue
            }

            if (this.isTrackingDeselectedRecords) {
                this.deselectedRecords.delete(key)

                continue
            }

            this.selectedRecords.add(key)
        }

        this.updatedSelectedRecords()
    },

    deselectRecords(keys) {
        for (let key of keys) {
            if (this.isTrackingDeselectedRecords) {
                this.deselectedRecords.add(key)

                continue
            }

            this.selectedRecords.delete(key)
        }

        this.updatedSelectedRecords()
    },

    updatedSelectedRecords() {
        if (canSelectMultipleRecords) {
            this.entangledSelectedRecords = [...this.selectedRecords]

            return
        }

        this.entangledSelectedRecords = [...this.selectedRecords][0] ?? null
    },

    toggleSelectedRecord(key) {
        if (this.isRecordSelected(key)) {
            this.deselectRecords([key])

            return
        }

        this.selectRecords([key])
    },

    async selectAllRecords() {
        if (!canTrackDeselectedRecords) {
            this.isLoading = true

            this.selectedRecords = new Set(
                await $wire.getAllSelectableTableRecordKeys(),
            )

            this.updatedSelectedRecords()

            this.isLoading = false

            return
        }

        this.isTrackingDeselectedRecords = true
        this.selectedRecords = new Set()
        this.deselectedRecords = new Set()

        this.updatedSelectedRecords()
    },

    deselectAllRecords() {
        this.isTrackingDeselectedRecords = false
        this.selectedRecords = new Set()
        this.deselectedRecords = new Set()

        this.updatedSelectedRecords()
    },

    isRecordSelected(key) {
        if (this.isTrackingDeselectedRecords) {
            return !this.deselectedRecords.has(key)
        }

        return this.selectedRecords.has(key)
    },

    areRecordsSelected(keys) {
        return keys.every((key) => this.isRecordSelected(key))
    },

    toggleCollapseGroup(group) {
        if (this.isGroupCollapsed(group)) {
            this.collapsedGroups.splice(this.collapsedGroups.indexOf(group), 1)

            return
        }

        this.collapsedGroups.push(group)
    },

    isGroupCollapsed(group) {
        return this.collapsedGroups.includes(group)
    },

    resetCollapsedGroups() {
        if (areAllGroupsCollapsed) {
            this.collapsedGroups = this.getAllGroupTitles()
        } else {
            this.collapsedGroups = []
        }
    },

    getAllGroupTitles() {
        const groupTitles = []
        
        for (let groupHeader of this.$root?.getElementsByClassName('fi-ta-group-heading') ?? []) {
            const title = groupHeader.textContent?.trim()
            if (title) {
                groupTitles.push(title)
            }
        }
        
        return groupTitles
    },

    watchForCheckboxClicks() {
        if (this.checkboxClickController) {
            this.checkboxClickController.abort()
        }

        this.checkboxClickController = new AbortController()

        const { signal } = this.checkboxClickController

        this.$root?.addEventListener(
            'click',
            (event) =>
                event.target?.matches('.fi-ta-record-checkbox') &&
                this.handleCheckboxClick(event, event.target),
            { signal },
        )
    },

    handleCheckboxClick(event, checkbox) {
        if (!this.lastChecked) {
            this.lastChecked = checkbox

            return
        }

        if (event.shiftKey) {
            let checkboxes = Array.from(
                this.$root?.getElementsByClassName('fi-ta-record-checkbox') ??
                    [],
            )

            if (!checkboxes.includes(this.lastChecked)) {
                this.lastChecked = checkbox

                return
            }

            let start = checkboxes.indexOf(this.lastChecked)
            let end = checkboxes.indexOf(checkbox)

            let range = [start, end].sort((a, b) => a - b)
            let values = []

            for (let i = range[0]; i <= range[1]; i++) {
                checkboxes[i].checked = checkbox.checked

                values.push(checkboxes[i].value)
            }

            if (checkbox.checked) {
                this.selectRecords(values)
            } else {
                this.deselectRecords(values)
            }
        }

        this.lastChecked = checkbox
    },
})
