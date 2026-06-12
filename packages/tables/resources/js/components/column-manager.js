export default function filamentTableColumnManager({ columns, isLive }) {
    return {
        error: undefined,

        isLoading: false,

        deferredColumns: [],

        columns,

        isLive,

        hasReordered: false,

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
                this.applyTableColumnManager()
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
                this.applyTableColumnManager()
            }
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
            } catch (error) {
                this.error = 'Failed to update column visibility'

                console.error('Table toggle columns error:', error)
            } finally {
                this.isLoading = false
            }
        },

        resetDeferredColumns() {
            this.deferredColumns = JSON.parse(JSON.stringify(this.columns))
            this.hasReordered = false
        },
    }
}
