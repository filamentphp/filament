export default (Alpine) => {
    Alpine.data('keyValueFormComponent', ({
        state,
    }) => ({
        state,

        rows: [],

        init: function () {
            this.updateRows()

            if (this.rows.length <= 0) {
                this.addRow()
            }

            this.$watch('state', () => {
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

        updateRows: function () {
            let rows = []

            for (let [key, value] of Object.entries(this.state ?? {})) {
                rows.push({
                    key,
                    value,
                })
            }

            this.rows = rows
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
