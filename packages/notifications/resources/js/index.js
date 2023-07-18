import NotificationComponentAlpinePlugin from './components/notification'
import {
    Action as NotificationAction,
    ActionGroup as NotificationActionGroup,
    Notification,
} from './Notification'

window.FilamentNotificationAction = NotificationAction
window.FilamentNotificationActionGroup = NotificationActionGroup
window.FilamentNotification = Notification

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(NotificationComponentAlpinePlugin)
})
