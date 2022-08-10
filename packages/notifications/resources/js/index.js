import NotificationComponentAlpinePlugin from './components/notification'
import SupportAlpinePlugin from '../../../support/dist/module.esm'

export default (Alpine) => {
    Alpine.plugin(NotificationComponentAlpinePlugin)
    Alpine.plugin(SupportAlpinePlugin)
}

export { NotificationComponentAlpinePlugin, SupportAlpinePlugin }
