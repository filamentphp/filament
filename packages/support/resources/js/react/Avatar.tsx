import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'
import { getAvatarClasses } from '../components/avatar'

export type AvatarProps = ComponentPropsWithoutRef<'img'> & {
    circular?: boolean
    size?: string
}

export default forwardRef<HTMLImageElement, AvatarProps>(function Avatar(
    { alt = '', circular = true, size = 'md', className, ...attributes },
    ref,
) {
    return createElement('img', {
        ...attributes,
        ref,
        alt,
        className: [getAvatarClasses(circular, size), className]
            .filter(Boolean)
            .join(' '),
    })
})
