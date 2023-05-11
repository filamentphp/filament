import * as FilePond from 'filepond'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'
import FilePondPluginMediaPreview from 'filepond-plugin-media-preview'
import FilePondPluginImageEdit from "filepond-plugin-image-edit";
import Cropper from 'cropperjs'

FilePond.registerPlugin(FilePondPluginFileValidateSize)
FilePond.registerPlugin(FilePondPluginFileValidateType)
FilePond.registerPlugin(FilePondPluginImageCrop)
FilePond.registerPlugin(FilePondPluginImageExifOrientation)
FilePond.registerPlugin(FilePondPluginImagePreview)
FilePond.registerPlugin(FilePondPluginImageResize)
FilePond.registerPlugin(FilePondPluginImageTransform)
FilePond.registerPlugin(FilePondPluginMediaPreview)
FilePond.registerPlugin(FilePondPluginImageEdit)

window.FilePond = FilePond

export default function fileUploadFormComponent({
    acceptedFileTypes,
    deleteUploadedFileUsing,
    getUploadedFilesUsing,
    imageCropAspectRatio,
    imagePreviewHeight,
    imageResizeMode,
    imageResizeTargetHeight,
    imageResizeTargetWidth,
    imageResizeUpscale,
    isAvatar,
    isDownloadable,
    isOpenable,
    isPreviewable,
    isReorderable,
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
    disabled,
    isCroppable,
    cropperViewPortWidth,
    cropperViewPortHeight,
    shape,
    updateCropperInputs,
    viewMode,
    fillColor,
}) {
    return {
        fileKeyIndex: {},

        pond: null,

        shouldUpdateState: true,

        state,

        lastState: null,

        uploadedFileIndex: {},

        showCropper: false,

        editingFile: {},

        currentRatio: '',

        cropper: {},

        init: async function () {
            FilePond.setOptions(locales[locale] ?? locales['en'])

            this.pond = FilePond.create(this.$refs.input, {
                acceptedFileTypes,
                allowImageExifOrientation: shouldOrientImageFromExif,
                allowPaste: false,
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
                itemInsertLocation: shouldAppendFiles ? 'after' : 'before',
                ...(placeholder && { labelIdle: placeholder }),
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
                allowImageEdit: isCroppable,
                imageEditEditor: {
                    open: (file, instructions)  => this.loadCropper(file),
                    onconfirm: (result) => {},
                    oncancel: () => this.cancelCropper(),
                    onclose: () => this.cancelCropper(),
                },
            })

            this.$watch('state', async () => {
                if (!this.pond) {
                    return
                }

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
                            : this.uploadedFileIndex[file.source] ?? null,
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
                if (file.status !== FilePond.FileStatus.PROCESSING_QUEUED) {
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
                                    FilePond.FileStatus.PROCESSING_QUEUED,
                        ).length
                ) {
                    return
                }

                this.dispatchFormEvent('file-upload-finished')
            }

            this.pond.on('processfile', handleFileProcessing)

            this.pond.on('processfileabort', handleFileProcessing)

            this.pond.on('processfilerevert', handleFileProcessing)

            this.$nextTick(() => this.initCropper())
        },

        initCropper() {
            this.cropper = new Cropper(this.$refs.cropper, {
                viewMode: viewMode,
                aspectRatio: cropperViewPortWidth / cropperViewPortHeight,
                autoCropArea: 1,
                guides: true,
                center: true,
                highlight: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: true,
                wheelZoomRatio: 0.02,
                responsive: true,
                preview: [
                    this.$refs.previewLG,
                    /*this.$refs.previewMD,
                    this.$refs.previewSM,
                    this.$refs.previewXS,*/
                ],
                crop: function (e) {
                    updateCropperInputs(e.detail)
                }
            });
        },

        cancelCropper() {
            this.editingFile = {}
            this.showCropper = false
        },

        loadCropper(file) {
            if(disabled || !isCroppable) return;
            if (file == null) return;

            this.editingFile = file;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.showCropper = true;
                this.bindCropper(e.target.result);
            };
            reader.readAsDataURL(file);
        },

        saveCropper() {
            if(disabled || !isCroppable) return;
            this.cropper.getCroppedCanvas({
                width: imageResizeTargetWidth,
                height: imageResizeTargetHeight,
                fillColor: fillColor,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            }).toBlob((croppedImage) => {

                //if editingFile.type is svg set it to png, because a Blob will never return svg
                const fileType = this.editingFile.type === 'image/svg+xml' ? 'image/png' : this.editingFile.type;

                const file = new File(
                    [croppedImage],
                    this.editingFile.name,
                    {
                        type: fileType,
                        lastModified: new Date().getTime(),
                    }
                );

                //get the filepond file object from the current editing file
                const filepondFile = this.pond.getFiles().find((f) => f.filename === this.editingFile.name);

                //remove the filepond file object
                this.pond.removeFile(filepondFile.id);

                //wait for the filepond file object to be removed
                this.$nextTick(() => {
                    this.pond.addFile(file).then(() => {
                        this.cancelCropper();
                    });
                });

            }, this.editingFile.type);
        },

        bindCropper(src) {
            //avoid problems with cropper container not being visible when binding
            setTimeout(() => {
                this.cropper.replace(src);
            }, 200);
        },

        destroy: function () {
            this.cropper.destroy()
            this.cropper = null
            FilePond.destroy(this.$refs.input)
            this.pond = null
        },

        dispatchFormEvent: function (name) {
            this.$el.closest('form')?.dispatchEvent(
                new CustomEvent(name, {
                    composed: true,
                    cancelable: true,
                }),
            )
        },

        getUploadedFiles: async function () {
            const uploadedFiles = await getUploadedFilesUsing()

            this.fileKeyIndex = uploadedFiles ?? {}

            this.uploadedFileIndex = Object.entries(this.fileKeyIndex)
                .filter(([key, value]) => value?.url)
                .reduce((obj, [key, value]) => {
                    obj[value.url] = key

                    return obj
                }, {})
        },

        getFiles: async function () {
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
                        ...(/^image/.test(uploadedFile.type)
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
