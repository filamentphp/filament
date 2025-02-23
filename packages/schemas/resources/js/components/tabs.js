export default function tabsSchemaComponent({
    activeTab,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
}) {
    return {
        tab,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            const tabs = this.getTabs()

            if (!this.tab || !tabs.includes(this.tab)) {
                this.tab = tabs[activeTab - 1]
            }

            Livewire.hook(
                'commit',
                ({ component, commit, succeed, fail, respond }) => {
                    succeed(({ snapshot, effect }) => {
                        this.$nextTick(() => {
                            if (component.id !== livewireId) {
                                return
                            }

                            const tabs = this.getTabs()

                            if (!tabs.includes(this.tab)) {
                                this.tab = tabs[activeTab - 1] ?? this.tab
                            }
                        })
                    })
                },
            )
        },

        getTabs: function () {
            if (!this.$refs.tabsData) {
                return []
            }

            return JSON.parse(this.$refs.tabsData.value)
        },

        updateQueryString: function () {
            if (!isTabPersistedInQueryString) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(tabQueryStringKey, this.tab)

            history.pushState(null, document.title, url.toString())
        },
    }
}
