import { createElement, useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, ref } from 'vue'
import { mount, unmount } from 'svelte'
import ReactSelect from '../../../packages/support/resources/js/react/Select'
import ReactWrapper from '../../../packages/support/resources/js/react/InputWrapper'
import VueSelect from '../../../packages/support/resources/js/vue/Select.vue'
import VueWrapper from '../../../packages/support/resources/js/vue/InputWrapper.vue'
import SvelteSelect from './select.svelte'

export default function mountSelect(host, framework) {
    const report = (value, multiple) => {
        host.dataset.value = JSON.stringify(value)
        host.dataset.multiple = JSON.stringify(multiple)
    }
    function render(
        create,
        Select,
        Wrapper,
        value,
        multiple,
        update,
        configuration,
        vue = false,
    ) {
        const options = (defaults = false) =>
            ['', 'drawing', 'ceramics', 'archived']
                .filter(
                    (value) => !(configuration.remove && value === 'drawing'),
                )
                .map((value) =>
                    create(
                        'option',
                        {
                            key: value,
                            value,
                            disabled: value === 'archived',
                            ...(vue && defaults
                                ? {
                                      selected:
                                          value === 'ceramics' ||
                                          (defaults === 'multiple' &&
                                              value === 'archived'),
                                  }
                                : {}),
                        },
                        value || 'Choose a workshop',
                    ),
                )
        const field = (
            name,
            selected,
            isMultiple = false,
            defaults = false,
        ) => {
            const attributes = {
                name,
                'aria-label': name,
                ref:
                    name === 'workshop'
                        ? (element) => {
                              host.select = vue ? element?.element : element
                          }
                        : undefined,
                multiple: isMultiple,
                required: !isMultiple,
                disabled: configuration.disabled,
                inlinePrefix: configuration.inline,
                onChange: (event) => {
                    host.dataset.callbackValue = JSON.stringify(
                        vue ? value.value : selected,
                    )
                    if (!vue && !defaults)
                        update(
                            name,
                            isMultiple
                                ? [...event.target.selectedOptions].map(
                                      (option) => option.value,
                                  )
                                : event.target.value,
                        )
                },
                ...(defaults
                    ? vue
                        ? {}
                        : {
                              defaultValue: isMultiple
                                  ? ['ceramics', 'archived']
                                  : 'ceramics',
                          }
                    : vue
                      ? {
                            modelValue: selected,
                            'onUpdate:modelValue': (next) => update(name, next),
                        }
                      : { value: selected }),
            }
            const select = () =>
                create(
                    Select,
                    attributes,
                    vue
                        ? {
                              default: () =>
                                  options(
                                      defaults && isMultiple
                                          ? 'multiple'
                                          : defaults,
                                  ),
                          }
                        : options(defaults),
                )
            return create(
                Wrapper,
                {
                    key: name,
                    disabled: configuration.disabled,
                    inlinePrefix: configuration.inline,
                    prefix: 'Art',
                },
                vue ? { default: select } : select(),
            )
        }
        const single = vue ? value.value : value
        const many = vue ? multiple.value : multiple
        const button = (id, action) =>
            create(
                'button',
                { key: id, type: 'button', 'data-testid': id, onClick: action },
                id,
            )
        return create('div', null, [
            create(
                'form',
                {
                    key: 'controlled',
                    onSubmit: (event) => {
                        event.preventDefault()
                        host.dataset.submission = JSON.stringify([
                            ...new FormData(event.currentTarget),
                        ])
                    },
                    onReset: (event) => {
                        event.preventDefault()
                        update('workshop', 'drawing')
                        update('extras', ['ceramics'])
                    },
                },
                [
                    field('workshop', single),
                    field('extras', many, true),
                    create(
                        'button',
                        {
                            key: 'submit',
                            type: 'submit',
                            'data-testid': 'submit',
                        },
                        'Submit',
                    ),
                    create(
                        'button',
                        { key: 'reset', type: 'reset', 'data-testid': 'reset' },
                        'Reset',
                    ),
                    button('empty', () => {
                        update('workshop', '')
                        update('extras', [])
                    }),
                    button('archived', () => {
                        update('workshop', 'archived')
                        update('extras', ['drawing', 'archived'])
                    }),
                ],
            ),
            create('form', { key: 'defaults', 'data-testid': 'defaults' }, [
                field('defaultWorkshop', undefined, false, true),
                field('defaultExtras', undefined, true, true),
                create(
                    'button',
                    { key: 'reset', type: 'reset' },
                    'Reset defaults',
                ),
            ]),
        ])
    }
    if (framework === 'react') {
        function Example({ configuration }) {
            const [value, setValue] = useState('drawing')
            const [multiple, setMultiple] = useState(['ceramics'])
            useEffect(() => report(value, multiple), [value, multiple])
            return render(
                createElement,
                ReactSelect,
                ReactWrapper,
                value,
                multiple,
                (name, next) =>
                    (name === 'workshop' ? setValue : setMultiple)(next),
                configuration,
            )
        }
        const root = createRoot(host)
        const update = (configuration) =>
            root.render(createElement(Example, { configuration }))
        update({})
        return { update, destroy: () => root.unmount() }
    }
    if (framework === 'vue') {
        const value = ref('drawing'),
            multiple = ref(['ceramics']),
            configuration = ref({})
        const application = createApp({
            render: () => {
                report(value.value, multiple.value)
                return render(
                    h,
                    VueSelect,
                    VueWrapper,
                    value,
                    multiple,
                    (name, next) => {
                        ;(name === 'workshop' ? value : multiple).value = next
                    },
                    configuration.value,
                    true,
                )
            },
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
        reportElement: (element) => (host.select = element),
        reportChange: (value) =>
            (host.dataset.callbackValue = JSON.stringify(value)),
        reportSubmission: (data) =>
            (host.dataset.submission = JSON.stringify([...data])),
    })
    const component = mount(SvelteSelect, { target: host, props })
    return {
        update: (next) => (props.configuration = next),
        destroy: () => unmount(component),
    }
}
