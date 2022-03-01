@once
    @push('scripts')
        @php
            $locale = strtolower(str_replace('_', '-', app()->getLocale()));
            
            $defaultLocaleData = ($placeholder = $getPlaceholder()) ? "{ labelIdle: '{$placeholder}' }" : '{}';
            
            if (! str_contains($locale, '-')) {
                $locale .= '-' . $locale;
            }
        @endphp
        
        <script type="module">
            import localeData from 'https://cdn.skypack.dev/filepond/locale/{{$locale}}.js'
            
            window.dispatchEvent(new CustomEvent('filepond-locale-updated', { detail: { ...localeData, ...{!! $defaultLocaleData !!} } }))
        </script>
    @endpush
@endonce

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
            canReorder: {{ $canReorder() ? 'true' : 'false' }},
            canPreview: {{ $canPreview() ? 'true' : 'false' }},
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
            reorderUploadedFilesUsing: async (files) => {
                return await $wire.reorderUploadedFiles('{{ $getStatePath() }}', files)
            },
            shouldAppendFiles: {{ $shouldAppendFiles() ? 'true' : 'false' }},
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
            uploadProgressIndicatorPosition: '{{ $getUploadProgressIndicatorPosition() }}',
            uploadUsing: async (fileKey, file, success, error, progress) => {
                $wire.upload(`{{ $getStatePath() }}.${fileKey}`, file, () => {
                    success(fileKey)
                }, error, progress)
            },
        })"
        x-on:filepond-locale-updated.window="pond.setOptions($event.detail)"
        wire:ignore
        style="min-height: {{ $isAvatar() ? '8em' : ($getPanelLayout() === 'compact' ? '2.625em' : '4.75em') }}"
        {{ $attributes->merge($getExtraAttributes())->class([
            'filament-forms-file-upload-component',
            'w-32 mx-auto' => $isAvatar(),
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
