export default (Alpine) => {
    Alpine.data('tableBuilderFormComponent', ({ state }) => ({
        state,

        columns: 1,

        init() {
            this.columns = this.state.reduce((maxLength, currentItem) => {
                return currentItem.length > maxLength
                    ? currentItem.length
                    : maxLength
            }, 1)
        },

        addRow() {
            this.state.push(Array(this.columns).fill(null))
        },

        addColumn() {
            this.columns++

            for (let column = 0; column < this.columns; column++) {
                for (const i in this.state) {
                    if (this.state[i][column] !== undefined) {
                        continue
                    }

                    this.state[i][column] = null
                }
            }
        },

        removeRow(rowToRemove) {
            this.state = this.state.filter((_, row) => row !== rowToRemove)
        },

        removeColumn(columnToRemove) {
            this.columns--

            this.state = this.state.map((row) => {
                return row.filter((_, column) => column !== columnToRemove - 1)
            })
        },
    }))
}
