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
import FilePondPluginMediaPreview from './file-upload/filepond-plugin-media-preview'

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

const cropperTemplate = [
    '<cropper-canvas>',
    '<cropper-image rotatable scalable skewable translatable></cropper-image>',
    '<cropper-shade hidden></cropper-shade>',
    '<cropper-handle action="select" plain></cropper-handle>',
    '<cropper-selection initial-coverage="1" movable resizable>',
    '<cropper-grid role="grid" bordered covered></cropper-grid>',
    '<cropper-crosshair centered></cropper-crosshair>',
    '<cropper-handle action="move" theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>',
    '<cropper-handle action="n-resize"></cropper-handle>',
    '<cropper-handle action="e-resize"></cropper-handle>',
    '<cropper-handle action="s-resize"></cropper-handle>',
    '<cropper-handle action="w-resize"></cropper-handle>',
    '<cropper-handle action="ne-resize"></cropper-handle>',
    '<cropper-handle action="nw-resize"></cropper-handle>',
    '<cropper-handle action="se-resize"></cropper-handle>',
    '<cropper-handle action="sw-resize"></cropper-handle>',
    '</cropper-selection>',
    '</cropper-canvas>',
].join('')

class CropperEditor {
    constructor(element, options = {}) {
        this.aspectRatio = this.normalizeAspectRatio(options.aspectRatio)
        this.viewMode = options.viewMode
        this.onCrop = options.onCrop
        this.rotation = 0
        this.scaleXValue = 1
        this.scaleYValue = 1

        this.cropper = new Cropper(element, {
            container: element.parentElement,
            template: cropperTemplate,
        })

        this.refreshElements()
        this.configureElements(options)
        this.bindEvents()
    }

    normalizeAspectRatio(aspectRatio) {
        const parsedAspectRatio = Number(aspectRatio)

        return Number.isFinite(parsedAspectRatio) && parsedAspectRatio > 0
            ? parsedAspectRatio
            : NaN
    }

    refreshElements() {
        this.canvas = this.cropper.getCropperCanvas()
        this.image = this.cropper.getCropperImage()
        this.selection = this.cropper.getCropperSelection()
        this.dragHandle =
            this.canvas?.querySelector('cropper-handle[plain]') ?? null
    }

    configureElements({ wheelZoomRatio }) {
        if (this.canvas) {
            this.canvas.scaleStep = wheelZoomRatio ?? 0.02
        }

        if (this.image) {
            this.image.initialCenterSize =
                this.viewMode === 3 ? 'cover' : 'contain'
        }

        if (this.selection) {
            this.selection.initialCoverage = 1
            this.selection.aspectRatio = this.aspectRatio
        }
    }

    bindEvents() {
        this.handleSelectionChange = (event) => {
            if (!this.isSelectionWithinCanvas(event.detail)) {
                event.preventDefault()

                return
            }

            this.notifyCrop(event.detail)
        }

        this.handleImageTransform = (event) => {
            if (!this.isImageWithinViewMode(event.detail.matrix)) {
                event.preventDefault()

                return
            }

            this.notifyCrop()
        }

        this.selection?.addEventListener('change', this.handleSelectionChange)
        this.image?.addEventListener('transform', this.handleImageTransform)
    }

    isSelectionWithinCanvas(selection) {
        if (!this.canvas || this.viewMode < 1) {
            return true
        }

        const precision = 1

        return (
            selection.x >= -precision &&
            selection.y >= -precision &&
            selection.x + selection.width <=
                this.canvas.offsetWidth + precision &&
            selection.y + selection.height <=
                this.canvas.offsetHeight + precision
        )
    }

    isImageWithinViewMode(matrix) {
        if (!this.canvas || !this.image || this.viewMode < 2) {
            return true
        }

        const imageRect = this.getTransformedImageRect(matrix)
        const canvasRect = this.canvas.getBoundingClientRect()

        if (!imageRect) {
            return true
        }

        if (this.viewMode === 2) {
            return !(
                (imageRect.top > canvasRect.top &&
                    imageRect.right < canvasRect.right) ||
                (imageRect.right < canvasRect.right &&
                    imageRect.bottom < canvasRect.bottom) ||
                (imageRect.bottom < canvasRect.bottom &&
                    imageRect.left > canvasRect.left) ||
                (imageRect.left > canvasRect.left &&
                    imageRect.top > canvasRect.top)
            )
        }

        return (
            imageRect.top <= canvasRect.top &&
            imageRect.right >= canvasRect.right &&
            imageRect.bottom >= canvasRect.bottom &&
            imageRect.left <= canvasRect.left
        )
    }

