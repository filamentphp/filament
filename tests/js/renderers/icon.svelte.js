import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactIcon from '../../../packages/support/resources/js/react/Icon'
import VueIcon from '../../../packages/support/resources/js/vue/Icon.vue'
import SvelteIcon from './icon.svelte'

export default function mountIcons(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('div')
        host.append(container)
        const reportClick = (event) => {
            container.dataset.clicked = event.currentTarget.tagName
        }
        const prepare = ({ class: className, empty, changed, ...props }) => ({
            ...props,
            [framework === 'react' ? 'className' : 'class']: className,
            [framework === 'svelte' ? 'onclick' : 'onClick']: reportClick,
        })
        const svg = (render, changed) =>
            render(
                'svg',
                {
                    width: 24,
                    height: 24,
                    viewBox: '0 0 24 24',
                    fill: 'none',
                    stroke: 'currentColor',
                    'aria-hidden': 'true',
                },
                [
                    render('path', {
                        key: 'path',
                        d: changed ? 'M6 6l12 12M6 18L18 6' : 'M4 12h16',
                    }),
                ],
            )
        if (framework === 'react') {
            const root = createRoot(container)
            const update = (attributes) =>
                root.render(
                    createElement(
                        ReactIcon,
                        {
                            ...prepare(attributes),
                            ref: (element) => {
                                container.dataset.ref =
                                    element?.tagName ?? 'none'
                            },
                        },
                        attributes.empty
                            ? null
                            : svg(createElement, attributes.changed),
                    ),
                )
            update(attributes)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const props = shallowRef(attributes)
            const application = createApp({
                render: () =>
                    h(
                        VueIcon,
                        prepare(props.value),
                        props.value.empty
                            ? undefined
                            : { default: () => svg(h, props.value.changed) },
                    ),
            })
            application.mount(container)
            return {
                update: (attributes) => {
                    props.value = attributes
                },
                destroy: () => application.unmount(),
            }
        }
        const props = $state({
            ...prepare(attributes),
            empty: attributes.empty,
            changed: attributes.changed,
        })
        const component = mount(SvelteIcon, { target: container, props })
        return {
            update: (attributes) => {
                const next = {
                    ...prepare(attributes),
                    empty: attributes.empty,
                    changed: attributes.changed,
                }
                for (const key of Object.keys(props))
                    if (!(key in next)) delete props[key]
                Object.assign(props, next)
            },
            destroy: () => unmount(component),
        }
    })
    return {
        update: () =>
            renderers.forEach((renderer) =>
                renderer.update({
                    size: 'xl',
                    class: 'custom-icon',
                    role: 'img',
                    'aria-label': 'Saved',
                    'data-state': 'updated',
                    changed: true,
                }),
            ),
        image: () =>
            renderers.forEach((renderer) =>
                renderer.update({
                    src: '/icon-browser-test.svg',
                    alt: 'Saved image',
                    size: 'sm',
                }),
            ),
        reset: () =>
            renderers.forEach((renderer, index) =>
                renderer.update(cases[index]),
            ),
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
