export default function tabsSchemaComponent({
    activeTab,
    isScrollable,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
}) {
    return {
        isScrollable,
        tab,
        withinDropdownIndex: null,
        withinDropdownMounted: false,

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

            if (!isScrollable) {
                this.updateTabsWithinDropdown()
            }
        },

        calculateAvailableWidth(containerEl) {
            const styles = window.getComputedStyle(containerEl)

            return (
                Math.floor(containerEl.clientWidth) -
                Math.ceil(parseFloat(styles.paddingLeft)) * 2
            )
        },

        calculateDropdownTriggerWidth(triggerEl) {
            const styles = window.getComputedStyle(triggerEl)
            const iconEl = triggerEl.querySelector('.fi-icon')

            return (
                Math.ceil(iconEl.clientWidth) +
                Math.ceil(parseFloat(styles.columnGap))
            )
        },

        calculateGapWidth(containerEl) {
            const styles = window.getComputedStyle(containerEl)

            return Math.ceil(parseFloat(styles.columnGap))
        },

        findOverflowIndex(tabElements, availableWidth, gapWidth, triggerWidth) {
            let remainingWidth = availableWidth

            for (let i = 0; i < tabElements.length; i++) {
                const currentTabWidth =
                    Math.ceil(tabElements[i].clientWidth) + gapWidth

                const widestRemainingTab = tabElements
                    .slice(i)
                    .reduce((widest, current) =>
                        current.clientWidth > widest.clientWidth
                            ? current
                            : widest,
                    )

                const requiredWidth =
                    Math.ceil(widestRemainingTab.clientWidth) +
                    gapWidth +
                    triggerWidth

                if (remainingWidth - requiredWidth <= 0) {
                    return i
                }

                remainingWidth -= currentTabWidth
            }

            return -1
        },

        get isDropdownButtonVisible() {
            if (!this.withinDropdownMounted) {
                return true
            }

            if (this.withinDropdownIndex === null) {
                return false
            }

            const activeTabIndex = this.getTabs().findIndex(
                (tab) => tab === this.tab,
            )

            return activeTabIndex < this.withinDropdownIndex
        },

        getTabs() {
            return this.$refs.tabsData
                ? JSON.parse(this.$refs.tabsData.value)
                : []
        },

        updateQueryString() {
            if (!isTabPersistedInQueryString) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(tabQueryStringKey, this.tab)

            history.replaceState(null, document.title, url.toString())
        },

        async updateTabsWithinDropdown() {
            this.withinDropdownIndex = null
            this.withinDropdownMounted = false

            await this.$nextTick()

            const containerEl = this.$el.querySelector('.fi-tabs')
            const triggerEl = containerEl.querySelector(
                '.fi-tabs-item:last-child',
            )
            const tabElements = Array.from(containerEl.children).slice(0, -1)

            const availableWidth = this.calculateAvailableWidth(containerEl)
            const gapWidth = this.calculateGapWidth(containerEl)
            const triggerWidth = this.calculateDropdownTriggerWidth(triggerEl)

            const overflowIndex = this.findOverflowIndex(
                tabElements,
                availableWidth,
                gapWidth,
                triggerWidth,
            )

            if (overflowIndex !== -1) {
                this.withinDropdownIndex = overflowIndex
            }

            this.withinDropdownMounted = true
        },
    }
}
