const originalReplaceState = window.history.replaceState
const originalPushState = window.history.pushState

window.history.replaceState = function (state, unused, url) {
    if (state?.url instanceof URL) {
        state.url = state.url.toString()
    }

    // Skip duplicate replaceState calls
    try {
        if (JSON.stringify(state) === JSON.stringify(window.history.state)) {
            return
        }
    } catch (e) {
        // If comparison fails, proceed with update
    }

    originalReplaceState.call(window.history, state, unused, url)
}

window.history.pushState = function (state, unused, url) {
    if (state?.url instanceof URL) {
        state.url = state.url.toString()
    }

    originalPushState.call(window.history, state, unused, url)
}
