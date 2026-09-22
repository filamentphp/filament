import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'

export type CheckboxProps = Omit<
    ComponentPropsWithoutRef<'input'>,
    'type' | 'children' | 'dangerouslySetInnerHTML'
> & {
    valid?: boolean
}

export default forwardRef<HTMLInputElement, CheckboxProps>(function Checkbox(
    { valid = true, className, ...attributes },
    ref,
) {
    return createElement('input', {
        ...attributes,
        type: 'checkbox',
        ref,
        className: [
            'fi-checkbox-input',
            valid ? 'fi-valid' : 'fi-invalid',
            className,
        ]
            .filter(Boolean)
            .join(' '),
    })
})
