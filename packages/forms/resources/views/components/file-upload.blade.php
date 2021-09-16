<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isAvatar() || $isMultiple() || $isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        @class([
            'w-32 mx-auto' => $isAvatar(),
        ])
        x-data="fileUploadFormComponent({
            acceptedFileTypes: {{ json_encode($getAcceptedFileTypes()) }},
            getUploadedFileUrlUsing: async () => {
                return await $wire.getUploadedFileUrl('{{ $getStatePath() }}')
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
            removeUploadedFileUsing: async (file = null) => {
                return await $wire.removeUploadedFile('{{ $getStatePath() }}', file)
            },
            removeUploadedFileButtonPosition: '{{ $getRemoveUploadedFileButtonPosition() }}',
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
            uploadProgressIndicatorPosition: '{{ $getUploadProgressIndicatorPosition() }}',
            uploadUsing: async (file, load, error, progress) => {
                return await $wire.upload('{{ $getStatePath() }}', file, load, error, progress)
            },
        })"
        wire:ignore
        {{ $attributes->merge($getExtraAttributes()) }}
    >
        <input
            x-ref="input"
            {{ $isDisabled() ? 'disabled' : '' }}
            {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
            type="file"
        />
    </div>
</x-forms::field-wrapper>
