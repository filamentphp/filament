import {
    createElement,
    forwardRef,
    type ComponentPropsWithoutRef,
    type ReactNode,
} from 'react'
import {
    breadcrumbSeparator,
    breadcrumbSeparatorRtl,
} from '../components/breadcrumbs'

export type BreadcrumbItem = Omit<
    ComponentPropsWithoutRef<'a'>,
    'children' | 'dangerouslySetInnerHTML'
> & { label: string }

export type BreadcrumbsProps = Omit<
    ComponentPropsWithoutRef<'nav'>,
    'children' | 'dangerouslySetInnerHTML'
> & {
    breadcrumbs?: readonly BreadcrumbItem[]
    separator?: ReactNode
    separatorRtl?: ReactNode
}

export default forwardRef<HTMLElement, BreadcrumbsProps>(function Breadcrumbs(
    {
        breadcrumbs = [],
        separator,
        separatorRtl,
        className,
        'aria-label': ariaLabel = 'Breadcrumbs',
        ...attributes
    },
    ref,
) {
    return createElement(
        'nav',
        {
            ...attributes,
            ref,
            'aria-label': ariaLabel,
            className: ['fi-breadcrumbs', className].filter(Boolean).join(' '),
        },
        createElement(
            'ol',
            { className: 'fi-breadcrumbs-list' },
            breadcrumbs.map(({ label, className, ...attributes }, index) =>
                createElement(
                    'li',
                    { className: 'fi-breadcrumbs-item', key: index },
                    index > 0 &&
                        (
                            [
                                ['ltr', separator, breadcrumbSeparator],
                                ['rtl', separatorRtl, breadcrumbSeparatorRtl],
                            ] as const
                        ).map(([direction, content, path]) =>
                            content !== undefined
                                ? createElement(
                                      'span',
                                      {
                                          key: direction,
                                          'aria-hidden': true,
                                          className: `fi-icon fi-size-md fi-breadcrumbs-item-separator fi-${direction}`,
                                      },
                                      content,
                                  )
                                : createElement(
                                      'svg',
                                      {
                                          key: direction,
                                          xmlns: 'http://www.w3.org/2000/svg',
                                          viewBox: '0 0 20 20',
                                          fill: 'currentColor',
                                          'aria-hidden': true,
                                          'data-slot': 'icon',
                                          className: `fi-icon fi-size-md fi-breadcrumbs-item-separator fi-${direction}`,
                                      },
                                      createElement('path', {
                                          fillRule: 'evenodd',
                                          d: path,
                                          clipRule: 'evenodd',
                                      }),
                                  ),
                        ),
                    createElement(
                        attributes.href === undefined ? 'span' : 'a',
                        {
                            ...attributes,
                            'aria-current':
                                index === breadcrumbs.length - 1
                                    ? 'page'
                                    : undefined,
                            className: ['fi-breadcrumbs-item-label', className]
                                .filter(Boolean)
                                .join(' '),
                        },
                        label,
                    ),
                ),
            ),
        ),
    )
})
