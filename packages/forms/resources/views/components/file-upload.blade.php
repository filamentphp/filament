<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isAvatar() || $isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @php
        $imageCropAspectRatio = $getImageCropAspectRatio();
        $imageResizeTargetHeight = $getImageResizeTargetHeight();
        $imageResizeTargetWidth = $getImageResizeTargetWidth();
        $imageResizeMode = $getImageResizeMode();
        $imageResizeUpscale = $getImageResizeUpscale();
        $shouldTransformImage = $imageCropAspectRatio || $imageResizeTargetHeight || $imageResizeTargetWidth;
    @endphp

    <div
        x-data="fileUploadFormComponent({
                    acceptedFileTypes: {{ json_encode($getAcceptedFileTypes()) }},
                    canDownload: {{ $canDownload() ? 'true' : 'false' }},
                    canOpen: {{ $canOpen() ? 'true' : 'false' }},
                    canPreview: {{ $canPreview() ? 'true' : 'false' }},
                    canReorder: {{ $canReorder() ? 'true' : 'false' }},
                    chunkUploads: {{ $chunkUploads() ? 'true' : 'false' }},
                    chunkSize: {{ $chunkUploads() ? "'{$getChunkSize()}'" : 'null' }},
                    deleteUploadedFileUsing: async (fileKey) => {
                        return await $wire.deleteUploadedFile('{{ $getStatePath() }}', fileKey)
                    },
                    getUploadedFileUrlsUsing: async () => {
                        return await $wire.getUploadedFileUrls('{{ $getStatePath() }}')
                    },
                    imageCropAspectRatio:
                        {{ $imageCropAspectRatio ? "'{$imageCropAspectRatio}'" : 'null' }},
                    imagePreviewHeight:
                        {{ ($height = $getImagePreviewHeight()) ? "'{$height}'" : 'null' }},
                    imageResizeMode: {{ $imageResizeMode ? "'{$imageResizeMode}'" : 'null' }},
                    imageResizeTargetHeight:
                        {{ $imageResizeTargetHeight ? "'{$imageResizeTargetHeight}'" : 'null' }},
                    imageResizeTargetWidth:
                        {{ $imageResizeTargetWidth ? "'{$imageResizeTargetWidth}'" : 'null' }},
                    imageResizeUpscale: {{ $imageResizeUpscale ? 'true' : 'false' }},
                    isAvatar: {{ $isAvatar() ? 'true' : 'false' }},
                    loadingIndicatorPosition: '{{ $getLoadingIndicatorPosition() }}',
                    locale: @js(app()->getLocale()),
                    panelAspectRatio:
                        {{ ($aspectRatio = $getPanelAspectRatio()) ? "'{$aspectRatio}'" : 'null' }},
                    panelLayout: {{ ($layout = $getPanelLayout()) ? "'{$layout}'" : 'null' }},
                    placeholder: @js($getPlaceholder()),
                    maxSize: {{ ($size = $getMaxSize()) ? "'{$size} KB'" : 'null' }},
                    minSize: {{ ($size = $getMinSize()) ? "'{$size} KB'" : 'null' }},
                    removeUploadedFileUsing: async (fileKey) => {
                        return await $wire.removeUploadedFile('{{ $getStatePath() }}', fileKey)
                    },
                    removeUploadedFileButtonPosition:
                        '{{ $getRemoveUploadedFileButtonPosition() }}',
                    reorderUploadedFilesUsing: async (files) => {
                        return await $wire.reorderUploadedFiles('{{ $getStatePath() }}', files)
                    },
                    shouldAppendFiles: {{ $shouldAppendFiles() ? 'true' : 'false' }},
                    shouldOrientImageFromExif:
                        {{ $shouldOrientImageFromExif() ? 'true' : 'false' }},
                    shouldTransformImage: {{ $shouldTransformImage ? 'true' : 'false' }},
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
                    uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
                    uploadProgressIndicatorPosition:
                        '{{ $getUploadProgressIndicatorPosition() }}',
                    uploadUsing: (fileKey, file, success, error, progress, abort, transfer, options) => {
                        if (options.chunkUploads) {
                            const chunksCount = Math.ceil(file.size / options.chunkSize);
                            let chunks = []
                            for (i = 0; i < chunksCount; i++) {
                                let chunkStart = i * options.chunkSize;
                                let chunkEnd = Math.min(chunkStart + options.chunkSize, file.size)
                                chunks = [...chunks, file.slice(chunkStart, chunkEnd)]
                            }

                            $wire.uploadMultiple(
                                `{{ $getStatePath() }}.${fileKey}`, 
                                chunks, 
                                () => {
                                    $wire.processChunks(`{{ $getStatePath() }}`, fileKey, file.name, file.size)
                                        .then(() => success(fileKey))
                                }, 
                                error,
                                progress,
                            )
                            return;
                        }
                        
                        $wire.upload(
                            `{{ $getStatePath() }}.${fileKey}`,
                            file,
                            () => {
                                success(fileKey)
                            },
                            error,
                            progress,
                        )
                    },
                })"
        wire:ignore
        {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
        style="
            min-height: {{ $isAvatar() ? '8em' : ($getPanelLayout() === 'compact' ? '2.625em' : '4.75em') }};
        "
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->class([
                    'filament-forms-file-upload-component',
                    'mx-auto w-32' => $isAvatar(),
                ])
        }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        <input
            x-ref="input"
            {{ $isDisabled() ? 'disabled' : '' }}
            {{ $isMultiple() ? 'multiple' : '' }}
            type="file"
            {{ $getExtraInputAttributeBag() }}
            dusk="filament.forms.{{ $getStatePath() }}"
        />
    </div>
</x-dynamic-component>
