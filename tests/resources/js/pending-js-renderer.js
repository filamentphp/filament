await new Promise((resolve) => {
    window.releaseRendererImport = resolve
})
if (window.failRendererImport) throw new Error('Expected import failure')

const mounting = new Promise((resolve) => {
    window.releaseRendererMount = resolve
})

export default async function mount({ host, props }) {
    const isField = 'value' in props
    const element = document.createElement(isField ? 'input' : 'p')
    if (isField) element.id = props.id
    const update = (props) => {
        if (isField) element.value = props.value ?? ''
        else
            element.textContent =
                props.configuration.message ?? props.configuration.description
    }
    update(props)
    host.append(element)

    await mounting
    if (window.failRendererMount) {
        element.remove()
        throw new Error('Expected mount failure')
    }

    return {
        update,
        destroy() {
            element.remove()
            window.rendererDisposals = (window.rendererDisposals ?? 0) + 1
        },
    }
}
