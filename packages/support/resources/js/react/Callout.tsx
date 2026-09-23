import {
    createElement,
    forwardRef,
    type ComponentPropsWithoutRef,
    type ReactNode,
} from 'react'
import Icon from './Icon'
import type { IconSize } from '../components/icon'

export type CalloutProps = Omit<
    ComponentPropsWithoutRef<'div'>,
    'dangerouslySetInnerHTML'
> & {
    color?: string
    heading?: ReactNode
    description?: ReactNode
    footer?: ReactNode
    controls?: ReactNode
    icon?: ReactNode
    iconColor?: string
    iconSize?: IconSize
}

export default forwardRef<HTMLDivElement, CalloutProps>(function Callout(
    {
        color = 'gray',
        heading,
        description,
        footer,
        controls,
        children,
        icon,
        iconColor = color,
        iconSize = 'lg',
        className,
        ...attributes
    },
    ref,
) {
    const hasContent = (content: ReactNode): boolean =>
        content != null &&
        typeof content !== 'boolean' &&
        (typeof content !== 'string' || content.trim() !== '')
    const actions = footer ?? children
    const hasText = hasContent(heading) || hasContent(description)
    return createElement(
        'div',
        {
            ...attributes,
            ref,
            className: [
                'fi-callout',
                color !== 'gray' && `fi-color fi-color-${color}`,
                className,
            ]
                .filter(Boolean)
                .join(' '),
        },
        hasContent(icon)
            ? createElement(
                  Icon,
                  {
                      size: iconSize,
                      'aria-hidden': true,
                      className: [
                          'fi-callout-icon',
                          iconColor !== 'gray' &&
                              `fi-color fi-color-${iconColor}`,
                      ]
                          .filter(Boolean)
                          .join(' '),
                  },
                  icon,
              )
            : null,
        hasText || hasContent(actions)
            ? createElement(
                  'div',
                  { className: 'fi-callout-main' },
                  hasText
                      ? createElement(
                            'div',
                            { className: 'fi-callout-text' },
                            hasContent(heading)
                                ? createElement(
                                      'h4',
                                      { className: 'fi-callout-heading' },
                                      heading,
                                  )
                                : null,
                            hasContent(description)
                                ? createElement(
                                      'p',
                                      { className: 'fi-callout-description' },
                                      description,
                                  )
                                : null,
                        )
                      : null,
                  hasContent(actions)
                      ? createElement(
                            'div',
                            { className: 'fi-callout-footer' },
                            actions,
                        )
                      : null,
              )
            : null,
        hasContent(controls)
            ? createElement(
                  'div',
                  { className: 'fi-callout-controls' },
                  controls,
              )
            : null,
    )
})
