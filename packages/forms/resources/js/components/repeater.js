import collapsibleItem from './collapsible-item.js'
import reordering from './reordering.js'

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
