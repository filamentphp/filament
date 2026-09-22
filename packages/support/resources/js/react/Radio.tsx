import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'

export type RadioProps = Omit<
    ComponentPropsWithoutRef<'input'>,
    'type' | 'children' | 'dangerouslySetInnerHTML'
> & {
    valid?: boolean
}

export default forwardRef<HTMLInputElement, RadioProps>(function Radio(
    { valid = true, className, ...attributes },
    ref,
) {
    return createElement('input', {
        ...attributes,
        type: 'radio',
        ref,
        className: ['fi-radio-input', !valid && 'fi-invalid', className]
            .filter(Boolean)
            .join(' '),
    })
})
