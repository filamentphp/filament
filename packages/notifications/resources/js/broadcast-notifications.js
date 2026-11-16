window.setUpFilamentBroadcastNotifications = ({
    $wire,
    broadcastChannel,
    event,
    onNotification,
}) => {
    let channel
    let isDestroyed = false
    let isHandlingNotifications = false
    let pendingNotifications = []

    // Notifications are handled one at a time, so that a burst delivered
    // together (for example, after a frozen tab resumes) is not bundled into
    // a single Livewire request that exceeds `payload.max_calls`.
    const handlePendingNotifications = async () => {
        if (isHandlingNotifications) {
            return
        }

        isHandlingNotifications = true

        while (pendingNotifications.length && !isDestroyed) {
            try {
                await onNotification(pendingNotifications.shift())
            } catch {
                // Livewire reports failed requests itself, so carry on with the
                // remaining notifications.
            }
        }

        isHandlingNotifications = false
    }

    const handleNotification = (notification) => {
        setTimeout(() => {
            if (isDestroyed) {
                return
            }

            pendingNotifications.push(notification)

            handlePendingNotifications()
        }, 500)
    }

    const registerEchoListener = () => {
        if (channel || isDestroyed) {
            return
        }

        channel = window.Echo.private(broadcastChannel)
        channel.listen(event, handleNotification)
    }

    window.addEventListener('EchoLoaded', registerEchoListener)

    $wire.__instance.addCleanup(() => {
        isDestroyed = true
        pendingNotifications = []
        window.removeEventListener('EchoLoaded', registerEchoListener)
        channel?.stopListening(event, handleNotification)
    })

    if (window.Echo) {
        registerEchoListener()
    }
}
