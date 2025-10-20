import { Mark } from '@tiptap/core'

export default Mark.create({
    name: 'small',

    parseHTML() {
        return [
            {
                tag: 'small',
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['small', HTMLAttributes, 0]
    },

    addCommands() {
        return {
            setSmall:
                () =>
                ({ commands }) => {
                    return commands.setMark(this.name)
                },
            toggleSmall:
                () =>
                ({ commands }) => {
                    return commands.toggleMark(this.name)
                },
            unsetSmall:
                () =>
                ({ commands }) => {
                    return commands.unsetMark(this.name)
                },
        }
    },
})
