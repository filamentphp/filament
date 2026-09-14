import { createInertiaApp } from '@inertiajs/svelte'
import { flushSync, hydrate, mount, unmount } from 'svelte'

export function createRenderer(options) {
    return async function mountPage(element, externalNavigation, ready) {
        let application

        try {
            await createInertiaApp({
                ...options,
                id: element.id,
                externalNavigation,
                setup({ el, App, props }) {
                    if (!element.isConnected) return

                    application = (
                        el.dataset.serverRendered === 'true' ? hydrate : mount
                    )(App, {
                        target: el,
                        props,
                    })
                    flushSync()
                    ready()
                },
            })
        } catch (exception) {
            if (application) await unmount(application)
            throw exception
        }

        return () => application && unmount(application)
    }
}
