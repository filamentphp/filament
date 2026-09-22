import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactBreadcrumbs from '../../../packages/support/resources/js/react/Breadcrumbs'
import VueBreadcrumbs from '../../../packages/support/resources/js/vue/Breadcrumbs.vue'
import SvelteBreadcrumbs from '../../../packages/support/resources/js/svelte/Breadcrumbs.svelte'
import SvelteCustomBreadcrumbs from './breadcrumbs-custom.svelte'

export default function mountBreadcrumbs(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('div')
        host.append(container)
        const onClick = (event) => {
            container.dataset.clicked = event.currentTarget.getAttribute('href')
            if (event.currentTarget.hasAttribute('data-intercept'))
                event.preventDefault()
        }
        const prepare = ({
            breadcrumbs,
            class: className,
            custom,
            ...props
        }) => ({
            ...props,
            [framework === 'react' ? 'className' : 'class']: className,
            breadcrumbs: breadcrumbs?.map((item) => ({
                ...item,
                [framework === 'svelte' ? 'onclick' : 'onClick']: onClick,
            })),
        })

        if (framework === 'react') {
            const root = createRoot(container)
            const update = (attributes) =>
                root.render(
                    createElement(ReactBreadcrumbs, {
                        ...prepare(attributes),
                        ...(attributes.custom
                            ? { separator: '/', separatorRtl: '\\' }
                            : {}),
                        ref: (navigation) => {
                            if (navigation) container.dataset.ready = 'true'
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
                    h(
                        VueBreadcrumbs,
                        prepare(props.value),
                        props.value.custom
                            ? { separator: () => '/', separatorRtl: () => '\\' }
                            : {},
                    ),
            })
            application.mount(container)
            container.dataset.ready = 'true'
            return {
                update: (attributes) => {
                    props.value = attributes
                },
                destroy: () => application.unmount(),
            }
        }
        const props = $state(prepare(attributes))
        const component = mount(
            attributes.custom ? SvelteCustomBreadcrumbs : SvelteBreadcrumbs,
            { target: container, props },
        )
        container.dataset.ready = 'true'
        return {
            update: (attributes) => {
                const next = prepare(attributes)
                for (const key of Object.keys(props))
                    if (!(key in next)) delete props[key]
                Object.assign(props, next)
            },
            destroy: () => unmount(component),
        }
    })
    return {
        update() {
            renderers[0].update({
                'aria-label': 'Updated location',
                dir: 'rtl',
                class: 'custom-breadcrumbs',
                breadcrumbs: [
                    {
                        label: 'Updated home',
                        href: '#updated',
                        'data-intercept': true,
                    },
                    { label: 'Former link' },
                    {
                        label: 'New current',
                        href: '#current',
                        target: '_blank',
                        rel: 'noopener',
                    },
                ],
            })
        },
        reset() {
            renderers[0].update({})
        },
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
