import { Editor } from '@tiptap/core'
import getExtensions from './rich-editor/extensions'
import { Selection } from '@tiptap/pm/state'
import { BubbleMenuPlugin } from '@tiptap/extension-bubble-menu'

export default function richEditorFormComponent({
    activePanel,
    deleteCustomBlockButtonIconHtml,
    editCustomBlockButtonIconHtml,
    extensions,
    key,
    isDisabled,
    isLiveDebounced,
    isLiveOnBlur,
    liveDebounce,
    livewireId,
    mergeTags,
    noMergeTagSearchResultsMessage,
    placeholder,
    state,
    statePath,
    uploadingFileMessage,
    floatingToolbars,
}) {
    let editor

    return {
        state,

        activePanel,

        editorSelection: { type: 'text', anchor: 1, head: 1 },

        isUploadingFile: false,

        shouldUpdateState: true,

        editorUpdatedAt: Date.now(),

        async init() {
            editor = new Editor({
                editable: !isDisabled,
                element: this.$refs.editor,
                extensions: await getExtensions({
                    customExtensionUrls: extensions,
                    deleteCustomBlockButtonIconHtml,
                    editCustomBlockButtonIconHtml,
                    editCustomBlockUsing: (id, config) =>
                        this.$wire.mountAction(
                            'customBlock',
                            {
                                editorSelection: this.editorSelection,
                                id,
                                config,
                                mode: 'edit',
                            },
                            { schemaComponent: key },
                        ),
                    insertCustomBlockUsing: (id, dragPosition = null) =>
                        this.$wire.mountAction(
                            'customBlock',
                            { id, dragPosition, mode: 'insert' },
                            { schemaComponent: key },
                        ),
                    key,
                    mergeTags,
                    noMergeTagSearchResultsMessage,
                    placeholder,
                    statePath,
                    uploadingFileMessage,
                    $wire: this.$wire,
                    floatingToolbars,
                }),
                content: this.state,
            })

            Object.keys(floatingToolbars).forEach((key) => {
                const element = this.$refs[`floatingToolbar::${key}`]

                if (!element) {
                    console.warn(`Floating toolbar [${key}] not found.`)

                    return
                }

                editor.registerPlugin(
                    BubbleMenuPlugin({
                        editor,
                        element,
                        pluginKey: `floatingToolbar::${key}`,
                        shouldShow: ({ editor }) =>
                            editor.isFocused && editor.isActive(key),
                        options: {
                            placement: 'bottom',
                            offset: 15,
                        },
                    }),
                )
            })

            editor.on('create', () => {
                this.editorUpdatedAt = Date.now()
            })

            editor.on('update', ({ editor }) =>
                this.$nextTick(() => {
                    this.editorUpdatedAt = Date.now()

                    this.state = editor.getJSON()

                    this.shouldUpdateState = false

                    if (isLiveDebounced) {
                        Alpine.debounce(
                            () => this.$wire.commit(),
                            liveDebounce ?? 300,
                        )
                    }
                }),
            )

            editor.on('selectionUpdate', ({ transaction }) => {
                this.editorUpdatedAt = Date.now()
                this.editorSelection = transaction.selection.toJSON()
            })

            if (isLiveOnBlur) {
                editor.on('blur', () => this.$wire.commit())
            }

            this.$watch('state', () => {
                if (!this.shouldUpdateState) {
                    this.shouldUpdateState = true

                    return
                }

                editor.commands.setContent(this.state)
            })

            window.addEventListener('run-rich-editor-commands', (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.runEditorCommands(event.detail)
            })

            window.addEventListener('rich-editor-uploading-file', (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = true

                event.stopPropagation()
            })

            window.addEventListener('rich-editor-uploaded-file', (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = false

                event.stopPropagation()
            })

            window.dispatchEvent(
                new CustomEvent(`schema-component-${livewireId}-${key}-loaded`),
            )
        },

        getEditor() {
            return editor
        },

        $getEditor() {
            return this.getEditor()
        },

        setEditorSelection(selection) {
            if (!selection) {
                return
            }

            this.editorSelection = selection

            editor
                .chain()
                .command(({ tr }) => {
                    tr.setSelection(
                        Selection.fromJSON(
                            editor.state.doc,
                            this.editorSelection,
                        ),
                    )

                    return true
                })
                .run()
        },

        runEditorCommands({ commands, editorSelection }) {
            this.setEditorSelection(editorSelection)

            let commandChain = editor.chain()

            commands.forEach(
                (command) =>
                    (commandChain = commandChain[command.name](
                        ...(command.arguments ?? []),
                    )),
            )

            commandChain.run()
        },

        togglePanel(id = null) {
            if (this.isPanelActive(id)) {
                this.activePanel = null

                return
            }

            this.activePanel = id
        },

        isPanelActive(id = null) {
            if (id === null) {
                return this.activePanel !== null
            }

            return this.activePanel === id
        },

        insertMergeTag(id) {
            editor
                .chain()
                .focus()
                .insertContent([
                    {
                        type: 'mergeTag',
                        attrs: { id },
                    },
                    {
                        type: 'text',
                        text: ' ',
                    },
                ])
                .run()
        },
    }
}
