window.setUpUnsavedDataChangesAlert = ({ body, livewireComponent, $wire }) => {
    window.addEventListener('beforeunload', (event) => {
        if (
            window.jsMd5(JSON.stringify($wire.data).replace(/\\/g, '')) ===
                $wire.savedDataHash ||
            $wire?.__instance?.effects?.redirect
        ) {
            return
        }

        event.preventDefault()
        event.returnValue = true
    })
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

    document.addEventListener('livewire:navigate', (event) => {
        if (typeof resolveLivewireComponentUsing() !== 'undefined') {
            if (!shouldPreventNavigation()) {
                return
            }

            if (showUnsavedChangesAlert()) {
                return
            }

            event.preventDefault()
        }
    })

    window.addEventListener('beforeunload', (event) => {
        if (!shouldPreventNavigation()) {
            return
        }

        event.preventDefault()
        event.returnValue = true
    })
}

window.setUpUnsavedActionChangesAlert = ({
    resolveLivewireComponentUsing,
    $wire,
}) => {
    window.addEventListener('beforeunload', (event) => {
        if (typeof resolveLivewireComponentUsing() === 'undefined') {
            return
        }

        if (
            ($wire.mountedActions?.length ?? 0) &&
            !$wire?.__instance?.effects?.redirect
        ) {
            event.preventDefault()
            event.returnValue = true

            return
        }
    })
}
