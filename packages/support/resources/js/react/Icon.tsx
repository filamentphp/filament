import { createElement, forwardRef, type ImgHTMLAttributes } from 'react'
import { getIconClasses, type IconSize } from '../components/icon'

export type IconProps = Omit<
    ImgHTMLAttributes<HTMLSpanElement | HTMLImageElement>,
    'dangerouslySetInnerHTML'
> & {
    size?: IconSize
}

export default forwardRef<HTMLSpanElement | HTMLImageElement, IconProps>(
    function Icon(
        { src, alt = '', size = 'md', className, children, ...attributes },
        ref,
    ) {
        const props = {
            ...attributes,
            ref,
            className: [getIconClasses(size), className]
                .filter(Boolean)
                .join(' '),
        }

        if (src) return createElement('img', { ...props, src, alt })

        return children == null ? null : createElement('span', props, children)
    },
)
