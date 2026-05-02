import { Extension } from '@tiptap/core'
import { DOMParser as ProseMirrorDOMParser } from '@tiptap/pm/model'
import { Plugin, PluginKey } from '@tiptap/pm/state'

const dispatchFormEvent = (editorView, name, detail = {}) => {
    editorView.dom.closest('form')?.dispatchEvent(
        new CustomEvent(name, {
            composed: true,
            cancelable: true,
            detail,
        }),
    )
}

const validateFiles = ({
    files,
    acceptedTypes,
    acceptedTypesValidationMessage,
    maxSize,
    maxSizeValidationMessage,
}) => {
    for (const file of files) {
        if (acceptedTypes && !acceptedTypes.includes(file.type)) {
            return acceptedTypesValidationMessage
        }

        if (maxSize && file.size > +maxSize * 1024) {
            return maxSizeValidationMessage
        }
    }

    return null
}

const generateFileKey = () =>
    ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16),
    )

const LocalFilesPlugin = ({
    editor,
    acceptedTypes,
    acceptedTypesValidationMessage,
    get$WireUsing,
    key,
    maxSize,
    maxSizeValidationMessage,
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

                const files = Array.from(event.dataTransfer.files)

                const validationMessage = validateFiles({
                    files,
                    acceptedTypes,
                    acceptedTypesValidationMessage,
                    maxSize,
                    maxSizeValidationMessage,
                })

                if (validationMessage) {
                    editorView.dom.dispatchEvent(
                        new CustomEvent('rich-editor-file-validation-message', {
                            bubbles: true,
                            detail: {
                                key,
                                livewireId: get$WireUsing().id,
                                validationMessage,
                            },
                        }),
                    )

                    return false
                }

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

                    const fileKey = generateFileKey()

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
                const files = event.clipboardData?.files?.length
                    ? Array.from(event.clipboardData.files)
                    : []
                const hasText = event.clipboardData?.getData('text')?.length > 0
                const html = event.clipboardData?.getData('text/html') || ''

                if (files.length && !hasText) {
                    const validationMessage = validateFiles({
                        files,
                        acceptedTypes,
                        acceptedTypesValidationMessage,
                        maxSize,
                        maxSizeValidationMessage,
                    })

                    if (validationMessage) {
                        editorView.dom.dispatchEvent(
                            new CustomEvent(
                                'rich-editor-file-validation-message',
                                {
                                    bubbles: true,
                                    detail: {
                                        key,
                                        livewireId: get$WireUsing().id,
                                        validationMessage,
                                    },
                                },
                            ),
                        )

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

                        const fileKey = generateFileKey()

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
                                                    livewireId:
                                                        get$WireUsing().id,
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
                }

                if (html) {
                    const domParser = new DOMParser()
                    const parsed = domParser.parseFromString(html, 'text/html')
                    const images = parsed.querySelectorAll('img[src]')

                    if (images.length) {
                        event.stopPropagation()

                        const { from, to } = editor.state.selection

                        dispatchFormEvent(
                            editorView,
                            'form-processing-started',
                            { message: uploadingMessage },
                        )

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
                        ;(async () => {
                            for (const image of images) {
                                const src = image.getAttribute('src')

                                if (!src.startsWith('data:image/')) {
                                    continue
                                }

                                let blob

                                try {
                                    const [header, base64] = src.split(',')
                                    const mimeType = header.match(/:(.*?);/)[1]
                                    const bytes = atob(base64)
                                    const array = new Uint8Array(bytes.length)

                                    for (let i = 0; i < bytes.length; i++) {
                                        array[i] = bytes.charCodeAt(i)
                                    }

                                    blob = new Blob([array], {
                                        type: mimeType,
                                    })
                                } catch {
                                    continue
                                }

                                const extension =
                                    blob.type.split('/')[1]?.split('+')[0] ||
                                    'png'
                                const file = new File(
                                    [blob],
                                    `image.${extension}`,
                                    { type: blob.type },
                                )

                                const validationMessage = validateFiles({
                                    files: [file],
                                    acceptedTypes,
                                    acceptedTypesValidationMessage,
                                    maxSize,
                                    maxSizeValidationMessage,
                                })

                                if (validationMessage) {
                                    continue
                                }

                                const fileKey = generateFileKey()

                                const url = await new Promise((resolve) => {
                                    get$WireUsing().upload(
                                        `componentFileAttachments.${statePath}.${fileKey}`,
                                        file,
                                        () => {
                                            getFileAttachmentUrl(fileKey).then(
                                                (uploadedUrl) =>
                                                    resolve(
                                                        uploadedUrl ?? null,
                                                    ),
                                            )
                                        },
                                        () => resolve(null),
                                    )
                                })

                                if (url) {
                                    image.setAttribute('src', url)
                                    image.setAttribute('data-id', fileKey)
                                }
                            }

                            for (const image of Array.from(
                                parsed.querySelectorAll('img[src]'),
                            )) {
                                const p = parsed.createElement('p')
                                image.replaceWith(p)
                                p.appendChild(image)
                            }

                            let cleanedHtml = parsed.body.innerHTML

                            editor.view.someProp('transformPastedHTML', (f) => {
                                cleanedHtml = f(cleanedHtml)
                            })

                            const wrapper = document.createElement('div')
                            wrapper.innerHTML = cleanedHtml

                            const slice = ProseMirrorDOMParser.fromSchema(
                                editor.state.schema,
                            ).parseSlice(wrapper, {
                                preserveWhitespace: false,
                            })

                            editor.view.dispatch(
                                editor.state.tr.replaceRange(from, to, slice),
                            )

                            editor.setEditable(true)

                            editorView.dom.dispatchEvent(
                                new CustomEvent('rich-editor-uploaded-file', {
                                    bubbles: true,
                                    detail: {
                                        key,
                                        livewireId: get$WireUsing().id,
                                    },
                                }),
                            )

                            dispatchFormEvent(
                                editorView,
                                'form-processing-finished',
                            )
                        })()

                        return true
                    }
                }

                return false
            },
        },
    })
}

export default Extension.create({
    name: 'localFiles',

    addOptions() {
        return {
            acceptedTypes: [],
            acceptedTypesValidationMessage: null,
            key: null,
            maxSize: null,
            maxSizeValidationMessage: null,
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
