import notification from './components/notification.js'
import {
    Action as NotificationAction,
    ActionGroup as NotificationActionGroup,
    Notification,
} from './notification.js'
import './broadcast-notifications.js'

window.FilamentNotificationAction = NotificationAction
window.FilamentNotificationActionGroup = NotificationActionGroup
window.FilamentNotification = Notification

document.addEventListener('alpine:init', () => {
    window.Alpine.data('notificationComponent', notification)
})

document.addEventListener('livewire:init', () => {
    Livewire.interceptRequest(({ request, onSuccess }) => {
        onSuccess(({ json }) => {
            if (
                !json.filamentNotifications ||
                json.components.some((component) => component.effects?.redirect)
            ) {
                return
            }

            // Wait for morphs and their component initialization, without relying
            // on any bundled component's element surviving the response.
            Promise.all(
                Array.from(
                    request.getActiveMessages(),
                    (message) =>
                        new Promise((resolve) => {
                            message.addInterceptor(({ onFinish }) =>
                                onFinish(resolve),
                            )
                        }),
                ),
            ).then(() =>
                window.Alpine.nextTick(() =>
                    Livewire.dispatch('notificationsSent'),
                ),
            )
        })
    })
})
