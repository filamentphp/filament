export default () => ({
    panelId: null,

    init() {
        document.addEventListener('livewire:navigate', () => this.close())

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

        this.syncAria()

        const observer = new MutationObserver(() => this.syncAria())

        // The floating UI plugin toggles the panel's `display` for open and close paths this
        // component does not drive itself (click-away, the plugin's own Escape handler), so observe
        // it directly to keep `aria-expanded` on the real trigger correct in every case. A Livewire
        // morph also strips the client-applied panel `id`, so observe that too and re-apply it.
        observer.observe(panel, {
            attributeFilter: ['id', 'style'],
        })

        // A Livewire morph re-renders the trigger from server HTML, stripping the client-applied
        // ARIA attributes, so observe them and re-apply. `syncAria()` only writes attributes whose
        // values have changed, so re-applying does not retrigger the observer in a loop.
        observer.observe(trigger, {
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

        this.setAttributeIfChanged(trigger, 'aria-haspopup', 'true')
        this.setAttributeIfChanged(trigger, 'aria-controls', this.panelId)
        this.setAttributeIfChanged(
            trigger,
            'aria-expanded',
            panel.style.display === 'block' ? 'true' : 'false',
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
        this.$refs.panel?.toggle(event)
        this.syncAria()
    },

    open(event) {
        this.$refs.panel?.open(event)
        this.syncAria()
    },

    close(event) {
        this.$refs.panel?.close(event)
        this.syncAria()
    },
})
