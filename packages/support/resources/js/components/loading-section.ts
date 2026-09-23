export type LoadingSectionColumns =
    | number
    | string
    | null
    | Record<string, number | string | null>

export interface LoadingSectionOptions {
    columnSpan?: LoadingSectionColumns
    columnStart?: LoadingSectionColumns
    height?: string | null
    loadingLabel?: string | null
}

export function getLoadingSectionLayout({
    columnSpan,
    columnStart,
    height,
}: LoadingSectionOptions) {
    const classes = ['fi-section', 'fi-loading-section', 'fi-grid-col']
    const style: Record<string, string> = { height: height ?? '8rem' }

    for (const [kind, columns] of [
        ['span', columnSpan],
        ['start', columnStart],
    ] as const) {
        const values = typeof columns === 'object' ? columns : { lg: columns }
        for (const [breakpoint, value] of Object.entries(values ?? {})) {
            if (!value || value === '0') continue
            if (kind === 'span' && breakpoint === 'default') {
                if (value === 'hidden') classes.push('fi-hidden')
            } else {
                classes.push(
                    `${breakpoint === 'default' ? '' : `${breakpoint}:`}fi-grid-col-${kind}`,
                )
            }
            const variable = breakpoint.replace(/@/g, 'c').replace(/!/g, 'n')
            style[`--col-${kind}-${variable}`] =
                kind === 'start'
                    ? String(value)
                    : value === 'full'
                      ? '1 / -1'
                      : `span ${value} / span ${value}`
        }
    }

    return { className: classes.join(' '), style }
}
