import {
    callOrReturn,
    getExtensionField,
    mergeAttributes,
    Node,
} from '@tiptap/core'
import { TextSelection } from '@tiptap/pm/state'

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

    extendNodeSchema(extension) {
        const context = {
            name: extension.name,
            options: extension.options,
            storage: extension.storage,
        }

        return {
            gridRole: callOrReturn(
                getExtensionField(extension, 'gridRole', context),
            ),
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
    const { grid, column } = getGridNodeTypes(schema)
    const columnNodes = []

    for (
        let index = 0;
        index < (startSpan && endSpan ? 2 : columns);
        index += 1
    ) {
        columnNodes.push(
            column.createAndFill({
                'data-col-span': [startSpan, endSpan][index] ?? 1,
            }),
        )
    }

    return grid.createChecked(
        {
            'data-columns': columns,
            'data-stack-at': fromBreakpoint,
        },
        columnNodes,
    )
}

function getGridNodeTypes(schema) {
    if (schema.cached.gridNodeTypes) {
        return schema.cached.gridNodeTypes
    }

    const roles = {}

    Object.keys(schema.nodes).forEach((type) => {
        const nodeType = schema.nodes[type]

        if (nodeType.spec.gridRole) {
            roles[nodeType.spec.gridRole] = nodeType
        }
    })

    schema.cached.gridNodeTypes = roles

    return roles
}
