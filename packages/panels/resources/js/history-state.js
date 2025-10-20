const originalReplaceState = window.history.replaceState
const originalPushState = window.history.pushState

window.history.replaceState = function (state, unused, url) {
    if (state?.url instanceof URL) {
        state.url = state.url.toString()
    }

    const targetUrl = url || state?.url || window.location.href
    const currentUrl = window.location.href

    // Always update if the URL has changed
    if (targetUrl !== currentUrl) {
        originalReplaceState.call(window.history, state, unused, url)
        return
    }

    // Skip duplicate `replaceState()` calls
    try {
        const currentState = window.history.state
        const stateChanged =
            JSON.stringify(state) !== JSON.stringify(currentState)

        if (stateChanged) {
            originalReplaceState.call(window.history, state, unused, url)
        }
    } catch (error) {
        // If comparison fails, proceed with the update
        originalReplaceState.call(window.history, state, unused, url)
    }
}

window.history.pushState = function (state, unused, url) {
    if (state?.url instanceof URL) {
        state.url = state.url.toString()
    }

    originalPushState.call(window.history, state, unused, url)
}
