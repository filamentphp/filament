import type { IconSize } from './icon'
import type { InteractiveOptions } from './interactive'

export interface BadgeOptions extends InteractiveOptions {
    tag?: 'span' | 'a' | 'button'
    color?: string
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'
    iconPosition?: 'before' | 'after'
    iconSize?: IconSize
    loading?: boolean
    deleteLabel?: string
    deleteLoading?: boolean
}

export function getBadgeClasses({
    color = 'primary',
    size = 'md',
    disabled = false,
    loading = false,
}: BadgeOptions): string {
    const colors =
        typeof window === 'undefined'
            ? undefined
            : (
                  window as Window & {
                      filamentData?: {
                          supportComponentColors?: {
                              badge?: Record<string, string[]>
                          }
                      }
                  }
              ).filamentData?.supportComponentColors?.badge
    return [
        'fi-badge',
        `fi-size-${size}`,
        colors?.[color]?.join(' ') ??
            (color !== 'gray' &&
                `fi-color fi-color-${color.replace(/[^a-zA-Z0-9_-]/g, '')} fi-text-color-700 dark:fi-text-color-200`),
        (disabled || loading) && 'fi-disabled',
    ]
        .filter(Boolean)
        .join(' ')
}
