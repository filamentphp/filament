import '../css/app.css'

import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../../forms/dist/module.esm'
import Trap from '@alpinejs/trap'

Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(Trap)

Alpine.store('sidebar', {
    isOpen: false,

    close() {
        this.isOpen = false
    },

    open() {
        this.isOpen = true
    },
})

window.Alpine = Alpine

Alpine.start()
