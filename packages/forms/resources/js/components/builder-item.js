export default function builderItemFormComponent({
    key,
    itemKey,
    isCollapsed,
    isLoaded,
    unloadOnCollapse,
}) {
    return {
        isCollapsed,

        isLoaded,

        isLoading: false,

        init() {
            // `$persistCollapsed` restored expanded state but schema not yet loaded — trigger load.
            if (! this.isCollapsed && ! this.isLoaded) {
                this.expand()
            }
        },

        async expand() {
            if (this.isLoading) return

            this.isCollapsed = false

            if (this.isLoaded) return

            this.isLoading = true

            try {
                await this.$wire.callSchemaComponentMethod(key, 'loadItem', {
                    itemKey,
                })
                this.isLoaded = true
            } finally {
                this.isLoading = false
            }
        },

        async collapse() {
            if (this.isLoading) return

            this.isCollapsed = true

            if (!(this.isLoaded && unloadOnCollapse)) return

            this.isLoading = true

            try {
                await this.$wire.callSchemaComponentMethod(key, 'unloadItem', {
                    itemKey,
                })
                this.isLoaded = false
            } finally {
                this.isLoading = false
            }
        },

        toggleCollapsed() {
            if (this.isLoading) return

            this.isCollapsed ? this.expand() : this.collapse()
        },
    }
}
