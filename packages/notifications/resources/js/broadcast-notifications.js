window.setUpFilamentBroadcastNotifications = ({
    $wire,
    broadcastChannel,
    event,
    onNotification,
    onNotifications,
}) => {
    let channel
    let isDestroyed = false
    let isHandlingNotifications = false
    let timeout
    const pendingNotifications = []

    const handlePendingNotifications = async () => {
        if (isHandlingNotifications) {
            return
        }

        isHandlingNotifications = true

        while (pendingNotifications.length && !isDestroyed) {
            try {
                await onNotification(pendingNotifications.shift())
            } catch {
                // Livewire reports failed requests. Continue with the queue.
            }
        }

        isHandlingNotifications = false
    }

    const handleNotification = (notification) => {
        pendingNotifications.push(notification)

        if (timeout) {
            return
        }

        timeout = setTimeout(() => {
            timeout = null

            if (isDestroyed) {
                return
            }

            if (onNotifications) {
                onNotifications(pendingNotifications.splice(0))

                return
            }

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
        clearTimeout(timeout)
        pendingNotifications.length = 0
        window.removeEventListener('EchoLoaded', registerEchoListener)
        channel?.stopListening(event, handleNotification)
    })

    if (window.Echo) {
        registerEchoListener()
    }
}
