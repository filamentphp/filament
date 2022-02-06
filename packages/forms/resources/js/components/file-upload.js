import * as FilePond from 'filepond'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'
import '../../css/components/file-upload.css'

FilePond.registerPlugin(FilePondPluginFileValidateSize)
FilePond.registerPlugin(FilePondPluginFileValidateType)
FilePond.registerPlugin(FilePondPluginImageCrop)
FilePond.registerPlugin(FilePondPluginImageExifOrientation)
FilePond.registerPlugin(FilePondPluginImagePreview)
FilePond.registerPlugin(FilePondPluginImageResize)
FilePond.registerPlugin(FilePondPluginImageTransform)

export default (Alpine) => {
    Alpine.data('fileUploadFormComponent', ({
        acceptedFileTypes,
        deleteUploadedFileUsing,
        getUploadedFileUrlUsing,
        imageCropAspectRatio,
        imagePreviewHeight,
        imageResizeTargetHeight,
        imageResizeTargetWidth,
        loadingIndicatorPosition,
        panelAspectRatio,
        panelLayout,
        placeholder,
        maxSize,
        minSize,
        removeUploadedFileButtonPosition,
        removeUploadedFileUsing,
        state,
        uploadButtonPosition,
        uploadProgressIndicatorPosition,
        uploadUsing,
    }) => {
        return {
            fileKeyIndex: {},

            files: [],

            pond: null,

            shouldUpdateState: true,

            state,

            uploadedFileUrlIndex: {},

            init: async function () {
                for (const [fileKey, file] of Object.entries(this.state)) {
                    if (file.startsWith('livewire-file:')) {
                        continue;
                    }

                    let uploadedFileUrl = await this.getUploadedFileUrl(fileKey)

                    if (! uploadedFileUrl) {
                        continue
                    }

                    this.files.push({
                        source: uploadedFileUrl,
                        options: {
                            type: 'local',
                        },
                    })
                }

                this.pond = FilePond.create(this.$refs.input, {
                    acceptedFileTypes,
                    credits: false,
                    files: this.files,
                    imageCropAspectRatio,
                    imagePreviewHeight,
                    imageResizeTargetHeight,
                    imageResizeTargetWidth,
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

                    let files = []

                    for (let fileKey of Object.keys(this.state)) {
                        let uploadedFileUrl = await this.getUploadedFileUrl(fileKey)

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

                    this.pond.files = files
                })
            },

            getUploadedFileUrl: async function (fileKey) {
                let uploadedFileUrl = this.fileKeyIndex[fileKey] ?? null

                if (uploadedFileUrl !== null) {
                    return uploadedFileUrl
                }

                uploadedFileUrl = await getUploadedFileUrlUsing(fileKey)

                this.uploadedFileUrlIndex[uploadedFileUrl] = fileKey
                this.fileKeyIndex[fileKey] = uploadedFileUrl

                return uploadedFileUrl
            },
        }
    })
}
