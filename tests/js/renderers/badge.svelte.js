import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactBadge from '../../../packages/support/resources/js/react/Badge'
import VueBadge from '../../../packages/support/resources/js/vue/Badge.vue'
import SvelteBadge from './badge.svelte'

export default function mountBadges(host, framework, cases) {
    const renderers = cases.map((initial) => {
        const container = document.createElement('div')
        host.append(container)
        const onElement = (element) => {
            container.element = element
        }
        const onClick = () => {
            container.dataset.clicks = String(
                Number(container.dataset.clicks ?? 0) + 1,
            )
        }
        const onDelete = () => {
            container.dataset.deletes = String(
                Number(container.dataset.deletes ?? 0) + 1,
            )
        }
        const prepare = ({ deletable, ...attributes }) => ({
            ...attributes,
            [framework === 'svelte' ? 'onclick' : 'onClick']: onClick,
            ...(deletable ? { onDelete } : {}),
        })
        if (framework === 'react') {
            const root = createRoot(container)
            const update = (attributes) =>
                root.render(
                    createElement(
                        ReactBadge,
                        { ...prepare(attributes), ref: onElement },
                        'Priority',
                    ),
                )
            update(initial)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const props = shallowRef(initial)
            const application = createApp({
                render: () =>
                    h(
                        VueBadge,
                        {
                            ...prepare(props.value),
                            ref: (component) => onElement(component?.element),
                        },
                        () => 'Priority',
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
        const props = $state({ ...prepare(initial), onElement })
        const component = mount(SvelteBadge, { target: container, props })
        return {
            update: (attributes) => {
                for (const name of Object.keys(props))
                    if (name !== 'onElement') delete props[name]
                Object.assign(props, prepare(attributes))
            },
            destroy: () => unmount(component),
        }
    })
    host.update = (attributes, index = 0) => renderers[index].update(attributes)
    host.destroy = () => renderers.forEach((renderer) => renderer.destroy())
    return { destroy: host.destroy }
}
