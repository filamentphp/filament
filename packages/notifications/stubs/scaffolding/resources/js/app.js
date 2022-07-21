import Alpine from 'alpinejs'
import NotificationsAlpinePlugin from '../../vendor/filament/notifications/dist/module.esm'

Alpine.plugin(NotificationsAlpinePlugin)

window.Alpine = Alpine

Alpine.start()
