window.setUpFilamentBroadcastNotifications = ({
    $wire,
    broadcastChannel,
    event,
    onNotification,
}) => {
    let channel
    let isDestroyed = false

    const handleNotification = (notification) => {
        setTimeout(() => {
            if (isDestroyed) {
                return
            }

            onNotification(notification)
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
        window.removeEventListener('EchoLoaded', registerEchoListener)
        channel?.stopListening(event, handleNotification)
    })

    if (window.Echo) {
        registerEchoListener()
    }
}