    getTransformedImageRect(matrix) {
        const clonedImage = this.image.cloneNode()

        clonedImage.style.opacity = '0'
        clonedImage.style.transform = `matrix(${matrix.join(', ')})`

        this.canvas.appendChild(clonedImage)

        const imageRect = clonedImage.getBoundingClientRect()

        this.canvas.removeChild(clonedImage)

        return imageRect
    }

    initializeSelection() {
        this.refreshElements()
        this.configureElements({ wheelZoomRatio: this.canvas?.scaleStep })
        this.selection?.$initSelection?.(true, true)
        this.notifyCrop()
    }

    notifyCrop(selection = this.selection) {
        if (!this.onCrop || !selection) {
            return
        }

        this.onCrop({
            x: selection.x,
            y: selection.y,
            height: selection.height,
            width: selection.width,
            rotate: this.rotation,
            scaleX: this.scaleXValue,
            scaleY: this.scaleYValue,
        })
    }

    setDragMode(mode) {
        if (!this.dragHandle) {
            return
        }

        this.dragHandle.action = mode === 'move' ? 'move' : 'select'
    }

    zoom(scale) {
        this.image?.$zoom(scale)
    }

    zoomTo(scale) {
        const [a, b] = this.image?.$getTransform() ?? [1, 0]
        const currentScale = Math.hypot(a, b) || 1

        this.image?.$scale(scale / currentScale)
    }

    move(x, y) {
        this.image?.$move(x, y)
    }

    rotate(degrees) {
        this.rotation += degrees
        this.image?.$rotate(`${degrees}deg`)
        this.notifyCrop()
    }

    rotateTo(degrees) {
        this.rotate(degrees - this.rotation)
    }

    scaleX(scale) {
        this.image?.$scale(scale / this.scaleXValue, 1)
        this.scaleXValue = scale
        this.notifyCrop()
    }

    scaleY(scale) {
        this.image?.$scale(1, scale / this.scaleYValue)
        this.scaleYValue = scale
        this.notifyCrop()
    }

    getData(rounded = false) {
        const data = {
            x: this.selection?.x ?? 0,
            y: this.selection?.y ?? 0,
            height: this.selection?.height ?? 0,
            width: this.selection?.width ?? 0,
            rotate: this.rotation,
            scaleX: this.scaleXValue,
            scaleY: this.scaleYValue,
        }

        if (!rounded) {
            return data
        }

        return Object.fromEntries(
            Object.entries(data).map(([key, value]) => [
                key,
                Math.round(value),
            ]),
        )
    }

    setData(data) {
        if (!this.selection) {
            return
        }

        this.selection.$change(
            Number(data.x ?? this.selection.x),
            Number(data.y ?? this.selection.y),
            Number(data.width ?? this.selection.width),
            Number(data.height ?? this.selection.height),
            this.selection.aspectRatio,
            true,
        )

        this.notifyCrop()
    }

    setAspectRatio(aspectRatio) {
        if (!this.selection) {
            return
        }

        this.aspectRatio = this.normalizeAspectRatio(aspectRatio)
        this.selection.aspectRatio = this.aspectRatio
        this.selection.$change(
            this.selection.x,
            this.selection.y,
            this.selection.width,
            this.selection.height,
            this.aspectRatio,
            true,
        )

        this.notifyCrop()
    }

    reset() {
        this.rotation = 0
        this.scaleXValue = 1
        this.scaleYValue = 1
        this.image?.$resetTransform()
        this.image?.$center(this.viewMode === 3 ? 'cover' : 'contain')
        this.selection?.$reset()
        this.notifyCrop()
    }

    replace(source) {
        this.rotation = 0
        this.scaleXValue = 1
        this.scaleYValue = 1
        this.image?.$resetTransform()

        if (!this.image) {
            return Promise.resolve()
        }

        this.image.src = source

        return this.image.$ready().then(() => {
            this.initializeSelection()
        })
    }

    getCroppedCanvas(options = {}) {
        if (!this.selection) {
            return Promise.resolve(null)
        }

        return this.selection.$toCanvas({
            height: options.height,
            width: options.width,
            beforeDraw: (context, canvas) => {
                context.fillStyle = options.fillColor ?? 'transparent'
                context.fillRect(0, 0, canvas.width, canvas.height)
                context.imageSmoothingEnabled =
                    options.imageSmoothingEnabled ?? true
                context.imageSmoothingQuality =
                    options.imageSmoothingQuality ?? 'high'
            },
        })
    }

