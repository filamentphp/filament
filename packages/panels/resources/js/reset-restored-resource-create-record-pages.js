document.addEventListener('livewire:init', () => {
    // In SPA mode, navigating back to a create page restores it from Livewire's
    // history cache, including an `isCreating` state of `true` from before the
    // post-creation redirect, which blocks the form from being submitted again.
    // A fresh page load always renders `isCreating` as `false`, so a create
    // page component can only initialize with `isCreating` as `true` if it was
    // restored from the cache. Outside of SPA mode, this cannot happen, since
    // Livewire's `DisableBackButtonCacheMiddleware` sends a `no-store` header
    // that excludes its pages from the browser's back/forward cache. Assigning
    // to `$wire` only mutates the client-side state, which is synced with the
    // server during the next request, such as the next creation attempt.
    window.Livewire.hook('component.init', ({ component }) => {
        if (
            !component.el?.classList?.contains('fi-resource-create-record-page')
        ) {
            return
        }

        if (component.snapshot?.data?.isCreating !== true) {
            return
        }

        component.$wire.isCreating = false
    })
})
