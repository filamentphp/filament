import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactInputWrapper from '../../../packages/support/resources/js/react/InputWrapper'
import ReactIcon from '../../../packages/support/resources/js/react/Icon'
import VueInputWrapper from '../../../packages/support/resources/js/vue/InputWrapper.vue'
import VueIcon from '../../../packages/support/resources/js/vue/Icon.vue'
import SvelteInputWrapper from './input-wrapper.svelte'

export default function mountWrappers(host, framework, cases) {
    const renderers = cases.map((configuration) => {
        const container = document.createElement('div')
        host.append(container)
        const reportClick = () => {
            container.dataset.clicked = 'true'
        }
        const reportWrapperClick = (event) => {
            container.dataset.wrapperClicked = event.currentTarget.tagName
        }
        const reportRef = (element) => {
            container.dataset.ref = element?.tagName ?? 'none'
        }
        const render = (configuration, create, Wrapper, Icon, vue = false) => {
            const { icons, actions, rich, changed, ...attributes } =
                configuration
            const icon = () =>
                create(
                    Icon,
                    {},
                    vue
                        ? {
                              default: () =>
                                  create(
                                      'svg',
                                      {
                                          viewBox: '0 0 24 24',
                                          'aria-hidden': 'true',
                                      },
                                      [create('path', { d: 'M4 12h16' })],
                                  ),
                          }
                        : create(
                              'svg',
                              { viewBox: '0 0 24 24', 'aria-hidden': 'true' },
                              create('path', { d: 'M4 12h16' }),
                          ),
                )
            const action = () =>
                create(
                    'button',
                    { type: 'button', onClick: reportClick },
                    'Clear',
                )
            const input = () =>
                create('input', {
                    [vue ? 'class' : 'className']: 'fi-input',
                    'aria-label': 'Amount',
                    required: true,
                })
            const content = {
                ...(icons ? { prefixIcon: icon, suffixIcon: icon } : {}),
                ...(actions
                    ? { prefixActions: action, suffixActions: action }
                    : {}),
                ...(rich
                    ? { prefix: () => create('strong', null, 'Price') }
                    : {}),
            }
            return create(
                Wrapper,
                {
                    ...attributes,
                    'data-state': changed ? 'changed' : undefined,
                    onClick: reportWrapperClick,
                    ref: vue
                        ? (component) => reportRef(component?.$el)
                        : reportRef,
                    ...(!vue
                        ? Object.fromEntries(
                              Object.entries(content).map(([name, value]) => [
                                  name,
                                  value(),
                              ]),
                          )
                        : {}),
                },
                vue ? { default: input, ...content } : input(),
            )
        }
        if (framework === 'react') {
            const root = createRoot(container)
            const update = (configuration) =>
                root.render(
                    render(
                        configuration,
                        createElement,
                        ReactInputWrapper,
                        ReactIcon,
                    ),
                )
            update(configuration)
            return { update, destroy: () => root.unmount() }
        }
        if (framework === 'vue') {
            const state = shallowRef(configuration)
            const application = createApp({
                render: () =>
                    render(state.value, h, VueInputWrapper, VueIcon, true),
            })
            application.mount(container)
            return {
                update: (configuration) => {
                    state.value = configuration
                },
                destroy: () => application.unmount(),
            }
        }
        const props = $state({
            configuration,
            reportClick,
            reportWrapperClick,
            reportRef,
        })
        const component = mount(SvelteInputWrapper, {
            target: container,
            props,
        })
        return {
            update: (configuration) => {
                props.configuration = configuration
            },
            destroy: () => unmount(component),
        }
    })
    return {
        update: () =>
            renderers.forEach((renderer) =>
                renderer.update({
                    prefix: '£',
                    suffix: 'GBP',
                    rich: true,
                    icons: true,
                    actions: true,
                    inlineSuffix: true,
                    disabled: true,
                    valid: false,
                    changed: true,
                }),
            ),
        clear: () => renderers.forEach((renderer) => renderer.update({})),
        reset: () =>
            renderers.forEach((renderer, index) =>
                renderer.update(cases[index]),
            ),
        destroy: () => renderers.forEach((renderer) => renderer.destroy()),
    }
}
