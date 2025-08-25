import * as FilePond from 'filepond'
import Cropper from 'cropperjs'
import mime from 'mime'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageEdit from 'filepond-plugin-image-edit'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'
import FilePondPluginMediaPreview from 'filepond-plugin-media-preview'

FilePond.registerPlugin(FilePondPluginFileValidateSize)
FilePond.registerPlugin(FilePondPluginFileValidateType)
FilePond.registerPlugin(FilePondPluginImageCrop)
FilePond.registerPlugin(FilePondPluginImageEdit)
FilePond.registerPlugin(FilePondPluginImageExifOrientation)
FilePond.registerPlugin(FilePondPluginImagePreview)
FilePond.registerPlugin(FilePondPluginImageResize)
FilePond.registerPlugin(FilePondPluginImageTransform)
FilePond.registerPlugin(FilePondPluginMediaPreview)

window.FilePond = FilePond

export default function fileUploadFormComponent({
    acceptedFileTypes,
    imageEditorEmptyFillColor,
    imageEditorMode,
    imageEditorViewportHeight,
    imageEditorViewportWidth,
    deleteUploadedFileUsing,
    isDeletable,
    isDisabled,
    getUploadedFilesUsing,
    imageCropAspectRatio,
    imagePreviewHeight,
    imageResizeMode,
    imageResizeTargetHeight,
    imageResizeTargetWidth,
    imageResizeUpscale,
    isAvatar,
    hasImageEditor,
    hasCircleCropper,
    canEditSvgs,
    isSvgEditingConfirmed,
    confirmSvgEditingMessage,
    disabledSvgEditingMessage,
    isDownloadable,
    isMultiple,
    isOpenable,
    isPasteable,
    isPreviewable,
    isReorderable,
    itemPanelAspectRatio,
    loadingIndicatorPosition,
    locale,
    maxFiles,
    maxFilesValidationMessage,
    maxSize,
    minSize,
    maxParallelUploads,
    mimeTypeMap,
    panelAspectRatio,
    panelLayout,
    placeholder,
    removeUploadedFileButtonPosition,
    removeUploadedFileUsing,
    reorderUploadedFilesUsing,
    shouldAppendFiles,
    shouldOrientImageFromExif,
    shouldTransformImage,
    state,
    uploadButtonPosition,
    uploadingMessage,
    uploadProgressIndicatorPosition,
    uploadUsing,
}) {
    return {
        fileKeyIndex: {},

        pond: null,

        shouldUpdateState: true,

        state,

        lastState: null,

        error: null,

        uploadedFileIndex: {},

        isEditorOpen: false,

        editingFile: {},

        currentRatio: '',

        editor: {},

        async init() {
            FilePond.setOptions(locales[locale] ?? locales['en'])

            this.pond = FilePond.create(this.$refs.input, {
                acceptedFileTypes,
                allowImageExifOrientation: shouldOrientImageFromExif,
                allowPaste: isPasteable,
                allowRemove: isDeletable,
                allowReorder: isReorderable,
                allowImagePreview: isPreviewable,
                allowVideoPreview: isPreviewable,
                allowAudioPreview: isPreviewable,
                allowImageTransform: shouldTransformImage,
                credits: false,
                files: await this.getFiles(),
                imageCropAspectRatio,
                imagePreviewHeight,
                imageResizeTargetHeight,
                imageResizeTargetWidth,
                imageResizeMode,
                imageResizeUpscale,
                imageTransformOutputStripImageHead: false,
                itemInsertLocation: shouldAppendFiles ? 'after' : 'before',
                ...(placeholder && { labelIdle: placeholder }),
                maxFiles,
                maxFileSize: maxSize,
                minFileSize: minSize,
                ...(maxParallelUploads && { maxParallelUploads }),
                styleButtonProcessItemPosition: uploadButtonPosition,
                styleButtonRemoveItemPosition: removeUploadedFileButtonPosition,
                styleItemPanelAspectRatio: itemPanelAspectRatio,
                styleLoadIndicatorPosition: loadingIndicatorPosition,
                stylePanelAspectRatio: panelAspectRatio,
                stylePanelLayout: panelLayout,
                styleProgressIndicatorPosition: uploadProgressIndicatorPosition,
                server: {
                    load: async (source, load) => {
                        let response = await fetch(source, {
                            cache: 'no-store',
                        })
                        let blob = await response.blob()

                        load(blob)
                    },
                    process: (
                        fieldName,
                        file,
                        metadata,
                        load,
                        error,
                        progress,
                    ) => {
                        this.shouldUpdateState = false

                        let fileKey = (
                            [1e7] +
                            -1e3 +
                            -4e3 +
                            -8e3 +
                            -1e11
                        ).replace(/[018]/g, (c) =>
                            (
                                c ^
                                (crypto.getRandomValues(new Uint8Array(1))[0] &
                                    (15 >> (c / 4)))
                            ).toString(16),
                        )

                        uploadUsing(
                            fileKey,
                            file,
                            (fileKey) => {
                                this.shouldUpdateState = true

                                load(fileKey)
                            },
                            error,
                            progress,
                        )
                    },
                    remove: async (source, load) => {
                        let fileKey = this.uploadedFileIndex[source] ?? null

                        if (!fileKey) {
                            return
                        }

                        await deleteUploadedFileUsing(fileKey)

                        load()
                    },
                    revert: async (uniqueFileId, load) => {
                        await removeUploadedFileUsing(uniqueFileId)

                        load()
                    },
                },
                allowImageEdit: hasImageEditor,
                imageEditEditor: {
                    open: (file) => this.loadEditor(file),
                    onconfirm: () => {},
                    oncancel: () => this.closeEditor(),
                    onclose: () => this.closeEditor(),
                },
                fileValidateTypeDetectType: (source, detectedType) => {
                    return new Promise((resolve, reject) => {
                        const extension = source.name
                            .split('.')
                            .pop()
                            .toLowerCase()
                        const mimeType =
                            mimeTypeMap[extension] ||
                            detectedType ||
                            mime.getType(extension)

                        mimeType ? resolve(mimeType) : reject()
                    })
                },
            })

            this.$watch('state', async () => {
                if (!this.pond) {
                    return
                }

                if (!this.shouldUpdateState) {
                    return
                }

                if (this.state === undefined) {
                    return
                }

                // We don't want to overwrite the files that are already in the input, if they haven't been saved yet.
                if (
                    this.state !== null &&
                    Object.values(this.state).filter((file) =>
                        file.startsWith('livewire-file:'),
                    ).length
                ) {
                    this.lastState = null

                    return
                }

                // Don't do anything if the state hasn't changed
                if (JSON.stringify(this.state) === this.lastState) {
                    return
                }

                this.lastState = JSON.stringify(this.state)

                this.pond.files = await this.getFiles()
            })

            this.pond.on('reorderfiles', async (files) => {
                const orderedFileKeys = files
                    .map((file) =>
                        file.source instanceof File
                            ? file.serverId
                            : (this.uploadedFileIndex[file.source] ?? null),
                    ) // file.serverId is null for a file that is not yet uploaded
                    .filter((fileKey) => fileKey)

                await reorderUploadedFilesUsing(
                    shouldAppendFiles
                        ? orderedFileKeys
                        : orderedFileKeys.reverse(),
                )
            })

            this.pond.on('initfile', async (fileItem) => {
                if (!isDownloadable) {
                    return
                }

                if (isAvatar) {
                    return
                }

                this.insertDownloadLink(fileItem)
            })

            this.pond.on('initfile', async (fileItem) => {
                if (!isOpenable) {
                    return
                }

                if (isAvatar) {
                    return
                }

                this.insertOpenLink(fileItem)
            })

            this.pond.on('addfilestart', async (file) => {
                this.error = null

                if (file.status !== FilePond.FileStatus.PROCESSING_QUEUED) {
                    return
                }

                this.dispatchFormEvent('form-processing-started', {
                    message: uploadingMessage,
                })
            })

            const handleFileProcessing = async () => {
                if (
                    this.pond
                        .getFiles()
                        .filter(
                            (file) =>
                                file.status ===
                                    FilePond.FileStatus.PROCESSING ||
                                file.status ===
                                    FilePond.FileStatus.PROCESSING_QUEUED,
                        ).length
                ) {
                    return
                }

                this.dispatchFormEvent('form-processing-finished')
            }

            this.pond.on('processfile', handleFileProcessing)

            this.pond.on('processfileabort', handleFileProcessing)

            this.pond.on('processfilerevert', handleFileProcessing)

            this.pond.on('warning', (warning) => {
                if (warning.body === 'Max files') {
                    this.error = maxFilesValidationMessage
                }
            })

            if (panelLayout === 'compact circle') {
                // The compact circle layout does not have enough space to render an error message inside the input.
                // As such, we need to display the error message outside of the input, using the `error` Alpine.js
                // property that is output as a message in the field's view.

                this.pond.on('error', (error) => {
                    // FilePond has a weird English translation for the error message when a file of an unexpected
                    // type is uploaded, for example: `File of invalid type: Expects  or image/*`. This is a
                    // hacky workaround to fix the message to be `File of invalid type: Expects image/*`.
                    this.error = `${error.main}: ${error.sub}`.replace(
                        'Expects  or',
                        'Expects',
                    )
                })
            }

            this.pond.on('removefile', () => (this.error = null))
        },

        destroy() {
            this.destroyEditor()

            FilePond.destroy(this.$refs.input)
            this.pond = null
        },

        dispatchFormEvent(name, detail = {}) {
            this.$el.closest('form')?.dispatchEvent(
                new CustomEvent(name, {
                    composed: true,
                    cancelable: true,
                    detail,
                }),
            )
        },

        async getUploadedFiles() {
            const uploadedFiles = await getUploadedFilesUsing()

            this.fileKeyIndex = uploadedFiles ?? {}

            this.uploadedFileIndex = Object.entries(this.fileKeyIndex)
                .filter(([key, value]) => value?.url)
                .reduce((obj, [key, value]) => {
                    obj[value.url] = key

                    return obj
                }, {})
        },

        async getFiles() {
            await this.getUploadedFiles()

            let files = []

            for (const uploadedFile of Object.values(this.fileKeyIndex)) {
                if (!uploadedFile) {
                    continue
                }

                files.push({
                    source: uploadedFile.url,
                    options: {
                        type: 'local',
                        ...(!uploadedFile.type ||
                        (isPreviewable &&
                            (/^audio/.test(uploadedFile.type) ||
                                /^image/.test(uploadedFile.type) ||
                                /^video/.test(uploadedFile.type)))
                            ? {}
                            : {
                                  file: {
                                      name: uploadedFile.name,
                                      size: uploadedFile.size,
                                      type: uploadedFile.type,
                                  },
                              }),
                    },
                })
            }

            return shouldAppendFiles ? files : files.reverse()
        },

        insertDownloadLink(file) {
            if (file.origin !== FilePond.FileOrigin.LOCAL) {
                return
            }

            const anchor = this.getDownloadLink(file)

            if (!anchor) {
                return
            }

            document
                .getElementById(`filepond--item-${file.id}`)
                .querySelector('.filepond--file-info-main')
                .prepend(anchor)
        },

        insertOpenLink(file) {
            if (file.origin !== FilePond.FileOrigin.LOCAL) {
                return
            }

            const anchor = this.getOpenLink(file)

            if (!anchor) {
                return
            }

            document
                .getElementById(`filepond--item-${file.id}`)
                .querySelector('.filepond--file-info-main')
                .prepend(anchor)
        },

        getDownloadLink(file) {
            let fileSource = file.source

            if (!fileSource) {
                return
            }

            const anchor = document.createElement('a')
            anchor.className = 'filepond--download-icon'
            anchor.href = fileSource
            anchor.download = file.file.name

            return anchor
        },

        getOpenLink(file) {
            let fileSource = file.source

            if (!fileSource) {
                return
            }

            const anchor = document.createElement('a')
            anchor.className = 'filepond--open-icon'
            anchor.href = fileSource
            anchor.target = '_blank'

            return anchor
        },

        initEditor() {
            if (isDisabled) {
                return
            }

            if (!hasImageEditor) {
                return
            }

            this.editor = new Cropper(this.$refs.editor, {
                aspectRatio:
                    imageEditorViewportWidth / imageEditorViewportHeight,
                autoCropArea: 1,
                center: true,
                crop: (event) => {
                    this.$refs.xPositionInput.value = Math.round(event.detail.x)
                    this.$refs.yPositionInput.value = Math.round(event.detail.y)
                    this.$refs.heightInput.value = Math.round(
                        event.detail.height,
                    )
                    this.$refs.widthInput.value = Math.round(event.detail.width)
                    this.$refs.rotationInput.value = event.detail.rotate
                },
                cropBoxResizable: true,
                guides: true,
                highlight: true,
                responsive: true,
                toggleDragModeOnDblclick: true,
                viewMode: imageEditorMode,
                wheelZoomRatio: 0.02,
            })
        },

        closeEditor() {
            this.editingFile = {}

            this.isEditorOpen = false

            this.destroyEditor()
        },

        fixImageDimensions(file, callback) {
            if (file.type !== 'image/svg+xml') {
                return callback(file)
            }

            const svgReader = new FileReader()

            svgReader.onload = (event) => {
                const svgElement = new DOMParser()
                    .parseFromString(event.target.result, 'image/svg+xml')
                    ?.querySelector('svg')

                if (!svgElement) {
                    return callback(file)
                }

                const viewBoxAttribute = ['viewBox', 'ViewBox', 'viewbox'].find(
                    (attribute) => svgElement.hasAttribute(attribute),
                )

                if (!viewBoxAttribute) {
                    return callback(file)
                }

                const viewBox = svgElement
                    .getAttribute(viewBoxAttribute)
                    .split(' ')

                if (!viewBox || viewBox.length !== 4) {
                    return callback(file)
                }

                svgElement.setAttribute('width', parseFloat(viewBox[2]) + 'pt')
                svgElement.setAttribute('height', parseFloat(viewBox[3]) + 'pt')

                return callback(
                    new File(
                        [
                            new Blob(
                                [
                                    new XMLSerializer().serializeToString(
                                        svgElement,
                                    ),
                                ],
                                { type: 'image/svg+xml' },
                            ),
                        ],
                        file.name,
                        {
                            type: 'image/svg+xml',
                            _relativePath: '',
                        },
                    ),
                )
            }

            svgReader.readAsText(file)
        },

        loadEditor(file) {
            if (isDisabled) {
                return
            }

            if (!hasImageEditor) {
                return
            }

            if (!file) {
                return
            }

            const isFileSvg = file.type === 'image/svg+xml'

            if (!canEditSvgs && isFileSvg) {
                alert(disabledSvgEditingMessage)

                return
            }

            if (
                isSvgEditingConfirmed &&
                isFileSvg &&
                !confirm(confirmSvgEditingMessage)
            ) {
                return
            }

            this.fixImageDimensions(file, (editingFile) => {
                this.editingFile = editingFile

                this.initEditor()

                const reader = new FileReader()

                reader.onload = (event) => {
                    this.isEditorOpen = true

                    setTimeout(
                        () => this.editor.replace(event.target.result),
                        200,
                    )
                }

                reader.readAsDataURL(file)
            })
        },

        getRoundedCanvas(sourceCanvas) {
            let width = sourceCanvas.width
            let height = sourceCanvas.height

            let canvas = document.createElement('canvas')
            canvas.width = width
            canvas.height = height

            let context = canvas.getContext('2d')
            context.imageSmoothingEnabled = true
            context.drawImage(sourceCanvas, 0, 0, width, height)
            context.globalCompositeOperation = 'destination-in'
            context.beginPath()
            context.ellipse(
                width / 2,
                height / 2,
                width / 2,
                height / 2,
                0,
                0,
                2 * Math.PI,
            )
            context.fill()

            return canvas
        },

        saveEditor() {
            if (isDisabled) {
                return
            }

            if (!hasImageEditor) {
                return
            }

            let croppedCanvas = this.editor.getCroppedCanvas({
                fillColor: imageEditorEmptyFillColor ?? 'transparent',
                height: imageResizeTargetHeight,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                width: imageResizeTargetWidth,
            })

            if (hasCircleCropper) {
                croppedCanvas = this.getRoundedCanvas(croppedCanvas)
            }

            croppedCanvas.toBlob(
                (croppedImage) => {
                    if (isMultiple) {
                        this.pond.removeFile(
                            this.pond
                                .getFiles()
                                .find(
                                    (uploadedFile) =>
                                        uploadedFile.filename ===
                                        this.editingFile.name,
                                )?.id,
                            { revert: true },
                        )
                    }

                    this.$nextTick(() => {
                        this.shouldUpdateState = false

                        let editingFileName = this.editingFile.name.slice(
                            0,
                            this.editingFile.name.lastIndexOf('.'),
                        )
                        let editingFileExtension = this.editingFile.name
                            .split('.')
                            .pop()

                        if (editingFileExtension === 'svg') {
                            editingFileExtension = 'png'
                        }

                        const fileNameVersionRegex = /-v(\d+)/

                        if (fileNameVersionRegex.test(editingFileName)) {
                            editingFileName = editingFileName.replace(
                                fileNameVersionRegex,
                                (match, number) => {
                                    const newNumber = Number(number) + 1

                                    return `-v${newNumber}`
                                },
                            )
                        } else {
                            editingFileName += '-v1'
                        }

                        this.pond
                            .addFile(
                                new File(
                                    [croppedImage],
                                    `${editingFileName}.${editingFileExtension}`,
                                    {
                                        type:
                                            this.editingFile.type ===
                                                'image/svg+xml' ||
                                            hasCircleCropper
                                                ? 'image/png'
                                                : this.editingFile.type,
                                        lastModified: new Date().getTime(),
                                    },
                                ),
                            )
                            .then(() => {
                                this.closeEditor()
                            })
                            .catch(() => {
                                this.closeEditor()
                            })
                    })
                },
                hasCircleCropper ? 'image/png' : this.editingFile.type,
            )
        },

        destroyEditor() {
            if (this.editor && typeof this.editor.destroy === 'function') {
                this.editor.destroy()
            }

            this.editor = null
        },
    }
}

