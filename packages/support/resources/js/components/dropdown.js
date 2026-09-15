// Both keys live on the root element rather than on the Alpine instance, so a
// second initialisation on the same element (Alpine re-running `initTree` after a
// Livewire morph) reuses the panel `id` and replaces the observer instead of
// competing with the first instance.
const PANEL_ID_KEY = '_fiDropdownPanelId'
const OBSERVER_KEY = '_fiDropdownObserver'

export default () => ({
    panelId: null,

    _onNavigate: null,

    init() {
        this._onNavigate = () => this.close()

        document.addEventListener('livewire:navigate', this._onNavigate)

        this.setUpAria()
    },

    destroy() {
        this.$el[OBSERVER_KEY]?.disconnect()
        delete this.$el[OBSERVER_KEY]

        if (this._onNavigate) {
            document.removeEventListener('livewire:navigate', this._onNavigate)
            this._onNavigate = null
        }
    },

    setUpAria() {
        const trigger = this.getTrigger()
        const panel = this.$refs.panel

        // Never leave two observers on the same element: an earlier instance that was
        // destroyed without a `destroy()` call would keep re-applying its own `id`, and
        // the two would trigger each other endlessly.
        this.$el[OBSERVER_KEY]?.disconnect()

        if (!trigger || !panel) {
            return
        }

        // Generate the panel `id` once per element, so `aria-controls` stays stable for
        // the lifetime of the page even when it is re-applied, and so a re-initialised
        // instance on the same element writes the same value instead of a new one.
        this.$el[PANEL_ID_KEY] ??=
            panel.id ||
            'fi-dropdown-panel-' + Math.random().toString(36).slice(2, 10)

        this.panelId = this.$el[PANEL_ID_KEY]

        this.syncAria()

        const observer = new MutationObserver(() => this.syncAria())

        this.$el[OBSERVER_KEY] = observer

        // The floating UI plugin toggles the panel's `display` for open and close paths this
        // component does not drive itself (click-away, the plugin's own Escape handler), so observe
        // it directly to keep `aria-expanded` on the real trigger correct in every case. A Livewire
        // morph also strips the client-applied panel `id`, so observe that too and re-apply it.
        observer.observe(panel, {
            attributeFilter: ['id', 'style'],
        })

        // A Livewire morph re-renders the trigger from server HTML, stripping the client-applied
        // ARIA attributes, so observe them and re-apply. `syncAria()` only writes attributes whose
        // values have changed, so re-applying does not retrigger the observer in a loop as long
        // as every instance on this element agrees on the `id` (see `PANEL_ID_KEY` above).
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
