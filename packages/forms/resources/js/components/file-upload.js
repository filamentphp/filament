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
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.css'
import '../../css/components/file-upload.css'

FilePond.registerPlugin(FilePondPluginFileValidateSize)
FilePond.registerPlugin(FilePondPluginFileValidateType)
FilePond.registerPlugin(FilePondPluginImageCrop)
FilePond.registerPlugin(FilePondPluginImageExifOrientation)
FilePond.registerPlugin(FilePondPluginImagePreview)
FilePond.registerPlugin(FilePondPluginImageResize)
FilePond.registerPlugin(FilePondPluginImageTransform)
FilePond.registerPlugin(FilePondPluginMediaPreview)

window.FilePond = FilePond

export default (Alpine) => {
    Alpine.data(
        'fileUploadFormComponent',
        ({
            acceptedFileTypes,
            canDownload,
            canOpen,
            canPreview,
            canReorder,
            deleteUploadedFileUsing,
            getUploadedFileUrlsUsing,
            imageCropAspectRatio,
            imagePreviewHeight,
            imageResizeMode,
            imageResizeTargetHeight,
            imageResizeTargetWidth,
            imageResizeUpscale,
            isAvatar,
            loadingIndicatorPosition,
            locale,
            panelAspectRatio,
            panelLayout,
            placeholder,
            maxSize,
            minSize,
            removeUploadedFileButtonPosition,
            removeUploadedFileUsing,
            reorderUploadedFilesUsing,
            shouldAppendFiles,
            shouldOrientImageFromExif,
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
                    FilePond.setOptions(locales[locale] ?? locales['en'])

                    this.pond = FilePond.create(this.$refs.input, {
                        acceptedFileTypes,
                        allowPaste: false,
                        allowReorder: canReorder,
                        allowImageExifOrientation: shouldOrientImageFromExif,
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
                        imageResizeUpscale,
                        itemInsertLocation: shouldAppendFiles
                            ? 'after'
                            : 'before',
                        ...(placeholder && { labelIdle: placeholder }),
                        maxFileSize: maxSize,
                        minFileSize: minSize,
                        styleButtonProcessItemPosition: uploadButtonPosition,
                        styleButtonRemoveItemPosition:
                            removeUploadedFileButtonPosition,
                        styleLoadIndicatorPosition: loadingIndicatorPosition,
                        stylePanelAspectRatio: panelAspectRatio,
                        stylePanelLayout: panelLayout,
                        styleProgressIndicatorPosition:
                            uploadProgressIndicatorPosition,
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
                                        (crypto.getRandomValues(
                                            new Uint8Array(1),
                                        )[0] &
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
                                let fileKey =
                                    this.uploadedFileUrlIndex[source] ?? null

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
                    })

                    this.$watch('state', async () => {
                        if (!this.shouldUpdateState) {
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
                                    : this.uploadedFileUrlIndex[file.source] ??
                                      null,
                            ) // file.serverId is null for a file that is not yet uploaded
                            .filter((fileKey) => fileKey)

                        await reorderUploadedFilesUsing(
                            shouldAppendFiles
                                ? orderedFileKeys
                                : orderedFileKeys.reverse(),
                        )
                    })

                    this.pond.on('initfile', async (fileItem) => {
                        if (!canDownload) {
                            return
                        }

                        if (isAvatar) {
                            return
                        }

                        this.insertDownloadLink(fileItem)
                    })

                    this.pond.on('initfile', async (fileItem) => {
                        if (!canOpen) {
                            return
                        }

                        if (isAvatar) {
                            return
                        }

                        this.insertOpenLink(fileItem)
                    })

                    this.pond.on('addfilestart', async (file) => {
                        if (
                            file.status !==
                            FilePond.FileStatus.PROCESSING_QUEUED
                        ) {
                            return
                        }

                        this.dispatchFormEvent('file-upload-started')
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
                                            FilePond.FileStatus
                                                .PROCESSING_QUEUED,
                                ).length
                        ) {
                            return
                        }

                        this.dispatchFormEvent('file-upload-finished')
                    }

                    this.pond.on('processfile', handleFileProcessing)

                    this.pond.on('processfileabort', handleFileProcessing)

                    this.pond.on('processfilerevert', handleFileProcessing)
                },

                dispatchFormEvent: function (name) {
                    this.$el.closest('form')?.dispatchEvent(
                        new CustomEvent(name, {
                            composed: true,
                            cancelable: true,
                        }),
                    )
                },

                getUploadedFileUrls: async function () {
                    const uploadedFileUrls = await getUploadedFileUrlsUsing()

                    this.fileKeyIndex = uploadedFileUrls ?? {}

                    this.uploadedFileUrlIndex = Object.entries(
                        this.fileKeyIndex,
                    )
                        .filter((value) => value)
                        .reduce((obj, [key, value]) => {
                            obj[value] = key

                            return obj
                        }, {})
                },

                getFiles: async function () {
                    await this.getUploadedFileUrls()

                    let files = []

                    for (const uploadedFileUrl of Object.values(
                        this.fileKeyIndex,
                    )) {
                        if (!uploadedFileUrl) {
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

                insertDownloadLink: function (file) {
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

                insertOpenLink: function (file) {
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

                getDownloadLink: function (file) {
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

                getOpenLink: function (file) {
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
            }
        },
    )
}

import ar from 'filepond/locale/ar-ar'
import cs from 'filepond/locale/cs-cz'
import da from 'filepond/locale/da-dk'
import de from 'filepond/locale/de-de'
import en from 'filepond/locale/en-en'
import es from 'filepond/locale/es-es'
import fa from 'filepond/locale/fa_ir'
import fi from 'filepond/locale/fi-fi'
import fr from 'filepond/locale/fr-fr'
import hu from 'filepond/locale/hu-hu'
import id from 'filepond/locale/id-id'
import it from 'filepond/locale/it-it'
import nl from 'filepond/locale/nl-nl'
import pl from 'filepond/locale/pl-pl'
import pt_BR from 'filepond/locale/pt-br'
import pt_PT from 'filepond/locale/pt-br'
import ro from 'filepond/locale/ro-ro'
import ru from 'filepond/locale/ru-ru'
import sv from 'filepond/locale/sv_se'
import tr from 'filepond/locale/tr-tr'
import uk from 'filepond/locale/uk-ua'
import vi from 'filepond/locale/vi-vi'
import zh_CN from 'filepond/locale/zh-cn'
import zh_TW from 'filepond/locale/zh-tw'

const locales = {
    ar,
    cs,
    da,
    de,
    en,
    es,
    fa,
    fi,
    fr,
    hu,
    id,
    it,
    nl,
    pl,
    pt_BR,
    pt_PT,
    ro,
    ru,
    sv,
    tr,
    uk,
    vi,
    zh_CN,
    zh_TW,
}
