import Image from '@tiptap/extension-image'

const sanitizeStyleLength = (value) => {
    if (!['number', 'string'].includes(typeof value)) return null

    const normalizedValue = String(value).trim()

    return /^\d+(?:\.\d+)?(?:%|px|em|rem|vw|vh|vmin|vmax|pt|pc|cm|mm|in|ch|ex)?$/i.test(
        normalizedValue,
    )
        ? normalizedValue
        : null
}

const normalizeStyleLength = (value) =>
    /^\d+(?:\.\d+)?$/.test(value) ? `${value}px` : value

export default Image.extend({
    addAttributes() {
        return {
            ...this.parent?.(),

            id: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-id'),
                renderHTML: (attributes) => {
                    if (!attributes.id) {
                        return {}
                    }

                    return {
                        'data-id': attributes.id,
                    }
                },
            },

            width: {
                default: null,
                parseHTML: (element) =>
                    element.getAttribute('width') ||
                    element.style.width ||
                    null,
                renderHTML: (attributes) => {
                    const width = sanitizeStyleLength(attributes.width)

                    if (!width) {
                        return {}
                    }

                    return {
                        width,
                        style: `width: ${normalizeStyleLength(width)}`,
                    }
                },
            },

            height: {
                default: null,
                parseHTML: (element) =>
                    element.getAttribute('height') ||
                    element.style.height ||
                    null,
                renderHTML: (attributes) => {
                    const height = sanitizeStyleLength(attributes.height)

                    if (!height) {
                        return {}
                    }

                    return {
                        height,
                        style: `height: ${normalizeStyleLength(height)}`,
                    }
                },
            },
        }
    },
})
