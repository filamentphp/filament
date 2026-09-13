const snapshot = (value) => JSON.parse(JSON.stringify(value))

export default function jsWidgetComponent({ renderer, rendererProps = {} }) {
    let instance
    let destroyed = false
    let initializationPhase = typeof renderer === 'string' ? 'import' : 'mount'
    const props = () => ({ config: snapshot(rendererProps) })

    return {
        hasError: false,

        updateRendererProps(value) {
            if (destroyed) return
            rendererProps = snapshot(value)
            this.updateRenderer()
        },

        reportError(phase, error) {
            console.error(
                'JS widget ' + phase + ' failed:',
                { renderer },
                error,
            )
        },

        disposeRenderer(mounted) {
            try {
                Promise.resolve(mounted?.destroy()).catch((error) =>
                    this.reportError('cleanup', error),
                )
            } catch (error) {
                this.reportError('cleanup', error)
            }
        },

        fail(phase, error) {
            if (destroyed) return
            destroyed = true
            this.reportError(phase, error)
            const mounted = instance
            instance = undefined
            this.disposeRenderer(mounted)
            this.$refs.host.replaceChildren()
            this.hasError = true
        },

        updateRenderer() {
            if (destroyed || !instance) return
            try {
                Promise.resolve(instance.update(props())).catch((error) =>
                    this.fail('update', error),
                )
            } catch (error) {
                this.fail('update', error)
            }
        },

        async init() {
            try {
                await this.mountRenderer()
            } catch (error) {
                this.fail(initializationPhase, error)
            }
        },

        async mountRenderer() {
            if (!renderer) return

            const mount =
                typeof renderer === 'string'
                    ? (
                          await import(
                              new URL(renderer, this.$el.ownerDocument.baseURI)
                                  .href
                          )
                      ).default
                    : renderer

            if (destroyed) return

            if (typeof mount !== 'function') {
                throw new TypeError(
                    'The renderer module must default-export a mount function.',
                )
            }

            initializationPhase = 'mount'
            const scope = this
            const mounted = await mount({
                host: this.$refs.host,
                props: props(),
                utilities: {
                    // Preserve Livewire's proxy when frameworks deeply proxy `utilities`.
                    get $wire() {
                        return scope.$wire
                    },
                },
            })

            if (
                typeof mounted?.update !== 'function' ||
                typeof mounted?.destroy !== 'function'
            ) {
                if (typeof mounted?.destroy === 'function')
                    this.disposeRenderer(mounted)
                throw new TypeError(
                    'The renderer must return update() and destroy() methods.',
                )
            }

            if (destroyed) {
                this.disposeRenderer(mounted)
                return
            }

            instance = mounted
            this.updateRenderer()
        },

        destroy() {
            destroyed = true
            const mounted = instance
            instance = undefined
            this.disposeRenderer(mounted)
        },
    }
}
