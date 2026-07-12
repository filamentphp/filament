// Dismiss any visible tooltip when `Escape` is pressed (WAI-ARIA APG Tooltip).
// Tippy has no built-in Escape handler, and Filament attaches tooltips to
// non-focusable hosts. Each mounted tippy popover stores its instance on the
// `[data-tippy-root]` element, so hide those directly — a top-level `hideAll()`
// would not work here, as `@ryangjchandler/alpine-tooltip` bundles its own tippy
// with a separate instance registry. Only acts — and stops the keypress from also
// closing a modal/dropdown — when a tooltip is actually visible; otherwise
// `Escape` bubbles as normal. Focus is left untouched.
document.addEventListener('keydown', (event) => {
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
        event.stopPropagation()
    }
})
