import { mergeAttributes, Node } from '@tiptap/core'
import { TextSelection } from '@tiptap/pm/state'

export default Node.create({
    name: 'grid',

    group: 'block',

    defining: true,

    isolating: true,

    allowGapCursor: false,

    content: 'gridColumn+',

    addOptions() {
        return {
            HTMLAttributes: {
                class: 'grid-layout',
            },
        }
    },

    addAttributes() {
        return {
            'data-cols': {
                default: 2,
                parseHTML: (element) => element.getAttribute('data-cols'),
            },
            'data-from-breakpoint': {
                default: 'md',
                parseHTML: (element) =>
                    element.getAttribute('data-from-breakpoint'),
            },
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                    return {
                        style: `grid-template-columns: repeat(${attributes['data-cols']}, 1fr)`,
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
                    node.classList.contains('grid-layout') && null,
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

    addCommands() {
        return {
            insertGrid:
                ({
                    columns = [1, 1],
                    fromBreakpoint,
                    coordinates = null,
                } = {}) =>
                ({ tr, dispatch, editor }) => {
                    const columnNodeType = editor.schema.nodes.gridColumn

                    const spans =
                        Array.isArray(columns) && columns.length
                            ? columns
                            : [1, 1]

                    const columnNodes = []

                    for (let index = 0; index < spans.length; index += 1) {
                        columnNodes.push(
                            columnNodeType.createAndFill({
                                'data-col-span': Number(spans[index] ?? 1) || 1,
                            }),
                        )
                    }

                    const totalColumnsCount = spans
                        .map((v) => Number(v) || 1)
                        .reduce((a, b) => a + b, 0)

                    const node = editor.schema.nodes.grid.createChecked(
                        {
                            'data-cols': totalColumnsCount,
                            'data-from-breakpoint': fromBreakpoint,
                        },
                        columnNodes,
                    )

                    if (dispatch) {
                        const offset = tr.selection.anchor + 1

                        if (![null, undefined].includes(coordinates?.from)) {
                            tr.replaceRangeWith(
                                coordinates.from,
                                coordinates.to,
                                node,
                            )
                                .scrollIntoView()
                                .setSelection(
                                    TextSelection.near(
                                        tr.doc.resolve(coordinates.from),
                                    ),
                                )
                        } else {
                            tr.replaceSelectionWith(node)
                                .scrollIntoView()
                                .setSelection(
                                    TextSelection.near(tr.doc.resolve(offset)),
                                )
                        }
                    }

                    return true
                },
        }
    },
})
