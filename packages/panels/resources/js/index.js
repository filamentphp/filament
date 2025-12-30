import Mousetrap from '@danharrin/alpine-mousetrap'
import sidebar from './stores/sidebar.js'
import './dark-mode.js'
import './history-state.js'
import './scroll-sidebar.js'
import './unsaved-changes-alert.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Mousetrap)

    window.Alpine.store('sidebar', sidebar())
})
