@pushonce('filament-styles:file-field')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.css">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root {
            font-family: inherit;
            font-size: .8125rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid rgb(209, 213, 219);
        }

        .filepond--label-action {
            font-weight: 500;
        }

        .filepond--panel-root {
            background-color: #fff;
        }

        .filepond--drop-label {
            color: inherit;
        }
    </style>
@endpushonce

@pushonce('filament-scripts:file-field')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
@endpushonce

<x-filament::field-group
    :error-key="$field->name"
    :for="$field->id"
    :help-message="__($field->helpMessage)"
    :hint="__($field->hint)"
    :label="__($field->label)"
    :required="$field->required"
>
    <div
        x-data="{}"
        x-init="
            FilePond.registerPlugin(FilePondPluginFileValidateSize)
            FilePond.registerPlugin(FilePondPluginFileValidateType)
            FilePond.registerPlugin(FilePondPluginImageCrop)
            FilePond.registerPlugin(FilePondPluginImageExifOrientation)
            FilePond.registerPlugin(FilePondPluginImagePreview)
            FilePond.registerPlugin(FilePondPluginImageResize)
            FilePond.registerPlugin(FilePondPluginImageTransform)

            let config = {
                acceptedFileTypes: {{ json_encode($field->acceptedFileTypes) }},
                files: [],
                {{ $field->imageCropAspectRatio !== null ? "imageCropAspectRatio: '{$field->imageCropAspectRatio}'," : null }}
                {{ $field->imagePreviewHeight !== null ? "imagePreviewHeight: {$field->imagePreviewHeight}," : null }}
                {{ $field->imageResizeTargetHeight !== null ? "imageResizeTargetHeight: {$field->imageResizeTargetHeight}," : null }}
                {{ $field->imageResizeTargetWidth !== null ? "imageResizeTargetWidth: {$field->imageResizeTargetWidth}," : null }}
                labelIdle: {{ $field->placeholder !== null ? __($field->placeholder) : 'HERE' }},
                {{ $field->maxSize !== null ? "maxFileSize: '{$field->maxSize} KB'," : null }}
                {{ $field->minSize !== null ? "minFileSize: '{$field->minSize} KB'," : null }}
                server: {
                    load: (source, load, error, progress, abort, headers) => {
                        fetch(source).then((response) => {
                            response.blob().then((blob) => {
                                load(blob)
                            })
                        })
                    },
                    process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                        @this.upload(
                            '{{ $field->getTemporaryUploadedFilePropertyName() }}',
                            file,
                            (uploadedFilename) => load(uploadedFilename),
                            () => error(),
                            (event) => progress(event.detail.progress)
                        )
                    },
                    remove: (source, load, error) => {
                        $wire.removeUploadedFile('{{ $field->name }}').then(() => {
                            load()
                        })
                    },
                    revert: (uniqueFileId, load, error) => {
                        $wire.clearTemporaryUploadedFile('{{ $field->name }}').then(() => {
                            load()
                        })
                    },
                },
                styleButtonProcessItemPosition: '{{ $field->uploadButtonPosition }}',
                styleButtonRemoveItemPosition: '{{ $field->removeUploadButtonPosition }}',
                styleLoadIndicatorPosition: '{{ $field->loadingIndicatorPosition }}',
                {{ $field->panelAspectRatio !== null ? "stylePanelAspectRatio: '{$field->panelAspectRatio}'," : null }}
                {{ $field->panelLayout !== null ? "stylePanelLayout: '{$field->panelLayout}'," : null }}
                styleProgressIndicatorPosition: '{{ $field->uploadProgressIndicatorPosition }}',
            }

            $wire.getUploadedFileUrl('{{ $field->name }}', '{{ $field->disk }}').then((uploadedFileUrl) => {
                if (uploadedFileUrl) {
                    config.files.push({
                        source: uploadedFileUrl,
                        options: {
                            type: 'local',
                        },
                    })
                }

                FilePond.create($refs.input, config)
            })
        "
        wire:ignore
        {{ Filament\format_attributes($field->extraAttributes) }}
    >
        <input
            x-ref="input"
            {{ $field->disabled ? 'disabled' : '' }}
            type="file"
        />
    </div>
</x-filament::field-group>
