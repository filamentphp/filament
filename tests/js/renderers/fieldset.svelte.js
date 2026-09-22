import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactFieldset from '../../../packages/support/resources/js/react/Fieldset'
import VueFieldset from '../../../packages/support/resources/js/vue/Fieldset.vue'
import SvelteFieldset from './fieldset.svelte'

export default function mountFieldsets(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('div')
        host.append(container)
        const onInput = (event) => {
            container.dataset.input = event.target.value
            container.dataset.eventTarget = event.currentTarget.tagName
        }
        const prepare = ({ class: className, rich, ...props }) => ({
            ...props,
            [framework === 'react' ? 'className' : 'class']: className,
            [framework === 'svelte' ? 'oninput' : 'onInput']: onInput,
        })
        if (framework === 'react') {
            const root = createRoot(container)
            const update = (attributes) =>
                root.render(
                    createElement(
                        ReactFieldset,
                        {
                            ...prepare(attributes),
                            ...(attributes.rich
                                ? {
                                      label: createElement(
                                          'strong',
                                          null,
                                          'Address',
                                      ),
                                  }
                                : {}),
                            ref: (fieldset) => {
                                if (fieldset)
                                    container.dataset.ready = String(
                                        fieldset instanceof HTMLFieldSetElement,
                                    )
                            },
                        },
                        createElement('input', {
                            'aria-label': 'Street',
                            name: 'street',
                        }),
                    ),
                )
            update(attributes)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const props = shallowRef(attributes)
            const application = createApp({
                render: () =>
                    h(VueFieldset, prepare(props.value), {
                        default: () =>
                            h('input', {
                                'aria-label': 'Street',
                                name: 'street',
                            }),
                        ...(props.value.rich
                            ? { label: () => h('strong', null, 'Address') }
                            : {}),
                    }),
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
        const props = $state({ ...prepare(attributes), rich: attributes.rich })
        const component = mount(SvelteFieldset, { target: container, props })
        container.dataset.ready = 'true'
        return {
            update: (attributes) => {
                const next = { ...prepare(attributes), rich: attributes.rich }
                for (const key of Object.keys(props))
                    if (!(key in next)) delete props[key]
                Object.assign(props, next)
            },
            destroy: () => unmount(component),
        }
    })
    return {
        update: () =>
            renderers[0].update({
                label: 'Updated address',
                labelHidden: true,
                contained: false,
                required: true,
                disabled: true,
                name: 'updated',
                form: 'profile',
                class: 'custom-fieldset',
            }),
        reset: () => renderers[0].update({}),
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
