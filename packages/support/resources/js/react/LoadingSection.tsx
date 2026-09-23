import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'
import {
    getLoadingSectionLayout,
    type LoadingSectionOptions,
} from '../components/loading-section'

export type LoadingSectionProps = Omit<
    ComponentPropsWithoutRef<'div'>,
    'children' | 'dangerouslySetInnerHTML'
> &
    LoadingSectionOptions

export default forwardRef<HTMLDivElement, LoadingSectionProps>(
    function LoadingSection(
        {
            columnSpan,
            columnStart,
            height,
            loadingLabel,
            className,
            style,
            ...attributes
        },
        ref,
    ) {
        const layout = getLoadingSectionLayout({
            columnSpan,
            columnStart,
            height,
        })
        return createElement(
            'div',
            {
                role: 'status',
                'aria-busy': true,
                ...attributes,
                ref,
                className: [layout.className, className]
                    .filter(Boolean)
                    .join(' '),
                style: { ...layout.style, ...style },
            },
            createElement(
                'span',
                { className: 'fi-sr-only' },
                loadingLabel ?? 'Loading...',
            ),
        )
    },
)
