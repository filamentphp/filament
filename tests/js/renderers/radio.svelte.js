import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, reactive } from 'vue'
import { mount, unmount } from 'svelte'
import ReactRadio from '../../../packages/support/resources/js/react/Radio'
import VueRadioFixture from './radio.vue'
import SvelteRadioFixture from './radio.svelte'

export default function mountRadios(host, framework, cases) {
    const form = `radio-form-${framework}`
    const reportChange = () =>
        (host.dataset.changes = String(Number(host.dataset.changes ?? 0) + 1))
    const initial = { valid: true, delivery: 'standard' }
    let settings,
        render = () => {},
        destroy
    if (framework === 'react') {
        settings = initial
        const root = createRoot(host)
        const reference = (element) => {
            if (element)
                host.dataset.nativeRef = String(
                    element instanceof HTMLInputElement,
                )
        }
        render = () =>
            root.render([
                ...cases.map(({ class: className, ...attributes }, index) =>
                    createElement(
                        'label',
                        { key: index },
                        createElement(ReactRadio, {
                            ...attributes,
                            className,
                            form,
                            valid: settings.valid,
                            onChange: reportChange,
                            ref: reference,
                        }),
                        `${attributes.name} ${attributes.value ?? ''}`,
                    ),
                ),
                ...['standard', 'express'].map((value) =>
                    createElement(
                        'label',
                        { key: value },
                        createElement(ReactRadio, {
                            name: 'controlled',
                            value,
                            form,
                            checked: settings.delivery === value,
                            valid: settings.valid,
                            onChange: (event) => {
                                settings.delivery = event.currentTarget.value
                                event.currentTarget.dataset.modelAtChange =
                                    settings.delivery
                                reportChange()
                                render()
                            },
                        }),
                        value,
                    ),
                ),
                createElement(
                    'output',
                    { key: 'model', 'data-model': settings.delivery },
                    settings.delivery,
                ),
            ])
        render()
        destroy = () => root.unmount()
    } else if (framework === 'vue') {
        settings = reactive(initial)
        const application = createApp({
            render: () =>
                h(VueRadioFixture, { settings, cases, form, reportChange }),
        })
        application.mount(host)
        destroy = () => application.unmount()
    } else {
        const state = $state(initial)
        settings = state
        const component = mount(SvelteRadioFixture, {
            target: host,
            props: { settings, cases, form, reportChange },
        })
        destroy = () => unmount(component)
    }
    return {
        invalidate: () => {
            settings.valid = false
            render()
        },
        select: () => {
            settings.delivery = 'express'
            render()
        },
        reset: () => {
            settings.delivery = 'standard'
            render()
        },
        destroy,
    }
}