    destroy() {
        this.selection?.removeEventListener(
            'change',
            this.handleSelectionChange,
        )
        this.image?.removeEventListener('transform', this.handleImageTransform)
        this.cropper.destroy()
    }
}

export default function fileUploadFormComponent({
    acceptedFileTypes,
    automaticallyCropImagesAspectRatio,
    automaticallyOpenImageEditorForAspectRatio,
    automaticallyResizeImagesHeight,
    automaticallyResizeImagesMode,
    automaticallyResizeImagesWidth,
    cancelUploadUsing,
    canEditSvgs,
    confirmSvgEditingMessage,
    deleteUploadedFileUsing,
    disabledSvgEditingMessage,
    getUploadedFilesUsing,
    hasCircleCropper,
    hasImageEditor,
    imageEditorEmptyFillColor,
    imageEditorMode,
    imageEditorViewportHeight,
    imageEditorViewportWidth,
    imagePreviewHeight,
    isAvatar,
    isDeletable,
    isDisabled,
    isDownloadable,
    isImageEditorExplicitlyEnabled,
    isMultiple,
    isOpenable,
    isPasteable,
    isPreviewable,
    isReorderable,
    isSvgEditingConfirmed,
    itemPanelAspectRatio,
    loadingIndicatorPosition,
    locale,
    maxFiles,
    maxFilesValidationMessage,
    maxParallelUploads,
    maxSize,
    mimeTypeMap,
    minSize,
    panelAspectRatio,
    panelLayout,
    placeholder,
    removeUploadedFileButtonPosition,
    removeUploadedFileUsing,
    reorderUploadedFilesUsing,
    shouldAppendFiles,
    shouldAutomaticallyUpscaleImagesWhenResizing,
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

        isEditorOpenedForAspectRatio: false,

        editingFile: {},

        currentRatio: '',

        editor: null,

        visibilityObserver: null,

        intersectionObserver: null,

        isInitializing: false,

        async init() {
            if (this.pond || this.isInitializing) {
                return
            }

            this.isInitializing = true

            // https://github.com/filamentphp/filament/issues/15394
            // https://github.com/filamentphp/filament/issues/16253
            // https://github.com/filamentphp/filament/issues/19522
            if (!this.visibilityObserver) {
                const onVisible = () => {
                    const isHidden =
                        this.$el.offsetParent === null ||
                        getComputedStyle(this.$el).visibility === 'hidden'

                    if (isHidden) {
                        return
                    }

                    if (!this.pond) {
                        this.init()
                    } else {
                        document.dispatchEvent(new Event('visibilitychange'))
                    }
                }

                this.visibilityObserver = new ResizeObserver(() => onVisible())
                this.visibilityObserver.observe(this.$el)

                this.intersectionObserver = new IntersectionObserver(
                    (entries) => {
                        if (entries[0]?.isIntersecting) {
                            onVisible()
                        }
                    },
                    { threshold: 0 },
                )
                this.intersectionObserver.observe(this.$el)
            }

            const isHidden =
                this.$el.offsetParent === null ||
                getComputedStyle(this.$el).visibility === 'hidden'

            if (isHidden) {
                this.isInitializing = false

                return
            }

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
                beforeAddFile: async (fileItem) => {
                    if (!automaticallyOpenImageEditorForAspectRatio) {
                        return true
                    }

                    if (!(fileItem.file instanceof File)) {
                        return true
                    }

                    if (!fileItem.file.type.startsWith('image/')) {
                        return true
                    }

                    if (await this.checkImageAspectRatio(fileItem.file)) {
                        return true
                    }

                    this.isEditorOpenedForAspectRatio = true

                    this.loadEditor(fileItem.file)

                    return false
                },
                credits: false,
                files: await this.getFiles(),
                imageCropAspectRatio: automaticallyCropImagesAspectRatio,
                imagePreviewHeight,
                imageResizeTargetHeight: automaticallyResizeImagesHeight,
                imageResizeTargetWidth: automaticallyResizeImagesWidth,
                imageResizeMode: automaticallyResizeImagesMode,
                imageResizeUpscale:
                    shouldAutomaticallyUpscaleImagesWhenResizing,
                imageTransformOutputStripImageHead: false,
                itemInsertLocation: shouldAppendFiles ? 'after' : 'before',
                ...(placeholder && { labelIdle: placeholder }),
                maxFiles,
                maxFileSize: maxSize,
                mediaPreviewHeight: imagePreviewHeight,
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
                        abort,
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

                        return {
                            abort: () => {
                                cancelUploadUsing(fileKey)
                                abort()
                            },
                        }
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
                allowImageEdit: isImageEditorExplicitlyEnabled,
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

            this.pond.on('removefile', handleFileProcessing)

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

            this.isInitializing = false
        },

        destroy() {
            this.visibilityObserver?.disconnect()
            this.intersectionObserver?.disconnect()

            this.destroyEditor()

            if (this.pond) {
                FilePond.destroy(this.$refs.input)
                this.pond = null
            }
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
                        metadata: {
                            openableUrl: uploadedFile.openableUrl,
                            downloadableUrl: uploadedFile.downloadableUrl,
                        },
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
            let downloadableUrl =
                file.getMetadata('downloadableUrl') ?? file.source

            if (!downloadableUrl) {
                return
            }

            const anchor = document.createElement('a')
            anchor.className = 'filepond--download-icon'
            anchor.href = downloadableUrl
            anchor.download = file.file.name

            return anchor
        },

        getOpenLink(file) {
            let openableUrl = file.getMetadata('openableUrl') ?? file.source

            if (!openableUrl) {
                return
            }

            const anchor = document.createElement('a')
            anchor.className = 'filepond--open-icon'
            anchor.href = openableUrl
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

            if (this.editor) {
                this.destroyEditor()
            }

            const cropperOptions = {
                aspectRatio:
                    automaticallyOpenImageEditorForAspectRatio ??
                    imageEditorViewportWidth / imageEditorViewportHeight,
                viewMode: imageEditorMode,
                wheelZoomRatio: 0.02,
            }

            if (isImageEditorExplicitlyEnabled) {
                cropperOptions.onCrop = (data) => {
                    this.$refs.xPositionInput.value = Math.round(data.x)
                    this.$refs.yPositionInput.value = Math.round(data.y)
                    this.$refs.heightInput.value = Math.round(data.height)
                    this.$refs.widthInput.value = Math.round(data.width)
                    this.$refs.rotationInput.value = Math.round(data.rotate)
                }
            }

            this.editor = new CropperEditor(this.$refs.editor, cropperOptions)
        },

        closeEditor() {
            if (this.isEditorOpenedForAspectRatio) {
                const fileItem = this.pond
                    .getFiles()
                    .find(
                        (uploadedFile) =>
                            uploadedFile.filename === this.editingFile.name,
                    )

                if (fileItem) {
                    this.pond.removeFile(fileItem.id, { revert: true })
                }

                this.isEditorOpenedForAspectRatio = false
            }

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

                const reader = new FileReader()

                reader.onload = (event) => {
                    this.isEditorOpen = true

                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.initEditor()
                            this.editor
                                .replace(event.target.result)
                                .catch(() => this.closeEditor())
                        }, 200)
                    })
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

        async saveEditor() {
            if (isDisabled) {
                return
            }

            if (!hasImageEditor) {
                return
            }

            if (!this.editor) {
                return
            }

            this.isEditorOpenedForAspectRatio = false

            let croppedCanvas = await this.editor.getCroppedCanvas({
                fillColor: imageEditorEmptyFillColor ?? 'transparent',
                height: automaticallyResizeImagesHeight,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                width: automaticallyResizeImagesWidth,
            })

            if (!croppedCanvas) {
                this.closeEditor()

                return
            }

            if (hasCircleCropper) {
                croppedCanvas = this.getRoundedCanvas(croppedCanvas)
            }

            croppedCanvas.toBlob(
                (croppedImage) => {
                    const editingFileItem = this.pond
                        .getFiles()
                        .find(
                            (uploadedFile) =>
                                uploadedFile.filename === this.editingFile.name,
                        )

                    if (editingFileItem) {
                        this.pond.removeFile(editingFileItem.id, {
                            revert: true,
                        })
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

        checkImageAspectRatio(file) {
            if (!automaticallyOpenImageEditorForAspectRatio) {
                return Promise.resolve(true)
            }

            return new Promise((resolve) => {
                const img = new Image()
                const objectUrl = URL.createObjectURL(file)

                img.onload = () => {
                    URL.revokeObjectURL(objectUrl)

                    const imageRatio = img.width / img.height
                    const tolerance = 0.01

                    resolve(
                        Math.abs(
                            imageRatio -
                                automaticallyOpenImageEditorForAspectRatio,
                        ) <= tolerance,
                    )
                }

                img.onerror = () => {
                    URL.revokeObjectURL(objectUrl)

                    resolve(true)
                }

                img.src = objectUrl
            })
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
import et from 'filepond/locale/et-ee'
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
import lus from 'filepond/locale/lus-lus'
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
import zh_HK from 'filepond/locale/zh-hk'
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
    et,
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
    lus,
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
    zh_HK,
    zh_TW,
}
