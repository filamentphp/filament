export default function keyValueFormComponent({ state }) {
    return {
        state,

        rows: [],

        init: function () {
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

        addRow: function () {
            this.rows.push({ key: '', value: '' })

            this.updateState()
        },

        deleteRow: function (index) {
            this.rows.splice(index, 1)

            if (this.rows.length <= 0) {
                this.addRow()
            }

            this.updateState()
        },

        reorderRows: function (event) {
            const rows = Alpine.raw(this.rows)

            this.rows = []

            const reorderedRow = rows.splice(event.oldIndex, 1)[0]
            rows.splice(event.newIndex, 0, reorderedRow)

            this.$nextTick(() => {
                this.rows = rows

                this.updateState()
            })
        },

        // Only update the rows if something has changed and include even invalid rows for live editing.
        // Issues 1107 and 12824
        updateRows: function () {
            let stateRows = []
            for (let [key, value] of Object.entries(this.state ?? {})) {
                stateRows.push({ key, value })
            }

            const extraRows = this.rows.filter((row, index, arr) => {
                if (row.key === '' || row.key === null) return true
                const isDuplicate =
                    arr.findIndex((r) => r.key === row.key) !== index
                return isDuplicate
            })

            const currentRows = this.rows.filter(
                (row) => row.key !== '' && row.key !== null,
            )
            const rowsMatchState =
                currentRows.length === stateRows.length &&
                currentRows.every(
                    (row, i) =>
                        row.key === stateRows[i].key &&
                        row.value === stateRows[i].value,
                )

            if (!rowsMatchState) {
                this.rows = [...stateRows, ...extraRows]
            }
        },

        // Only update the items that are not empty or not a duplicate
        // Issues 1107 and 12824
        updateState: function () {
            let state = {}
            let seenKeys = new Set()

            this.rows.forEach((row) => {
                if (
                    row.key === '' ||
                    row.key === null ||
                    seenKeys.has(row.key)
                ) {
                    return
                }

                seenKeys.add(row.key)
                state[row.key] = row.value
            })

            this.state = state
        },
    }
}
