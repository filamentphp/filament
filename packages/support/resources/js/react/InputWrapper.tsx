import {
    createElement,
    forwardRef,
    type ComponentPropsWithoutRef,
    type ReactNode,
} from 'react'

export type InputWrapperProps = Omit<
    ComponentPropsWithoutRef<'div'>,
    'prefix' | 'dangerouslySetInnerHTML'
> & {
    disabled?: boolean
    valid?: boolean
    inlinePrefix?: boolean
    inlineSuffix?: boolean
    prefix?: ReactNode
    suffix?: ReactNode
    prefixIcon?: ReactNode
    suffixIcon?: ReactNode
    prefixActions?: ReactNode
    suffixActions?: ReactNode
}

export default forwardRef<HTMLDivElement, InputWrapperProps>(
    function InputWrapper(
        {
            disabled = false,
            valid = true,
            inlinePrefix = false,
            inlineSuffix = false,
            prefix,
            suffix,
            prefixIcon,
            suffixIcon,
            prefixActions,
            suffixActions,
            className,
            children,
            ...attributes
        },
        ref,
    ) {
        const hasPrefixLabel =
            prefix != null &&
            typeof prefix !== 'boolean' &&
            (typeof prefix !== 'string' || prefix.trim() !== '')
        const hasSuffixLabel =
            suffix != null &&
            typeof suffix !== 'boolean' &&
            (typeof suffix !== 'string' || suffix.trim() !== '')
        const hasPrefix = hasPrefixLabel || !!prefixIcon || !!prefixActions
        const hasSuffix = hasSuffixLabel || !!suffixIcon || !!suffixActions

        return createElement(
            'div',
            {
                ...attributes,
                ref,
                className: [
                    'fi-input-wrp',
                    disabled && 'fi-disabled',
                    !valid && 'fi-invalid',
                    className,
                ]
                    .filter(Boolean)
                    .join(' '),
            },
            hasPrefix
                ? createElement(
                      'div',
                      {
                          className: [
                              'fi-input-wrp-prefix',
                              'fi-input-wrp-prefix-has-content',
                              inlinePrefix && 'fi-inline',
                              hasPrefixLabel && 'fi-input-wrp-prefix-has-label',
                          ]
                              .filter(Boolean)
                              .join(' '),
                      },
                      prefixActions
                          ? createElement(
                                'div',
                                { className: 'fi-input-wrp-actions' },
                                prefixActions,
                            )
                          : null,
                      prefixIcon,
                      hasPrefixLabel
                          ? createElement(
                                'span',
                                { className: 'fi-input-wrp-label' },
                                prefix,
                            )
                          : null,
                  )
                : null,
            createElement(
                'div',
                { className: 'fi-input-wrp-content-ctn' },
                children,
            ),
            hasSuffix
                ? createElement(
                      'div',
                      {
                          className: [
                              'fi-input-wrp-suffix',
                              inlineSuffix && 'fi-inline',
                              hasSuffixLabel && 'fi-input-wrp-suffix-has-label',
                          ]
                              .filter(Boolean)
                              .join(' '),
                      },
                      hasSuffixLabel
                          ? createElement(
                                'span',
                                { className: 'fi-input-wrp-label' },
                                suffix,
                            )
                          : null,
                      suffixIcon,
                      suffixActions
                          ? createElement(
                                'div',
                                { className: 'fi-input-wrp-actions' },
                                suffixActions,
                            )
                          : null,
                  )
                : null,
        )
    },
)
