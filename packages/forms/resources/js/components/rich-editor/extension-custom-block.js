import { mergeAttributes, Node, NodePos } from '@tiptap/core'
import { Node as ProseMirrorNode } from '@tiptap/pm/model'
import { Plugin, PluginKey } from '@tiptap/pm/state'

export default Node.create({
    name: 'customBlock',

    group: 'block',

    atom: true,

    defining: true,

    draggable: true,

    selectable: true,

    isolating: true,

    allowGapCursor: true,

    inline: false,

    addNodeView() {
        return ({
            editor,
            node,
            getPos,
            HTMLAttributes,
            decorations,
            extension,
        }) => {
            const dom = document.createElement('div')
            dom.setAttribute('data-config', node.attrs.config)
            dom.setAttribute('data-id', node.attrs.id)
            dom.setAttribute('data-type', 'customBlock')

            const header = document.createElement('div')
            header.className =
                'fi-fo-rich-editor-custom-block-header fi-not-prose'
            dom.appendChild(header)

            if (
                editor.isEditable &&
                typeof node.attrs.config === 'object' &&
                node.attrs.config !== null &&
                Object.keys(node.attrs.config).length > 0
            ) {
                const editButtonContainer = document.createElement('div')
                editButtonContainer.className =
                    'fi-fo-rich-editor-custom-block-edit-btn-ctn'
                header.appendChild(editButtonContainer)

                const editButton = document.createElement('button')
                editButton.className = 'fi-icon-btn'
                editButton.type = 'button'
                editButton.innerHTML =
                    extension.options.editCustomBlockButtonIconHtml
                editButton.addEventListener('click', () =>
                    extension.options.editCustomBlockUsing(
                        node.attrs.id,
                        node.attrs.config,
                    ),
                )
                editButtonContainer.appendChild(editButton)
            }

            const heading = document.createElement('p')
            heading.className = 'fi-fo-rich-editor-custom-block-heading'
            heading.textContent = node.attrs.label
            header.appendChild(heading)

            if (editor.isEditable) {
                const deleteButtonContainer = document.createElement('div')
                deleteButtonContainer.className =
                    'fi-fo-rich-editor-custom-block-delete-btn-ctn'
                header.appendChild(deleteButtonContainer)

                const deleteButton = document.createElement('button')
                deleteButton.className = 'fi-icon-btn'
                deleteButton.type = 'button'
                deleteButton.innerHTML =
                    extension.options.deleteCustomBlockButtonIconHtml
                deleteButton.addEventListener('click', () =>
                    editor
                        .chain()
                        .setNodeSelection(getPos())
                        .deleteSelection()
                        .run(),
                )
                deleteButtonContainer.appendChild(deleteButton)
            }

            if (node.attrs.preview) {
                const preview = document.createElement('div')
                preview.className =
                    'fi-fo-rich-editor-custom-block-preview fi-not-prose'
                preview.innerHTML = new TextDecoder().decode(
                    Uint8Array.from(atob(node.attrs.preview), (char) =>
                        char.charCodeAt(0),
                    ),
                )
                dom.appendChild(preview)
            }

            return {
                dom,
            }
        }
    },

    addOptions() {
        return {
            deleteCustomBlockButtonIconHtml: null,
            editCustomBlockButtonIconHtml: null,
            editCustomBlockUsing: () => {},
            insertCustomBlockUsing: () => {},
        }
    },

    addAttributes() {
        return {
            config: {
                default: null,
                parseHTML: (element) =>
                    JSON.parse(element.getAttribute('data-config')),
            },

            id: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-id'),
                renderHTML: (attributes) => {
                    if (!attributes.id) {
                        return {}
                    }

                    return {
                        'data-id': attributes.id,
                    }
                },
            },

            label: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-label'),
                rendered: false,
            },

            preview: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-preview'),
                rendered: false,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: `div[data-type="${this.name}"]`,
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes(HTMLAttributes)]
    },

    addKeyboardShortcuts() {
        return {
            Backspace: () =>
                this.editor.commands.command(({ tr, state }) => {
                    let isCustomBlock = false
                    const { selection } = state
                    const { empty, anchor } = selection

                    if (!empty) {
                        return false
                    }

                    // Store node and position for later use
                    let customBlockNode = new ProseMirrorNode()
                    let customBlockPos = 0

                    state.doc.nodesBetween(anchor - 1, anchor, (node, pos) => {
                        if (node.type.name === this.name) {
                            isCustomBlock = true
                            customBlockNode = node
                            customBlockPos = pos
                            return false
                        }
                    })

                    return isCustomBlock
                }),
        }
    },

    addProseMirrorPlugins() {
        const { insertCustomBlockUsing } = this.options

        return [
            new Plugin({
                props: {
                    handleDrop(view, event) {
                        if (!event) {
                            return false
                        }

                        event.preventDefault()

                        if (!event.dataTransfer.getData('customBlock')) {
                            return false
                        }

                        const customBlockId =
                            event.dataTransfer.getData('customBlock')

                        insertCustomBlockUsing(
                            customBlockId,
                            view.posAtCoords({
                                left: event.clientX,
                                top: event.clientY,
                            }).pos,
                        )

                        return false
                    },
                },
            }),
        ]
    },
})
