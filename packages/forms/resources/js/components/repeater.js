import collapsibleItem from '../utilities/collapsible-item.js'
import reordering from '../utilities/reordering.js'

export default function repeaterFormComponent({ statePath, ...configuration }) {
    return {
        ...reordering(configuration),

        collapseAll() {
            this.$dispatch('repeater-collapse', statePath)
        },

        expandAll() {
            this.$dispatch('repeater-expand', statePath)
        },

        item(configuration) {
            return collapsibleItem({ statePath, ...configuration })
        },
    }
}
