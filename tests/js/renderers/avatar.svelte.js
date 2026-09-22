import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactAvatar from '../../../packages/support/resources/js/react/Avatar'
import VueAvatar from '../../../packages/support/resources/js/vue/Avatar.vue'
import SvelteAvatar from '../../../packages/support/resources/js/svelte/Avatar.svelte'

export default function mountAvatars(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('span')
        host.append(container)
        const reportLoad = () => container.setAttribute('data-loaded', 'true')

        if (framework === 'react') {
            const root = createRoot(container)
            const update = ({ class: className, ...props }) =>
                root.render(
                    createElement(ReactAvatar, {
                        ...props,
                        className,
                        onLoad: reportLoad,
                        ref: (image) => {
                            if (image instanceof HTMLImageElement) {
                                container.setAttribute('data-image-ref', 'true')
                            }
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
                    h(VueAvatar, { ...props.value, onLoad: reportLoad }),
            })
            application.mount(container)
            return {
                update: (attributes) => {
                    props.value = attributes
                },
                destroy: () => application.unmount(),
            }
        }

        const props = $state({ ...attributes, onload: reportLoad })
        const component = mount(SvelteAvatar, { target: container, props })
        return {
            update: (attributes) => {
                for (const key of Object.keys(props)) {
                    if (key !== 'onload' && !(key in attributes))
                        delete props[key]
                }
                Object.assign(props, attributes)
            },
            destroy: () => unmount(component),
        }
    })

    return {
        update() {
            renderers.forEach((renderer, index) =>
                renderer.update({
                    ...cases[index],
                    alt: 'Updated portrait',
                    circular: false,
                    size: 'avatar-custom-size',
                    class: 'avatar-custom-theme',
                }),
            )
        },
        reset() {
            renderers.forEach((renderer, index) =>
                renderer.update({ src: cases[index].src }),
            )
        },
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
