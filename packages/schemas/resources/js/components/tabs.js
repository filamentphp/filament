import {
    autoUpdate,
    computePosition,
    flip,
    offset,
    shift,
    size,
} from '@floating-ui/dom'

export default function tabsSchemaComponent({
    activeTab,
    hasTabPanels = true,
    isScrollable,
    isTabPersisted,
    isTabPersistedInQueryString,
    isVertical = false,
    livewireId,
    livewireProperty = null,
    schemaKey,
    tab,
    tabQueryStringKey,
}) {
    return {
        tab,
        focusedTab: null,
        availableTabs: [],
        overflowTabs: [],
        overflowReady: false,
        isOverflowOpen: false,
        overflowReference: null,
        cleanupOverflowPosition: null,
        overflowPositionVersion: 0,
        pendingOverflowFocus: null,
        overflowTouchY: null,
        hasVisibleTabHeaders: true,
        shouldMeasureOverflow: true,
        root: null,
        lastFocusedElement: null,
        lastFocusedTabset: null,
        lastFocusedPanel: null,
        selectionFocus: null,
        shouldAutofocusAfterUpdate: false,
        pendingServerTab: null,
        observer: null,
        resizeObserver: null,
        animationFrame: null,
        unsubscribeLivewireHook: null,
        boundResetHandler: null,
        boundFocusHandler: null,
        isDestroyed: false,

        init() {
            this.root = this.$el
            const tabs = this.getTabs()
            this.availableTabs = tabs
            const queryString = new URLSearchParams(window.location.search)

            if (
                !livewireProperty &&
                isTabPersistedInQueryString &&
                tabs.includes(queryString.get(tabQueryStringKey))
            ) {
                this.tab = queryString.get(tabQueryStringKey)
            }

            if (!livewireProperty && !tabs.includes(this.tab)) {
                this.tab = this.getDefaultTab()
            }

            this.$watch('tab', () => {
                this.updateQueryString()
                this.scheduleUpdate()
            })

            this.boundFocusHandler = (event) => {
                if (!this.root.contains(event.target)) {
                    this.closeOverflow()
                    this.lastFocusedElement = null
                    this.lastFocusedTabset = null
                    this.lastFocusedPanel = null
                    this.focusedTab = null
                    this.selectionFocus = null
                    return
                }

                this.lastFocusedElement = event.target
                this.lastFocusedTabset = event.target.closest('.fi-sc-tabs')
                this.lastFocusedPanel = Array.from(this.root.children).find(
                    (element) =>
                        element.classList.contains('fi-sc-tabs-tab') &&
                        element.contains(event.target),
                )

                if (this.lastFocusedTabset !== this.root) {
                    this.closeOverflow()
                    this.focusedTab = null
                    this.selectionFocus = null
                    return
                }

                if (this.getTabElements().includes(event.target)) {
                    this.focusedTab = event.target.dataset.tabKey
                    if (hasTabPanels) {
                        if (this.overflowTabs.includes(this.focusedTab)) {
                            this.pendingOverflowFocus = event.target
                            this.openOverflow()
                        } else this.closeOverflow()
                    }
                } else {
                    this.focusedTab = null
                    this.closeOverflow()
                }
            }
            document.addEventListener('focusin', this.boundFocusHandler)

            this.observer = new MutationObserver((records) =>
                this.scheduleUpdate(
                    records.some(
                        (record) =>
                            record.attributeName !== 'style' &&
                            this.getHeaderElement()
                                .querySelector(':scope > [x-ref=tablist]')
                                .contains(record.target),
                    ),
                ),
            )
            this.observer.observe(this.root, {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true,
                attributeFilter: [
                    'data-tab-available',
                    'data-active-tab',
                    'disabled',
                    'aria-disabled',
                ],
            })

            if (!isScrollable) {
                this.observer.observe(this.getHeaderElement(), {
                    attributeFilter: ['style'],
                    subtree: true,
                })
                this.resizeObserver = new ResizeObserver(() =>
                    this.scheduleUpdate(true),
                )
                this.resizeObserver.observe(this.getHeaderElement())
            }

            this.unsubscribeLivewireHook = Livewire.hook(
                'commit',
                ({ component, succeed }) => {
                    if (component.id !== livewireId) return

                    succeed(() => this.scheduleUpdate())
                },
            )

            this.boundResetHandler = (event) => {
                if (
                    livewireProperty ||
                    event.detail.livewireId !== livewireId ||
                    event.detail.schemaKey !== schemaKey ||
                    isTabPersisted ||
                    isTabPersistedInQueryString
                )
                    return

                if (this.isDestroyed) return
                this.tab = this.getDefaultTab()
                this.shouldAutofocusAfterUpdate = true
                this.scheduleUpdate()
            }
            window.addEventListener(
                'reset-schema-component-state',
                this.boundResetHandler,
            )

            this.$nextTick(() => {
                if (this.isDestroyed) return
                this.update()
                this.autofocusFields(true)
            })
        },

        getHeaderElement() {
            return this.root.querySelector(':scope > [x-ref=tabsHeader]')
        },

        getOverflowPanel() {
            return this.getHeaderElement().querySelector(
                ':scope > .fi-dropdown > [x-ref=overflowPanel]',
            )
        },

        getTabElements() {
            return Array.from(
                this.getHeaderElement().querySelectorAll(
                    ':scope > [x-ref=tablist] > [data-tab-key]',
                ),
            )
        },

        isAvailable(element) {
            return (
                element.dataset.tabAvailable !== 'false' &&
                !element.disabled &&
                element.getAttribute('aria-disabled') !== 'true'
            )
        },

        getTabs() {
            return this.getTabElements()
                .filter((element) => this.isAvailable(element))
                .map((element) => element.dataset.tabKey)
        },

        getDefaultTab() {
            const configured = this.getTabElements()[activeTab - 1]
            return configured && this.isAvailable(configured)
                ? configured.dataset.tabKey
                : (this.getTabs()[0] ?? null)
        },

        isTabSelected(key) {
            return this.tab === key && this.availableTabs.includes(key)
        },

        getVisibleTabs() {
            return this.getTabElements().filter(
                (element) =>
                    this.isAvailable(element) && element.checkVisibility(),
            )
        },

        getOverflowTrigger() {
            return Array.from(
                this.getHeaderElement().querySelectorAll(
                    '[data-tabs-overflow-trigger]',
                ),
            ).find((element) => element.checkVisibility())
        },

        getTabFocusTarget(key) {
            if (hasTabPanels)
                return this.getTabElements().find(
                    (element) =>
                        element.dataset.tabKey === key &&
                        this.isAvailable(element),
                )
            return (
                this.getVisibleTabs().find(
                    (element) => element.dataset.tabKey === key,
                ) ?? this.getOverflowTrigger()
            )
        },

        getTabIndex(key) {
            if (!hasTabPanels) return 0
            const tabs = this.availableTabs
            const entry = tabs.includes(this.focusedTab)
                ? this.focusedTab
                : tabs.includes(this.tab)
                  ? this.tab
                  : tabs[0]
            return key === entry ? 0 : -1
        },

        handleKeydown(event) {
            if (!hasTabPanels || !this.getTabElements().includes(event.target))
                return

            if (event.key === 'Escape' && this.isOverflowOpen) {
                event.preventDefault()
                event.stopPropagation()
                this.dismissOverflow()
                return
            }

            const tabs = this.getTabElements().filter((element) =>
                this.isAvailable(element),
            )
            const index = tabs.indexOf(event.target)
            const isRtl =
                getComputedStyle(event.target.parentElement).direction === 'rtl'
            const previous = isVertical
                ? 'ArrowUp'
                : isRtl
                  ? 'ArrowRight'
                  : 'ArrowLeft'
            const next = isVertical
                ? 'ArrowDown'
                : isRtl
                  ? 'ArrowLeft'
                  : 'ArrowRight'
            let destination

            if (event.key === previous)
                destination = (index - 1 + tabs.length) % tabs.length
            else if (event.key === next) destination = (index + 1) % tabs.length
            else if (event.key === 'Home') destination = 0
            else if (event.key === 'End') destination = tabs.length - 1
            else return

            event.preventDefault()
            this.focusTab(tabs[destination]?.dataset.tabKey)
        },

        focusTab(key) {
            const header = this.getTabFocusTarget(key)
            if (!header) return
            if (hasTabPanels && this.overflowTabs.includes(key)) {
                this.pendingOverflowFocus = header
                this.openOverflow()
                return
            }
            this.closeOverflow()
            header.focus({ preventScroll: true })
            header.scrollIntoView({ block: 'nearest', inline: 'nearest' })
        },

        toggleOverflow() {
            if (this.isOverflowOpen) {
                this.dismissOverflow()
                return
            }
            this.focusTab(
                this.overflowTabs.includes(this.tab) &&
                    this.availableTabs.includes(this.tab)
                    ? this.tab
                    : this.overflowTabs.find((key) =>
                          this.availableTabs.includes(key),
                      ),
            )
        },

        openOverflow() {
            if (!hasTabPanels || isScrollable || this.isDestroyed) return
            this.isOverflowOpen = true
            this.$nextTick(() => {
                if (!this.isDestroyed && this.isOverflowOpen)
                    this.syncOverflowPosition()
            })
        },

        closeOverflow() {
            this.isOverflowOpen = false
            this.pendingOverflowFocus = null
            this.overflowTouchY = null
            this.overflowPositionVersion++
            this.cleanupOverflowPosition?.()
            this.cleanupOverflowPosition = null
            this.overflowReference = null
        },

        dismissOverflow() {
            if (!this.isOverflowOpen) return
            const inline = this.getVisibleTabs().filter(
                (element) =>
                    !this.overflowTabs.includes(element.dataset.tabKey),
            )
            const destination =
                inline.find((element) => element.dataset.tabKey === this.tab) ??
                inline.at(-1) ??
                Array.from(this.root.children).find(
                    (element) =>
                        element.getAttribute('role') === 'tabpanel' &&
                        element.checkVisibility(),
                )
            destination?.focus({ preventScroll: true })
            this.closeOverflow()
        },

        scrollOverflow(event) {
            if (
                !this.isOverflowOpen ||
                (event.type === 'wheel' && event.ctrlKey) ||
                !this.overflowTabs.includes(
                    event.target.closest('[data-tab-key]')?.dataset.tabKey,
                )
            )
                return
            const panel = this.getOverflowPanel()
            if (event.type !== 'wheel' && event.touches.length !== 1) {
                this.overflowTouchY = null
                return
            }
            if (
                event.type === 'touchstart' ||
                (event.type === 'touchmove' && this.overflowTouchY === null)
            ) {
                this.overflowTouchY = event.touches[0].clientY
                return
            }
            let wheelScale = 1
            if (event.type === 'wheel') {
                if (event.deltaMode === WheelEvent.DOM_DELTA_LINE) {
                    const styles = getComputedStyle(
                        event.target.closest('[data-tab-key]'),
                    )
                    wheelScale =
                        parseFloat(styles.lineHeight) ||
                        parseFloat(styles.fontSize) * 1.2
                } else if (event.deltaMode === WheelEvent.DOM_DELTA_PAGE)
                    wheelScale = panel.clientHeight
            }
            const delta =
                event.type === 'wheel'
                    ? event.deltaY * wheelScale
                    : this.overflowTouchY - event.touches[0].clientY
            if (event.type === 'touchmove')
                this.overflowTouchY = event.touches[0].clientY
            if (
                (delta > 0 &&
                    panel.scrollTop + panel.clientHeight <
                        panel.scrollHeight) ||
                (delta < 0 && panel.scrollTop > 0)
            ) {
                event.preventDefault()
                panel.scrollTop += delta
            }
        },

        syncOverflowPosition() {
            const trigger = this.getOverflowTrigger()
            if (
                !trigger ||
                !this.overflowTabs.some((key) =>
                    this.availableTabs.includes(key),
                )
            ) {
                this.closeOverflow()
                return
            }
            if (this.overflowReference !== trigger) {
                this.cleanupOverflowPosition?.()
                this.overflowReference = trigger
                this.cleanupOverflowPosition = autoUpdate(
                    trigger,
                    this.getOverflowPanel(),
                    () => this.positionOverflow(),
                )
            } else this.positionOverflow()
        },

        async positionOverflow() {
            const version = ++this.overflowPositionVersion
            const panel = this.getOverflowPanel()
            const trigger = this.overflowReference
            if (!this.isOverflowOpen || !trigger || !panel?.checkVisibility())
                return
            const position = await computePosition(trigger, panel, {
                strategy: 'fixed',
                placement:
                    getComputedStyle(trigger).direction === 'rtl'
                        ? 'bottom-end'
                        : 'bottom-start',
                middleware: [
                    offset(8),
                    flip(),
                    shift({ padding: 8, crossAxis: true }),
                    size({
                        padding: 8,
                        apply: ({ availableHeight }) => {
                            const maxHeight = `${Math.max(0, availableHeight)}px`
                            if (panel.style.maxHeight !== maxHeight)
                                panel.style.maxHeight = maxHeight
                        },
                    }),
                ],
            })
            if (
                this.isDestroyed ||
                !this.isOverflowOpen ||
                !panel.checkVisibility() ||
                version !== this.overflowPositionVersion
            )
                return
            for (const [name, value] of Object.entries({
                left: position.x,
                top: position.y,
            })) {
                const pixels = `${value}px`
                if (panel.style[name] !== pixels) panel.style[name] = pixels
            }
            const rows = new Map(
                Array.from(
                    panel.querySelectorAll('[data-tab-menu-key]'),
                    (element) => [element.dataset.tabMenuKey, element],
                ),
            )
            const focusedRow = rows.get(
                this.pendingOverflowFocus?.dataset.tabKey,
            )
            const panelBounds = panel.getBoundingClientRect()
            if (focusedRow) {
                const bounds = focusedRow.getBoundingClientRect()
                const scale = panelBounds.height / panel.offsetHeight
                if (bounds.top < panelBounds.top)
                    panel.scrollTop += (bounds.top - panelBounds.top) / scale
                else if (bounds.bottom > panelBounds.bottom)
                    panel.scrollTop +=
                        (bounds.bottom - panelBounds.bottom) / scale
            }
            for (const header of this.getTabElements()) {
                if (!this.overflowTabs.includes(header.dataset.tabKey)) continue
                const row = rows.get(header.dataset.tabKey)
                if (!row?.checkVisibility()) continue
                const bounds = row.getBoundingClientRect()
                const headerPosition = await computePosition(row, header, {
                    strategy: 'fixed',
                    placement: 'bottom-start',
                    middleware: [
                        offset(({ rects }) => -rects.reference.height),
                        size({
                            apply: ({ rects }) => {
                                for (const name of ['width', 'height']) {
                                    const property = `--tab-overflow-${name}`
                                    const pixels = `${rects.reference[name]}px`
                                    if (
                                        header.style.getPropertyValue(
                                            property,
                                        ) !== pixels
                                    )
                                        header.style.setProperty(
                                            property,
                                            pixels,
                                        )
                                }
                            },
                        }),
                    ],
                })
                if (
                    this.isDestroyed ||
                    !this.isOverflowOpen ||
                    version !== this.overflowPositionVersion
                )
                    return
                for (const [name, value] of Object.entries({
                    left: headerPosition.x,
                    top: headerPosition.y,
                })) {
                    const property = `--tab-overflow-${name}`
                    const pixels = `${value}px`
                    if (header.style.getPropertyValue(property) !== pixels)
                        header.style.setProperty(property, pixels)
                }
                const scale =
                    bounds.height /
                    parseFloat(
                        header.style.getPropertyValue('--tab-overflow-height'),
                    )
                const clip = `inset(${Math.max(0, panelBounds.top - bounds.top) / scale}px 0 ${Math.max(0, bounds.bottom - panelBounds.bottom) / scale}px 0)`
                if (
                    header.style.getPropertyValue('--tab-overflow-clip') !==
                    clip
                )
                    header.style.setProperty('--tab-overflow-clip', clip)
            }
            const focused = this.pendingOverflowFocus
            this.pendingOverflowFocus = null
            if (focused?.isConnected && this.isAvailable(focused))
                focused.focus({ preventScroll: true })
        },

        selectTab(key, event = null, fromMenu = false) {
            if (!this.getTabs().includes(key)) return false

            const header = this.getTabElements().find(
                (element) => element.dataset.tabKey === key,
            )
            this.selectionFocus = {
                key,
                element: fromMenu ? this.getOverflowTrigger() : header,
                autofocus:
                    !fromMenu &&
                    !this.overflowTabs.includes(key) &&
                    event?.detail > 0,
                returnToHeader: fromMenu || event?.detail === 0,
            }

            if (!livewireProperty) this.tab = key
            this.scheduleUpdate()
            return true
        },

        finishSelection() {
            const selection = this.selectionFocus
            if (!selection || selection.key !== this.tab) return
            this.selectionFocus = null
            if (
                document.activeElement !== selection.element &&
                !(
                    document.activeElement === document.body &&
                    !selection.element?.checkVisibility()
                )
            )
                return
            if (selection.autofocus)
                this.autofocusFields(false, selection.element)
            else if (selection.returnToHeader) this.focusTab(selection.key)
        },

        scheduleUpdate(shouldMeasureOverflow = false) {
            this.shouldMeasureOverflow ||= shouldMeasureOverflow
            if (this.isDestroyed || this.animationFrame !== null) return
            this.animationFrame = requestAnimationFrame(() => {
                this.animationFrame = null
                if (!this.isDestroyed) this.update()
            })
        },

        update() {
            if (livewireProperty) {
                const selected = this.$refs.tabsData.dataset.activeTab
                if (this.tab !== selected) this.tab = selected
                if (this.pendingServerTab === selected)
                    this.pendingServerTab = null
            }

            const tabs = this.getTabs()
            this.availableTabs = tabs
            let selectedTab = this.tab
            if (hasTabPanels && !tabs.includes(this.tab) && tabs.length) {
                const destination = this.getDefaultTab()
                selectedTab = destination
                this.selectionFocus = null
                if (!livewireProperty) this.tab = destination
                else if (this.pendingServerTab !== destination) {
                    this.pendingServerTab = destination
                    this.$wire.$set(
                        livewireProperty,
                        destination === '' ? null : destination,
                    )
                }
            }

            if (this.shouldMeasureOverflow) {
                if (
                    hasTabPanels &&
                    this.isOverflowOpen &&
                    !this.pendingOverflowFocus &&
                    this.getTabElements().includes(document.activeElement)
                )
                    this.pendingOverflowFocus = document.activeElement
                this.shouldMeasureOverflow = false
                this.updateOverflow()
            }
            this.hasVisibleTabHeaders = this.getTabElements().some(
                (element) =>
                    element.dataset.tabAvailable !== 'false' &&
                    (hasTabPanels ||
                        !this.overflowTabs.includes(element.dataset.tabKey)),
            )
            this.$nextTick(() => {
                if (this.isDestroyed) return
                if (
                    hasTabPanels &&
                    !this.isOverflowOpen &&
                    this.getTabElements().includes(document.activeElement) &&
                    this.isAvailable(document.activeElement) &&
                    this.overflowTabs.includes(
                        document.activeElement.dataset.tabKey,
                    )
                ) {
                    this.pendingOverflowFocus = document.activeElement
                    this.openOverflow()
                }
                if (this.isOverflowOpen) this.syncOverflowPosition()
                this.finishSelection()
                const focused = this.lastFocusedElement
                if (
                    focused &&
                    (this.lastFocusedTabset === this.root ||
                        (this.lastFocusedPanel &&
                            !this.lastFocusedPanel.checkVisibility())) &&
                    (!focused.checkVisibility() ||
                        (this.getTabElements().includes(focused) &&
                            !this.isAvailable(focused))) &&
                    (document.activeElement === focused ||
                        document.activeElement === document.body)
                ) {
                    this.lastFocusedElement = null
                    const target =
                        !hasTabPanels &&
                        this.overflowTabs.includes(focused.dataset.tabKey)
                            ? this.getOverflowTrigger()
                            : this.getTabFocusTarget(selectedTab)
                    if (hasTabPanels)
                        this.focusTab(
                            target?.dataset.tabKey ?? this.availableTabs[0],
                        )
                    else (target ?? this.getVisibleTabs()[0])?.focus()
                }
                if (this.shouldAutofocusAfterUpdate) {
                    this.shouldAutofocusAfterUpdate = false
                    this.autofocusFields()
                }
            })
        },

        updateOverflow() {
            if (isScrollable || this.isDestroyed) return
            const header = this.getHeaderElement()
            const trigger = header.querySelector(
                '[data-tabs-overflow-ellipsis]',
            )
            const tabs = this.getTabElements()
            const triggers = Array.from(
                header.querySelectorAll('[data-tabs-overflow-trigger]'),
            )
            const measuredElements = [...tabs, ...triggers]
            const originalStyles = measuredElements.map(
                (element) => element.style.display,
            )
            const overflowClasses = tabs.map((element) => [
                element.classList.contains('fi-sc-tabs-header-overflow'),
                element.classList.contains('fi-sc-tabs-header-overflow-open'),
            ])
            tabs.forEach((element) =>
                element.classList.remove(
                    'fi-sc-tabs-header-overflow',
                    'fi-sc-tabs-header-overflow-open',
                ),
            )
            tabs.forEach((element) => (element.style.display = ''))
            triggers.forEach(
                (element) =>
                    (element.style.display = element === trigger ? '' : 'none'),
            )
            header.offsetHeight
            const overflowIndex = tabs.length
                ? this.findOverflowIndex(header, tabs, trigger)
                : -1
            measuredElements.forEach(
                (element, index) =>
                    (element.style.display = originalStyles[index]),
            )
            tabs.forEach((element, index) => {
                element.classList.toggle(
                    'fi-sc-tabs-header-overflow',
                    overflowClasses[index][0],
                )
                element.classList.toggle(
                    'fi-sc-tabs-header-overflow-open',
                    overflowClasses[index][1],
                )
            })
            this.overflowTabs =
                overflowIndex === -1
                    ? []
                    : tabs
                          .slice(overflowIndex)
                          .map((element) => element.dataset.tabKey)
            this.overflowReady = true
            if (!this.overflowTabs.length) {
                if (hasTabPanels) this.closeOverflow()
                else Alpine.$data(trigger.closest('.fi-dropdown')).close()
            }
        },

        findOverflowIndex(header, tabs, trigger) {
            const headerStyles = getComputedStyle(header)
            const tabStyles = getComputedStyle(tabs[0])
            const availableWidth =
                Math.floor(header.clientWidth) -
                Math.ceil(parseFloat(headerStyles.paddingLeft)) * 2
            const containerGap = Math.ceil(parseFloat(headerStyles.columnGap))
            const dropdownIconWidth = Math.ceil(
                trigger.querySelector('.fi-icon').clientWidth,
            )
            const tabItemGap = Math.ceil(parseFloat(tabStyles.columnGap) || 8)
            const tabItemPadding =
                Math.ceil(parseFloat(tabStyles.paddingLeft)) +
                Math.ceil(parseFloat(tabStyles.paddingRight))
            const tabWidths = tabs.map((element) =>
                Math.ceil(element.clientWidth),
            )
            const tabContentWidths = tabs.map((element) => {
                const labelWidth = Math.ceil(
                    element.querySelector('.fi-tabs-item-label').clientWidth,
                )
                const badge = element.querySelector('.fi-badge')
                const badgeWidth = badge ? Math.ceil(badge.clientWidth) : 0
                return (
                    labelWidth + (badgeWidth > 0 ? tabItemGap + badgeWidth : 0)
                )
            })

            // Preserve the existing prefix cutoff and widest collapsed trigger reservation.
            for (let index = 0; index < tabs.length; index++) {
                const visibleTabsWidth = tabWidths
                    .slice(0, index + 1)
                    .reduce((total, width) => total + width, 0)
                const collapsedContents = tabContentWidths.slice(index + 1)
                const triggerWidth = collapsedContents.length
                    ? tabItemPadding +
                      Math.max(...collapsedContents) +
                      tabItemGap +
                      dropdownIconWidth +
                      containerGap
                    : 0
                if (
                    visibleTabsWidth + index * containerGap + triggerWidth >
                    availableWidth
                )
                    return index
            }
            return -1
        },

        updateQueryString() {
            if (
                !isTabPersistedInQueryString ||
                !this.getTabs().includes(this.tab)
            )
                return
            const url = new URL(window.location.href)
            url.searchParams.set(tabQueryStringKey, this.tab)
            history.replaceState(null, document.title, url.toString())
        },

        autofocusFields(respectCurrentFocus = false, trigger = null) {
            this.$nextTick(() => {
                if (this.isDestroyed) return
                if (trigger && document.activeElement !== trigger) return
                if (
                    respectCurrentFocus &&
                    document.activeElement &&
                    document.activeElement !== document.body &&
                    (this.root.contains(document.activeElement) ||
                        this.root.compareDocumentPosition(
                            document.activeElement,
                        ) & Node.DOCUMENT_POSITION_PRECEDING)
                )
                    return
                const panels = Array.from(
                    this.root.querySelectorAll('.fi-sc-tabs-tab.fi-active'),
                ).filter(
                    (element) =>
                        element.closest('.fi-sc-tabs') === this.root &&
                        element.checkVisibility(),
                )
                for (const field of panels.flatMap((panel) =>
                    Array.from(panel.querySelectorAll('[autofocus]')),
                )) {
                    field.focus()
                    if (document.activeElement === field) break
                }
            })
        },

        revealTab(key) {
            this.selectionFocus = null
            if (!livewireProperty) this.tab = key
            this.autofocusFields()
        },

        destroy() {
            this.isDestroyed = true
            this.closeOverflow()
            this.observer?.disconnect()
            this.resizeObserver?.disconnect()
            this.unsubscribeLivewireHook?.()
            cancelAnimationFrame(this.animationFrame)
            document.removeEventListener('focusin', this.boundFocusHandler)
            window.removeEventListener(
                'reset-schema-component-state',
                this.boundResetHandler,
            )
        },
    }
}
