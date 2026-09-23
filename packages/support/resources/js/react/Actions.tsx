import { createElement, forwardRef, type ComponentPropsWithoutRef } from 'react'
import { getActionsClasses } from '../components/actions'

export type ActionsProps = Omit<
    ComponentPropsWithoutRef<'div'>,
    'dangerouslySetInnerHTML'
> & {
    alignment?: string | null
    fullWidth?: boolean
}

export default forwardRef<HTMLDivElement, ActionsProps>(function Actions(
    { alignment, fullWidth, className, children, ...attributes },
    ref,
) {
    return createElement(
        'div',
        {
            ...attributes,
            ref,
            className: [getActionsClasses(alignment, fullWidth), className]
                .filter(Boolean)
                .join(' '),
        },
        children,
    )
})
