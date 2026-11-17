await new Promise((resolve) => {
    window.releaseAsyncDownload = resolve
})

export default function () {
    let finishInitialization
    let isDestroyed = false
    const report = (event) =>
        window.dispatchEvent(
            new CustomEvent('async-control-event', { detail: event }),
        )
    const onProbe = () => report('probe')

    return {
        value: '',

        init() {
            window.addEventListener('async-control-probe', onProbe)
            report('started')

            return new Promise((resolve) => {
                finishInitialization = resolve
                window.releaseAsyncInitialization = resolve
            }).then(() => {
                if (isDestroyed) {
                    return
                }

                this.value = 'Ready to edit'
                report('ready')
                this.$dispatch('async-component-ready')
            })
        },

        destroy() {
            isDestroyed = true
            window.removeEventListener('async-control-probe', onProbe)
            report('destroyed')
            finishInitialization?.()
        },
    }
}
