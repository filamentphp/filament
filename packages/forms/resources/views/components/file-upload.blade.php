@pushonce('filament-styles:file-upload-component')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.css">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root {
            font-family: inherit;
            border: 1px dashed var(--f-gray-300);
            border-radius: 0.25rem;
            overflow: hidden
        }

        .filepond--panel-root {
            background-color: #fff;
        }

        .filepond--drop-label label {
            color: inherit;
            font-size: .8125rem;
            line-height: 1.25;
            cursor: pointer;
        }

        .filepond--label-action {
            font-weight: 500;
        }
    </style>
@endpushonce

@pushonce('filament-scripts:file-upload-component')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script>
        function fileUpload(config) {
            return {
                init: function () {
                    FilePond.registerPlugin(FilePondPluginFileValidateSize)
                    FilePond.registerPlugin(FilePondPluginFileValidateType)
                    FilePond.registerPlugin(FilePondPluginImageCrop)
                    FilePond.registerPlugin(FilePondPluginImageExifOrientation)
                    FilePond.registerPlugin(FilePondPluginImagePreview)
                    FilePond.registerPlugin(FilePondPluginImageResize)
                    FilePond.registerPlugin(FilePondPluginImageTransform)

                    this.$wire.getUploadedFileUrl(config.name, config.disk).then((uploadedFileUrl) => {
                        if (uploadedFileUrl) {
                            config.files = [{
                                source: uploadedFileUrl,
                                options: {
                                    type: 'local',
                                },
                            }]
                        }

                        this.pond = FilePond.create(this.$refs.input, config)
                    })

                    this.$watch('value', () => {
                        this.$wire.getUploadedFileUrl(config.name, config.disk).then((uploadedFileUrl) => {
                            if (uploadedFileUrl) {
                                this.pond.files = [{
                                    source: uploadedFileUrl,
                                    options: {
                                        type: 'local',
                                    },
                                }]
                            } else {
                                this.pond.files = []
                            }
                        })
                    })
                },
                pond: null,
                value: config.value,
            }
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->getId()"
    :help-message="$formComponent->helpMessage"
    :hint="$formComponent->hint"
    :label="$formComponent->label"
    :required="$formComponent->required"
>
    <div
        x-data="fileUpload({
            acceptedFileTypes: {{ json_encode($formComponent->acceptedFileTypes) }},
            disk: '{{ $formComponent->disk }}',
            files: [],
            {{ $formComponent->imageCropAspectRatio !== null ? "imageCropAspectRatio: '{$formComponent->imageCropAspectRatio}'," : null }}
            {{ $formComponent->imagePreviewHeight !== null ? "imagePreviewHeight: {$formComponent->imagePreviewHeight}," : null }}
            {{ $formComponent->imageResizeTargetHeight !== null ? "imageResizeTargetHeight: {$formComponent->imageResizeTargetHeight}," : null }}
            {{ $formComponent->imageResizeTargetWidth !== null ? "imageResizeTargetWidth: {$formComponent->imageResizeTargetWidth}," : null }}
            {{ __($formComponent->placeholder) !== null ? 'labelIdle: \'' . __($formComponent->placeholder) . '\',' : null }}
            {{ $formComponent->maxSize !== null ? "maxFileSize: '{$formComponent->maxSize} KB'," : null }}
            {{ $formComponent->minSize !== null ? "minFileSize: '{$formComponent->minSize} KB'," : null }}
            name: '{{ $formComponent->name }}',
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
                        '{{ $formComponent->getTemporaryUploadedFilePropertyName() }}',
                        file,
                        (uploadedFilename) => load(uploadedFilename),
                        () => error(),
                        (event) => progress(event.detail.progress)
                    )
                },
                remove: (source, load, error) => {
                    @this.removeUploadedFile('{{ $formComponent->name }}').then(() => {
                        load()
                    })
                },
                revert: (uniqueFileId, load, error) => {
                    @this.clearTemporaryUploadedFile('{{ $formComponent->name }}').then(() => {
                        load()
                    })
                },
            },
            styleButtonProcessItemPosition: '{{ $formComponent->uploadButtonPosition }}',
            styleButtonRemoveItemPosition: '{{ $formComponent->removeUploadButtonPosition }}',
            styleLoadIndicatorPosition: '{{ $formComponent->loadingIndicatorPosition }}',
            {{ $formComponent->panelAspectRatio !== null ? "stylePanelAspectRatio: '{$formComponent->panelAspectRatio}'," : null }}
            {{ $formComponent->panelLayout !== null ? "stylePanelLayout: '{$formComponent->panelLayout}'," : null }}
            styleProgressIndicatorPosition: '{{ $formComponent->uploadProgressIndicatorPosition }}',
            value: @entangle($formComponent->name){{ Str::of($formComponent->nameAttribute)->after('wire:model') }},
        })"
        x-init="init()"
        wire:ignore
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    >
        <input
            x-ref="input"
            {{ $formComponent->disabled ? 'disabled' : '' }}
            type="file"
        />
    </div>
</x-forms::field-group>
