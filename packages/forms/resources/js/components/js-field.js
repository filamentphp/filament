const snapshot = (value) => JSON.parse(JSON.stringify(value))

export default function jsFieldFormComponent({
    state,
    renderer,
    rendererProps = {},
    configuration,
}) {
    let instance
    let destroyed = false
    let timer
    let pendingValue
    let initializationPhase = typeof renderer === 'string' ? 'import' : 'mount'
    const props = (state) => ({
        ...configuration,
        value: snapshot(state),
        config: snapshot(rendererProps),
    })

    return {
        state,
        hasError: false,

        updateRendererProps(value) {
            if (destroyed) return
            rendererProps = snapshot(value)
            this.updateRenderer()
        },

        reportError(phase, error) {
            console.error(
                `JS field ${phase} failed:`,
                {
                    statePath: this.$statePath,
                    renderer,
                },
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
            clearTimeout(timer)
            this.reportError(phase, error)
            const mounted = instance
            instance = undefined
            this.disposeRenderer(mounted)
            this.$refs.host.replaceChildren()
            this.hasError = true
        },

        updateRenderer() {
            if (destroyed || !instance || this.state === undefined) return
            try {
                Promise.resolve(instance.update(props(this.state))).catch(
                    (error) => this.fail('update', error),
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
            if (!renderer) {
                return
            }

            const mount =
                typeof renderer === 'string'
                    ? (
                          await import(
                              new URL(renderer, this.$el.ownerDocument.baseURI)
                                  .href
                          )
                      ).default
                    : renderer

            if (destroyed) {
                return
            }

            if (typeof mount !== 'function') {
                throw new TypeError(
                    'The renderer module must default-export a mount function.',
                )
            }

            initializationPhase = 'mount'

            const cancelPendingCommit = () => {
                clearTimeout(timer)
                pendingValue = undefined
            }
            const commit = () => {
                cancelPendingCommit()
                this.$wire.$commit()
            }

            this.$watch('state', (value) => {
                if (destroyed) {
                    return
                }

                if (JSON.stringify(value) !== pendingValue) {
                    cancelPendingCommit()
                }

                // A repeater path can disappear before Alpine destroys its host.
                if (value === undefined) {
                    return
                }

                this.updateRenderer()
            })

            const scope = this
            const utilities = {
                ...this.$schemaComponentMethods,
                // Preserve Livewire's proxy when frameworks deeply proxy `utilities`.
                get $wire() {
                    return scope.$wire
                },
                $callSchemaComponentMethod: this.$callSchemaComponentMethod,
                $get: this.$get,
                $set: this.$set,
                $statePath: this.$statePath,
                get $state() {
                    return scope.$state
                },
            }

            const mounted = await mount({
                host: this.$refs.host,
                props: {
                    ...props(this.state),
                    onChange: (value) => {
                        if (
                            destroyed ||
                            configuration.disabled ||
                            configuration.readOnly
                        ) {
                            return
                        }

                        const serialized = JSON.stringify(value)

                        if (serialized === JSON.stringify(this.state)) {
                            return
                        }

                        this.state = snapshot(value)
                        pendingValue = serialized
                        clearTimeout(timer)

                        if (
                            !configuration.isLive ||
                            configuration.isLiveOnBlur
                        ) {
                            return
                        }

                        timer = setTimeout(() => {
                            if (pendingValue === JSON.stringify(this.state)) {
                                commit()
                            }
                        }, configuration.debounce ?? 0)
                    },
                    onBlur: () => {
                        if (
                            !destroyed &&
                            configuration.isLive &&
                            configuration.isLiveOnBlur &&
                            pendingValue !== undefined
                        ) {
                            commit()
                        }
                    },
                },
                utilities,
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
            clearTimeout(timer)
            const mounted = instance
            instance = undefined
            this.disposeRenderer(mounted)
        },
    }
}
