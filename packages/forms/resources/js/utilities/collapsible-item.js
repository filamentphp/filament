export default function collapsibleItem({
    statePath,
    isCollapsed,
    collapseKey,
}) {
    return {
        isCollapsed: collapseKey
            ? Alpine.$persist(isCollapsed).as(collapseKey)
            : isCollapsed,

        collapseFromEvent(event) {
            if (event.detail === statePath) this.isCollapsed = true
        },

        expandFromEvent(event) {
            if (event.detail === statePath) this.isCollapsed = false
        },

        expand() {
            this.isCollapsed = false
        },

        toggleCollapsed() {
            this.isCollapsed = !this.isCollapsed
        },

        get itemClasses() {
            return { 'fi-collapsed': this.isCollapsed }
        },
    }
}
