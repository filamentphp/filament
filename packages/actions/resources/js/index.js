import modals from './components/modals.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentActionModals', modals)
})
