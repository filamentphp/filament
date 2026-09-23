import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'

export type SelectProps = ComponentPropsWithoutRef<'select'> & {
    inlinePrefix?: boolean
}

export default forwardRef<HTMLSelectElement, SelectProps>(function Select(
    { inlinePrefix = false, className, ...attributes },
    ref,
) {
    return createElement('select', {
        ...attributes,
        ref,
        className: [
            'fi-select-input',
            inlinePrefix && 'fi-select-input-has-inline-prefix',
            className,
        ]
            .filter(Boolean)
            .join(' '),
    })
})
