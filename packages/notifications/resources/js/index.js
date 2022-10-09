import NotificationComponentAlpinePlugin from './components/notification'
import {
    Action as NotificationAction,
    ActionGroup as NotificationActionGroup,
    Notification,
} from './Notification'

window.NotificationAction = NotificationAction
window.NotificationActionGroup = NotificationActionGroup
window.Notification = Notification

export default (Alpine) => {
    Alpine.plugin(NotificationComponentAlpinePlugin)
}

export {
    NotificationAction,
    NotificationActionGroup,
    Notification,
    NotificationComponentAlpinePlugin,
}
