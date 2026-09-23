import { createElement, Fragment, useState } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactBadge from '../../../packages/support/resources/js/react/Badge'
import VueBadge from '../../../packages/support/resources/js/vue/Badge.vue'
import SvelteBadge from './badge.svelte'
import { interactive } from '../../../packages/support/resources/js/components/interactive'
import mousetrap from 'mousetrap'

export default function mountBadges(host, framework, cases) {
    host.interactive = interactive
    host.applicationShortcuts = mousetrap
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
        const prepare = ({ deletable, controlledSequence, ...attributes }) => ({
            ...attributes,
            [framework === 'svelte' ? 'onclick' : 'onClick']: onClick,
            ...(deletable ? { onDelete } : {}),
        })
        if (framework === 'react') {
            const root = createRoot(container)
            function Fixture({ attributes }) {
                const [value, setValue] = useState('')
                return createElement(
                    Fragment,
                    null,
                    attributes.controlledSequence &&
                        createElement('input', {
                            'aria-label': 'Sequence input',
                            'data-testid': 'sequence-input',
                            value,
                            onChange: (event) => setValue(event.target.value),
                        }),
                    createElement(
                        ReactBadge,
                        {
                            ...prepare(attributes),
                            ref: onElement,
                            ...(attributes.controlledSequence
                                ? { keyBindings: ['g p'] }
                                : {}),
                        },
                        'Priority',
                    ),
                    attributes.controlledSequence &&
                        createElement(
                            'output',
                            { 'data-testid': 'sequence-value' },
                            value,
                        ),
                )
            }
            const update = (attributes) =>
                root.render(createElement(Fixture, { attributes }))
            update(initial)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const props = shallowRef(initial)
            const value = shallowRef('')
            const application = createApp({
                render: () => [
                    props.value.controlledSequence &&
                        h('input', {
                            'aria-label': 'Sequence input',
                            'data-testid': 'sequence-input',
                            value: value.value,
                            onInput: (event) => {
                                value.value = event.target.value
                            },
                        }),
                    h(
                        VueBadge,
                        {
                            ...prepare(props.value),
                            ...(props.value.controlledSequence
                                ? { keyBindings: ['g p'] }
                                : {}),
                            ref: (component) => onElement(component?.element),
                        },
                        () => 'Priority',
                    ),
                    props.value.controlledSequence &&
                        h(
                            'output',
                            { 'data-testid': 'sequence-value' },
                            value.value,
                        ),
                ],
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
            ...prepare(initial),
            controlledSequence: initial.controlledSequence,
            onElement,
        })
        const component = mount(SvelteBadge, { target: container, props })
        return {
            update: (attributes) => {
                for (const name of Object.keys(props))
                    if (name !== 'onElement') delete props[name]
                Object.assign(props, prepare(attributes), {
                    controlledSequence: attributes.controlledSequence,
                })
            },
            destroy: () => unmount(component),
        }
    })
    host.update = (attributes, index = 0) => renderers[index].update(attributes)
    host.destroy = () => renderers.forEach((renderer) => renderer.destroy())
    return { destroy: host.destroy }
}
