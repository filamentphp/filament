export default function table() {
    return {
        checkboxClickController: null,

        collapsedGroups: [],

        isLoading: false,

        selectedRecords: [],

        shouldCheckUniqueSelection: true,

        lastCheckedRecord: null,

        livewireId: null,

        init: function () {
            this.livewireId =
                this.$root.closest('[wire\\:id]').attributes['wire:id'].value

            this.$wire.$on('deselectAllTableRecords', () =>
                this.deselectAllRecords(),
            )

            this.$watch('selectedRecords', () => {
                if (!this.shouldCheckUniqueSelection) {
                    this.shouldCheckUniqueSelection = true

                    return
                }

                this.selectedRecords = [...new Set(this.selectedRecords)]

                this.shouldCheckUniqueSelection = false
            })

            this.$nextTick(() => this.watchForCheckboxClicks())

            Livewire.hook('element.init', ({ component }) => {
                if (component.id === this.livewireId) {
                    this.watchForCheckboxClicks()
                }
            })
        },

        mountAction: function (name, record = null) {
            this.$wire.set('selectedTableRecords', this.selectedRecords, false)
            this.$wire.mountTableAction(name, record)
        },

        mountBulkAction: function (name) {
            this.$wire.set('selectedTableRecords', this.selectedRecords, false)
            this.$wire.mountTableBulkAction(name)
        },

        toggleSelectRecordsOnPage: function () {
            const keys = this.getRecordsOnPage()

            if (this.areRecordsSelected(keys)) {
                this.deselectRecords(keys)

                return
            }

            this.selectRecords(keys)
        },

        toggleSelectRecordsInGroup: async function (group) {
            this.isLoading = true

            if (this.areRecordsSelected(this.getRecordsInGroupOnPage(group))) {
                this.deselectRecords(
                    await this.$wire.getGroupedSelectableTableRecordKeys(group),
                )

                return
            }

            this.selectRecords(
                await this.$wire.getGroupedSelectableTableRecordKeys(group),
            )

            this.isLoading = false
        },

        getRecordsInGroupOnPage: function (group) {
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

        getRecordsOnPage: function () {
            const keys = []

            for (let checkbox of this.$root?.getElementsByClassName(
                'fi-ta-record-checkbox',
            ) ?? []) {
                keys.push(checkbox.value)
            }

            return keys
        },

        selectRecords: function (keys) {
            for (let key of keys) {
                if (this.isRecordSelected(key)) {
                    continue
                }

                this.selectedRecords.push(key)
            }
        },

        deselectRecords: function (keys) {
            for (let key of keys) {
                let index = this.selectedRecords.indexOf(key)

                if (index === -1) {
                    continue
                }

                this.selectedRecords.splice(index, 1)
            }
        },

        selectAllRecords: async function () {
            this.isLoading = true

            this.selectedRecords =
                await this.$wire.getAllSelectableTableRecordKeys()

            this.isLoading = false
        },

        deselectAllRecords: function () {
            this.selectedRecords = []
        },

        isRecordSelected: function (key) {
            return this.selectedRecords.includes(key)
        },

        areRecordsSelected: function (keys) {
            return keys.every((key) => this.isRecordSelected(key))
        },

        toggleCollapseGroup: function (group) {
            if (this.isGroupCollapsed(group)) {
                this.collapsedGroups.splice(
                    this.collapsedGroups.indexOf(group),
                    1,
                )

                return
            }

            this.collapsedGroups.push(group)
        },

        isGroupCollapsed: function (group) {
            return this.collapsedGroups.includes(group)
        },

        resetCollapsedGroups: function () {
            this.collapsedGroups = []
        },

        watchForCheckboxClicks: function () {
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

        handleCheckboxClick: function (event, checkbox) {
            if (!this.lastChecked) {
                this.lastChecked = checkbox

                return
            }

            if (event.shiftKey) {
                let checkboxes = Array.from(
                    this.$root?.getElementsByClassName(
                        'fi-ta-record-checkbox',
                    ) ?? [],
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
    }
}
