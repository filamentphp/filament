import { callOrReturn, getExtensionField, mergeAttributes, Node } from '@tiptap/core'
import { TextSelection } from '@tiptap/pm/state'
import { createGrid } from './grid-utils.js'

export default Node.create({
    name: 'grid',

    group: 'block',

    defining: true,

    isolating: true,

    allowGapCursor: false,

    content: 'gridColumn+',

    gridRole: 'grid',

    addOptions() {
        return {
            HTMLAttributes: {
                class: 'fi-re-grid',
            },
        }
    },

    addAttributes() {
        return {
            'data-asymmetric': {
                default: false,
                parseHTML: (element) => element.getAttribute('data-asymmetric'),
            },
            'data-columns': {
                default: 2,
                parseHTML: (element) => element.getAttribute('data-columns'),
            },
            'data-stack-at': {
                default: 'md',
                parseHTML: (element) => element.getAttribute('data-stack-at'),
            },
            'data-left-span': {
                default: null,
                parseHTML: (element) => element.getAttribute('data-left-span'),
            },
            'data-right-span': {
                default: null,
                parseHTML: (element) => element.getAttribute('data-right-span'),
            },
            'style': {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                    return {
                        style: `grid-template-columns: repeat(${attributes['data-columns']}, 1fr);`,
                    }
                },
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'div',
                getAttrs: (node) => (node.classList.contains("fi-re-grid") && ! node.classList.contains("-column")) && null,
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
    },

    addCommands() {
        return {
            insertGrid:
                ({ columns = 2, stack_at, asymmetric, leftSpan = null, rightSpan = null, coordinates = null } = {}) =>
                    ({ tr, dispatch, editor }) => {
                        const node = createGrid(
                            editor.schema,
                            columns,
                            stack_at,
                            asymmetric,
                            leftSpan,
                            rightSpan
                        )

                        if (dispatch) {
                            const offset = tr.selection.anchor + 1

                            if (! [null, undefined].includes(coordinates?.from)) {
                                tr.replaceRangeWith(coordinates.from, coordinates.to, node)
                                    .scrollIntoView()
                                    .setSelection(TextSelection.near(tr.doc.resolve(coordinates.from)))
                            } else {
                                tr.replaceSelectionWith(node)
                                    .scrollIntoView()
                                    .setSelection(TextSelection.near(tr.doc.resolve(offset)))
                            }
                        }

                        return true
                    },
        }
    },

    extendNodeSchema(extension) {
        const context = {
            name: extension.name,
            options: extension.options,
            storage: extension.storage,
        }

        return {
            gridRole: callOrReturn(getExtensionField(extension, 'gridRole', context)),
        }
    },
})
