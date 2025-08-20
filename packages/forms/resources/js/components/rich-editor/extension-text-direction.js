import { Extension } from '@tiptap/core'

// Adds support for setting text direction (LTR / RTL) per block (paragraph & heading).
export default Extension.create({
    name: 'textDirection',

    addGlobalAttributes() {
        return [
            {
                types: ['paragraph', 'heading'],
                attributes: {
                    dir: {
                        default: null,
                        parseHTML: (element) =>
                            element.getAttribute('dir') ?? null,
                        renderHTML: (attributes) => {
                            if (!attributes.dir) return {}
                            return { dir: attributes.dir }
                        },
                    },
                },
            },
        ]
    },

    addCommands() {
        return {
            setTextDirection:
                (dir) =>
                ({ commands }) => {
                    if (!dir) {
                        // Unset direction
                        return (
                            commands.updateAttributes('paragraph', {
                                dir: null,
                            }) ||
                            commands.updateAttributes('heading', { dir: null })
                        )
                    }

                    if (!['ltr', 'rtl'].includes(dir)) {
                        return false
                    }

                    // Try to update paragraph or heading, depending on current selection
                    const updatedParagraph = commands.updateAttributes(
                        'paragraph',
                        { dir },
                    )
                    const updatedHeading = commands.updateAttributes(
                        'heading',
                        { dir },
                    )

                    return updatedParagraph || updatedHeading
                },
            unsetTextDirection:
                () =>
                ({ commands }) =>
                    commands.updateAttributes('paragraph', { dir: null }) ||
                    commands.updateAttributes('heading', { dir: null }),
        }
    },
})
