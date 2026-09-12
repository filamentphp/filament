import * as TipTapCore from '@tiptap/core'
import * as TipTapPmState from '@tiptap/pm/state'
import * as TipTapPmView from '@tiptap/pm/view'
import * as TipTapPmModel from '@tiptap/pm/model'
import getExtensions from './rich-editor/extensions'
import { BubbleMenuPlugin } from '@tiptap/extension-bubble-menu'

const { Editor } = TipTapCore
const { Selection } = TipTapPmState

// Expose the bundled TipTap/ProseMirror modules so custom extensions loaded
// via `RichContentPlugin::getTipTapJsExtensions()` can share the same
// ProseMirror instance (required for `instanceof` checks across bundles).
window.FilamentRichEditor = window.FilamentRichEditor || {}
window.FilamentRichEditor.tiptap = {
    core: TipTapCore,
    pmState: TipTapPmState,
    pmView: TipTapPmView,
    pmModel: TipTapPmModel,
}

export default function richEditorFormComponent({
    acceptedFileTypes,
    acceptedFileTypesValidationMessage,
    activePanel,
    canAttachFiles,
    deleteCustomBlockButtonIconHtml,
    deleteCustomBlockButtonLabel,
    editCustomBlockButtonIconHtml,
    editCustomBlockButtonLabel,
    extensions,
    floatingToolbars,
    hasResizableImages,
    hasStickyToolbar = false,
    isDisabled,
    isLiveDebounced,
    isLiveOnBlur,
    key,
    label,
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
    let toolbarResizeObserver
    let modalResizeObserver
    let modalMutationObserver

    return {
        state,

        activePanel,

        customBlockSearch: '',

        editorSelection: { type: 'text', anchor: 1, head: 1 },

        isUploadingFile: false,

        fileValidationMessage: null,

        shouldUpdateState: true,

        editorUpdatedAt: Date.now(),

        async init() {
            if (hasStickyToolbar && this.$refs.toolbar) {
                toolbarResizeObserver = new ResizeObserver(([entry]) => {
                    this.$el.style.setProperty(
                        '--fi-fo-rich-editor-toolbar-height',
                        `${entry.borderBoxSize[0].blockSize}px`,
                    )
                })
                toolbarResizeObserver.observe(this.$refs.toolbar, {
                    box: 'border-box',
                })
            }

            const modal = this.$el.closest('.fi-modal')
            const hasStickyPanels = !!this.$el.querySelector(
                '.fi-fo-rich-editor-sticky-panels',
            )

            if (modal && (hasStickyToolbar || hasStickyPanels)) {
                const modalWindow = modal.querySelector(
                    ':scope > .fi-modal-window-ctn > .fi-modal-window',
                )
                let modalHeader
                let modalFooter
                let modalViewport

                const updateModalMeasurements = () => {
                    const nextModalHeader = modal.matches(
                        '.fi-modal-has-sticky-header',
                    )
                        ? modalWindow.querySelector(':scope > .fi-modal-header')
                        : null
                    const nextModalFooter =
                        hasStickyPanels &&
                        modal.matches('.fi-modal-has-sticky-footer')
                            ? modalWindow.querySelector(
                                  ':scope > .fi-modal-footer',
                              )
                            : null
                    const nextModalViewport = hasStickyPanels
                        ? modalWindow.parentElement
                        : null

                    for (const [previous, next, property, box] of [
                        [
                            modalHeader,
                            nextModalHeader,
                            '--fi-fo-rich-editor-modal-header-height',
                            'border-box',
                        ],
                        [
                            modalFooter,
                            nextModalFooter,
                            '--fi-fo-rich-editor-modal-footer-height',
                            'border-box',
                        ],
                        [
                            modalViewport,
                            nextModalViewport,
                            '--fi-fo-rich-editor-modal-viewport-height',
                            'content-box',
                        ],
                    ]) {
                        if (previous === next) {
                            continue
                        }

                        if (previous) {
                            modalResizeObserver.unobserve(previous)
                        }

                        this.$el.style.removeProperty(property)

                        if (next) {
                            modalResizeObserver.observe(next, { box })
                        }
                    }

                    modalHeader = nextModalHeader
                    modalFooter = nextModalFooter
                    modalViewport = nextModalViewport
                }

                modalResizeObserver = new ResizeObserver((entries) => {
                    for (const entry of entries) {
                        const property =
                            entry.target === modalHeader
                                ? '--fi-fo-rich-editor-modal-header-height'
                                : entry.target === modalFooter
                                  ? '--fi-fo-rich-editor-modal-footer-height'
                                  : '--fi-fo-rich-editor-modal-viewport-height'
                        const height =
                            entry.target === modalViewport
                                ? entry.contentBoxSize[0].blockSize
                                : entry.borderBoxSize[0].blockSize

                        this.$el.style.setProperty(property, `${height}px`)
                    }
                })

                updateModalMeasurements()

                modalMutationObserver = new MutationObserver(
                    updateModalMeasurements,
                )
                modalMutationObserver.observe(modal, {
                    attributes: true,
                    attributeFilter: ['class'],
                })
                modalMutationObserver.observe(modalWindow, {
                    childList: true,
                })
            }

            editor = new Editor({
                editable: !isDisabled,
                element: this.$refs.editor,
                editorProps: {
                    attributes: {
                        ...(label ? { 'aria-label': label } : {}),
                    },
                },
                extensions: await getExtensions({
                    acceptedFileTypes,
                    acceptedFileTypesValidationMessage,
                    canAttachFiles,
                    customExtensionUrls: extensions,
                    deleteCustomBlockButtonIconHtml,
                    deleteCustomBlockButtonLabel,
                    editCustomBlockButtonIconHtml,
                    editCustomBlockButtonLabel,
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

        matchesCustomBlockSearch(labels) {
            const search = this.customBlockSearch.trim().toLocaleLowerCase()

            return (
                !search ||
                labels.some((label) =>
                    label.toLocaleLowerCase().includes(search),
                )
            )
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
            toolbarResizeObserver?.disconnect()
            modalResizeObserver?.disconnect()
            modalMutationObserver?.disconnect()

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
