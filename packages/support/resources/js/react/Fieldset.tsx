import {
    createElement,
    forwardRef,
    type ComponentPropsWithoutRef,
    type ReactNode,
} from 'react'

export type FieldsetProps = ComponentPropsWithoutRef<'fieldset'> & {
    contained?: boolean
    label?: ReactNode
    labelHidden?: boolean
    required?: boolean
}

export default forwardRef<HTMLFieldSetElement, FieldsetProps>(function Fieldset(
    {
        contained = true,
        label = null,
        labelHidden = false,
        required = false,
        className,
        children,
        ...attributes
    },
    ref,
) {
    return createElement(
        'fieldset',
        {
            ...attributes,
            ref,
            className: [
                'fi-fieldset',
                labelHidden && 'fi-fieldset-label-hidden',
                !contained && 'fi-fieldset-not-contained',
                className,
            ]
                .filter(Boolean)
                .join(' '),
        },
        label != null &&
            typeof label !== 'boolean' &&
            (typeof label !== 'string' || label.trim() !== '')
            ? createElement(
                  'legend',
                  null,
                  label,
                  required
                      ? createElement(
                            'sup',
                            { className: 'fi-fieldset-label-required-mark' },
                            '*',
                        )
                      : null,
              )
            : null,
        children,
    )
})
