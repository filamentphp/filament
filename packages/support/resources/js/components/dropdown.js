window.addEventListener(
    'keydown',
    (event) => {
        if (event.key !== 'Escape' || !(event.target instanceof Element)) {
            return
        }

        let dropdown = event.target.closest('.fi-dropdown')

        while (dropdown) {
            if (window.Alpine.$data(dropdown)?.handleEscape(event)) {
                return
            }

            dropdown = dropdown.parentElement?.closest('.fi-dropdown')
        }
    },
    true,
)

export default ({ isMenu = true } = {}) => ({
    isMenu,

    panelId: null,

    triggerId: null,

    activeTrigger: null,

    isOpen: false,

    isClosing: false,

    shouldAutofocus: true,

    observer: null,

    navigateListener: null,

    resizeListener: null,

    resizeAnimationFrame: null,

    triggerKeydownListener: null,

    menuKeydownListener: null,

    menuClickListener: null,

    menuFocusinListener: null,

    menuFocusoutListener: null,

    activeMenuItem: null,

    activeMenuItemIndex: 0,

    menuFocusPosition: 'first',

    init() {
        this.navigateListener = () => this.close()
        this.resizeListener = () => {
            const previousTrigger = this.activeTrigger

            window.cancelAnimationFrame(this.resizeAnimationFrame)
            this.resizeAnimationFrame = window.requestAnimationFrame(() => {
                this.resizeAnimationFrame = null
                this.handleResize(previousTrigger)
            })
        }

        document.addEventListener('livewire:navigate', this.navigateListener)
        window.addEventListener('resize', this.resizeListener)

        this.setUpAria()

        this.triggerKeydownListener = (event) =>
            this.handleTriggerKeydown(event)
        this.getTriggerContainer()?.addEventListener(
            'keydown',
            this.triggerKeydownListener,
        )

        if (this.isMenu && this.$refs.panel) {
            this.menuKeydownListener = (event) => this.handleMenuKeydown(event)
            this.menuClickListener = (event) => this.handleMenuClick(event)
            this.menuFocusinListener = (event) => this.handleMenuFocusin(event)
            this.menuFocusoutListener = (event) =>
                this.handleMenuFocusout(event)

            this.$refs.panel.addEventListener(
                'keydown',
                this.menuKeydownListener,
            )
            this.$refs.panel.addEventListener('click', this.menuClickListener)
            this.$refs.panel.addEventListener(
                'focusin',
                this.menuFocusinListener,
            )
            this.$refs.panel.addEventListener(
                'focusout',
                this.menuFocusoutListener,
            )
        }
    },

    setUpAria() {
        const trigger = this.getTrigger()
        const panel = this.$refs.panel

        if (!trigger || !panel) {
            return
        }

        // Generate the panel `id` once per component instance, so `aria-controls`
        // stays stable for the lifetime of the page even when it is re-applied.
        this.panelId ??=
            panel.id ||
            'fi-dropdown-panel-' + Math.random().toString(36).slice(2, 10)

        if (this.isMenu) {
            this.triggerId = this.getTriggerId(trigger)

            this.setUpMenu()
        }

        // Livewire morph matches elements without a `wire:key` by their `id`. The server HTML has
        // no panel `id`, so if it were set directly, the keys would differ and every morph would
        // replace the panel, leaving the observer below attached to a detached element. Alpine
        // morph copies an Alpine-bound `id` onto the incoming HTML before comparing keys, so the
        // keys match and the existing panel is patched in place.
        Alpine.bind(panel, { id: this.panelId })

        this.syncAria()

        this.observer = new MutationObserver(() => {
            this.syncAria()
            this.setUpMenu()
            this.recoverMenuFocus()
        })

        // The floating UI plugin toggles the panel's `display` for open and close paths this
        // component does not drive itself (click-away, the plugin's own Escape handler), so observe
        // it directly to keep `aria-expanded` on the real trigger correct in every case.
        this.observer.observe(panel, {
            attributeFilter: this.isMenu
                ? ['aria-labelledby', 'style']
                : ['style'],
            childList: this.isMenu,
            subtree: this.isMenu,
        })

        // A Livewire morph re-renders the trigger from server HTML, stripping the client-applied
        // ARIA attributes, so observe them and re-apply. `syncAria()` only writes attributes whose
        // values have changed, so re-applying does not retrigger the observer in a loop.
        this.observer.observe(this.getTriggerContainer(), {
            attributeFilter: [
                'aria-controls',
                'aria-expanded',
                'aria-haspopup',
                'style',
            ],
            subtree: true,
        })
    },

    getTriggerContainer() {
        return this.$el.querySelector(':scope > .fi-dropdown-trigger')
    },

    getTriggers() {
        return Array.from(
            this.$el.querySelectorAll(
                ':scope > .fi-dropdown-trigger button, :scope > .fi-dropdown-trigger a, :scope > .fi-dropdown-trigger [tabindex]',
            ),
        )
    },

    getTrigger() {
        const triggerContainer = this.getTriggerContainer()
        const triggers = this.getTriggers()
        const visibleTrigger = triggers.find((trigger) =>
            this.isElementVisible(trigger),
        )

        if (
            this.activeTrigger?.isConnected &&
            triggerContainer?.contains(this.activeTrigger) &&
            (this.activeTrigger === visibleTrigger || !visibleTrigger)
        ) {
            return this.activeTrigger
        }

        if (this.activeTrigger) {
            this.activeTrigger.removeAttribute('aria-controls')
            this.activeTrigger.removeAttribute('aria-expanded')
            this.activeTrigger.removeAttribute('aria-haspopup')
        }

        this.activeTrigger = visibleTrigger ?? triggers[0]

        return this.activeTrigger
    },

    isElementVisible(element) {
        const style = window.getComputedStyle(element)

        return (
            style.display !== 'none' &&
            style.visibility !== 'hidden' &&
            element.getClientRects().length > 0
        )
    },

    setActiveTrigger(event) {
        const triggerContainer = this.getTriggerContainer()
        const eventTarget =
            event instanceof Element
                ? event
                : event?.target instanceof Element
                  ? event.target
                  : null
        const trigger = eventTarget?.closest('button, a, [tabindex]')

        if (!trigger || !triggerContainer?.contains(trigger)) {
            return
        }

        if (this.activeTrigger && this.activeTrigger !== trigger) {
            this.activeTrigger.removeAttribute('aria-controls')
            this.activeTrigger.removeAttribute('aria-expanded')
            this.activeTrigger.removeAttribute('aria-haspopup')
        }

        this.activeTrigger = trigger
    },

    getTriggerId(trigger) {
        if (!trigger.id) {
            Alpine.bind(trigger, {
                id:
                    'fi-dropdown-trigger-' +
                    Math.random().toString(36).slice(2, 10),
            })
        }

        return trigger.id
    },

    handleResize(previousTrigger) {
        const trigger = this.getTrigger()
        const panel = this.$refs.panel
        const isOpen = panel?.style.display === 'block'

        if (
            !trigger ||
            (trigger === previousTrigger &&
                (!isOpen || panel.trigger === trigger))
        ) {
            return
        }

        if (isOpen) {
            this.close()

            return
        }

        this.syncAria()
    },

    syncAria() {
        const trigger = this.getTrigger()
        const panel = this.$refs.panel

        if (!trigger || !panel) {
            return
        }

        const wasOpen = this.isOpen

        this.isOpen = panel.style.display === 'block'

        if (!this.isOpen) {
            this.activeMenuItem = null
        }

        this.setAttributeIfChanged(
            trigger,
            'aria-haspopup',
            this.isMenu ? 'menu' : 'true',
        )

        if (this.isMenu) {
            this.triggerId = this.getTriggerId(trigger)
            this.setAttributeIfChanged(panel, 'role', 'menu')
            this.setAttributeIfChanged(panel, 'aria-labelledby', this.triggerId)
        }

        this.setAttributeIfChanged(trigger, 'aria-controls', this.panelId)
        this.setAttributeIfChanged(
            trigger,
            'aria-expanded',
            this.isOpen ? 'true' : 'false',
        )

        // The floating UI plugin also writes `aria-expanded` onto the non-focusable
        // `.fi-dropdown-trigger` wrapper; remove it so the state only lives on the real control.
        this.$el
            .querySelector(':scope > .fi-dropdown-trigger')
            ?.removeAttribute('aria-expanded')

        // The floating UI plugin opens asynchronously, so notify content only after the panel
        // actually becomes visible. Later attribute updates must not repeat the notification.
        if (this.isOpen && !wasOpen) {
            panel.dispatchEvent(
                new CustomEvent('dropdown-opened', {
                    detail: {
                        shouldAutofocus: this.shouldAutofocus,
                    },
                }),
            )

            if (this.isMenu && this.shouldAutofocus) {
                this.focusMenuItem(this.menuFocusPosition)
            }
        }
    },

    setAttributeIfChanged(element, attribute, value) {
        if (element.getAttribute(attribute) !== value) {
            element.setAttribute(attribute, value)
        }
    },

    setUpMenu() {
        const panel = this.$refs.panel

        if (!this.isMenu || !panel) {
            return
        }

        panel.querySelectorAll('.fi-dropdown-list').forEach((list) => {
            if (list.closest('.fi-dropdown-panel') === panel) {
                this.setAttributeIfChanged(list, 'role', 'group')
            }
        })

        panel.querySelectorAll('.fi-dropdown-list-item').forEach((item) => {
            if (item.closest('.fi-dropdown-panel') !== panel) {
                return
            }

            if (!item.hasAttribute('role')) {
                item.setAttribute('role', 'menuitem')
            }

            item.setAttribute('tabindex', '-1')

            if (item.hasAttribute('disabled')) {
                item.setAttribute('aria-disabled', 'true')
                item.removeAttribute('disabled')
            }
        })
    },

    handleTriggerKeydown(event) {
        this.setActiveTrigger(event)

        if (
            event.target.closest('[role="menu"]') &&
            event.target.closest(
                '[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]',
            )
        ) {
            return
        }

        if (
            this.isMenu &&
            event.key === 'Tab' &&
            this.$refs.panel?.style.display === 'block'
        ) {
            this.closeMenuHierarchy()

            return
        }

        if (['Enter', ' '].includes(event.key)) {
            event.preventDefault()
            this.toggle(event)

            return
        }

        if (this.isMenu && ['ArrowDown', 'ArrowUp'].includes(event.key)) {
            event.preventDefault()
            this.open(event)
        }
    },

    toggle(event) {
        this.setActiveTrigger(event)
        this.isClosing = false
        this.shouldAutofocus = !(event instanceof MouseEvent)
        this.menuFocusPosition = event?.key === 'ArrowUp' ? 'last' : 'first'

        this.$refs.panel?.toggle(this.getTrigger() ?? event)
        this.syncAria()
    },

    open(event) {
        this.setActiveTrigger(event)
        this.isClosing = false
        this.shouldAutofocus = !(event instanceof MouseEvent)
        this.menuFocusPosition = event?.key === 'ArrowUp' ? 'last' : 'first'

        if (this.isMenu && this.$refs.panel?.style.display === 'block') {
            if (this.shouldAutofocus) {
                this.focusMenuItem(this.menuFocusPosition)
            }

            return
        }

        this.$refs.panel?.open(this.getTrigger() ?? event)
        this.syncAria()
    },

    handleEscape(event) {
        if (event.key !== 'Escape') {
            return
        }

        const panel = this.$refs.panel
        const trigger = this.$el.querySelector(':scope > .fi-dropdown-trigger')
        const isFromTrigger = trigger?.contains(event.target)

        if (
            !panel ||
            panel.style.display !== 'block' ||
            (!isFromTrigger && !panel.contains(event.target))
        ) {
            return false
        }

        // Content inside the panel may cancel this to keep the panel open, e.g. a search
        // input that clears its value first.
        const shouldClose =
            isFromTrigger ||
            event.target.dispatchEvent(
                new CustomEvent('dropdown-escape', {
                    bubbles: true,
                    cancelable: true,
                }),
            )

        // Stop the floating UI plugin and any enclosing modal from also acting on this `Escape`.
        event.stopImmediatePropagation()

        if (!shouldClose) {
            return true
        }

        this.close()

        this.getTrigger()?.focus()

        return true
    },

    getMenuItems() {
        const panel = this.$refs.panel

        if (!panel) {
            return []
        }

        return Array.from(
            panel.querySelectorAll(
                '[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]',
            ),
        ).filter(
            (item) =>
                item.closest('[role="menu"]') === panel &&
                this.isElementVisible(item),
        )
    },

    focusMenuItem(position) {
        const items = this.getMenuItems()

        if (!items.length) {
            return
        }

        items[position === 'last' ? items.length - 1 : 0].focus()
    },

    handleMenuFocusin(event) {
        const items = this.getMenuItems()
        const index = items.indexOf(event.target)

        if (index === -1) {
            return
        }

        this.activeMenuItem = event.target
        this.activeMenuItemIndex = index
    },

    handleMenuFocusout(event) {
        if (!event.relatedTarget) {
            return
        }

        const itemDropdown = event.target.closest('.fi-dropdown')

        if (
            itemDropdown &&
            itemDropdown !== this.$el &&
            event.relatedTarget instanceof Element &&
            event.relatedTarget.closest('.fi-dropdown') === itemDropdown
        ) {
            return
        }

        if (
            event.relatedTarget instanceof Element &&
            event.relatedTarget.closest('[role="menu"]') === this.$refs.panel
        ) {
            return
        }

        this.activeMenuItem = null
    },

    recoverMenuFocus() {
        if (
            !this.isMenu ||
            !this.isOpen ||
            !this.activeMenuItem ||
            this.activeMenuItem.isConnected ||
            document.activeElement !== document.body
        ) {
            return
        }

        const items = this.getMenuItems()
        const item = items[Math.min(this.activeMenuItemIndex, items.length - 1)]

        this.activeMenuItem = null

        item?.focus()
    },

    handleMenuKeydown(event) {
        if (!(event.target instanceof Element)) {
            return
        }

        const panel = this.$refs.panel
        const item = event.target.closest(
            '[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]',
        )

        if (!item || item.closest('[role="menu"]') !== panel) {
            return
        }

        if (
            item.getAttribute('aria-disabled') === 'true' &&
            ['Enter', ' ', 'ArrowRight'].includes(event.key)
        ) {
            event.preventDefault()

            return
        }

        const childDropdown = item.closest('.fi-dropdown')

        if (
            childDropdown &&
            childDropdown !== this.$el &&
            ['Enter', ' ', 'ArrowRight'].includes(event.key)
        ) {
            event.preventDefault()
            const childDropdownData = window.Alpine.$data(childDropdown)

            childDropdownData?.open(childDropdownData.getTrigger())

            return
        }

        if (event.key === ' ' && item.matches('a[href]')) {
            event.preventDefault()
            item.click()

            return
        }

        if (event.key === 'Tab') {
            this.closeMenuHierarchy()

            return
        }

        const items = this.getMenuItems()
        const index = items.indexOf(item)

        if (['ArrowDown', 'ArrowUp'].includes(event.key)) {
            event.preventDefault()

            const nextIndex =
                event.key === 'ArrowDown'
                    ? (index + 1) % items.length
                    : (index - 1 + items.length) % items.length

            items[nextIndex]?.focus()

            return
        }

        if (event.key === 'ArrowLeft') {
            const parentMenu = this.$el.parentElement?.closest('[role="menu"]')

            if (parentMenu) {
                event.preventDefault()
                this.close()
                this.getTrigger()?.focus()
            }

            return
        }
    },

    handleMenuClick(event) {
        if (!(event.target instanceof Element)) {
            return
        }

        const item = event.target.closest(
            '[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]',
        )

        if (!item || item.closest('[role="menu"]') !== this.$refs.panel) {
            return
        }

        if (item.getAttribute('aria-disabled') === 'true') {
            event.preventDefault()

            return
        }

        const childDropdown = item.closest('.fi-dropdown')

        if (childDropdown && childDropdown !== this.$el) {
            return
        }

        this.closeMenuHierarchy()
    },

    closeMenuHierarchy() {
        let dropdown = this.$el
        let trigger = this.getTrigger()

        while (dropdown?.matches('.fi-dropdown')) {
            const data = window.Alpine.$data(dropdown)

            if (!data?.isMenu) {
                break
            }

            trigger = data?.getTrigger() ?? trigger
            data?.close()
            dropdown = dropdown.parentElement?.closest('.fi-dropdown')
        }

        trigger?.focus()
    },

    close(event) {
        if (this.isClosing) {
            return
        }

        this.isClosing = true
        this.activeMenuItem = null

        this.$refs.panel
            ?.querySelectorAll('.fi-dropdown')
            .forEach((dropdown) => {
                window.Alpine.$data(dropdown)?.close(event)
            })

        this.$refs.panel?.close(event)
        this.syncAria()
    },

    destroy() {
        this.getTriggerContainer()?.removeEventListener(
            'keydown',
            this.triggerKeydownListener,
        )
        this.$refs.panel?.removeEventListener(
            'keydown',
            this.menuKeydownListener,
        )
        this.$refs.panel?.removeEventListener('click', this.menuClickListener)
        this.$refs.panel?.removeEventListener(
            'focusin',
            this.menuFocusinListener,
        )
        this.$refs.panel?.removeEventListener(
            'focusout',
            this.menuFocusoutListener,
        )

        this.triggerKeydownListener = null
        this.menuKeydownListener = null
        this.menuClickListener = null
        this.menuFocusinListener = null
        this.menuFocusoutListener = null

        this.observer?.disconnect()
        this.observer = null

        document.removeEventListener('livewire:navigate', this.navigateListener)
        window.removeEventListener('resize', this.resizeListener)
        window.cancelAnimationFrame(this.resizeAnimationFrame)
        this.navigateListener = null
        this.resizeListener = null
        this.resizeAnimationFrame = null
    },
})
