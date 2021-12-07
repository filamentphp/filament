export default (Alpine) => {
    Alpine.data('keyValueFormComponent', ({
        canAddRows,
        canDeleteRows,
        state,
    }) => ({
        canAddRows,

        canDeleteRows,

        state,

        rows: [],

        init: function () {
            for (let [value, key] of Object.entries(this.state ?? {})) {
                this.rows.push({
                    key,
                    value,
                })
            }

            if (this.rows.length <= 0) {
                this.addRow()
            }
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

        updateState: function () {
            let state = {}

            this.rows.forEach((row) => {
                state[row.key] = row.value
            })

            this.state = state
        },
    }))
}
