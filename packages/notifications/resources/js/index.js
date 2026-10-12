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
