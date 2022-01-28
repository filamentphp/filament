<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isAvatar() || $isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="fileUploadFormComponent({
            acceptedFileTypes: {{ json_encode($getAcceptedFileTypes()) }},
            deleteUploadedFileUsing: async (fileKey) => {
                return await $wire.deleteUploadedFile('{{ $getStatePath() }}', fileKey)
            },
            getUploadedFileUrlUsing: async (fileKey) => {
                return await $wire.getUploadedFileUrl('{{ $getStatePath() }}', fileKey)
            },
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
            removeUploadedFileUsing: async (fileKey) => {
                return await $wire.removeUploadedFile('{{ $getStatePath() }}', fileKey)
            },
            removeUploadedFileButtonPosition: '{{ $getRemoveUploadedFileButtonPosition() }}',
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
            uploadProgressIndicatorPosition: '{{ $getUploadProgressIndicatorPosition() }}',
            uploadUsing: async (fileKey, file, success, error, progress) => {
                $wire.upload(`{{ $getStatePath() }}.${fileKey}`, file, () => {
                    success(fileKey)
                }, error, progress)
            },
        })"
        wire:ignore
        {{ $attributes->merge($getExtraAttributes())->class([
            'w-32 mx-auto' => $isAvatar(),
            'filament-forms-file-upload-component'
        ]) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        <input
            x-ref="input"
            {{ $isDisabled() ? 'disabled' : '' }}
            {{ $isMultiple() ? 'multiple' : '' }}
            {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
            type="file"
            {{ $getExtraInputAttributeBag() }}
        />
    </div>
</x-forms::field-wrapper>
