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
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <div
        x-data="{
            disk: '{{ $formComponent->getDiskName() }}',
            files: [],
            initialUrl: {{ $formComponent->getUploadedFileUrl() ? "'{$formComponent->getUploadedFileUrl()}'" : 'null' }},
            name: '{{ $formComponent->getName() }}',
            pond: null,
            value: @entangle($formComponent->getName()){{ Str::of($formComponent->getBindingAttribute())->after('wire:model') }},
        }"
        x-init="
            FilePond.registerPlugin(FilePondPluginFileValidateSize)
            FilePond.registerPlugin(FilePondPluginFileValidateType)
            FilePond.registerPlugin(FilePondPluginImageCrop)
            FilePond.registerPlugin(FilePondPluginImageExifOrientation)
            FilePond.registerPlugin(FilePondPluginImagePreview)
            FilePond.registerPlugin(FilePondPluginImageResize)
            FilePond.registerPlugin(FilePondPluginImageTransform)

            if (initialUrl) {
                files = [{
                    source: initialUrl,
                    options: {
                        type: 'local',
                    },
                }]
            }

            pond = FilePond.create($refs.input, {
                acceptedFileTypes: {{ json_encode($formComponent->getAcceptedFileTypes()) }},
                files: files,
                {{ $formComponent->getImageCropAspectRatio() !== null ? "imageCropAspectRatio: '{$formComponent->getImageCropAspectRatio()}'," : null }}
                {{ $formComponent->getImagePreviewHeight() !== null ? "imagePreviewHeight: {$formComponent->getImagePreviewHeight()}," : null }}
                {{ $formComponent->getImageResizeTargetHeight() !== null ? "imageResizeTargetHeight: {$formComponent->getImageResizeTargetHeight()}," : null }}
                {{ $formComponent->getImageResizeTargetWidth() !== null ? "imageResizeTargetWidth: {$formComponent->getImageResizeTargetWidth()}," : null }}
                {{ __($formComponent->getPlaceholder()) !== null ? 'labelIdle: \'' . __($formComponent->getPlaceholder()) . '\',' : null }}
                {{ $formComponent->getMaxSize() !== null ? "maxFileSize: '{$formComponent->getMaxSize()} KB'," : null }}
                {{ $formComponent->getMinSize() !== null ? "minFileSize: '{$formComponent->getMinSize()} KB'," : null }}
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
                        @this.removeUploadedFile('{{ $formComponent->getName() }}').then(() => {
                            load()
                        })
                    },
                    revert: (uniqueFileId, load, error) => {
                        @this.clearTemporaryUploadedFile('{{ $formComponent->getName() }}').then(() => {
                            load()
                        })
                    },
                },
                styleButtonProcessItemPosition: '{{ $formComponent->getUploadButtonPosition() }}',
                styleButtonRemoveItemPosition: '{{ $formComponent->getRemoveUploadButtonPosition() }}',
                styleLoadIndicatorPosition: '{{ $formComponent->getLoadingIndicatorPosition() }}',
                {{ $formComponent->getPanelAspectRatio() !== null ? "stylePanelAspectRatio: '{$formComponent->getPanelAspectRatio()}'," : null }}
                {{ $formComponent->getPanelLayout() !== null ? "stylePanelLayout: '{$formComponent->getPanelLayout()}'," : null }}
                styleProgressIndicatorPosition: '{{ $formComponent->getUploadProgressIndicatorPosition() }}',
            })

            $watch('value', () => {
                @this.getUploadedFileUrl(name, disk).then((uploadedFileUrl) => {
                    if (uploadedFileUrl) {
                        pond.files = [{
                            source: uploadedFileUrl,
                            options: {
                                type: 'local',
                            },
                        }]
                    } else {
                        pond.files = []
                    }
                })
            })
        "
        wire:ignore
        {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
    >
        <input
            x-ref="input"
            {{ $formComponent->isDisabled() ? 'disabled' : '' }}
            type="file"
        />
    </div>
</x-forms::field-group>
