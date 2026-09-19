import { createInertiaApp } from '@inertiajs/vue3'
import { createApp, createSSRApp, h } from 'vue'

export function createRenderer(options) {
    return async function mount(element, externalNavigation, ready, fail) {
        let application

        try {
            await createInertiaApp({
                ...options,
                id: element.id,
                externalNavigation,
                setup({ el, App, props, plugin }) {
                    if (!element.isConnected) return

                    const create =
                        el.dataset.serverRendered === 'true'
                            ? createSSRApp
                            : createApp

                    application = create({ render: () => h(App, props) })
                    application.use(plugin)
                    application.config.errorHandler = fail
                    application.mount(el)
                    ready()
                },
            })
        } catch (exception) {
            application?.unmount()
            throw exception
        }

        return () => application?.unmount()
    }
}
