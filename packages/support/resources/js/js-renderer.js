export const snapshot = (value) => JSON.parse(JSON.stringify(value))

export default function createJsRenderer({
    renderer,
    getBaseUrl,
    getContext,
    getProps,
    onError,
    onFailure,
    onDestroy = () => {},
}) {
    let instance
    let destroyed = false

    const dispose = (mounted) => {
        try {
            Promise.resolve(mounted?.destroy()).catch((error) =>
                onError('cleanup', error),
            )
        } catch (error) {
            onError('cleanup', error)
        }
    }

    const destroy = () => {
        if (destroyed) return
        destroyed = true
        onDestroy()
        const mounted = instance
        instance = undefined
        dispose(mounted)
    }

    const fail = (phase, error) => {
        if (destroyed) return
        destroyed = true
        onDestroy()
        onError(phase, error)
        const mounted = instance
        instance = undefined
        dispose(mounted)
        onFailure()
    }

    const update = () => {
        if (destroyed || !instance) return
        try {
            const props = getProps()
            if (props === undefined) return
            Promise.resolve(instance.update(props)).catch((error) =>
                fail('update', error),
            )
        } catch (error) {
            fail('update', error)
        }
    }

    return {
        async init() {
            let phase = typeof renderer === 'string' ? 'import' : 'mount'
            try {
                if (!renderer) return
                const mount =
                    typeof renderer === 'string'
                        ? (await import(new URL(renderer, getBaseUrl()).href))
                              .default
                        : renderer

                if (destroyed) return
                if (typeof mount !== 'function') {
                    throw new TypeError(
                        'The renderer module must default-export a mount function.',
                    )
                }

                phase = 'mount'
                const mounted = await mount(getContext())
                if (
                    typeof mounted?.update !== 'function' ||
                    typeof mounted?.destroy !== 'function'
                ) {
                    if (typeof mounted?.destroy === 'function') dispose(mounted)
                    throw new TypeError(
                        'The renderer must return update() and destroy() methods.',
                    )
                }

                if (destroyed) {
                    dispose(mounted)
                    return
                }

                instance = mounted
                update()
            } catch (error) {
                fail(phase, error)
            }
        },
        update,
        destroy,
    }
}
