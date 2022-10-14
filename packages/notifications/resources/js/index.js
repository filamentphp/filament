import NotificationComponentAlpinePlugin from './components/notification'
import {
    Action as NotificationAction,
    ActionGroup as NotificationActionGroup,
    Notification,
} from './Notification'

window.NotificationAction = NotificationAction
window.NotificationActionGroup = NotificationActionGroup
window.Notification = Notification

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(NotificationComponentAlpinePlugin)
})
