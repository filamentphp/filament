import { Extension } from '@tiptap/core'
import { Plugin, PluginKey } from '@tiptap/pm/state'

const allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp']

const dispatchFormEvent = (editorView, name, detail = {}) => {
    editorView.dom.closest('form')?.dispatchEvent(
        new CustomEvent(name, {
            composed: true,
            cancelable: true,
            detail,
        }),
    )
}

const LocalFilesPlugin = ({
    editor,
    get$WireUsing,
    key,
    statePath,
    uploadingMessage,
}) => {
    const getFileAttachmentUrl = (fileKey) =>
        get$WireUsing().callSchemaComponentMethod(
            key,
            'getUploadedFileAttachmentTemporaryUrl',
            {
                attachment: fileKey,
            },
        )

    return new Plugin({
        key: new PluginKey('localFiles'),
        props: {
            handleDrop(editorView, event) {
                if (!event.dataTransfer?.files.length) {
                    return false
                }

                const files = Array.from(event.dataTransfer.files).filter(
                    (file) => allowedMimeTypes.includes(file.type),
                )

                if (!files.length) {
                    return false
                }

                dispatchFormEvent(editorView, 'form-processing-started', {
                    message: uploadingMessage,
                })

                event.preventDefault()
                event.stopPropagation()

                const position = editorView.posAtCoords({
                    left: event.clientX,
                    top: event.clientY,
                })

                files.forEach((file, fileIndex) => {
                    editor.setEditable(false)
                    editorView.dom.dispatchEvent(
                        new CustomEvent('rich-editor-uploading-file', {
                            bubbles: true,
                            detail: {
                                key,
                                livewireId: get$WireUsing().id,
                            },
                        }),
                    )

                    let fileKey = ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(
                        /[018]/g,
                        (c) =>
                            (
                                c ^
                                (crypto.getRandomValues(new Uint8Array(1))[0] &
                                    (15 >> (c / 4)))
                            ).toString(16),
                    )

                    get$WireUsing().upload(
                        `componentFileAttachments.${statePath}.${fileKey}`,
                        file,
                        () => {
                            getFileAttachmentUrl(fileKey).then((url) => {
                                if (!url) {
                                    return
                                }

                                editor
                                    .chain()
                                    .insertContentAt(position?.pos ?? 0, {
                                        type: 'image',
                                        attrs: {
                                            id: fileKey,
                                            src: url,
                                        },
                                    })
                                    .run()

                                editor.setEditable(true)
                                editorView.dom.dispatchEvent(
                                    new CustomEvent(
                                        'rich-editor-uploaded-file',
                                        {
                                            bubbles: true,
                                            detail: {
                                                key,
                                                livewireId: get$WireUsing().id,
                                            },
                                        },
                                    ),
                                )

                                if (fileIndex === files.length - 1) {
                                    dispatchFormEvent(
                                        editorView,
                                        'form-processing-finished',
                                    )
                                }
                            })
                        },
                    )
                })

                return true
            },
            handlePaste(editorView, event) {
                if (!event.clipboardData?.files.length) {
                    return false
                }

                if (event.clipboardData?.getData('text').length) {
                    return false
                }

                const files = Array.from(event.clipboardData.files).filter(
                    (file) => allowedMimeTypes.includes(file.type),
                )

                if (!files.length) {
                    return false
                }

                event.preventDefault()
                event.stopPropagation()

                dispatchFormEvent(editorView, 'form-processing-started', {
                    message: uploadingMessage,
                })

                files.forEach((file, fileIndex) => {
                    editor.setEditable(false)
                    editorView.dom.dispatchEvent(
                        new CustomEvent('rich-editor-uploading-file', {
                            bubbles: true,
                            detail: {
                                key,
                                livewireId: get$WireUsing().id,
                            },
                        }),
                    )

                    let fileKey = ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(
                        /[018]/g,
                        (c) =>
                            (
                                c ^
                                (crypto.getRandomValues(new Uint8Array(1))[0] &
                                    (15 >> (c / 4)))
                            ).toString(16),
                    )

                    get$WireUsing().upload(
                        `componentFileAttachments.${statePath}.${fileKey}`,
                        file,
                        () => {
                            getFileAttachmentUrl(fileKey).then((url) => {
                                if (!url) {
                                    return
                                }

                                editor
                                    .chain()
                                    .insertContentAt(
                                        editor.state.selection.anchor,
                                        {
                                            type: 'image',
                                            attrs: {
                                                id: fileKey,
                                                src: url,
                                            },
                                        },
                                    )
                                    .run()

                                editor.setEditable(true)
                                editorView.dom.dispatchEvent(
                                    new CustomEvent(
                                        'rich-editor-uploaded-file',
                                        {
                                            bubbles: true,
                                            detail: {
                                                key,
                                                livewireId: get$WireUsing().id,
                                            },
                                        },
                                    ),
                                )

                                if (fileIndex === files.length - 1) {
                                    dispatchFormEvent(
                                        editorView,
                                        'form-processing-finished',
                                    )
                                }
                            })
                        },
                    )
                })

                return true
            },
        },
    })
}

export default Extension.create({
    name: 'localFiles',

    addOptions() {
        return {
            key: null,
            statePath: null,
            uploadingMessage: null,
            get$WireUsing: null,
        }
    },

    addProseMirrorPlugins() {
        return [
            LocalFilesPlugin({
                editor: this.editor,
                ...this.options,
            }),
        ]
    },
})
