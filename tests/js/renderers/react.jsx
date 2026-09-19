import React, { useState } from 'react'
import { createRoot } from 'react-dom/client'
import './field.css'

function Field({
    value,
    config,
    id,
    ariaDescribedBy,
    disabled,
    readOnly,
    required,
    invalid,
    onChange,
    onBlur,
    utilities,
}) {
    const [report, setReport] = useState('')
    const locked = disabled || readOnly
    return (
        <div>
            <input
                id={id}
                aria-label="Title"
                aria-describedby={ariaDescribedBy}
                value={value.title}
                disabled={disabled}
                readOnly={readOnly}
                required={required}
                aria-invalid={invalid}
                onChange={(event) =>
                    onChange({ ...value, title: event.target.value })
                }
                onBlur={onBlur}
            />
            <label>
                <input
                    type="checkbox"
                    checked={value.enabled}
                    disabled={locked}
                    onChange={(event) =>
                        onChange({ ...value, enabled: event.target.checked })
                    }
                    onBlur={onBlur}
                />
                Enabled
            </label>
            <button
                type="button"
                disabled={locked}
                onClick={() =>
                    onChange({
                        ...value,
                        tags: value.tags.includes('sms') ? [] : ['sms', 'push'],
                    })
                }
                onBlur={onBlur}
            >
                Toggle channels
            </button>
            <output data-channels>{JSON.stringify(value.tags)}</output>
            <output data-config>{JSON.stringify(config)}</output>
            <button
                type="button"
                disabled={locked}
                onClick={() => {
                    setReport(
                        JSON.stringify({
                            path: utilities.$statePath,
                            state: utilities.$state,
                            sibling: utilities.$get('caption'),
                            root: utilities.$get('data.caption', true),
                            relative: utilities.$get('../../caption'),
                        }),
                    )
                    utilities.$set('caption', 'Changed by renderer', false)
                    utilities.$set(
                        'data.caption',
                        'Changed root by renderer',
                        true,
                        true,
                    )
                }}
            >
                Use utilities
            </button>
            <output data-report>{report}</output>
            <button
                type="button"
                onClick={async () => {
                    const { $replaceTitle } = utilities
                    setReport(
                        JSON.stringify(
                            await $replaceTitle({
                                title: 'PHP café replacement',
                            }),
                        ),
                    )
                }}
            >
                Call field method
            </button>
            <button
                type="button"
                onClick={async () => {
                    setReport(
                        JSON.stringify(
                            await utilities.$callSchemaComponentMethod(
                                'inspectTitle',
                                { prefix: 'Read: ' },
                            ),
                        ),
                    )
                }}
            >
                Call renderless method
            </button>
            <button
                type="button"
                onClick={async () => {
                    setReport(
                        JSON.stringify(
                            await utilities.$callSchemaComponentMethod(
                                'unexposedMethod',
                            ),
                        ),
                    )
                }}
            >
                Call unexposed method
            </button>
            <button
                type="button"
                onClick={async () => {
                    setReport(
                        JSON.stringify(
                            await utilities.$wire.$call(
                                'changeCaption',
                                'Via $wire café',
                            ),
                        ),
                    )
                }}
            >
                Call Livewire method
            </button>
        </div>
    )
}

export default function mount({ host, props: initialProps, utilities }) {
    const root = createRoot(host)
    const update = (props) =>
        root.render(
            <Field {...initialProps} {...props} utilities={utilities} />,
        )
    update(initialProps)
    return { update, destroy: () => root.unmount() }
}
