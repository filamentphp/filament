export default function filamentTableColumnManager({ columns, isLive }) {
    return {
        error: undefined,

        isLoading: false,

        deferredColumns: [],

        columns,

        isLive,

        hasReordered: false,

        reorderAnnouncement: '',

        focusRevision: 0,

        init() {
            if (!this.columns || this.columns.length === 0) {
                this.columns = []

                return
            }

            this.deferredColumns = JSON.parse(JSON.stringify(this.columns))

            this.$watch('columns', () => {
                this.resetDeferredColumns()
            })
        },

        get groupedColumns() {
            const groupedColumns = {}

            this.deferredColumns
                .filter((column) => column.type === 'group')
                .forEach((column) => {
                    groupedColumns[column.name] =
                        this.calculateGroupedColumns(column)
                })

            return groupedColumns
        },

        calculateGroupedColumns(group) {
            const visibleChildren =
                group?.columns?.filter((column) => !column.isHidden) ?? []

            if (visibleChildren.length === 0) {
                return {
                    hidden: true,
                    checked: false,
                    disabled: false,
                    indeterminate: false,
                }
            }

            const toggleableChildren = group.columns.filter(
                (column) => !column.isHidden && column.isToggleable !== false,
            )

            if (toggleableChildren.length === 0) {
                return { checked: true, disabled: true, indeterminate: false }
            }

            const toggledChildren = toggleableChildren.filter(
                (column) => column.isToggled,
            ).length
            const nonToggleableChildren = group.columns.filter(
                (column) => !column.isHidden && column.isToggleable === false,
            )

            if (toggledChildren === 0 && nonToggleableChildren.length > 0) {
                return { checked: true, disabled: false, indeterminate: true }
            }

            if (toggledChildren === 0) {
                return { checked: false, disabled: false, indeterminate: false }
            }

            if (toggledChildren === toggleableChildren.length) {
                return { checked: true, disabled: false, indeterminate: false }
            }

            return { checked: true, disabled: false, indeterminate: true }
        },

        getColumn(name, groupName = null) {
            if (groupName) {
                const group = this.deferredColumns.find(
                    (group) =>
                        group.type === 'group' && group.name === groupName,
                )

                return group?.columns?.find((column) => column.name === name)
            }

            return this.deferredColumns.find((column) => column.name === name)
        },

        toggleGroup(groupName) {
            const group = this.deferredColumns.find(
                (group) => group.type === 'group' && group.name === groupName,
            )

            if (!group?.columns) {
                return
            }

            const groupedColumns = this.calculateGroupedColumns(group)

            if (groupedColumns.disabled) {
                return
            }

            const toggleableChildren = group.columns.filter(
                (column) => column.isToggleable !== false,
            )
            const anyChildOn = toggleableChildren.some(
                (column) => column.isToggled,
            )
            const newValue = groupedColumns.indeterminate ? true : !anyChildOn

            group.columns
                .filter((column) => column.isToggleable !== false)
                .forEach((column) => {
                    column.isToggled = newValue
                })

            this.deferredColumns = [...this.deferredColumns]

            if (this.isLive) {
                this.applyTableColumnManager()
            }
        },

        toggleColumn(name, groupName = null) {
            const column = this.getColumn(name, groupName)

            if (!column || column.isToggleable === false) {
                return
            }

            column.isToggled = !column.isToggled
            this.deferredColumns = [...this.deferredColumns]

            if (this.isLive) {
                this.applyTableColumnManager()
            }
        },

        reorderColumns(sortedIds) {
            const newOrder = sortedIds.map((id) => id.split('::'))
            this.reorderTopLevel(newOrder)
            this.hasReordered = true

            if (this.isLive) {
                return this.applyTableColumnManager()
            }
        },

        reorderGroupColumns(sortedIds, groupName) {
            const group = this.deferredColumns.find(
                (column) =>
                    column.type === 'group' && column.name === groupName,
            )

            if (!group) {
                return
            }

            const newOrder = sortedIds.map((id) => id.split('::'))
            const reordered = []

            newOrder.forEach(([type, name]) => {
                const item = group.columns.find(
                    (column) => column.name === name,
                )

                if (item) {
                    reordered.push(item)
                }
            })

            group.columns = reordered
            this.deferredColumns = [...this.deferredColumns]
            this.hasReordered = true

            if (this.isLive) {
                return this.applyTableColumnManager()
            }
        },

        getVisibleColumns(groupName = null) {
            const columns =
                groupName === null
                    ? this.deferredColumns
                    : (this.deferredColumns.find(
                          (column) =>
                              column.type === 'group' &&
                              column.name === groupName,
                      )?.columns ?? [])

            return columns.filter((column) => !column.isHidden && column.label)
        },

        getColumnLabel(column) {
            const element = document.createElement('span')
            element.innerHTML = column.label

            return element.textContent
        },

        getColumnPosition(column, groupName = null) {
            return (
                this.getVisibleColumns(groupName).findIndex(
                    (item) =>
                        item.type === column.type && item.name === column.name,
                ) + 1
            )
        },

        canMoveColumn(column, direction, groupName = null) {
            const position = this.getColumnPosition(column, groupName)

            return (
                position > 0 &&
                (direction === 'up'
                    ? position > 1
                    : position < this.getVisibleColumns(groupName).length)
            )
        },

        async moveColumn(column, direction, groupName, button, announcement) {
            if (
                this.isLoading ||
                !this.canMoveColumn(column, direction, groupName)
            ) {
                return
            }

            const columns = this.getVisibleColumns(groupName)
            const index = this.getColumnPosition(column, groupName) - 1
            const nextIndex = index + (direction === 'up' ? -1 : 1)
            ;[columns[index], columns[nextIndex]] = [
                columns[nextIndex],
                columns[index],
            ]

            const hadFocus = document.activeElement === button
            const focusRevision = this.focusRevision
            const controls = button.parentElement
            const sortedIds = columns.map(
                (column) => `${column.type}::${column.name}`,
            )
            this.reorderAnnouncement = ''

            const applied =
                groupName === null
                    ? this.reorderColumns(sortedIds)
                    : this.reorderGroupColumns(sortedIds, groupName)

            this.$nextTick(() => {
                if (
                    !hadFocus ||
                    this.focusRevision !== focusRevision ||
                    !controls.getClientRects().length ||
                    (document.activeElement !== button &&
                        document.activeElement !== document.body)
                ) {
                    return
                }

                if (button.disabled) {
                    controls.querySelector('button:not(:disabled)')?.focus()
                } else {
                    button.focus()
                }
            })

            if (this.isLive && !(await applied)) {
                return
            }

            if (this.getColumnPosition(column, groupName) !== nextIndex + 1) {
                return
            }

            this.reorderAnnouncement = announcement
                .replace(':column', this.getColumnLabel(column))
                .replace(':position', nextIndex + 1)
                .replace(':count', columns.length)
                .replace(
                    ':group',
                    groupName === null
                        ? ''
                        : this.getColumnLabel({ label: groupName }),
                )
        },

        reorderTopLevel(newOrder) {
            const cloned = this.deferredColumns
            const reordered = []

            newOrder.forEach(([type, name]) => {
                const item = cloned.find((column) => {
                    if (type === 'group') {
                        return column.type === 'group' && column.name === name
                    } else if (type === 'column') {
                        return column.type !== 'group' && column.name === name
                    }
                    return false
                })

                if (item) {
                    reordered.push(item)
                }
            })

            this.deferredColumns = reordered
        },

        async applyTableColumnManager() {
            this.isLoading = true

            try {
                this.columns = JSON.parse(JSON.stringify(this.deferredColumns))

                await this.$wire.call(
                    'applyTableColumnManager',
                    this.columns,
                    this.hasReordered,
                )

                this.hasReordered = false
                this.error = undefined

                return true
            } catch (error) {
                this.error = 'Failed to update column visibility'

                console.error('Table toggle columns error:', error)

                return false
            } finally {
                this.isLoading = false
            }
        },

        resetDeferredColumns() {
            this.deferredColumns = JSON.parse(JSON.stringify(this.columns))
            this.hasReordered = false
            this.reorderAnnouncement = ''
        },
    }
}
