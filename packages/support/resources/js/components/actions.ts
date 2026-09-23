export function getActionsClasses(
    alignment: string | null = 'start',
    fullWidth = false,
): string {
    alignment ??= 'start'
    if (!alignment.trim()) alignment = ''

    return [
        'fi-ac',
        fullWidth
            ? 'fi-width-full'
            : [
                    'start',
                    'left',
                    'center',
                    'end',
                    'right',
                    'between',
                    'justify',
                ].includes(alignment)
              ? `fi-align-${alignment}`
              : alignment,
    ]
        .filter(Boolean)
        .join(' ')
}
