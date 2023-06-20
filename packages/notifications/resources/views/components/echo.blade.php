@props([
    'channel',
])

<div
    x-data="{}"
    x-init="
        window.addEventListener('EchoLoaded', () => {
            window.Echo.private(@js($channel))
                .notification((notification) => {
                    setTimeout(
                        () => $wire.handleBroadcastNotification(notification),
                        500,
                    )
                })
                .listen('.database-notifications.sent', () => {
                    setTimeout(() => $wire.call('$refresh'), 500)
                })
        })

        if (window.Echo) {
            window.dispatchEvent(new CustomEvent('EchoLoaded'))
        }
    "
    {{ $attributes }}
></div>
