@pushonce('filament-styles:file-field')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.css">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root {
            font-family: inherit;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid var(--f-gray-300);
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

@php
    $formComponent->placeholder = __($formComponent->placeholder);
@endphp

<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->id"
    :help-message="__($formComponent->helpMessage)"
    :hint="__($formComponent->hint)"
    :label="__($formComponent->label)"
    :required="$formComponent->required"
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
                acceptedFileTypes: {{ json_encode($formComponent->acceptedFileTypes) }},
                files: [],
                {{ $formComponent->imageCropAspectRatio !== null ? "imageCropAspectRatio: '{$formComponent->imageCropAspectRatio}'," : null }}
                {{ $formComponent->imagePreviewHeight !== null ? "imagePreviewHeight: {$formComponent->imagePreviewHeight}," : null }}
                {{ $formComponent->imageResizeTargetHeight !== null ? "imageResizeTargetHeight: {$formComponent->imageResizeTargetHeight}," : null }}
                {{ $formComponent->imageResizeTargetWidth !== null ? "imageResizeTargetWidth: {$formComponent->imageResizeTargetWidth}," : null }}
                {{ $formComponent->placeholder !== null ? "labelIdle: '{$formComponent->placeholder} KB'," : null }}
                {{ $formComponent->maxSize !== null ? "maxFileSize: '{$formComponent->maxSize} KB'," : null }}
                {{ $formComponent->minSize !== null ? "minFileSize: '{$formComponent->minSize} KB'," : null }}
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
                        $wire.removeUploadedFile('{{ $formComponent->name }}').then(() => {
                            load()
                        })
                    },
                    revert: (uniqueFileId, load, error) => {
                        $wire.clearTemporaryUploadedFile('{{ $formComponent->name }}').then(() => {
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
            }

            $wire.getUploadedFileUrl('{{ $formComponent->name }}', '{{ $formComponent->disk }}').then((uploadedFileUrl) => {
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
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    >
        <input
            x-ref="input"
            {{ $formComponent->disabled ? 'disabled' : '' }}
            type="file"
        />
    </div>
</x-forms::field-group>
