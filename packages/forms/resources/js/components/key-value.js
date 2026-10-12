export default function keyValueFormComponent({ state, reordering = {} }) {
    return {
        state,

        rows: [],

        nextRowId: 0,

        init() {
            this.updateRows()

            if (this.rows.length <= 0) {
                this.addRow()
            } else {
                this.updateState()
            }

            this.$watch('state', (state, oldState) => {
                if (!Array.isArray(state)) {
                    return
                }

                // Local state echoes must not rebuild rows or relocate blank drafts.
                if (JSON.stringify(state) === JSON.stringify(this.getState())) {
                    return
                }

                const getLength = (value) => {
                    if (value === null) {
                        return 0
                    }

                    if (Array.isArray(value)) {
                        return value.length
                    }

                    if (typeof value !== 'object') {
                        return 0
                    }

                    return Object.keys(value).length
                }

                if (getLength(state) === 0 && getLength(oldState) === 0) {
                    return
                }

                this.updateRows()
            })
        },

        addRow() {
            this.rows.push({ id: this.nextRowId++, key: '', value: '' })

            this.updateState()
        },

        deleteRow(index) {
            this.rows.splice(index, 1)

            if (this.rows.length <= 0) {
                this.addRow()
            }

            this.updateState()
        },

        reorderRows(event) {
            if (event.oldDraggableIndex === event.newDraggableIndex) {
                return
            }

            this.moveRow(
                this.rows[event.oldDraggableIndex].id,
                event.newDraggableIndex,
            )
        },

        moveRow(id, position, focusedElement = document.activeElement) {
            const index = this.rows.findIndex((row) => row.id === id)

            if (
                index < 0 ||
                position < 0 ||
                position >= this.rows.length ||
                index === position
            ) {
                return
            }

            const [row] = this.rows.splice(index, 1)
            this.rows.splice(position, 0, row)
            this.updateState()

            if (!reordering.moved) {
                return
            }

            this.$refs.reorderStatus.textContent = ''
            this.$nextTick(() => {
                if (!this.$root.isConnected || !this.rows.includes(row)) {
                    return
                }

                this.$refs.reorderStatus.textContent = this.rowDescription(
                    row,
                    this.rows.indexOf(row),
                    reordering.moved,
                )

                if (
                    [document.body, focusedElement].includes(
                        document.activeElement,
                    ) &&
                    focusedElement.isConnected &&
                    this.$root.contains(focusedElement) &&
                    !focusedElement.disabled &&
                    !focusedElement.hasAttribute('data-reorder-hidden')
                ) {
                    focusedElement.focus({ preventScroll: true })
                }
            })
        },

        rowDescription(row, index, message = reordering.position) {
            return message.replace(
                /:label|:position|:count/g,
                (placeholder) => {
                    return {
                        ':label':
                            String(row.key ?? '').trim() || reordering.row,
                        ':position': String(index + 1),
                        ':count': String(this.rows.length),
                    }[placeholder]
                },
            )
        },

        // https://github.com/filamentphp/filament/issues/1107
        // https://github.com/filamentphp/filament/issues/12824
        updateRows() {
            const state = Alpine.raw(this.state)
            const mergedRows = state.map(({ key, value }) => ({
                id: this.nextRowId++,
                key,
                value,
            }))

            this.rows.forEach((row) => {
                if (row.key === '' || row.key === null) {
                    mergedRows.push(row)
                }
            })

            this.rows = mergedRows
        },

        getState() {
            return this.rows
                .filter((row) => row.key !== '' && row.key !== null)
                .map(({ key, value }) => ({ key, value }))
        },

        updateState() {
            const state = this.getState()
            if (JSON.stringify(this.state) !== JSON.stringify(state)) {
                this.state = state
            }
        },
    }
}
