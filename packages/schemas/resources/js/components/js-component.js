import createJsRenderer, {
    snapshot,
} from '../../../../support/resources/js/js-renderer.js'

export default function jsSchemaComponent({
    renderer,
    rendererProps = {},
    configuration,
}) {
    let controller
    const props = () => ({
        ...configuration,
        config: snapshot(rendererProps),
    })

    return {
        hasError: false,

        updateRendererProps(value) {
            rendererProps = snapshot(value)
            controller?.update()
        },

        init() {
            const scope = this
            controller = createJsRenderer({
                renderer,
                getBaseUrl: () => this.$el.ownerDocument.baseURI,
                getProps: props,
                getContext: () => ({
                    host: this.$refs.host,
                    props: props(),
                    utilities: {
                        ...this.$schemaComponentMethods,
                        // Preserve Livewire's proxy when frameworks deeply proxy `utilities`.
                        get $wire() {
                            return scope.$wire
                        },
                        $callSchemaComponentMethod:
                            this.$callSchemaComponentMethod,
                        $get: this.$get,
                        $set: this.$set,
                        $statePath: this.$statePath,
                        get $state() {
                            return scope.$state
                        },
                    },
                }),
                onError: (phase, error) => {
                    console.error(
                        `JS schema component ${phase} failed:`,
                        { statePath: this.$statePath, renderer },
                        error,
                    )
                },
                onFailure: () => {
                    this.$refs.host.replaceChildren()
                    this.hasError = true
                },
            })

            return controller.init()
        },

        destroy() {
            controller?.destroy()
        },
    }
}
