export default function tabsSchemaComponent({
    activeTab,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
    isScrollable,
}) {
    return {
        tab,
        isScrollable,
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

        withinDropdownMounted: false,
        withinDropdownIndex: null,
        get isDropDownButtonVisible() {
            return (
                (this.withinDropdownIndex !== null &&
                    this.getTabs().findIndex((tab) => tab === this.tab) <
                        this.withinDropdownIndex) ||
                !this.withinDropdownMounted
            )
        },

        async updateTabsWithinDropdown() {
            this.withinDropdownMounted = false
            this.withinDropdownIndex = null

            await this.$nextTick()

            const tabs = this.getTabs()

            const tabsContainerElement = this.$el.querySelector('.fi-tabs')
            const dropdownTriggerElement = tabsContainerElement.querySelector(
                '.fi-tabs-item:last-child',
            )
            const dropdownTriggerIconElement =
                dropdownTriggerElement.querySelector('.fi-icon')

            const tabsContainerElementComputedStyles =
                window.getComputedStyle(tabsContainerElement)

            const dropdownTriggerElementComputedStyles =
                window.getComputedStyle(dropdownTriggerElement)

            const dropDownTriggerDimensions = {
                width: Math.ceil(dropdownTriggerElement.clientWidth),
                iconWidth: Math.ceil(dropdownTriggerIconElement.clientWidth),
                gapWidth: Math.ceil(
                    parseFloat(dropdownTriggerElementComputedStyles.columnGap),
                ),
            }

            const tabsContainerElementDimensions = {
                width: Math.floor(tabsContainerElement.clientWidth),
                padding:
                    Math.ceil(
                        parseFloat(
                            tabsContainerElementComputedStyles.paddingLeft,
                        ),
                    ) * 2,
                gapWidth: Math.ceil(
                    parseFloat(tabsContainerElementComputedStyles.columnGap),
                ),
            }

            let tabsContainerAvailableWidth =
                tabsContainerElementDimensions.width -
                tabsContainerElementDimensions.padding

            const maxVisibleTabs = Array.from(tabsContainerElement.children)
                .slice(0, -1)
                .findIndex((tab, index, tabs) => {
                    const nextWidestTab = tabs
                        .slice(index)
                        .reduce((max, current) => {
                            return max.clientWidth > current.clientWidth
                                ? max
                                : current
                        }, tab)

                    const nextWidestTabWidth =
                        Math.ceil(nextWidestTab.clientWidth) +
                        tabsContainerElementDimensions.gapWidth +
                        dropDownTriggerDimensions.iconWidth +
                        dropDownTriggerDimensions.gapWidth

                    const remainingSpace =
                        tabsContainerAvailableWidth - nextWidestTabWidth

                    if (remainingSpace <= 0) {
                        return true
                    }

                    const currentTabWidth =
                        Math.ceil(tab.clientWidth) +
                        tabsContainerElementDimensions.gapWidth

                    tabsContainerAvailableWidth =
                        tabsContainerAvailableWidth - currentTabWidth
                })

            if (maxVisibleTabs !== -1) {
                this.withinDropdownIndex = maxVisibleTabs - 1
            }

            this.withinDropdownMounted = true
        },
    }
}
