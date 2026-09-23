import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactEmptyState from '../../../packages/support/resources/js/react/EmptyState'
import VueEmptyState from '../../../packages/support/resources/js/vue/EmptyState.vue'
import SvelteEmptyState from './empty-state.svelte'

export default function mountEmptyStates(host, framework, cases) {
    const renderers = cases.map((attributes) => {
        const container = document.createElement('div')
        host.append(container)
        const onAction = () => {
            container.dataset.actions = String(
                Number(container.dataset.actions ?? 0) + 1,
            )
        }
        const onInput = (event) => {
            container.dataset.input = event.target.value
            container.dataset.target = event.currentTarget.tagName
        }
        const prepare = ({
            rich,
            withIcon,
            withActions,
            class: className,
            ...props
        }) => ({
            ...props,
            [framework === 'react' ? 'className' : 'class']: className,
            [framework === 'svelte' ? 'oninput' : 'onInput']: onInput,
        })
        const content = (node, attributes) => ({
            ...(attributes.rich
                ? {
                      heading: node('strong', null, 'Projects'),
                      description: node('em', null, 'Start here'),
                  }
                : {}),
            ...(attributes.withIcon
                ? {
                      icon: node(
                          'svg',
                          { 'aria-hidden': 'true', viewBox: '0 0 24 24' },
                          node('path', { d: 'M4 4h16v16H4z' }),
                      ),
                  }
                : {}),
            ...(attributes.withActions
                ? {
                      footer: [
                          node('input', {
                              key: 'input',
                              'aria-label': 'Project name',
                              name: 'project',
                          }),
                          node(
                              'button',
                              {
                                  key: 'button',
                                  type: 'button',
                                  onClick: onAction,
                              },
                              'Create project',
                          ),
                          node(
                              'a',
                              { key: 'link', href: '#projects' },
                              'Browse projects',
                          ),
                      ],
                  }
                : {}),
        })
        if (framework === 'react') {
            const root = createRoot(container)
            const update = (attributes) =>
                root.render(
                    createElement(ReactEmptyState, {
                        ...prepare(attributes),
                        ...content(createElement, attributes),
                        ref: (element) => {
                            container.element = element
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
                        VueEmptyState,
                        {
                            ...prepare(props.value),
                            ref: (component) => {
                                container.element = component?.$el
                            },
                        },
                        Object.fromEntries(
                            Object.entries(content(h, props.value)).map(
                                ([name, value]) => [name, () => value],
                            ),
                        ),
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
        const onElement = (element) => {
            container.element = element
        }
        const props = $state({
            ...prepare(attributes),
            rich: attributes.rich,
            withIcon: attributes.withIcon,
            withActions: attributes.withActions,
            onAction,
            onElement,
        })
        const component = mount(SvelteEmptyState, { target: container, props })
        return {
            update: (attributes) => {
                const next = {
                    ...prepare(attributes),
                    rich: attributes.rich,
                    withIcon: attributes.withIcon,
                    withActions: attributes.withActions,
                    onAction,
                    onElement,
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
            renderers[0].update({
                heading: 'Updated projects',
                description: 'Choose your next project',
                compact: true,
                contained: false,
                headingTag: 'h3',
                iconColor: 'gray',
                iconSize: 'sm',
                withIcon: true,
                withActions: true,
                title: 'Updated',
            }),
        remove: () =>
            renderers[0].update({
                heading: 'No projects',
                description: '  ',
                footer: '  ',
            }),
        reset: () => renderers[0].update(cases[0]),
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
