export default function tabsSchemaComponent({
    activeTab,
    isScrollable,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
}) {
    return {
        boundResizeHandler: null,
        isScrollable,
        resizeDebounceTimer: null,
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
                this.boundResizeHandler =
                    this.debouncedUpdateTabsWithinDropdown.bind(this)

                window.addEventListener('resize', this.boundResizeHandler)

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

        calculateContainerGap(containerEl) {
            const styles = window.getComputedStyle(containerEl)

            return Math.ceil(parseFloat(styles.columnGap))
        },

        calculateDropdownIconWidth(triggerEl) {
            const iconEl = triggerEl.querySelector('.fi-icon')

            return Math.ceil(iconEl.clientWidth)
        },

        calculateTabItemGap(tabEl) {
            const styles = window.getComputedStyle(tabEl)

            return Math.ceil(parseFloat(styles.columnGap) || 8)
        },

        calculateTabItemPadding(tabEl) {
            const styles = window.getComputedStyle(tabEl)

            return (
                Math.ceil(parseFloat(styles.paddingLeft)) +
                Math.ceil(parseFloat(styles.paddingRight))
            )
        },

        findOverflowIndex(
            tabElements,
            availableWidth,
            containerGap,
            tabItemGap,
            tabItemPadding,
            dropdownIconWidth,
        ) {
            const tabWidths = tabElements.map((el) => Math.ceil(el.clientWidth))

            const tabContentWidths = tabElements.map((el) => {
                const labelEl = el.querySelector('.fi-tabs-item-label')
                const badgeEl = el.querySelector('.fi-badge')

                const labelWidth = Math.ceil(labelEl.clientWidth)
                const badgeWidth = badgeEl ? Math.ceil(badgeEl.clientWidth) : 0

                return {
                    label: labelWidth,
                    badge: badgeWidth,
                    total:
                        labelWidth +
                        (badgeWidth > 0 ? tabItemGap + badgeWidth : 0),
                }
            })

            for (let i = 0; i < tabElements.length; i++) {
                const visibleTabsWidth = tabWidths
                    .slice(0, i + 1)
                    .reduce((sum, w) => sum + w, 0)

                const gapsBetweenVisibleTabs = i * containerGap

                const collapsedContents = tabContentWidths.slice(i + 1)
                const hasCollapsedTabs = collapsedContents.length > 0

                const widestCollapsedContent = hasCollapsedTabs
                    ? Math.max(...collapsedContents.map((c) => c.total))
                    : 0

                const triggerWidth = hasCollapsedTabs
                    ? tabItemPadding +
                      widestCollapsedContent +
                      tabItemGap +
                      dropdownIconWidth +
                      containerGap
                    : 0

                const totalWidth =
                    visibleTabsWidth + gapsBetweenVisibleTabs + triggerWidth

                if (totalWidth > availableWidth) {
                    return i
                }
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

        debouncedUpdateTabsWithinDropdown() {
            clearTimeout(this.resizeDebounceTimer)

            this.resizeDebounceTimer = setTimeout(
                () => this.updateTabsWithinDropdown(),
                150,
            )
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

            // Force all tabs visible for accurate measurement
            const originalStyles = tabElements.map((el) => el.style.display)
            tabElements.forEach((el) => (el.style.display = ''))

            // Force reflow to ensure dimensions are calculated
            containerEl.offsetHeight

            const availableWidth = this.calculateAvailableWidth(containerEl)
            const containerGap = this.calculateContainerGap(containerEl)
            const dropdownIconWidth = this.calculateDropdownIconWidth(triggerEl)
            const tabItemGap = this.calculateTabItemGap(tabElements[0])
            const tabItemPadding = this.calculateTabItemPadding(tabElements[0])

            const overflowIndex = this.findOverflowIndex(
                tabElements,
                availableWidth,
                containerGap,
                tabItemGap,
                tabItemPadding,
                dropdownIconWidth,
            )

            // Restore original display styles
            tabElements.forEach(
                (el, i) => (el.style.display = originalStyles[i]),
            )

            if (overflowIndex !== -1) {
                this.withinDropdownIndex = overflowIndex
            }

            this.withinDropdownMounted = true
        },

        destroy() {
            if (this.boundResizeHandler) {
                window.removeEventListener('resize', this.boundResizeHandler)
            }

            clearTimeout(this.resizeDebounceTimer)
        },
    }
}
