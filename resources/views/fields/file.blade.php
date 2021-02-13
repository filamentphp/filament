@pushonce('head:file-field')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.css">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endpushonce

@pushonce('js:file-field')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
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
            FilePond.registerPlugin(FilePondPluginImagePreview)

            let config = {
                acceptedFileTypes: {{ json_encode($field->acceptedFileTypes) }},
                files: [],
                labelIdle: '{{ __($field->placeholder) }}',
                {{ $field->maxSize ? "maxFileSize: '$field->maxSize KB'," : null }}
                {{ $field->minSize ? "minFileSize: '$field->minSize KB'," : null }}
                server: {
                    load: (source, load, error, progress, abort, headers) => {
                        fetch(source).then((response) => {
                            response.blob().then((myBlob) => {
                                load(myBlob)
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
    >
        <input x-ref="input" type="file" />
    </div>
</x-filament::field-group>
