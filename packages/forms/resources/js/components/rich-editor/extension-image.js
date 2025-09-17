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
        }
    },
})
