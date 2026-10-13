import editableTableColumn from '../../utilities/editable-table-column.js'

export default function textInputTableColumn(configuration) {
    return {
        ...editableTableColumn(configuration),

        init() {
            this.initEditableColumn()
        },

        getServerState() {
            if (!this.$refs.serverState) {
                return undefined
            }

            return [null, undefined].includes(this.$refs.serverState.value)
                ? ''
                : this.$refs.serverState.value.replaceAll(
                      '\\' + String.fromCharCode(34),
                      String.fromCharCode(34),
                  )
        },
    }
}
