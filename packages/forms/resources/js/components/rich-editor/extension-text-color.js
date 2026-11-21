import { Mark } from '@tiptap/core'

const sanitizeCssColor = (value) => {
    if (typeof value !== 'string') return null

    const normalizedValue = value.trim()

    if (/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i.test(normalizedValue)) {
        return normalizedValue
    }

    if (/^[a-z]+$/i.test(normalizedValue)) return normalizedValue

    return /^(?:rgb|rgba|hsl|hsla|hwb|lab|lch|oklab|oklch|color)\([^();:"']*\)$/i.test(
        normalizedValue,
    )
        ? normalizedValue
        : null
}

export default Mark.create({
    name: 'textColor',

    addOptions() {
        return {
            textColors: {},
        }
    },

    parseHTML() {
        return [
            {
                tag: 'span',
                getAttrs: (element) => element.classList?.contains('color'),
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        const attrs = { ...HTMLAttributes }
        const existingClass = HTMLAttributes.class
        attrs.class = ['color', existingClass].filter(Boolean).join(' ')

        const colorName = HTMLAttributes['data-color']
        const colors = this.options.textColors || {}
        const config = colors[colorName]

        const hasColorName =
            typeof colorName === 'string' && colorName.length > 0
        const sanitizedColor = hasColorName ? sanitizeCssColor(colorName) : null
        const cssVars = config
            ? `--color: ${config.color}; --dark-color: ${config.darkColor}`
            : sanitizedColor
              ? `--color: ${sanitizedColor}; --dark-color: ${sanitizedColor}`
              : null

        if (cssVars) {
            const existingStyle =
                typeof HTMLAttributes.style === 'string'
                    ? HTMLAttributes.style
                    : ''
            attrs.style = existingStyle
                ? `${cssVars}; ${existingStyle}`
                : cssVars
        }

        return ['span', attrs, 0]
    },

    addAttributes() {
        return {
            'data-color': {
                default: null,
                parseHTML: (element) => element.getAttribute('data-color'),
                renderHTML: (attributes) => {
                    if (!attributes['data-color']) return {}
                    return { 'data-color': attributes['data-color'] }
                },
            },
        }
    },

    addCommands() {
        return {
            setTextColor:
                ({ color }) =>
                ({ commands }) => {
                    return commands.setMark(this.name, { 'data-color': color })
                },
            unsetTextColor:
                () =>
                ({ commands }) => {
                    return commands.unsetMark(this.name)
                },
        }
    },
})
