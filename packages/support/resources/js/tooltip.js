// Dismiss any visible tooltip when `Escape` is pressed (WAI-ARIA APG Tooltip).
// Tippy has no built-in Escape handler, and Filament attaches tooltips to
// non-focusable hosts. Each mounted tippy popover stores its instance on the
// `[data-tippy-root]` element, so hide those directly — a top-level `hideAll()`
// would not work here, as `@ryangjchandler/alpine-tooltip` bundles its own tippy
// with a separate instance registry. Only acts — and stops the keypress from also
// closing a modal/dropdown — when a tooltip is actually visible; otherwise
// `Escape` propagates as normal. Focus is left untouched.
//
// Registered on `window` in the capture phase so it runs before
// `@awcodes/alpine-floating-ui`, which closes open dropdowns from its own
// `window` capture-phase `keydown` listener. Because this module evaluates at
// load — before the float plugin registers its listener on first open — this
// handler comes first among the `window` capture listeners, so
// `stopImmediatePropagation()` prevents both the dropdown's capture handler and
// any modal's bubble-phase handler from also acting on the same `Escape`.
window.addEventListener(
    'keydown',
    (event) => {
        if (event.key !== 'Escape') {
            return
        }

        let didHideTooltip = false

        document.querySelectorAll('[data-tippy-root]').forEach((popover) => {
            const tooltip = popover._tippy

            if (tooltip?.state.isVisible) {
                tooltip.hide()

                didHideTooltip = true
            }
        })

        if (didHideTooltip) {
            event.stopImmediatePropagation()
        }
    },
    true,
)
