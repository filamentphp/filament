import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'

export type InputProps = Omit<
    ComponentPropsWithoutRef<'input'>,
    'children' | 'dangerouslySetInnerHTML'
> & {
    inlinePrefix?: boolean
    inlineSuffix?: boolean
}

export default forwardRef<HTMLInputElement, InputProps>(function Input(
    { inlinePrefix = false, inlineSuffix = false, className, ...attributes },
    ref,
) {
    return createElement('input', {
        ...attributes,
        ref,
        className: [
            'fi-input',
            inlinePrefix && 'fi-input-has-inline-prefix',
            inlineSuffix && 'fi-input-has-inline-suffix',
            className,
        ]
            .filter(Boolean)
            .join(' '),
    })
})
