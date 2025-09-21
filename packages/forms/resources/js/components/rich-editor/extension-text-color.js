import { Mark } from '@tiptap/core'

export default Mark.create({
    name: 'textColor',

    parseHTML() {
        return [
            {
                tag: 'span',
                getAttrs: (element) => element.classList?.contains('color'),
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['span', { ...HTMLAttributes, class: ['color', HTMLAttributes.class].filter(Boolean).join(' ') }, 0]
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
