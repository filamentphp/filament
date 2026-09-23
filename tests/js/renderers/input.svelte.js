import { createElement, useState } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, ref, shallowRef } from 'vue'
import { mount, unmount } from 'svelte'
import ReactInput from '../../../packages/support/resources/js/react/Input'
import ReactWrapper from '../../../packages/support/resources/js/react/InputWrapper'
import VueInput from '../../../packages/support/resources/js/vue/Input.vue'
import VueWrapper from '../../../packages/support/resources/js/vue/InputWrapper.vue'
import SvelteInput from './input.svelte'

export default function mountInput(host, framework) {
    const report = (element, value, data) => {
        host.input = element
        host.dataset.value = JSON.stringify(value) ?? 'undefined'
        if (data)
            host.dataset.submission = JSON.stringify(Object.fromEntries(data))
    }
    const render = (
        configuration,
        value,
        update,
        create,
        Input,
        Wrapper,
        vue = false,
    ) => {
        const wrap = (props, attributes) =>
            create(
                Wrapper,
                props,
                vue
                    ? { default: () => create(Input, attributes) }
                    : create(Input, attributes),
            )
        const form = create(
            'form',
            {
                key: 'controlled',
                onSubmit: (event) => {
                    event.preventDefault()
                    report(host.input, value, new FormData(event.currentTarget))
                },
                onReset: (event) => {
                    event.preventDefault()
                    update(0)
                    event.currentTarget.elements.title.value = 'Workshop'
                    event.currentTarget.elements.email.value = 'ada@example.com'
                },
            },
            [
                wrap(
                    {
                        key: 'amount',
                        disabled: configuration.disabled,
                        inlinePrefix: configuration.inline,
                        prefix: '£',
                    },
                    {
                        ref: (element) =>
                            report(vue ? element?.element : element, value),
                        type: 'number',
                        name: 'amount',
                        'aria-label': 'Amount',
                        min: 0,
                        required: true,
                        disabled: configuration.disabled,
                        [vue ? 'readonly' : 'readOnly']: configuration.readOnly,
                        inlinePrefix: configuration.inline,
                        onInput: (event) => {
                            host.dataset.inputEvent = event.currentTarget.value
                        },
                        ...(vue
                            ? {
                                  modelValue: value,
                                  'onUpdate:modelValue': update,
                              }
                            : {
                                  value,
                                  onChange: (event) =>
                                      update(event.target.value),
                              }),
                    },
                ),
                wrap(
                    { key: 'title' },
                    {
                        name: 'title',
                        'aria-label': 'Title',
                        defaultValue: 'Workshop',
                    },
                ),
                wrap(
                    { key: 'email' },
                    {
                        type: 'email',
                        name: 'email',
                        'aria-label': 'Email',
                        required: true,
                        defaultValue: 'ada@example.com',
                    },
                ),
                create(
                    'button',
                    { key: 'submit', type: 'submit', 'data-testid': 'submit' },
                    'Submit',
                ),
                create(
                    'button',
                    { key: 'reset', type: 'reset', 'data-testid': 'reset' },
                    'Reset',
                ),
                create(
                    'button',
                    {
                        key: 'zero',
                        type: 'button',
                        'data-testid': 'zero',
                        onClick: () => update(0),
                    },
                    'Zero',
                ),
                create(
                    'button',
                    {
                        key: 'empty',
                        type: 'button',
                        'data-testid': 'empty',
                        onClick: () => update(''),
                    },
                    'Empty',
                ),
            ],
        )
        return create('div', null, [
            form,
            create(
                'form',
                { key: 'uncontrolled', 'data-testid': 'uncontrolled' },
                [
                    wrap(
                        { key: 'zero' },
                        {
                            type: 'number',
                            name: 'zero',
                            'aria-label': 'Uncontrolled number',
                            defaultValue: 0,
                        },
                    ),
                    wrap(
                        { key: 'empty' },
                        {
                            name: 'empty',
                            'aria-label': 'Uncontrolled text',
                            defaultValue: '',
                        },
                    ),
                    create(
                        'button',
                        { key: 'reset', type: 'reset' },
                        'Reset uncontrolled',
                    ),
                ],
            ),
        ])
    }
    if (framework === 'react') {
        function Example({ configuration }) {
            const [value, update] = useState(0)
            return render(
                configuration,
                value,
                update,
                createElement,
                ReactInput,
                ReactWrapper,
            )
        }
        const root = createRoot(host)
        const update = (configuration) =>
            root.render(createElement(Example, { configuration }))
        update({})
        return { update, destroy: () => root.unmount() }
    }
    if (framework === 'vue') {
        const configuration = shallowRef({})
        const value = ref(0)
        const application = createApp({
            render: () =>
                render(
                    configuration.value,
                    value.value,
                    (next) => (value.value = next),
                    h,
                    VueInput,
                    VueWrapper,
                    true,
                ),
        })
        application.mount(host)
        return {
            update: (next) => (configuration.value = next),
            destroy: () => application.unmount(),
        }
    }
    const props = $state({
        configuration: {},
        report,
        reportInput: (event) => {
            host.dataset.inputEvent = event.currentTarget.value
        },
    })
    const component = mount(SvelteInput, { target: host, props })
    return {
        update: (next) => (props.configuration = next),
        destroy: () => unmount(component),
    }
}
