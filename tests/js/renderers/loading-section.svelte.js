import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactLoadingSection from '../../../packages/support/resources/js/react/LoadingSection'
import VueLoadingSection from '../../../packages/support/resources/js/vue/LoadingSection.vue'
import SvelteLoadingSection from './loading-section.svelte'

export default function mountLoadingSections(host, framework, cases) {
    const renderers = cases.map((initial) => {
        const container = document.createElement('div')
        host.append(container)
        const onElement = (element) => {
            container.element = element
        }
        const onClick = (event) => {
            container.dataset.target = event.currentTarget.tagName
        }
        const prepare = ({ class: className, ...attributes }) => ({
            ...attributes,
            [framework === 'react' ? 'className' : 'class']: className,
            [framework === 'svelte' ? 'onclick' : 'onClick']: onClick,
        })
        if (framework === 'react') {
            const root = createRoot(container)
            const update = ({ state = 'loading', ...attributes }) =>
                root.render(
                    state === 'loading'
                        ? createElement(ReactLoadingSection, {
                              ...prepare(attributes),
                              ref: onElement,
                          })
                        : createElement('p', { role: 'status' }, state),
                )
            update(initial)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const props = shallowRef(initial)
            const application = createApp({
                render: () => {
                    const { state = 'loading', ...attributes } = props.value
                    return state === 'loading'
                        ? h(VueLoadingSection, {
                              ...prepare(attributes),
                              ref: (component) => onElement(component?.$el),
                          })
                        : h('p', { role: 'status' }, state)
                },
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
            state: 'loading',
            ...prepare(initial),
            onElement,
        })
        const component = mount(SvelteLoadingSection, {
            target: container,
            props,
        })
        return {
            update: (attributes) =>
                Object.assign(props, {
                    state: 'loading',
                    height: undefined,
                    columnSpan: undefined,
                    columnStart: undefined,
                    loadingLabel: undefined,
                    title: undefined,
                    ...prepare(attributes),
                }),
            destroy: () => unmount(component),
        }
    })
    host.update = (attributes) => renderers[0].update(attributes)
    host.destroy = () => renderers.forEach((renderer) => renderer.destroy())
    return { destroy: host.destroy }
}
