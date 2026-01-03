import { Editor } from '@tiptap/core'
import getExtensions from './rich-editor/extensions'
import { Selection } from '@tiptap/pm/state'
import { BubbleMenuPlugin } from '@tiptap/extension-bubble-menu'

export default function richEditorFormComponent({
    acceptedFileTypes,
    acceptedFileTypesValidationMessage,
    activePanel,
    canAttachFiles,
    deleteCustomBlockButtonIconHtml,
    editCustomBlockButtonIconHtml,
    extensions,
    floatingToolbars,
    hasResizableImages,
    isDisabled,
    isLiveDebounced,
    isLiveOnBlur,
    key,
    linkProtocols,
    liveDebounce,
    livewireId,
    maxFileSize,
    maxFileSizeValidationMessage,
    mergeTags,
    mentions,
    getMentionSearchResultsUsing,
    getMentionLabelsUsing,
    noMergeTagSearchResultsMessage,
    placeholder,
    state,
    statePath,
    textColors,
    uploadingFileMessage,
}) {
    let editor
    let eventListeners = []
    let isDestroyed = false

    return {
        state,

        activePanel,

        editorSelection: { type: 'text', anchor: 1, head: 1 },

        isUploadingFile: false,

        fileValidationMessage: null,

        shouldUpdateState: true,

        editorUpdatedAt: Date.now(),

        async init() {
            editor = new Editor({
                editable: !isDisabled,
                element: this.$refs.editor,
                extensions: await getExtensions({
                    acceptedFileTypes,
                    acceptedFileTypesValidationMessage,
                    canAttachFiles,
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
                    floatingToolbars,
                    hasResizableImages,
                    insertCustomBlockUsing: (id, dragPosition = null) =>
                        this.$wire.mountAction(
                            'customBlock',
                            { id, dragPosition, mode: 'insert' },
                            { schemaComponent: key },
                        ),
                    key,
                    linkProtocols,
                    maxFileSize,
                    maxFileSizeValidationMessage,
                    mergeTags,
                    mentions,
                    getMentionSearchResultsUsing,
                    getMentionLabelsUsing,
                    noMergeTagSearchResultsMessage,
                    placeholder,
                    statePath,
                    textColors,
                    uploadingFileMessage,
                    $wire: this.$wire,
                }),
                content: this.state,
            })

            const hasParagraphToolbar = 'paragraph' in floatingToolbars

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
                        shouldShow: ({ editor }) => {
                            if (key === 'paragraph') {
                                return (
                                    editor.isFocused &&
                                    editor.isActive(key) &&
                                    !editor.state.selection.empty
                                )
                            }

                            if (
                                hasParagraphToolbar &&
                                !editor.state.selection.empty &&
                                editor.isActive('paragraph')
                            ) {
                                return false
                            }

                            return editor.isFocused && editor.isActive(key)
                        },
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

            const debouncedCommit = Alpine.debounce(() => {
                if (!isDestroyed) {
                    this.$wire.commit()
                }
            }, liveDebounce ?? 300)

            editor.on('update', ({ editor }) =>
                this.$nextTick(() => {
                    if (isDestroyed) return

                    this.editorUpdatedAt = Date.now()

                    this.state = editor.getJSON()

                    this.shouldUpdateState = false

                    this.fileValidationMessage = null

                    if (isLiveDebounced) {
                        debouncedCommit()
                    }
                }),
            )

            editor.on('selectionUpdate', ({ transaction }) => {
                if (isDestroyed) return

                this.editorUpdatedAt = Date.now()
                this.editorSelection = transaction.selection.toJSON()
            })

            editor.on('transaction', () => {
                if (isDestroyed) return

                this.editorUpdatedAt = Date.now()
            })

            if (isLiveOnBlur) {
                editor.on('blur', () => {
                    if (!isDestroyed) {
                        this.$wire.commit()
                    }
                })
            }

            this.$watch('state', () => {
                if (isDestroyed) return

                if (!this.shouldUpdateState) {
                    this.shouldUpdateState = true

                    return
                }

                editor.commands.setContent(this.state)
            })

            const runCommandsHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.runEditorCommands(event.detail)
            }
            window.addEventListener(
                'run-rich-editor-commands',
                runCommandsHandler,
            )
            eventListeners.push([
                'run-rich-editor-commands',
                runCommandsHandler,
            ])

            const uploadingFileHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = true
                this.fileValidationMessage = null

                event.stopPropagation()
            }
            window.addEventListener(
                'rich-editor-uploading-file',
                uploadingFileHandler,
            )
            eventListeners.push([
                'rich-editor-uploading-file',
                uploadingFileHandler,
            ])

            const uploadedFileHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = false

                event.stopPropagation()
            }
            window.addEventListener(
                'rich-editor-uploaded-file',
                uploadedFileHandler,
            )
            eventListeners.push([
                'rich-editor-uploaded-file',
                uploadedFileHandler,
            ])

            const validationMessageHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = false
                this.fileValidationMessage = event.detail.validationMessage

                event.stopPropagation()
            }
            window.addEventListener(
                'rich-editor-file-validation-message',
                validationMessageHandler,
            )
            eventListeners.push([
                'rich-editor-file-validation-message',
                validationMessageHandler,
            ])

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

        destroy() {
            isDestroyed = true

            eventListeners.forEach(([eventName, handler]) => {
                window.removeEventListener(eventName, handler)
            })
            eventListeners = []

            if (editor) {
                editor.destroy()
                editor = null
            }

            this.shouldUpdateState = true
        },
    }
}
