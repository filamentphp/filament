export type LoadingSectionColumns =
    | number
    | string
    | null
    | Record<string, number | string | null>

export type LoadingSectionColumnStart =
    | number
    | `${number}`
    | null
    | Record<string, number | `${number}` | null>

export interface LoadingSectionOptions {
    columnSpan?: LoadingSectionColumns
    columnStart?: LoadingSectionColumnStart
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
            if (kind === 'span' && !value) continue
            if (value == null || value === '' || value === 0 || value === '0')
                continue
            let start: number | undefined
            if (kind === 'start') {
                start =
                    typeof value === 'number' ||
                    /^[\t\n\r\f\v ]*[+-]?(?:\d+\.?\d*|\.\d+)(?:e[+-]?\d+)?[\t\n\r\f\v ]*$/i.test(
                        value,
                    )
                        ? Number(value)
                        : NaN
                if (
                    !Number.isFinite(start) ||
                    start < -(2 ** 63) ||
                    start >= 2 ** 63
                ) {
                    throw new TypeError(
                        'columnStart must contain numeric values that fit a PHP integer.',
                    )
                }
                start = Math.trunc(start)
                // PHP coerces scalar floats before `gridColumn()` filters them.
                if (typeof columns === 'number' && !start) continue
            }
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
                    ? String(start)
                    : value === 'full'
                      ? '1 / -1'
                      : `span ${value} / span ${value}`
        }
    }

    return { className: classes.join(' '), style }
}
