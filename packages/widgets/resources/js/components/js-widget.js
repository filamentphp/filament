import createJsRenderer, {
    snapshot,
} from '../../../../support/resources/js/js-renderer.js'

export default function jsWidgetComponent({ renderer, rendererProps = {} }) {
    let lifecycle
    let destroyed = false
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

        updateRenderer() {
            lifecycle?.update()
        },

        async init() {
            lifecycle = createJsRenderer({
                renderer,
                getBaseUrl: () => this.$el.ownerDocument.baseURI,
                getContext: () => this.getRendererContext(),
                getProps: props,
                onError: (phase, error) => this.reportError(phase, error),
                onFailure: () => {
                    this.$refs.host.replaceChildren()
                    this.hasError = true
                },
                onDestroy: () => {
                    destroyed = true
                },
            })
            await lifecycle.init()
        },

        getRendererContext() {
            const scope = this

            return {
                host: this.$refs.host,
                props: props(),
                utilities: {
                    // Preserve Livewire's proxy when frameworks deeply proxy `utilities`.
                    get $wire() {
                        return scope.$wire
                    },
                },
            }
        },

        destroy() {
            lifecycle?.destroy()
        },
    }
}
