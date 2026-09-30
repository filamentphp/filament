import { findParentNodeClosestToPos, mergeAttributes, Node } from '@tiptap/core'
import { TextSelection } from '@tiptap/pm/state'

const maximumColumnsCount = 12

const getGridContext = (selection, gridNodeName) => {
    const grid = findParentNodeClosestToPos(
        selection.$from,
        (node) => node.type.name === gridNodeName,
    )
    const column =
        selection.node?.type.name === 'gridColumn'
            ? {
                  node: selection.node,
                  pos: selection.from,
                  depth: selection.$from.depth + 1,
              }
            : findParentNodeClosestToPos(
                  selection.$from,
                  (node) => node.type.name === 'gridColumn',
              )

    return grid && column && column.depth === grid.depth + 1
        ? { grid, column }
        : null
}

const getColumnSpan = (column) =>
    Math.max(1, Number(column.attrs['data-col-span']) || 1)

const getGreatestCommonDivisor = (firstNumber, secondNumber) =>
    secondNumber
        ? getGreatestCommonDivisor(secondNumber, firstNumber % secondNumber)
        : firstNumber

const isGridSymmetric = (grid) => {
    let isSymmetric = true

    grid.forEach((column) => {
        if (getColumnSpan(column) !== 1) {
            isSymmetric = false
        }
    })

    return isSymmetric
}

const addGridColumn = ({
    tr,
    dispatch,
    editor,
    gridNodeName,
    shouldInsertAfter,
}) => {
    const context = getGridContext(tr.selection, gridNodeName)

    if (
        !context ||
        !isGridSymmetric(context.grid.node) ||
        context.grid.node.childCount >= maximumColumnsCount
    ) {
        return false
    }

    if (dispatch) {
        const column = editor.schema.nodes.gridColumn.createAndFill({
            'data-col-span': 1,
        })
        const insertionPosition = shouldInsertAfter
            ? context.column.pos + context.column.node.nodeSize
            : context.column.pos

        tr.setNodeMarkup(context.grid.pos, undefined, {
            ...context.grid.node.attrs,
            'data-cols': context.grid.node.childCount + 1,
        })
            .insert(insertionPosition, column)
            .scrollIntoView()
    }

    return true
}

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
                    const columns = Math.max(
                        1,
                        Number(attributes['data-cols']) || 1,
                    )

                    return {
                        style: `--cols: repeat(${columns}, minmax(0, 1fr))`,
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
            addGridColumnBefore:
                () =>
                ({ tr, dispatch, editor }) =>
                    addGridColumn({
                        tr,
                        dispatch,
                        editor,
                        gridNodeName: this.name,
                        shouldInsertAfter: false,
                    }),
            addGridColumnAfter:
                () =>
                ({ tr, dispatch, editor }) =>
                    addGridColumn({
                        tr,
                        dispatch,
                        editor,
                        gridNodeName: this.name,
                        shouldInsertAfter: true,
                    }),
            deleteGridColumn:
                () =>
                ({ tr, dispatch }) => {
                    const context = getGridContext(tr.selection, this.name)

                    if (!context || context.grid.node.childCount <= 1) {
                        return false
                    }

                    if (dispatch) {
                        const remainingColumns = []

                        context.grid.node.forEach((column, offset) => {
                            const position = context.grid.pos + 1 + offset

                            if (position === context.column.pos) {
                                return
                            }

                            remainingColumns.push({
                                column,
                                position,
                                span: getColumnSpan(column),
                            })
                        })

                        const greatestCommonDivisor = remainingColumns
                            .map((column) => column.span)
                            .reduce(getGreatestCommonDivisor)
                        const totalColumnsCount = remainingColumns.reduce(
                            (total, column) =>
                                total + column.span / greatestCommonDivisor,
                            0,
                        )

                        remainingColumns.forEach(
                            ({ column, position, span }) => {
                                tr.setNodeMarkup(position, undefined, {
                                    ...column.attrs,
                                    'data-col-span':
                                        span / greatestCommonDivisor,
                                })
                            },
                        )

                        tr.setNodeMarkup(context.grid.pos, undefined, {
                            ...context.grid.node.attrs,
                            'data-cols': totalColumnsCount,
                        })
                            .delete(
                                context.column.pos,
                                context.column.pos +
                                    context.column.node.nodeSize,
                            )
                            .setSelection(
                                TextSelection.near(
                                    tr.doc.resolve(context.column.pos),
                                ),
                            )
                            .scrollIntoView()
                    }

                    return true
                },
            deleteGrid:
                () =>
                ({ tr, dispatch }) => {
                    const grid = findParentNodeClosestToPos(
                        tr.selection.$from,
                        (node) => node.type.name === this.name,
                    )

                    if (!grid) {
                        return false
                    }

                    if (dispatch) {
                        const content = []

                        grid.node.forEach((column) => {
                            column.forEach((node) => content.push(node))
                        })

                        tr.replaceWith(
                            grid.pos,
                            grid.pos + grid.node.nodeSize,
                            content,
                        )
                            .setSelection(
                                TextSelection.near(tr.doc.resolve(grid.pos)),
                            )
                            .scrollIntoView()
                    }

                    return true
                },
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
