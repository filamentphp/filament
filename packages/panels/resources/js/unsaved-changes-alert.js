window.setUpUnsavedDataChangesAlert = ({ body, livewireComponent, $wire }) => {
    const beforeUnloadHandler = (event) => {
        if (
            window.jsMd5(JSON.stringify($wire.data).replace(/\\/g, '')) ===
                $wire.savedDataHash ||
            $wire?.__instance?.effects?.redirect
        ) {
            return
        }

        event.preventDefault()
        event.returnValue = true
    }

    window.addEventListener('beforeunload', beforeUnloadHandler)

    $wire.__instance.addCleanup(() =>
        window.removeEventListener('beforeunload', beforeUnloadHandler),
    )
}

window.setUpSpaModeUnsavedDataChangesAlert = ({
    body,
    resolveLivewireComponentUsing,
    $wire,
}) => {
    const shouldPreventNavigation = () => {
        if ($wire?.__instance?.effects?.redirect) {
            return false
        }

        return (
            window.jsMd5(JSON.stringify($wire.data).replace(/\\/g, '')) !==
            $wire.savedDataHash
        )
    }

    const showUnsavedChangesAlert = () => {
        return confirm(body)
    }

    const navigateHandler = (event) => {
        if (typeof resolveLivewireComponentUsing() !== 'undefined') {
            if (!shouldPreventNavigation()) {
                return
            }

            if (showUnsavedChangesAlert()) {
                return
            }

            event.preventDefault()
        }
    }

    const beforeUnloadHandler = (event) => {
        if (!shouldPreventNavigation()) {
            return
        }

        event.preventDefault()
        event.returnValue = true
    }

    document.addEventListener('livewire:navigate', navigateHandler)
    window.addEventListener('beforeunload', beforeUnloadHandler)

    $wire.__instance.addCleanup(() => {
        document.removeEventListener('livewire:navigate', navigateHandler)
        window.removeEventListener('beforeunload', beforeUnloadHandler)
    })
}

window.setUpUnsavedActionChangesAlert = ({
    resolveLivewireComponentUsing,
    $wire,
}) => {
    const beforeUnloadHandler = (event) => {
        if (typeof resolveLivewireComponentUsing() === 'undefined') {
            return
        }

        if (
            ($wire.mountedActions ?? []).some(
                (mountedAction) => mountedAction.hasUnsavedChangesAlert ?? true,
            ) &&
            !$wire?.__instance?.effects?.redirect
        ) {
            event.preventDefault()
            event.returnValue = true

            return
        }
    }

    window.addEventListener('beforeunload', beforeUnloadHandler)

    $wire.__instance.addCleanup(() =>
        window.removeEventListener('beforeunload', beforeUnloadHandler),
    )
}
