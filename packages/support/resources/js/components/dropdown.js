export default () => ({
    panelId: null,

    isOpen: false,

    observer: null,

    navigateListener: null,

    escapeListener: null,

    init() {
        this.navigateListener = () => this.close()

        document.addEventListener('livewire:navigate', this.navigateListener)

        // The floating UI plugin only adds its own window `keydown` listener when the panel
        // first opens, so registering here guarantees this listener runs before it and before
        // any enclosing modal's `Escape` handler.
        if (this.$refs.panel?.querySelector('[data-dropdown-escape]')) {
            this.escapeListener = (event) => this.handleEscape(event)

            window.addEventListener('keydown', this.escapeListener, true)
        }

        this.setUpAria()
    },

    destroy() {
        this.observer?.disconnect()
        this.observer = null

        document.removeEventListener('livewire:navigate', this.navigateListener)
        this.navigateListener = null

        window.removeEventListener('keydown', this.escapeListener, true)
        this.escapeListener = null
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

        this.syncAria()

        this.observer = new MutationObserver(() => {
            this.syncAria()
        })

        // The floating UI plugin toggles the panel's `display` for open and close paths this
        // component does not drive itself (click-away, the plugin's own Escape handler), so observe
        // it directly to keep `aria-expanded` on the real trigger correct in every case. A Livewire
        // morph also strips the client-applied panel `id`, so observe that too and re-apply it.
        this.observer.observe(panel, {
            attributeFilter: ['id', 'style'],
        })

        // A Livewire morph re-renders the trigger from server HTML, stripping the client-applied
        // ARIA attributes, so observe them and re-apply. `syncAria()` only writes attributes whose
        // values have changed, so re-applying does not retrigger the observer in a loop.
        this.observer.observe(trigger, {
            attributeFilter: [
                'aria-controls',
                'aria-expanded',
                'aria-haspopup',
            ],
        })

        this.observer.observe(
            this.$el.querySelector(':scope > .fi-dropdown-trigger'),
            {
                attributeFilter: ['aria-expanded'],
            },
        )
    },

    getTrigger() {
        return this.$el.querySelector(
            ':scope > .fi-dropdown-trigger button, :scope > .fi-dropdown-trigger a, :scope > .fi-dropdown-trigger [tabindex]',
        )
    },

    syncAria() {
        const trigger = this.getTrigger()
        const panel = this.$refs.panel

        if (!trigger || !panel) {
            return
        }

        if (panel.id !== this.panelId) {
            panel.id = this.panelId
        }

        const wasOpen = this.isOpen

        this.isOpen = panel.style.display === 'block'

        this.setAttributeIfChanged(trigger, 'aria-haspopup', 'true')
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

        // The floating UI plugin opens asynchronously, so focus only after the panel
        // actually becomes visible. Later attribute updates must not reset the search.
        if (this.isOpen && !wasOpen) {
            const autofocusable = panel.querySelector(
                '[data-dropdown-autofocus]',
            )

            autofocusable?.dispatchEvent(new CustomEvent('dropdown-autofocus'))
            autofocusable?.focus()
        }
    },

    setAttributeIfChanged(element, attribute, value) {
        if (element.getAttribute(attribute) !== value) {
            element.setAttribute(attribute, value)
        }
    },

    toggle(event) {
        this.$refs.panel?.toggle(event)
        this.syncAria()
    },

    open(event) {
        this.$refs.panel?.open(event)
        this.syncAria()
    },

    handleEscape(event) {
        if (event.key !== 'Escape') {
            return
        }

        const panel = this.$refs.panel

        // Only intercept for content that explicitly opts into staged Escape handling.
        // Other controls, such as searchable selects, need the original keydown event
        // to reach their own listeners before the floating UI plugin closes the panel.
        if (
            !panel ||
            panel.style.display !== 'block' ||
            event.target
                .closest('[data-dropdown-escape]')
                ?.closest('.fi-dropdown-panel') !== panel
        ) {
            return
        }

        // Content inside the panel may cancel this to keep the panel open, e.g. a search
        // input that clears its value first.
        const shouldClose = event.target.dispatchEvent(
            new CustomEvent('dropdown-escape', {
                bubbles: true,
                cancelable: true,
            }),
        )

        // Stop the floating UI plugin and any enclosing modal from also acting on this `Escape`.
        event.stopImmediatePropagation()

        if (!shouldClose) {
            return
        }

        this.close()

        this.getTrigger()?.focus()
    },

    close(event) {
        this.$refs.panel?.close(event)
        this.syncAria()
    },
})
