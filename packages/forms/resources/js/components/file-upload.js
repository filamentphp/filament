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
            cachedFileKeys: {},

            files: [],

            pond: null,

            state,

            init: async function () {
                for (const [fileKey, file] of Object.entries(this.state)) {
                    if (file.startsWith('livewire-file:')) {
                        continue;
                    }

                    let uploadedFileUrl = await getUploadedFileUrlUsing(fileKey)

                    if (! uploadedFileUrl) {
                        continue
                    }

                    this.files.push({
                        source: uploadedFileUrl,
                        options: {
                            type: 'local',
                        },
                    })

                    this.cachedFileKeys[uploadedFileUrl] = fileKey
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
                        process: async (fieldName, file, metadata, load, error, progress) => {
                            await uploadUsing(file, load, error, progress)
                        },
                        remove: async (source, load) => {
                            let fileKey = this.cachedFileKeys[source] ?? null

                            if (! fileKey) {
                                return
                            }

                            await removeUploadedFileUsing(fileKey)

                            load()
                        },
                        revert: async (uniqueFileId, load) => {
                            await removeUploadedFileUsing(uniqueFileId)

                            load()
                        },
                    },
                })

                this.$watch('state', async () => {
                    if (this.state.length === 0) {
                        this.pond.removeFiles()

                        return
                    }

                    if (Object.values(this.state).filter((file) => file.startsWith('livewire-file:')).length) {
                        return
                    }

                    this.pond.files = []

                    for (let fileKey of Object.keys(this.state)) {
                        let uploadedFileUrl = await getUploadedFileUrlUsing(fileKey)

                        if (! uploadedFileUrl) {
                            continue
                        }

                        this.pond.files.push({
                            source: uploadedFileUrl,
                            options: {
                                type: 'local',
                            },
                        })

                        this.cachedFileKeys[uploadedFileUrl] = fileKey
                    }
                })
            }
        }
    })
}
