<div
    x-data="FileUploadFormComponent({
        state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        statePath: '{{ $getStatePath() }}',
        disk: '{{ $getDiskName() }}',
        uploadedFileUrl: {{ ($url = $getUploadedFileUrl()) ? "'{$url}'" : 'null' }},
        getUploadedFileUrlUsing: $wire.getUploadedFileUrl,
        uploadUsing: $wire.upload,
        removeTemporaryUploadedFileUsing: $wire.removeUpload,
        removeUploadedFileUsing: async function (statePath) {
            await $wire.set(statePath, null)
        },
        acceptedFileTypes: {{ json_encode($getAcceptedFileTypes()) }},
        imageCropAspectRatio: {{ ($aspectRatio = $getImageCropAspectRatio()) ? "'{$aspectRatio}'" : 'null' }},
        imagePreviewHeight: {{ ($height = $getImagePreviewHeight()) ? "'{$height}'" : 'null' }},
        imageResizeTargetHeight: {{ ($height = $getImageResizeTargetHeight()) ? "'{$height}'" : 'null' }},
        imageResizeTargetWidth: {{ ($width = $getImageResizeTargetWidth()) ? "'{$width}'" : 'null' }},
        loadingIndicatorPosition: '{{ $getLoadingIndicatorPosition() }}',
        panelAspectRatio: {{ ($aspectRatio = $getPanelAspectRatio()) ? "'{$aspectRatio}'" : 'null' }},
        panelLayout: {{ ($layout = $getPanelLayout()) ? "'{$layout}'" : 'null' }},
        placeholder: {{ ($placeholder = $getPlaceholder()) ? "'{$placeholder}'" : 'null' }},
        maxSize: {{ ($size = $getMaxSize()) ? "'{$size} KB'" : 'null' }},
        minSize: {{ ($size = $getMinSize()) ? "'{$size} KB'" : 'null' }},
        removeUploadedFilePosition: '{{ $getRemoveUploadedFileButtonPosition() }}',
        uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
        uploadProgressIndicatorPosition: '{{ $getUploadProgressIndicatorPosition() }}',
    })"
    wire:ignore
>
    <input
        x-ref="input"
        {{ $isDisabled() ? 'disabled' : '' }}
        id="{{ $getId() }}"
        type="file"
    />
</div>
