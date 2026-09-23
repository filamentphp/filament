import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactActions from '../../../packages/support/resources/js/react/Actions'
import VueActions from '../../../packages/support/resources/js/vue/Actions.vue'
import SvelteActions from './actions.svelte'

export default function mountActions(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('form')
        host.append(container)
        container.onsubmit = (event) => {
            event.preventDefault()
            container.dataset.submitted = new FormData(container).get('project')
        }
        const onAction = () => {
            container.dataset.actions = String(
                Number(container.dataset.actions ?? 0) + 1,
            )
        }
        const onInput = (event) => {
            container.dataset.target = event.currentTarget.tagName
        }
        const prepare = ({ class: className, ...props }) => ({
            ...props,
            [framework === 'react' ? 'className' : 'class']: className,
            [framework === 'svelte' ? 'oninput' : 'onInput']: onInput,
        })
        const children = (node) => [
            node('input', {
                key: 'input',
                'aria-label': 'Project name',
                name: 'project',
            }),
            node(
                'button',
                { key: 'save', type: 'submit', onClick: onAction },
                'Save',
            ),
            node(
                'button',
                { key: 'disabled', type: 'button', disabled: true },
                'Unavailable',
            ),
            node('a', { key: 'link', href: '#projects' }, 'Projects'),
        ]
        if (framework === 'react') {
            const root = createRoot(container)
            const update = (attributes) =>
                root.render(
                    createElement(
                        ReactActions,
                        {
                            ...prepare(attributes),
                            ref: (element) => {
                                container.element = element
                            },
                        },
                        children(createElement),
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
                        VueActions,
                        {
                            ...prepare(props.value),
                            ref: (component) => {
                                container.element = component?.$el
                            },
                        },
                        { default: () => children(h) },
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
            onAction,
            onElement: (element) => {
                container.element = element
            },
        })
        const component = mount(SvelteActions, { target: container, props })
        return {
            update: (attributes) => Object.assign(props, prepare(attributes)),
            destroy: () => unmount(component),
        }
    })
    host.update = (attributes) => renderers[0].update(attributes)
    return {
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
