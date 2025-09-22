import { Node, mergeAttributes } from '@tiptap/core'

export default Node.create({
    name: 'gridColumn',

    content: 'block+',

    isolating: true,

    addOptions() {
        return {
            HTMLAttributes: {
                class: 'grid-layout-col',
            },
        }
    },

    addAttributes() {
        return {
            'data-col-span': {
                default: 1,
                parseHTML: (element) => element.getAttribute('data-col-span'),
                renderHTML: (attributes) => {
                    return {
                        'data-col-span': attributes['data-col-span'] ?? 1,
                    }
                },
            },
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                    return {
                        style: `grid-column: span ${attributes['data-col-span'] ?? 1};`,
                    }
                },
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'div',
                getAttrs: (node) =>
                    node.classList.contains('grid-layout-col') && null,
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            mergeAttributes(this.options.HTMLAttributes, HTMLAttributes),
            0,
        ]
    },
})
