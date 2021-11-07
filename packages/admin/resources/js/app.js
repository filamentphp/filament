import '../css/app.css'

import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../../forms/dist/module.esm'
import Trap from '@alpinejs/trap'

Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(Trap)

window.Alpine = Alpine

Alpine.start()
