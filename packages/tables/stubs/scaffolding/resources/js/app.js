import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm'
import Trap from '@alpinejs/trap'

Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(Trap)

window.Alpine = Alpine

Alpine.start()
