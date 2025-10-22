export default function tabsSchemaComponent({
    activeTab,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
    isScrollable
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

            if(!isScrollable) {
                this.setUpScrollable()
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

        containerElementDimensions: null,
        dropDownButtonDimensions: null,
        tabsElementsDimensions: [],
        tabsWithinDropdown: [],

        setUpScrollable() {
            this.$nextTick(() => {
                const dropDownButtonElement = Array.from(this.$refs.tabsContainer.children).at(-1);
                this.dropDownButtonDimensions = {
                    width: Math.floor(dropDownButtonElement.clientWidth) * 2
                }

                const containerElementStyles = window.getComputedStyle(this.$refs.tabsContainer)
                this.containerElementDimensions = {
                    width: Math.floor(this.$refs.tabsContainer.clientWidth),
                    padding: Math.floor(parseFloat(containerElementStyles.paddingLeft)) * 2,
                    gap: {
                        width: Math.floor(parseFloat(containerElementStyles.columnGap))
                    }
                }

                Array.from(this.$refs.tabsContainer.children).slice(0, -1).forEach((el) => {
                    this.tabsElementsDimensions.push({
                        width: Math.ceil(el.clientWidth),
                        height: Math.ceil(el.clientHeight),
                        key: el.dataset.tabKey
                    })
                })

                this.updateTabsWithinDropdown()
            })
        },

        updateTabsWithinDropdown() {
            this.tabsWithinDropdown = []
            this.containerElementDimensions.width = Math.floor(this.$refs.tabsContainer.clientWidth)

            const containerWidth = this.containerElementDimensions.width - this.containerElementDimensions.padding - this.dropDownButtonDimensions.width

            let currentWidth = 0
            this.tabsElementsDimensions.forEach(tab =>  {
                const nextWidth = currentWidth + tab.width + this.containerElementDimensions.gap.width
                if(nextWidth >= containerWidth) {
                    this.tabsWithinDropdown.push(tab.key)
                }
                currentWidth = nextWidth
            })
        }
    }
}
