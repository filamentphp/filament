import editableTableColumn from '../../utilities/editable-table-column.js'

export default function checkboxTableColumn(configuration) {
    return {
        ...editableTableColumn(configuration),

        init() {
            this.initEditableColumn()
        },

        getServerState() {
            if (!this.$refs.serverState) {
                return undefined
            }

            return [1, '1'].includes(this.$refs.serverState.value)
        },
    }
}
