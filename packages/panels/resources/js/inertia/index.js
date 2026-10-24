import inertiaHost from './host.js'

const register = () => Alpine.data('inertiaHost', inertiaHost)

if (window.Alpine) {
    register()
} else {
    document.addEventListener('alpine:init', register, { once: true })
}
