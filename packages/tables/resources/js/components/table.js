import { autoUpdate, computePosition, offset, shift } from '@floating-ui/dom'

export default ({
    areGroupsCollapsedByDefault,
    canTrackDeselectedRecords,
    currentSelectionLivewireProperty,
    maxSelectableRecords,
    reorderMessage,
    selectsCurrentPageOnly,
    $wire,
}) => ({
    areFiltersOpen: false,

    checkboxClickController: null,

    groupVisibility: [],

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

    cleanUpFiltersDropdown: null,

    livewireEventListenersController: null,

    unsubscribeLivewireHook: null,

    tableReorderOperation: null,

    tableReorderFrame: null,

    tableReorderRoot: null,

    init() {
        this.tableReorderRoot = this.$root
        this.livewireId =
            this.$root.closest('[wire\\:id]')?.attributes['wire:id'].value

        this.livewireEventListenersController = new AbortController()

        const { signal } = this.livewireEventListenersController

        $wire.$el.addEventListener(
            'deselectAllTableRecords',
            () => this.deselectAllRecords(),
            { signal },
        )
        $wire.$el.addEventListener(
            'scrollToTopOfTable',
            () =>
                this.$root.scrollIntoView({
                    block: 'start',
                    inline: 'nearest',
                }),
            { signal },
        )

        if (currentSelectionLivewireProperty) {
            if (maxSelectableRecords !== 1) {
                this.selectedRecords = new Set(this.entangledSelectedRecords)
            } else {
                this.selectedRecords = new Set(
                    this.entangledSelectedRecords
                        ? [this.entangledSelectedRecords]
                        : [],
                )
            }
        }

        this.$nextTick(() => this.watchForCheckboxClicks())

        this.unsubscribeLivewireHook = Livewire.hook(
            'element.init',
            ({ component }) => {
                if (component.id === this.livewireId) {
                    this.watchForCheckboxClicks()
                }
            },
        )
    },

    getTableReorderItems() {
        return Array.from(
            this.tableReorderRoot.querySelector('[x-ref="tableReorderRecords"]')
                ?.children ?? [],
        ).filter((element) => element.hasAttribute('x-sortable-item'))
    },

    beginTableReorder(recordKey, direction) {
        if (this.tableReorderOperation) return false

        this.tableReorderOperation = {
            recordKey,
            direction,
            before: this.getTableReorderItems().map((element) =>
                element.getAttribute('x-sortable-item'),
            ),
        }

        return true
    },

    startTableReorder(event) {
        if (event.target !== this.$refs.tableReorderRecords) return
        this.beginTableReorder(
            event.item.getAttribute('x-sortable-item'),
            'drag',
        )
    },

    async reorderTableRecords(event) {
        if (event.target !== this.$refs.tableReorderRecords) return
        if (event.oldDraggableIndex === event.newDraggableIndex) {
            this.tableReorderOperation = null
            return
        }

        await this.finishTableReorder(() =>
            $wire.reorderTable(
                event.target.sortable.toArray(),
                event.item.getAttribute('x-sortable-item'),
            ),
        )
    },

    async moveTableRecord(event, recordKey, direction) {
        if (
            event.currentTarget.disabled ||
            !this.beginTableReorder(recordKey, direction)
        )
            return
        await this.finishTableReorder(() =>
            $wire.moveTableRecord(recordKey, direction),
        )
    },

    async finishTableReorder(request) {
        const operation = this.tableReorderOperation
        if (!operation) return
        const records = this.tableReorderRoot.querySelector(
            '[x-ref="tableReorderRecords"]',
        )
        records?.sortable?.option('disabled', true)

        try {
            await request()
        } catch {
            if (
                this.tableReorderOperation !== operation ||
                !this.tableReorderRoot.isConnected
            )
                return
            // Recover the server order after a failed drag without announcing an unverified move.
            try {
                await $wire.$refresh()
            } catch {
                if (
                    this.tableReorderOperation !== operation ||
                    !this.tableReorderRoot.isConnected
                )
                    return

                this.tableReorderRoot
                    .querySelector('[x-ref="tableReorderRecords"]')
                    ?.sortable?.sort(operation.before)
            }
        }

        if (
            this.tableReorderOperation !== operation ||
            !this.tableReorderRoot.isConnected
        )
            return

        this.tableReorderFrame = requestAnimationFrame(() => {
            this.tableReorderFrame = null
            if (
                this.tableReorderOperation !== operation ||
                !this.tableReorderRoot.isConnected
            )
                return

            const items = this.getTableReorderItems()
            const after = items.map((element) =>
                element.getAttribute('x-sortable-item'),
            )
            const beforePeers = operation.before.filter((key) =>
                after.includes(key),
            )
            const afterPeers = after.filter((key) =>
                operation.before.includes(key),
            )
            const item = items.find(
                (element) =>
                    element.getAttribute('x-sortable-item') ===
                    operation.recordKey,
            )
            const moved =
                item &&
                beforePeers.indexOf(operation.recordKey) !==
                    afterPeers.indexOf(operation.recordKey)

            const status = this.tableReorderRoot.querySelector(
                '[x-ref="tableReorderStatus"]',
            )

            if (moved && status) {
                const label = item.querySelector('[data-table-reorder-label]')
                    ?.dataset.tableReorderLabel
                status.textContent = reorderMessage.replace(
                    /:label|:position|:count/g,
                    (placeholder) =>
                        ({
                            ':label': label,
                            ':position': String(
                                after.indexOf(operation.recordKey) + 1,
                            ),
                            ':count': String(after.length),
                        })[placeholder],
                )
            }

            this.tableReorderOperation = null
            this.tableReorderRoot
                .querySelector('[x-ref="tableReorderRecords"]')
                ?.sortable?.option('disabled', false)
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

    toggleSelectRecords(keys) {
        if (this.areRecordsSelected(keys)) {
            this.deselectRecords(keys)
        } else {
            this.selectRecords(keys)
        }
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
        if (maxSelectableRecords === 1) {
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
        if (maxSelectableRecords !== 1) {
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
        if (!canTrackDeselectedRecords || selectsCurrentPageOnly) {
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

    canSelectAllRecords() {
        if (selectsCurrentPageOnly) {
            const recordsOnPage = this.getRecordsOnPage()

            return (
                !this.areRecordsSelected(recordsOnPage) &&
                this.areRecordsToggleable(recordsOnPage)
            )
        }

        const allSelectableRecordsCount = parseInt(
            this.$refs.allSelectableRecordsCount?.value,
        )

        if (!allSelectableRecordsCount) {
            return false
        }

        const selectedRecordsCount = this.getSelectedRecordsCount()

        if (allSelectableRecordsCount === selectedRecordsCount) {
            return false
        }

        return (
            maxSelectableRecords === null ||
            allSelectableRecordsCount <= maxSelectableRecords
        )
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

    areRecordsPartiallySelected(keys) {
        if (keys.length === 0) {
            return false
        }

        const selectedCount = keys.filter((key) =>
            this.isRecordSelected(key),
        ).length

        return selectedCount > 0 && selectedCount < keys.length
    },

    areRecordsToggleable(keys) {
        if (maxSelectableRecords === null) {
            return true
        }

        if (maxSelectableRecords === 1) {
            return true
        }

        const selectedRecords = keys.filter((key) => this.isRecordSelected(key))

        if (selectedRecords.length === keys.length) {
            return true
        }

        return (
            this.getSelectedRecordsCount() +
                (keys.length - selectedRecords.length) <=
            maxSelectableRecords
        )
    },

    toggleCollapseGroup(group) {
        if (this.isGroupCollapsed(group)) {
            if (areGroupsCollapsedByDefault) {
                this.groupVisibility.push(group)
            } else {
                this.groupVisibility.splice(
                    this.groupVisibility.indexOf(group),
                    1,
                )
            }
        } else {
            if (areGroupsCollapsedByDefault) {
                this.groupVisibility.splice(
                    this.groupVisibility.indexOf(group),
                    1,
                )
            } else {
                this.groupVisibility.push(group)
            }
        }
    },

    isGroupCollapsed(group) {
        if (areGroupsCollapsedByDefault) {
            return !this.groupVisibility.includes(group)
        }

        return this.groupVisibility.includes(group)
    },

    resetCollapsedGroups() {
        this.groupVisibility = []
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
                values.push(checkboxes[i].value)
            }

            if (checkbox.checked) {
                if (!this.areRecordsToggleable(values)) {
                    checkbox.checked = false
                    this.deselectRecords([checkbox.value])

                    return
                }

                this.selectRecords(values)
            } else {
                this.deselectRecords(values)
            }
        }

        this.lastChecked = checkbox
    },

    toggleFiltersDropdown() {
        this.areFiltersOpen = !this.areFiltersOpen

        if (this.areFiltersOpen) {
            const trigger = this.$refs.filtersTriggerActionContainer
            const filters = this.$refs.filtersContentContainer
            let isPositioningActive = true

            const cleanUpAutoUpdate = autoUpdate(trigger, filters, async () => {
                const { x, y } = await computePosition(trigger, filters, {
                    placement: 'bottom-end',
                    middleware: [offset(8), shift({ padding: 8 })],
                })

                if (!isPositioningActive) {
                    return
                }

                Object.assign(filters.style, {
                    left: `${x}px`,
                    top: `${y}px`,
                })
            })

            const onClickAway = (event) => {
                if (
                    (filters && filters.contains(event.target)) ||
                    (trigger && trigger.contains(event.target))
                ) {
                    return
                }

                this.areFiltersOpen = false

                if (this.cleanUpFiltersDropdown) {
                    this.cleanUpFiltersDropdown()
                    this.cleanUpFiltersDropdown = null
                }
            }

            document.addEventListener('mousedown', onClickAway)
            document.addEventListener('touchstart', onClickAway, {
                passive: true,
            })
            const onKeydown = (event) => {
                if (event.key === 'Escape') {
                    onClickAway(event)
                }
            }
            document.addEventListener('keydown', onKeydown)

            this.cleanUpFiltersDropdown = () => {
                isPositioningActive = false
                cleanUpAutoUpdate()
                document.removeEventListener('mousedown', onClickAway)
                document.removeEventListener('touchstart', onClickAway, {
                    passive: true,
                })
                document.removeEventListener('keydown', onKeydown)
            }
        } else if (this.cleanUpFiltersDropdown) {
            this.cleanUpFiltersDropdown()
            this.cleanUpFiltersDropdown = null
        }
    },

    destroy() {
        this.tableReorderOperation = null
        cancelAnimationFrame(this.tableReorderFrame)
        this.cleanUpFiltersDropdown?.()
        this.livewireEventListenersController?.abort()
        this.unsubscribeLivewireHook?.()
    },
})

export const tableFilters = () => ({
    focusFrame: null,

    actionFocusFrame: null,

    actionRevision: 0,

    removeActionCommitHook: null,

    dropdownPanel: null,

    dropdownOpenedListener: null,

    init() {
        this.dropdownPanel = this.$el.closest('.fi-dropdown-panel')
        this.dropdownOpenedListener = (event) => {
            this.actionRevision++

            if (!event.detail.shouldAutofocus) {
                return
            }

            this.focusFirstControl()
            this.$el.focus()
        }

        this.dropdownPanel?.addEventListener(
            'dropdown-opened',
            this.dropdownOpenedListener,
        )
    },

    handleActionClick(event) {
        const action = event.target.closest('[data-table-filter-action]')

        if (action === null) {
            return
        }

        const actionName = action.dataset.tableFilterAction
        const method = action.getAttribute('wire:click')?.split('(')[0]
        const shouldCloseDropdown =
            actionName === 'apply' && this.dropdownPanel !== null

        if (!method || (!shouldCloseDropdown && event.detail !== 0)) {
            return
        }

        const actionRevision = ++this.actionRevision

        this.removeActionCommitHook?.()
        this.removeActionCommitHook = Livewire.interceptMessage(
            ({ message, onSuccess }) => {
                if (
                    message.component.id !== this.$wire.$id ||
                    !message
                        .getActions()
                        .some((action) => action.name === method)
                ) {
                    return
                }

                this.removeActionCommitHook?.()
                this.removeActionCommitHook = null

                onSuccess(({ payload, onMorphed }) => {
                    if (
                        Object.keys(payload.snapshot.memo.errors ?? {}).some(
                            (field) =>
                                field === 'tableDeferredFilters' ||
                                field.startsWith('tableDeferredFilters.'),
                        )
                    ) {
                        return
                    }

                    onMorphed(() => {
                        this.actionFocusFrame = requestAnimationFrame(() => {
                            this.actionFocusFrame = null

                            if (
                                message.isCancelled() ||
                                this.actionRevision !== actionRevision ||
                                !this.$el.isConnected ||
                                this.dropdownPanel?.style.display !== 'block'
                            ) {
                                return
                            }

                            if (shouldCloseDropdown) {
                                this.closeDropdown()

                                return
                            }

                            if (document.activeElement !== document.body) {
                                return
                            }

                            this.$el
                                .querySelector(
                                    `[data-table-filter-action="${actionName}"]`,
                                )
                                ?.focus()
                        })
                    })
                })
            },
        )
    },

    closeDropdown() {
        const dropdown = window.Alpine.$data(
            this.dropdownPanel.closest('.fi-dropdown'),
        )

        const shouldFocusTrigger =
            document.activeElement === document.body ||
            this.dropdownPanel.contains(document.activeElement)

        dropdown?.close()

        if (shouldFocusTrigger) {
            dropdown?.getTrigger()?.focus()
        }
    },

    focusFirstControl() {
        if (this.focusFrame !== null) {
            cancelAnimationFrame(this.focusFrame)
        }

        this.focusFrame = requestAnimationFrame(() => {
            this.focusFrame = null

            if (
                !this.$el.isConnected ||
                this.$el.closest('.fi-dropdown-panel')?.style.display !==
                    'block' ||
                document.activeElement !== this.$el
            ) {
                return
            }

            const control = Array.from(
                this.$el.querySelectorAll(
                    'button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [href], [tabindex]:not([tabindex="-1"])',
                ),
            ).find(
                (element) =>
                    !element.matches(':disabled') &&
                    !element.closest(
                        '.fi-ta-filters-header, .fi-ta-filters-actions-ctn, [x-load][x-ignore], fieldset[disabled]',
                    ) &&
                    element.getClientRects().length > 0 &&
                    getComputedStyle(element).visibility !== 'hidden',
            )

            control?.focus()
        })
    },

    destroy() {
        this.actionRevision++

        if (this.actionFocusFrame !== null) {
            cancelAnimationFrame(this.actionFocusFrame)
            this.actionFocusFrame = null
        }

        if (this.focusFrame !== null) {
            cancelAnimationFrame(this.focusFrame)
            this.focusFrame = null
        }

        this.removeActionCommitHook?.()
        this.removeActionCommitHook = null

        this.dropdownPanel?.removeEventListener(
            'dropdown-opened',
            this.dropdownOpenedListener,
        )
    },
})
