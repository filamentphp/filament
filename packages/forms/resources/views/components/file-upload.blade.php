<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :label-sr-only="$isAvatar() || $isLabelHidden()"
>
    @php
        $imageCropAspectRatio = $getImageCropAspectRatio();
        $imageResizeTargetHeight = $getImageResizeTargetHeight();
        $imageResizeTargetWidth = $getImageResizeTargetWidth();
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
                return await $wire.getUploadedFiles(@js($statePath))
            },
            imageCropAspectRatio: @js($imageCropAspectRatio),
            imagePreviewHeight: @js($getImagePreviewHeight()),
            imageResizeMode: @js($getImageResizeMode()),
            imageResizeTargetHeight: @js($imageResizeTargetHeight),
            imageResizeTargetWidth: @js($imageResizeTargetWidth),
            imageResizeUpscale: @js($getImageResizeUpscale()),
            isAvatar: {{ $isAvatar() ? 'true' : 'false' }},
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
                return await $wire.removeUploadedFile(@js($statePath), fileKey)
            },
            removeUploadedFileButtonPosition: @js($getRemoveUploadedFileButtonPosition()),
            reorderUploadedFilesUsing: async (files) => {
                return await $wire.reorderUploadedFiles(@js($statePath), files)
            },
            shouldAppendFiles: @js($shouldAppendFiles()),
            shouldOrientImageFromExif: @js($shouldOrientImageFromExif()),
            shouldTransformImage: @js($imageCropAspectRatio || $imageResizeTargetHeight || $imageResizeTargetWidth),
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }},
            uploadButtonPosition: @js($getUploadButtonPosition()),
            uploadProgressIndicatorPosition: @js($getUploadProgressIndicatorPosition()),
            uploadUsing: (fileKey, file, success, error, progress) => {
                $wire.upload(`{{ $statePath }}.${fileKey}`, file, () => {
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
                        'dusk' => "filament.forms.{$statePath}",
                        'multiple' => $isMultiple(),
                        'type' => 'file',
                    ], escape: false)
            }}
        />
    </div>
</x-dynamic-component>
