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
            deleteGrid:
                () =>
                ({ tr, dispatch, editor, state }) => {
                    const { selection } = state
                    const gridType = editor.schema.nodes.grid

                    // If the selection is a node selection on the grid itself
                    if (selection.node && selection.node.type === gridType) {
                        if (dispatch) {
                            const from = selection.from
                            const to = selection.to
                            tr.delete(from, to)
                                .scrollIntoView()
                                .setSelection(
                                    TextSelection.near(
                                        tr.doc.resolve(Math.max(0, from - 1)),
                                        -1,
                                    ),
                                )
                        }
                        return true
                    }

                    // Otherwise, find the nearest ancestor grid node from the current selection
                    const $from = selection.$from
                    for (let depth = $from.depth; depth > 0; depth--) {
                        const node = $from.node(depth)
                        if (node.type === gridType) {
                            const fromPos = $from.before(depth)
                            const toPos = $from.after(depth)
                            if (dispatch) {
                                tr.delete(fromPos, toPos)
                                    .scrollIntoView()
                                    .setSelection(
                                        TextSelection.near(
                                            tr.doc.resolve(Math.max(0, fromPos - 1)),
                                            -1,
                                        ),
                                    )
                            }
                            return true
                        }
                    }

                    return false
                },
        }
    },
})
