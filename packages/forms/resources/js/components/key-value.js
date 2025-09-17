export default function keyValueFormComponent({ state }) {
    return {
        state,

        rows: [],

        init() {
            this.updateRows()

            if (this.rows.length <= 0) {
                this.rows.push({ key: '', value: '' })
            } else {
                this.updateState()
            }

            this.$watch('state', (state, oldState) => {
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
            this.rows.push({ key: '', value: '' })

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
            const rows = Alpine.raw(this.rows)

            this.rows = []

            const reorderedRow = rows.splice(event.oldIndex, 1)[0]
            rows.splice(event.newIndex, 0, reorderedRow)

            this.$nextTick(() => {
                this.rows = rows

                this.updateState()
            })
        },

        // https://github.com/filamentphp/filament/issues/1107
        // https://github.com/filamentphp/filament/issues/12824
        updateRows() {
            const state = Alpine.raw(this.state)
            const mergedRows = state.map(({ key, value }) => ({ key, value }))

            this.rows.forEach((row) => {
                if (row.key === '' || row.key === null) {
                    mergedRows.push({
                        key: '',
                        value: row.value,
                    })
                }
            })

            this.rows = mergedRows
        },

        updateState() {
            let state = []

            this.rows.forEach((row) => {
                if (row.key === '' || row.key === null) {
                    return
                }

                state.push({
                    key: row.key,
                    value: row.value,
                })
            })

            if (JSON.stringify(this.state) !== JSON.stringify(state)) {
                this.state = state
            }
        },
    }
}
