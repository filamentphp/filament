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
            'data-columns': {
                default: 2,
                parseHTML: (element) => element.getAttribute('data-columns'),
            },
            'data-stack-at': {
                default: 'md',
                parseHTML: (element) => element.getAttribute('data-stack-at'),
            },
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                    return {
                        style: `display: grid; gap: 1rem; grid-template-columns: repeat(${attributes['data-columns']}, 1fr);`,
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
                    node.classList.contains('grid-layout') &&
                    !node.classList.contains('-col') &&
                    null,
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
                    columns = 2,
                    fromBreakpoint,
                    startSpan = null,
                    endSpan = null,
                    coordinates = null,
                } = {}) =>
                ({ tr, dispatch, editor }) => {
                    const node = createGrid(
                        editor.schema,
                        columns,
                        fromBreakpoint,
                        startSpan,
                        endSpan,
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

function createGrid(
    schema,
    columns,
    fromBreakpoint,
    startSpan = null,
    endSpan = null,
) {
    const columnNodeType = schema.nodes.gridColumn

    const columnNodes = []

    for (
        let index = 0;
        index < (startSpan && endSpan ? 2 : columns);
        index += 1
    ) {
        columnNodes.push(
            columnNodeType.createAndFill({
                'data-col-span': [startSpan, endSpan][index] ?? 1,
            }),
        )
    }

    return schema.nodes.grid.createChecked(
        {
            'data-columns': columns,
            'data-stack-at': fromBreakpoint,
        },
        columnNodes,
    )
}
