import { createInertiaApp } from '@inertiajs/react'
import { Component, createElement, useLayoutEffect } from 'react'
import { createRoot, hydrateRoot } from 'react-dom/client'

class RenderBoundary extends Component {
    state = { failed: false }

    static getDerivedStateFromError() {
        return { failed: true }
    }

    componentDidCatch(exception) {
        this.props.fail(exception)
    }

    render() {
        return this.state.failed ? null : this.props.children
    }
}

function Mounted({ children, ready }) {
    useLayoutEffect(() => {
        ready()
    }, [ready])

    return children
}

export function createRenderer(options) {
    return async function mount(element, externalNavigation, ready, fail) {
        let root
        let disposeRouter

        const dispose = () => {
            try {
                root?.unmount()
            } finally {
                disposeRouter?.()
            }
        }

        try {
            await createInertiaApp({
                ...options,
                id: element.id,
                externalNavigation,
                setup({ el, App, props, dispose }) {
                    disposeRouter = dispose
                    if (!element.isConnected) return

                    const application = createElement(
                        RenderBoundary,
                        { fail },
                        createElement(
                            Mounted,
                            { ready },
                            createElement(App, props),
                        ),
                    )

                    if (el.dataset.serverRendered === 'true') {
                        root = hydrateRoot(el, application)
                    } else {
                        root = createRoot(el)
                        root.render(application)
                    }
                },
            })
        } catch (exception) {
            dispose()
            throw exception
        }

        return dispose
    }
}
