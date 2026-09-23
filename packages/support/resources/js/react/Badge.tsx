import {
    createElement,
    forwardRef,
    useEffect,
    useImperativeHandle,
    useRef,
    type AnchorHTMLAttributes,
    type ButtonHTMLAttributes,
    type ReactNode,
} from 'react'
import { getBadgeClasses, type BadgeOptions } from '../components/badge'
import { interactive } from '../components/interactive'
import Icon from './Icon'
import LoadingIndicator from './LoadingIndicator'

export type BadgeProps = Omit<
    ButtonHTMLAttributes<HTMLElement> & AnchorHTMLAttributes<HTMLElement>,
    'children' | 'dangerouslySetInnerHTML' | 'color'
> &
    Omit<BadgeOptions, 'tag'> & {
        children?: ReactNode
        icon?: ReactNode
    } & (
        | {
              tag?: 'span'
              onDelete?: React.MouseEventHandler<HTMLButtonElement>
          }
        | { tag: 'a' | 'button'; onDelete?: never }
    )

export default forwardRef<HTMLElement, BadgeProps>(function Badge(
    {
        tag = 'span',
        color = 'primary',
        size = 'md',
        icon,
        iconPosition = 'before',
        iconSize = 'sm',
        loading = false,
        disabled = false,
        tooltip,
        keyBindings,
        deleteLabel = 'Delete',
        deleteLoading = false,
        onDelete,
        children,
        className,
        style,
        onClick,
        href,
        type = 'button',
        tabIndex,
        ...attributes
    },
    ref,
) {
    const element = useRef<HTMLElement>(null)
    useImperativeHandle(ref, () => element.current!, [tag])
    const blocked = disabled || loading
    const keyBindingsSignature = JSON.stringify(keyBindings ?? [])
    useEffect(
        () =>
            element.current
                ? interactive(element.current, {
                      tooltip,
                      keyBindings: JSON.parse(keyBindingsSignature),
                      disabled: blocked,
                  })
                : undefined,
        [tooltip, keyBindingsSignature, blocked, tag],
    )
    if (onDelete && tag !== 'span')
        throw new Error('Deletable badges must use tag="span".')
    const indicator = loading
        ? createElement(LoadingIndicator, { size: iconSize })
        : icon != null
          ? createElement(Icon, { size: iconSize, 'aria-hidden': true }, icon)
          : null
    return createElement(
        tag,
        {
            ...attributes,
            ref: element,
            href: tag === 'a' && !blocked ? href : undefined,
            role:
                attributes.role ??
                (tag === 'a' && href != null && blocked ? 'link' : undefined),
            type: tag === 'button' ? type : undefined,
            disabled:
                tag === 'button' && blocked && !tooltip ? true : undefined,
            'aria-disabled': blocked || undefined,
            'aria-busy': loading || undefined,
            tabIndex: blocked && tooltip ? (tabIndex ?? 0) : tabIndex,
            style: {
                ...style,
                ...(blocked && tooltip ? { pointerEvents: 'auto' } : {}),
            },
            className: [
                getBadgeClasses({ color, size, disabled, loading }),
                className,
            ]
                .filter(Boolean)
                .join(' '),
            onClick: (event: React.MouseEvent<HTMLElement>) => {
                if (blocked) {
                    event.preventDefault()
                    event.stopPropagation()
                    return
                }
                onClick?.(event)
            },
        },
        iconPosition === 'before' ? indicator : null,
        createElement(
            'span',
            { className: 'fi-badge-label-ctn' },
            createElement('span', { className: 'fi-badge-label' }, children),
        ),
        onDelete
            ? createElement(
                  'button',
                  {
                      type: 'button',
                      className: 'fi-badge-delete-btn',
                      disabled: blocked || deleteLoading,
                      'aria-busy': deleteLoading || undefined,
                      onClick: (event: React.MouseEvent<HTMLButtonElement>) => {
                          event.stopPropagation()
                          if (!blocked && !deleteLoading) onDelete(event)
                      },
                  },
                  deleteLoading
                      ? createElement(LoadingIndicator, { size: 'xs' })
                      : createElement('span', {
                            className: 'fi-badge-delete-btn-icon',
                            'aria-hidden': true,
                        }),
                  createElement(
                      'span',
                      { className: 'fi-sr-only' },
                      deleteLabel.trim() ? deleteLabel : 'Delete',
                  ),
              )
            : iconPosition === 'after'
              ? indicator
              : null,
    )
})