import am from 'filepond/locale/am-et'
import ar from 'filepond/locale/ar-ar'
import az from 'filepond/locale/az-az'
import ca from 'filepond/locale/ca-ca'
import ckb from 'filepond/locale/ku-ckb'
import cs from 'filepond/locale/cs-cz'
import da from 'filepond/locale/da-dk'
import de from 'filepond/locale/de-de'
import el from 'filepond/locale/el-el'
import en from 'filepond/locale/en-en'
import es from 'filepond/locale/es-es'
import fa from 'filepond/locale/fa_ir'
import fi from 'filepond/locale/fi-fi'
import fr from 'filepond/locale/fr-fr'
import he from 'filepond/locale/he-he'
import hr from 'filepond/locale/hr-hr'
import hu from 'filepond/locale/hu-hu'
import id from 'filepond/locale/id-id'
import it from 'filepond/locale/it-it'
import ja from 'filepond/locale/ja-ja'
import km from 'filepond/locale/km-km'
import ko from 'filepond/locale/ko-kr'
import lt from 'filepond/locale/lt-lt'
import lv from 'filepond/locale/lv-lv'
import nb from 'filepond/locale/no_nb'
import nl from 'filepond/locale/nl-nl'
import pl from 'filepond/locale/pl-pl'
import pt from 'filepond/locale/pt-pt'
import pt_BR from 'filepond/locale/pt-br'
import ro from 'filepond/locale/ro-ro'
import ru from 'filepond/locale/ru-ru'
import sk from 'filepond/locale/sk-sk'
import sv from 'filepond/locale/sv_se'
import tr from 'filepond/locale/tr-tr'
import uk from 'filepond/locale/uk-ua'
import vi from 'filepond/locale/vi-vi'
import zh_CN from 'filepond/locale/zh-cn'
import zh_TW from 'filepond/locale/zh-tw'

const locales = {
    am,
    ar,
    az,
    ca,
    ckb,
    cs,
    da,
    de,
    el,
    en,
    es,
    fa,
    fi,
    fr,
    he,
    hr,
    hu,
    id,
    it,
    ja,
    km,
    ko,
    lt,
    lv,
    nb,
    nl,
    pl,
    pt,
    pt_BR,
    ro,
    ru,
    sk,
    sv,
    tr,
    uk,
    vi,
    zh_CN,
    zh_TW,
}
