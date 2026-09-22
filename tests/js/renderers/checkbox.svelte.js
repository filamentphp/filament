import { createElement } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp, h, reactive } from 'vue'
import { mount, unmount } from 'svelte'
import ReactCheckbox from '../../../packages/support/resources/js/react/Checkbox'
import VueCheckboxFixture from './checkbox.vue'
import SvelteCheckboxFixture from './checkbox.svelte'

export default function mountCheckboxes(host, framework, cases) {
    const form = `checkbox-form-${framework}`
    const reportChange = () =>
        (host.dataset.changes = String(Number(host.dataset.changes ?? 0) + 1))
    const initial = { valid: true, checked: false }
    let settings,
        render = () => {},
        destroy
    if (framework === 'react') {
        settings = initial
        const root = createRoot(host)
        const references = cases.map(({ indeterminate }) => (element) => {
            if (!element) return
            host.dataset.nativeRef = String(element instanceof HTMLInputElement)
            if (indeterminate !== undefined)
                element.indeterminate = indeterminate
        })
        render = () =>
            root.render([
                ...cases.map(
                    (
                        { class: className, indeterminate, ...attributes },
                        index,
                    ) =>
                        createElement(
                            'label',
                            { key: attributes.name },
                            createElement(ReactCheckbox, {
                                ...attributes,
                                className,
                                form,
                                valid: settings.valid,
                                onChange: reportChange,
                                ref: references[index],
                            }),
                            attributes.name,
                        ),
                ),
                createElement(
                    'label',
                    { key: 'controlled' },
                    createElement(ReactCheckbox, {
                        name: 'controlled',
                        value: 'enabled',
                        form,
                        checked: settings.checked,
                        valid: settings.valid,
                        onChange: (event) => {
                            settings.checked = event.currentTarget.checked
                            reportChange()
                            render()
                        },
                    }),
                    'Controlled ',
                ),
                createElement(
                    'output',
                    { key: 'model', 'data-model': String(settings.checked) },
                    String(settings.checked),
                ),
            ])
        render()
        destroy = () => root.unmount()
    } else if (framework === 'vue') {
        settings = reactive(initial)
        const application = createApp({
            render: () =>
                h(VueCheckboxFixture, { settings, cases, form, reportChange }),
        })
        application.mount(host)
        destroy = () => application.unmount()
    } else {
        const state = $state(initial)
        settings = state
        const component = mount(SvelteCheckboxFixture, {
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
        toggle: () => {
            settings.checked = !settings.checked
            render()
        },
        destroy,
    }
}
