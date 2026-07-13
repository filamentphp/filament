export default () => ({
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

        if (!panel.id) {
            panel.id =
                'fi-dropdown-panel-' + Math.random().toString(36).slice(2, 10)
        }

        trigger.setAttribute('aria-haspopup', 'true')
        trigger.setAttribute('aria-controls', panel.id)

        this.syncAria()

        // The floating UI plugin toggles the panel's `display` for open and close paths this
        // component does not drive itself (click-away, the plugin's own Escape handler), so observe
        // it directly to keep `aria-expanded` on the real trigger correct in every case.
        new MutationObserver(() => this.syncAria()).observe(panel, {
            attributeFilter: ['style'],
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

        trigger.setAttribute(
            'aria-expanded',
            panel.style.display === 'block' ? 'true' : 'false',
        )

        // The floating UI plugin also writes `aria-expanded` onto the non-focusable
        // `.fi-dropdown-trigger` wrapper; remove it so the state only lives on the real control.
        this.$el
            .querySelector(':scope > .fi-dropdown-trigger')
            ?.removeAttribute('aria-expanded')
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
