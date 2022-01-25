import '../css/app.css'

import Alpine from 'alpinejs'
import Chart from 'chart.js/auto'
import FormsAlpinePlugin from '../../../forms/dist/module.esm'
import Focus from '@alpinejs/focus'

Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(Focus)

Alpine.store('sidebar', {
    isOpen: false,

    close() {
        this.isOpen = false
    },

    open() {
        this.isOpen = true
    },
})

Chart.defaults.font.family = `'DM Sans', sans-serif`
Chart.defaults.backgroundColor = '#737373'

window.Alpine = Alpine
window.Chart = Chart

Alpine.start()
