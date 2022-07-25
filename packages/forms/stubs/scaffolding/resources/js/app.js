import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm'
import NotificationsAlpinePlugin from '../../vendor/filament/notifications/dist/module.esm'

Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(NotificationsAlpinePlugin)

window.Alpine = Alpine

Alpine.start()
