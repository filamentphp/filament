<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :label-sr-only="$isLabelHidden()"
>
    @php
        $imageCropAspectRatio = $getImageCropAspectRatio();
        $imageResizeTargetHeight = $getImageResizeTargetHeight();
        $imageResizeTargetWidth = $getImageResizeTargetWidth();
        $isAvatar = $isAvatar();
        $statePath = $getStatePath();
    @endphp

    <div
        x-ignore
        ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('file-upload', 'filament/forms') }}"
        x-data="fileUploadFormComponent({
                    acceptedFileTypes: @js($getAcceptedFileTypes()),
                    deleteUploadedFileUsing: async (fileKey) => {
                        return await $wire.deleteUploadedFile(@js($statePath), fileKey)
                    },
                    getUploadedFilesUsing: async () => {
                        return await $wire.getFormUploadedFiles(@js($statePath))
                    },
                    imageCropAspectRatio: @js($imageCropAspectRatio),
                    imagePreviewHeight: @js($getImagePreviewHeight()),
                    imageResizeMode: @js($getImageResizeMode()),
                    imageResizeTargetHeight: @js($imageResizeTargetHeight),
                    imageResizeTargetWidth: @js($imageResizeTargetWidth),
                    imageResizeUpscale: @js($getImageResizeUpscale()),
                    isAvatar: {{ $isAvatar ? 'true' : 'false' }},
                    isDownloadable: @js($isDownloadable()),
                    isOpenable: @js($isOpenable()),
                    isPreviewable: @js($isPreviewable()),
                    isReorderable: @js($isReorderable()),
                    loadingIndicatorPosition: @js($getLoadingIndicatorPosition()),
                    locale: @js(app()->getLocale()),
                    panelAspectRatio: @js($getPanelAspectRatio()),
                    panelLayout: @js($getPanelLayout()),
                    placeholder: @js($getPlaceholder()),
                    maxSize: {{ ($size = $getMaxSize()) ? "'{$size} KB'" : 'null' }},
                    minSize: {{ ($size = $getMinSize()) ? "'{$size} KB'" : 'null' }},
                    removeUploadedFileUsing: async (fileKey) => {
                        return await $wire.removeFormUploadedFile(@js($statePath), fileKey)
                    },
                    removeUploadedFileButtonPosition: @js($getRemoveUploadedFileButtonPosition()),
                    reorderUploadedFilesUsing: async (files) => {
                        return await $wire.reorderFormUploadedFiles(@js($statePath), files)
                    },
                    shouldAppendFiles: @js($shouldAppendFiles()),
                    shouldOrientImageFromExif: @js($shouldOrientImagesFromExif()),
                    shouldTransformImage: @js($imageCropAspectRatio || $imageResizeTargetHeight || $imageResizeTargetWidth),
                    state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
                    uploadButtonPosition: @js($getUploadButtonPosition()),
                    uploadProgressIndicatorPosition: @js($getUploadProgressIndicatorPosition()),
                    uploadUsing: (fileKey, file, success, error, progress) => {
                        $wire.upload(
                            `{{ $statePath }}.${fileKey}`,
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
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'filament-forms-file-upload-component flex',
                    match ($getAlignment()) {
                        'center' => 'justify-center',
                        'end' => 'justify-end',
                        'left' => 'justify-left',
                        'right' => 'justify-right',
                        'start', null => 'justify-start',
                    },
                ])
        }}
    >
        <div
            style="
                min-height: {{ $isAvatar ? '8em' : ($getPanelLayout() === 'compact' ? '2.625em' : '4.75em') }};
            "
            @class([
                'w-32' => $isAvatar,
                'w-full' => ! $isAvatar,
            ])
        >
            <input
                x-ref="input"
                {{
                    $getExtraInputAttributeBag()
                        ->merge([
                            'disabled' => $isDisabled(),
                            'dusk' => "filament.forms.{$statePath}",
                            'multiple' => $isMultiple(),
                            'type' => 'file',
                        ], escape: false)
                }}
            />
        </div>
    </div>
</x-dynamic-component>
