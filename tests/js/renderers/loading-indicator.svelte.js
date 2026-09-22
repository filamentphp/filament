import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactLoadingIndicator from '../../../packages/support/resources/js/react/LoadingIndicator'
import VueLoadingIndicator from '../../../packages/support/resources/js/vue/LoadingIndicator.vue'
import SvelteLoadingIndicator from '../../../packages/support/resources/js/svelte/LoadingIndicator.svelte'

export default function mountIndicators(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('span')
        host.append(container)
        const reportClick = () => container.setAttribute('data-clicked', 'true')
        if (framework === 'react') {
            const root = createRoot(container)
            const update = ({ class: className, ...props }) =>
                root.render(
                    createElement(ReactLoadingIndicator, {
                        ...props,
                        className,
                        onClick: reportClick,
                        ref: (element) => {
                            if (element instanceof SVGSVGElement)
                                container.setAttribute('data-svg-ref', 'true')
                        },
                    }),
                )
            update(attributes)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const props = shallowRef(attributes)
            const application = createApp({
                render: () =>
                    h(VueLoadingIndicator, {
                        ...props.value,
                        onClick: reportClick,
                    }),
            })
            application.mount(container)
            return {
                update: (attributes) => {
                    props.value = attributes
                },
                destroy: () => application.unmount(),
            }
        }
        const props = $state({ ...attributes, onclick: reportClick })
        const component = mount(SvelteLoadingIndicator, {
            target: container,
            props,
        })
        return {
            update: (attributes) => {
                for (const key of Object.keys(props))
                    if (key !== 'onclick' && !(key in attributes))
                        delete props[key]
                Object.assign(props, attributes)
            },
            destroy: () => unmount(component),
        }
    })
    return {
        update: () =>
            renderers.forEach((renderer) =>
                renderer.update({
                    size: 'xl',
                    class: 'custom-indicator',
                    viewBox: '0 0 32 32',
                    fill: 'red',
                    'aria-hidden': false,
                    role: 'img',
                    'aria-label': 'Loading updated content',
                    'data-state': 'updated',
                }),
            ),
        reset: () => renderers.forEach((renderer) => renderer.update({})),
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
