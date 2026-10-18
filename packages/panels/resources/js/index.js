import Mousetrap from '@danharrin/alpine-mousetrap'
import globalSearch from './components/global-search.js'
import sidebar from './components/sidebar.js'
import sidebarBackground from './components/sidebar-background.js'
import sidebarTooltip from './components/sidebar-tooltip.js'
import tenantMenu from './components/tenant-menu.js'
import sidebarStore from './stores/sidebar.js'
import './dark-mode.js'
import './error-notifications.js'
import './reset-restored-resource-create-record-pages.js'
import './scroll-sidebar.js'
import './unsaved-changes-alert.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Mousetrap)

    window.Alpine.data('filamentGlobalSearch', globalSearch)
    window.Alpine.data('filamentSidebar', sidebar)
    window.Alpine.data('filamentSidebarBackground', sidebarBackground)
    window.Alpine.data('filamentSidebarTooltip', sidebarTooltip)
    window.Alpine.data('filamentTenantMenu', tenantMenu)
    window.Alpine.store('sidebar', sidebarStore())
})
