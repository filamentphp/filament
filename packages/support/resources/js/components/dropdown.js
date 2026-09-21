export default () => ({
    panelId: null,

    isOpen: false,

    observer: null,

    navigateListener: null,

    escapeListener: null,

    isAutofocusPending: false,

    init() {
        this.navigateListener = () => this.close()

        document.addEventListener('livewire:navigate', this.navigateListener)

        // The floating UI plugin only adds its own window `keydown` listener when the panel
        // first opens, so registering here guarantees this listener runs before it and before
        // any enclosing modal's `Escape` handler.
        this.escapeListener = (event) => this.handleEscape(event)

        window.addEventListener('keydown', this.escapeListener, true)

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
            this.focusAutofocusable()
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
    },

    setAttributeIfChanged(element, attribute, value) {
        if (element.getAttribute(attribute) !== value) {
            element.setAttribute(attribute, value)
        }
    },

    toggle(event) {
        const wasOpen = this.$refs.panel?.style.display === 'block'

        this.$refs.panel?.toggle(event)
        this.syncAria()

        if (!wasOpen) {
            this.autofocus()
        }
    },

    open(event) {
        const wasOpen = this.$refs.panel?.style.display === 'block'

        this.$refs.panel?.open(event)
        this.syncAria()

        if (!wasOpen) {
            this.autofocus()
        }
    },

    autofocus() {
        // The floating UI plugin makes the panel visible asynchronously and focus only works
        // on visible elements, so the `MutationObserver` on the panel's `style` retries this
        // once `display` actually changes.
        this.isAutofocusPending = true

        this.focusAutofocusable()
    },

    focusAutofocusable() {
        if (!this.isAutofocusPending) {
            return
        }

        const panel = this.$refs.panel

        if (!panel || panel.style.display !== 'block') {
            return
        }

        this.isAutofocusPending = false

        const autofocusable = panel.querySelector('[data-dropdown-autofocus]')

        if (!autofocusable) {
            return
        }

        autofocusable.dispatchEvent(new CustomEvent('dropdown-autofocus'))

        autofocusable.focus()
    },

    handleEscape(event) {
        if (event.key !== 'Escape') {
            return
        }

        // An ancestor's listener may run first. Route to the closest open owner before
        // consuming the event, including panels moved outside their dropdown by teleporting.
        let ownerElement = event.target.closest(
            '.fi-dropdown, .fi-dropdown-panel',
        )

        while (ownerElement) {
            const owner = Alpine.$data(ownerElement)

            if (owner.$refs.panel?.style.display === 'block') {
                if (owner.$refs.panel !== this.$refs.panel) {
                    owner.handleEscape(event)

                    return
                }

                break
            }

            ownerElement = ownerElement.parentElement?.closest(
                '.fi-dropdown, .fi-dropdown-panel',
            )
        }

        const panel = this.$refs.panel

        if (!panel || panel.style.display !== 'block') {
            return
        }

        // A teleported panel is not a descendant of its dropdown.
        if (!this.$el.contains(event.target) && !panel.contains(event.target)) {
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
        this.isAutofocusPending = false

        this.$refs.panel?.close(event)
        this.syncAria()
    },
})
