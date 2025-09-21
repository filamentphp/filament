import { Mark } from '@tiptap/core'

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
        const colorName = HTMLAttributes['data-color']
        const config = this.options.textColors?.[colorName]

        // Build CSS variables string from provided textColors map
        let variables = ''
        if (config && (config.color || config.darkColor)) {
            const cssParts = []
            if (config.color) cssParts.push(`--color: ${config.color}`)
            if (config.darkColor) cssParts.push(`--dark-color: ${config.darkColor}`)
            variables = cssParts.join('; ')
        }

        // Merge with any existing style (string). If object is provided, ignore to keep minimal complexity
        let style = HTMLAttributes.style || ''
        if (variables) {
            style = [variables, style].filter(Boolean).join('; ')
        }

        const attrs = { ...HTMLAttributes, class: ['color', HTMLAttributes.class].filter(Boolean).join(' ') }
        if (style) attrs.style = style

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
