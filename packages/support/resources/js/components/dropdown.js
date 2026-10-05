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

export default () => ({
    panelId: null,

    isOpen: false,

    isClosing: false,

    shouldAutofocus: true,

    observer: null,

    navigateListener: null,

    init() {
        this.navigateListener = () => this.close()

        document.addEventListener('livewire:navigate', this.navigateListener)

        this.setUpAria()
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

        // Livewire morph matches elements without a `wire:key` by their `id`. The server HTML has
        // no panel `id`, so if it were set directly, the keys would differ and every morph would
        // replace the panel, leaving the observer below attached to a detached element. Alpine
        // morph copies an Alpine-bound `id` onto the incoming HTML before comparing keys, so the
        // keys match and the existing panel is patched in place.
        Alpine.bind(panel, { id: this.panelId })

        this.syncAria()

        this.observer = new MutationObserver(() => {
            this.syncAria()
        })

        // The floating UI plugin toggles the panel's `display` for open and close paths this
        // component does not drive itself (click-away, the plugin's own Escape handler), so observe
        // it directly to keep `aria-expanded` on the real trigger correct in every case.
        this.observer.observe(panel, {
            attributeFilter: ['style'],
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
        }
    },

    setAttributeIfChanged(element, attribute, value) {
        if (element.getAttribute(attribute) !== value) {
            element.setAttribute(attribute, value)
        }
    },

    toggle(event) {
        this.isClosing = false
        this.shouldAutofocus = !(event instanceof MouseEvent)

        this.$refs.panel?.toggle(event)
        this.syncAria()
    },

    open(event) {
        this.isClosing = false
        this.shouldAutofocus = !(event instanceof MouseEvent)

        this.$refs.panel?.open(event)
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

    close(event) {
        if (this.isClosing) {
            return
        }

        this.isClosing = true

        this.$refs.panel
            ?.querySelectorAll('.fi-dropdown')
            .forEach((dropdown) => {
                window.Alpine.$data(dropdown)?.close(event)
            })

        this.$refs.panel?.close(event)
        this.syncAria()
    },

    destroy() {
        this.observer?.disconnect()
        this.observer = null

        document.removeEventListener('livewire:navigate', this.navigateListener)
        this.navigateListener = null
    },
})
