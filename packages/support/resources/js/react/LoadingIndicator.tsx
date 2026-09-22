import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'
import {
    getLoadingIndicatorClasses,
    loadingIndicatorTrack,
    loadingIndicatorArc,
    type LoadingIndicatorSize,
} from '../components/loading-indicator'

export type LoadingIndicatorProps = Omit<
    ComponentPropsWithoutRef<'svg'>,
    'children' | 'dangerouslySetInnerHTML'
> & {
    size?: LoadingIndicatorSize
}

export default forwardRef<SVGSVGElement, LoadingIndicatorProps>(
    function LoadingIndicator({ size = 'md', className, ...attributes }, ref) {
        return createElement(
            'svg',
            {
                fill: 'none',
                viewBox: '0 0 24 24',
                xmlns: 'http://www.w3.org/2000/svg',
                'aria-hidden': true,
                ...attributes,
                ref,
                className: [getLoadingIndicatorClasses(size), className]
                    .filter(Boolean)
                    .join(' '),
            },
            createElement('path', {
                clipRule: 'evenodd',
                d: loadingIndicatorTrack,
                fillRule: 'evenodd',
                fill: 'currentColor',
                opacity: '0.2',
            }),
            createElement('path', {
                d: loadingIndicatorArc,
                fill: 'currentColor',
            }),
        )
    },
)
