export default function tabsSchemaComponent({
    activeTab,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
}) {
    return {
        tab,

        init() {
            const tabs = this.getTabs()

            const queryString = new URLSearchParams(window.location.search)

            if (
                isTabPersistedInQueryString &&
                queryString.has(tabQueryStringKey) &&
                tabs.includes(queryString.get(tabQueryStringKey))
            ) {
                this.tab = queryString.get(tabQueryStringKey)
            }

            this.$watch('tab', () => this.updateQueryString())

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

        getTabs() {
            if (!this.$refs.tabsData) {
                return []
            }

            return JSON.parse(this.$refs.tabsData.value)
        },

        updateQueryString() {
            if (!isTabPersistedInQueryString) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(tabQueryStringKey, this.tab)

            history.replaceState(null, document.title, url.toString())
        },
    }
}
