import {
    createElement,
    forwardRef,
    type ComponentPropsWithoutRef,
    type ReactNode,
} from 'react'
import Icon from './Icon'
import type { IconSize } from '../components/icon'

export type EmptyStateProps = Omit<
    ComponentPropsWithoutRef<'div'>,
    'dangerouslySetInnerHTML'
> & {
    compact?: boolean
    contained?: boolean
    heading?: ReactNode
    headingTag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
    description?: ReactNode
    footer?: ReactNode
    icon?: ReactNode
    iconColor?: string
    iconSize?: IconSize
}

export default forwardRef<HTMLDivElement, EmptyStateProps>(function EmptyState(
    {
        compact = false,
        contained = true,
        heading,
        headingTag = 'h2',
        description,
        footer,
        children,
        icon,
        iconColor = 'primary',
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
    const color = iconColor === 'gray' ? '' : `fi-color fi-color-${iconColor}`
    const actions = footer ?? children

    return createElement(
        'div',
        {
            ...attributes,
            ref,
            className: [
                'fi-empty-state',
                compact && 'fi-compact',
                !contained && 'fi-empty-state-not-contained',
                className,
            ]
                .filter(Boolean)
                .join(' '),
        },
        createElement(
            'div',
            { className: 'fi-empty-state-content' },
            hasContent(icon)
                ? createElement(
                      'div',
                      {
                          className: ['fi-empty-state-icon-bg', color]
                              .filter(Boolean)
                              .join(' '),
                      },
                      createElement(
                          Icon,
                          { size: iconSize, className: color || undefined },
                          icon,
                      ),
                  )
                : null,
            createElement(
                'div',
                { className: 'fi-empty-state-text-ctn' },
                createElement(
                    headingTag,
                    { className: 'fi-empty-state-heading' },
                    heading,
                ),
                hasContent(description)
                    ? createElement(
                          'p',
                          { className: 'fi-empty-state-description' },
                          description,
                      )
                    : null,
                hasContent(actions)
                    ? createElement(
                          'footer',
                          { className: 'fi-empty-state-footer' },
                          actions,
                      )
                    : null,
            ),
        ),
    )
})
