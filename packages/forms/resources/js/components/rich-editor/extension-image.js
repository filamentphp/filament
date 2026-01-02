import Image from '@tiptap/extension-image'

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
                    if (!attributes.width) {
                        return {}
                    }

                    return {
                        width: attributes.width,
                        style: `width: ${attributes.width}`,
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
                    if (!attributes.height) {
                        return {}
                    }

                    return {
                        height: attributes.height,
                        style: `height: ${attributes.height}`,
                    }
                },
            },
        }
    },
})
