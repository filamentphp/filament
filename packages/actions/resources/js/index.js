import modals from './components/modals.js'
import eventListener from '../../../support/resources/js/utilities/event-listener.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentActionModals', modals)
    window.Alpine.data('filamentActionEventListener', eventListener)
})
