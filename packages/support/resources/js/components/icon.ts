export type IconSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'

export function getIconClasses(size: IconSize = 'md'): string {
    return `fi-icon fi-size-${size}`
}
