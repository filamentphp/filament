export default function inertiaHost({
    renderer,
    spa,
    spaUrlExceptions,
    rememberScope,
    rememberKey,
}) {
    let container
    let dispose
    let originalMarkup
    let stopped = false
    let failed = false

    const unmount = () => {
        const callback = dispose
        dispose = undefined
        callback?.()
    }

    const stop = () => {
        if (stopped) return

        stopped = true
        unmount()

        // Cache the original SSR tree, not a hydrated tree with stale initial props.
        if (container) container.innerHTML = originalMarkup
    }

    return {
        init() {
            container = this.$el.querySelector('[data-inertia-container]')
            originalMarkup = container.innerHTML

            const root = container.querySelector('#filament-inertia')
            const content = container.querySelector('[data-inertia-content]')
            const stage = container.querySelector('[data-inertia-stage]')
            const loading = container.querySelector('[data-inertia-loading]')
            const error = container.querySelector('[data-inertia-error]')

            const fail = (exception) => {
                if (stopped || failed) return

                failed = true
                loading.hidden = true
                content.setAttribute('inert', '')
                stage.setAttribute('aria-busy', 'false')
                error.hidden = false
                queueMicrotask(unmount)
                console.error('Unable to mount the Inertia page.', exception)
            }

            const excludedUrls = spaUrlExceptions.map(
                (pattern) =>
                    new RegExp(
                        '^' +
                            pattern
                                .split('*')
                                .map((part) =>
                                    part.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'),
                                )
                                .join('.*') +
                            '$',
                        'su',
                    ),
            )

            container.querySelector('[data-inertia-retry]').onclick = () =>
                location.reload()

            document.addEventListener('livewire:navigating', stop)

            const storageKey = (key) =>
                `filament-inertia:${rememberScope}:${rememberKey}:${key}`

            requestAnimationFrame(() =>
                setTimeout(async () => {
                    if (stopped) return

                    try {
                        const { default: mount } = await import(renderer)

                        if (stopped) return

                        dispose = await mount(
                            root,
                            {
                                navigate(url) {
                                    const destination = new URL(
                                        url,
                                        location.href,
                                    )

                                    if (
                                        spa &&
                                        destination.origin ===
                                            location.origin &&
                                        !excludedUrls.some((pattern) =>
                                            pattern.test(destination.href),
                                        )
                                    ) {
                                        Livewire.navigate(url)
                                    } else {
                                        location.assign(url)
                                    }
                                },
                                remember(data, key) {
                                    sessionStorage.setItem(
                                        storageKey(key),
                                        JSON.stringify(data),
                                    )
                                },
                                restore(key) {
                                    const data = sessionStorage.getItem(
                                        storageKey(key),
                                    )

                                    return data === null
                                        ? undefined
                                        : JSON.parse(data)
                                },
                            },
                            () => {
                                if (stopped || failed) return

                                loading.hidden = true
                                content.removeAttribute('inert')
                                stage.setAttribute('aria-busy', 'false')
                            },
                            fail,
                        )

                        if (stopped || failed) unmount()
                    } catch (exception) {
                        fail(exception)
                    }
                }, 0),
            )
        },
        destroy() {
            document.removeEventListener('livewire:navigating', stop)
            stop()
        },
    }
}
