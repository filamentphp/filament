export function getAvatarClasses(circular = true, size = 'md'): string {
    return [
        'fi-avatar',
        circular && 'fi-circular',
        ['sm', 'md', 'lg'].includes(size) ? `fi-size-${size}` : size,
    ]
        .filter(Boolean)
        .join(' ')
}
