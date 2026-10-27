// Keep ARIA-disabled buttons focusable while suppressing activation before Alpine,
// Livewire, or the button's native submit behavior can handle the click.
// This window-lifetime listener also covers morphed and optimized buttons.
if (!window.filamentDisabledButtonClickListener) {
    window.filamentDisabledButtonClickListener = (event) => {
        const button =
            event.target instanceof Element
                ? event.target.closest(
                      'button:is(.fi-btn, .fi-icon-btn, .fi-badge, .fi-link, .fi-dropdown-list-item)[aria-disabled="true"], [data-prevent-disabled-clicks][aria-disabled="true"]',
                  )
                : null

        if (!button) {
            return
        }

        event.preventDefault()
        event.stopImmediatePropagation()
    }

    window.addEventListener(
        'click',
        window.filamentDisabledButtonClickListener,
        true,
    )
}
