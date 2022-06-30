import * as FilePond from 'filepond'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'
import FilePondPluginMediaPreview from 'filepond-plugin-media-preview'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.css';
import '../../css/components/file-upload.css'

FilePond.registerPlugin(FilePondPluginFileValidateSize)
FilePond.registerPlugin(FilePondPluginFileValidateType)
FilePond.registerPlugin(FilePondPluginImageCrop)
FilePond.registerPlugin(FilePondPluginImageExifOrientation)
FilePond.registerPlugin(FilePondPluginImagePreview)
FilePond.registerPlugin(FilePondPluginImageResize)
FilePond.registerPlugin(FilePondPluginImageTransform)
FilePond.registerPlugin(FilePondPluginMediaPreview)

window.FilePond = FilePond;

export default (Alpine) => {
    Alpine.data('fileUploadFormComponent', ({
        acceptedFileTypes,
        canDownload,
        canOpen,
        shouldOpenInNewTab,
        canPreview,
        canReorder,
        deleteUploadedFileUsing,
        getUploadedFileUrlsUsing,
        imageCropAspectRatio,
        imagePreviewHeight,
        imageResizeMode,
        imageResizeTargetHeight,
        imageResizeTargetWidth,
        isAvatar,
        loadingIndicatorPosition,
        panelAspectRatio,
        panelLayout,
        placeholder,
        maxSize,
        minSize,
        removeUploadedFileButtonPosition,
        removeUploadedFileUsing,
        reorderUploadedFilesUsing,
        shouldAppendFiles,
        shouldTransformImage,
        state,
        uploadButtonPosition,
        uploadProgressIndicatorPosition,
        uploadUsing,
    }) => {
        return {
            fileKeyIndex: {},

            pond: null,

            shouldUpdateState: true,

            state,

            lastState: null,

            uploadedFileUrlIndex: {},

            init: async function () {
                this.pond = FilePond.create(this.$refs.input, {
                    acceptedFileTypes,
                    allowPaste: false,
                    allowReorder: canReorder,
                    allowImagePreview: canPreview,
                    allowVideoPreview: canPreview,
                    allowAudioPreview: canPreview,
                    allowImageTransform: shouldTransformImage,
                    credits: false,
                    files: await this.getFiles(),
                    imageCropAspectRatio,
                    imagePreviewHeight,
                    imageResizeTargetHeight,
                    imageResizeTargetWidth,
                    imageResizeMode,
                    itemInsertLocation: shouldAppendFiles ? 'after' : 'before',
                    ...(placeholder && {labelIdle: placeholder}),
                    maxFileSize: maxSize,
                    minFileSize: minSize,
                    styleButtonProcessItemPosition: uploadButtonPosition,
                    styleButtonRemoveItemPosition: removeUploadedFileButtonPosition,
                    styleLoadIndicatorPosition: loadingIndicatorPosition,
                    stylePanelAspectRatio: panelAspectRatio,
                    stylePanelLayout: panelLayout,
                    styleProgressIndicatorPosition: uploadProgressIndicatorPosition,
                    server: {
                        load: async (source, load) => {
                            let response = await fetch(source)
                            let blob = await response.blob()

                            load(blob)
                        },
                        process: (fieldName, file, metadata, load, error, progress) => {
                            this.shouldUpdateState = false

                            let fileKey = ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                            )

                            uploadUsing(fileKey, file, (fileKey) => {
                                this.shouldUpdateState = true

                                load(fileKey)
                            }, error, progress)
                        },
                        remove: async (source, load) => {
                            let fileKey = this.uploadedFileUrlIndex[source] ?? null

                            if (! fileKey) {
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
                })

                this.$watch('state', async () => {
                    if (! this.shouldUpdateState) {
                        return;
                    }

                    // We don't want to overwrite the files that are already in the input, if they haven't been saved yet.
                    if (Object.values(this.state).filter((file) => file.startsWith('livewire-file:')).length) {
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
                        .map(file => file.source instanceof File ? file.serverId : this.uploadedFileUrlIndex[file.source] ?? null) // file.serverId is null for a file that is not yet uploaded
                        .filter(fileKey => fileKey)

                    await reorderUploadedFilesUsing(shouldAppendFiles ? orderedFileKeys : orderedFileKeys.reverse())
                })

                this.pond.on('initfile', async (fileItem) => {
                    if (! (canOpen || canDownload)) {
                        return
                    }

                    if (isAvatar) {
                        return
                    }

                    this.insertLinkToFile(fileItem)
                })

                this.pond.on('processfilestart', async () => {
                    this.dispatchFormEvent('file-upload-started')
                })

                this.pond.on('processfileprogress', async () => {
                    this.dispatchFormEvent('file-upload-started')
                })

                this.pond.on('processfile', async () => {
                    this.dispatchFormEvent('file-upload-finished')
                })

                this.pond.on('processfiles', async () => {
                    this.dispatchFormEvent('file-upload-finished')
                })

                this.pond.on('processfileabort', async () => {
                    this.dispatchFormEvent('file-upload-finished')
                })

                this.pond.on('processfilerevert', async () => {
                    this.dispatchFormEvent('file-upload-finished')
                })
            },

            dispatchFormEvent: function (name) {
                this.$el.closest('form')?.dispatchEvent(
                    new CustomEvent(name, {
                        composed: true,
                        cancelable: true,
                    })
                )
            },

            getUploadedFileUrls: async function () {
                const uploadedFileUrls = await getUploadedFileUrlsUsing()

                this.fileKeyIndex = uploadedFileUrls ?? {}

                this.uploadedFileUrlIndex = Object.entries(this.fileKeyIndex)
                    .filter(value => value)
                    .reduce((obj, [key, value]) => {
                        obj[value] = key

                        return obj
                    }, {})
            },

            getFiles: async function () {
                await this.getUploadedFileUrls()

                let files = []

                for (const uploadedFileUrl of Object.values(this.fileKeyIndex)) {
                    if (! uploadedFileUrl) {
                        continue
                    }

                    files.push({
                        source: uploadedFileUrl,
                        options: {
                            type: 'local',
                        },
                    })
                }

                return shouldAppendFiles ? files : files.reverse()
            },

            insertLinkToFile: function (file) {
                if (file.origin !== FilePond.FileOrigin.LOCAL) {
                    return
                }

                const url = this.getFileUrl(file)

                if (! url) {
                    return
                }

                document.getElementById(`filepond--item-${file.id}`)
                    .querySelector('.filepond--file-info-main')
                    .prepend(url)
            },

            getFileUrl: function (file) {
                let fileSource = file.source

                if (! fileSource) {
                    return
                }

                const anchor = document.createElement('a')
                anchor.className = canDownload ? 'filepond--download-icon' : 'filepond--external-icon'
                anchor.href = fileSource

                if (shouldOpenInNewTab) {
                    anchor.target = '_blank'
                }

                if (canDownload) {
                    anchor.download = file.file.name
                }

                return anchor
            }
        }
    })
}
