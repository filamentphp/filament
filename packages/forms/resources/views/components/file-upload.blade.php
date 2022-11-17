<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :label-sr-only="$isAvatar() || $isLabelHidden()"
>
    @php
        $imageCropAspectRatio = $getImageCropAspectRatio();
        $imageResizeTargetHeight = $getImageResizeTargetHeight();
        $imageResizeTargetWidth = $getImageResizeTargetWidth();
        $imageResizeMode = $getImageResizeMode();
        $shouldTransformImage = $imageCropAspectRatio || $imageResizeTargetHeight || $imageResizeTargetWidth;
    @endphp
    <div
        x-ignore
        ax-load
        ax-load-src="/js/filament/forms/components/file-upload.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
        x-data="fileUploadFormComponent({
            acceptedFileTypes: {{ json_encode($getAcceptedFileTypes()) }},
            canDownload: {{ $canDownload() ? 'true' : 'false' }},
            canOpen: {{ $canOpen() ? 'true' : 'false' }},
            canPreview: {{ $canPreview() ? 'true' : 'false' }},
            canReorder: {{ $canReorder() ? 'true' : 'false' }},
            deleteUploadedFileUsing: async (fileKey) => {
                return await $wire.deleteUploadedFile('{{ $getStatePath() }}', fileKey)
            },
            getUploadedFilesUsing: async () => {
                return await $wire.getUploadedFiles('{{ $getStatePath() }}')
            },
            imageCropAspectRatio: {{ $imageCropAspectRatio ? "'{$imageCropAspectRatio}'" : 'null' }},
            imagePreviewHeight: {{ ($height = $getImagePreviewHeight()) ? "'{$height}'" : 'null' }},
            imageResizeMode: {{ $imageResizeMode ? "'{$imageResizeMode}'" : 'null' }},
            imageResizeTargetHeight: {{ $imageResizeTargetHeight ? "'{$imageResizeTargetHeight}'" : 'null' }},
            imageResizeTargetWidth: {{ $imageResizeTargetWidth ? "'{$imageResizeTargetWidth}'" : 'null' }},
            isAvatar: {{ $isAvatar() ? 'true' : 'false' }},
            loadingIndicatorPosition: '{{ $getLoadingIndicatorPosition() }}',
            locale: @js(app()->getLocale()),
            panelAspectRatio: {{ ($aspectRatio = $getPanelAspectRatio()) ? "'{$aspectRatio}'" : 'null' }},
            panelLayout: {{ ($layout = $getPanelLayout()) ? "'{$layout}'" : 'null' }},
            placeholder: @js($getPlaceholder()),
            maxSize: {{ ($size = $getMaxSize()) ? "'{$size} KB'" : 'null' }},
            minSize: {{ ($size = $getMinSize()) ? "'{$size} KB'" : 'null' }},
            removeUploadedFileUsing: async (fileKey) => {
                return await $wire.removeUploadedFile('{{ $getStatePath() }}', fileKey)
            },
            removeUploadedFileButtonPosition: '{{ $getRemoveUploadedFileButtonPosition() }}',
            reorderUploadedFilesUsing: async (files) => {
                return await $wire.reorderUploadedFiles('{{ $getStatePath() }}', files)
            },
            shouldAppendFiles: {{ $shouldAppendFiles() ? 'true' : 'false' }},
            shouldTransformImage: {{ $shouldTransformImage ? 'true' : 'false' }},
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
            uploadProgressIndicatorPosition: '{{ $getUploadProgressIndicatorPosition() }}',
            uploadUsing: (fileKey, file, success, error, progress) => {
                $wire.upload(`{{ $getStatePath() }}.${fileKey}`, file, () => {
                    success(fileKey)
                }, error, progress)
            },
        })"
        wire:ignore
        style="min-height: {{ $isAvatar() ? '8em' : ($getPanelLayout() === 'compact' ? '2.625em' : '4.75em') }}"
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'filament-forms-file-upload-component',
                    'w-32 mx-auto' => $isAvatar(),
                ])
        }}
    >
        <input
            x-ref="input"
            {{
                $getExtraInputAttributeBag()
                    ->merge([
                        'disabled' => $isDisabled(),
                        'dusk' => "filament.forms.{$getStatePath()}",
                        'multiple' => $isMultiple(),
                        'type' => 'file',
                    ], escape: false)
            }}
        />
    </div>
</x-dynamic-component>
