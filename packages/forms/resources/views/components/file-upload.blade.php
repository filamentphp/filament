@once
    @push('scripts')
        @php
            if (\Illuminate\Support\Facades\Lang::has($localeString = 'forms::components.file_upload.filepond_locale')) {
                $locale = __($localeString);
            } else {
                $locale = strtolower(str_replace('_', '-', app()->getLocale()));

                if (! str_contains($locale, '-')) {
                    $locale .= '-' . $locale;
                }
            }

            $defaultLocaleData = ($placeholder = $getPlaceholder()) ? "{ labelIdle: '{$placeholder}' }" : '{}';
        @endphp

        <script type="module">
            import localeData from 'https://cdn.skypack.dev/filepond/locale/{{$locale}}.js'

            window.FilePond && window.FilePond.setOptions({ ...localeData, ...{!! $defaultLocaleData !!} })
        </script>
    @endpush
@endonce

<x-dynamic-component
    :component="$getFieldWrapperView()"
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
            getUploadedFileUrlsUsing: async () => {
                return await $wire.getUploadedFileUrls('{{ $getStatePath() }}')
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
        wire:ignore
        {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
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
            type="file"
            {{ $getExtraInputAttributeBag() }}
            dusk="filament.forms.{{ $getStatePath() }}"
        />
    </div>
</x-dynamic-component>
