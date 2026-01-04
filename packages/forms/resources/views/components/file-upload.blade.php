@php
    use Filament\Support\Enums\Alignment;

    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $automaticallyCropImagesAspectRatio = $getAutomaticallyCropImagesAspectRatio();
    $automaticallyResizeImagesHeight = $getAutomaticallyResizeImagesHeight();
    $automaticallyResizeImagesWidth = $getAutomaticallyResizeImagesWidth();
    $isAvatar = $isAvatar();
    $isMultiple = $isMultiple();
    $key = $getKey();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $hasImageEditor = $hasImageEditor();
    $isImageEditorExplicitlyEnabled = $isImageEditorExplicitlyEnabled();
    $hasCircleCropper = $hasCircleCropper();
    $livewireKey = $getLivewireKey();

    $alignment = $getAlignment() ?? Alignment::Start;

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    label-tag="div"
>
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('file-upload', 'filament/forms') }}"
        x-data="fileUploadFormComponent({
                    acceptedFileTypes: @js($getAcceptedFileTypes()),
                    automaticallyCropImagesAspectRatio: @js($automaticallyCropImagesAspectRatio),
                    automaticallyOpenImageEditorForAspectRatio: @js($getAutomaticallyOpenImageEditorForAspectRatio()),
                    automaticallyResizeImagesMode: @js($getAutomaticallyResizeImagesMode()),
                    automaticallyResizeImagesHeight: @js($automaticallyResizeImagesHeight),
                    automaticallyResizeImagesWidth: @js($automaticallyResizeImagesWidth),
                    cancelUploadUsing: (fileKey) => {
                        $wire.cancelUpload(`{{ $statePath }}.${fileKey}`)
                    },
                    canEditSvgs: @js($canEditSvgs()),
                    confirmSvgEditingMessage: @js(__('filament-forms::components.file_upload.editor.svg.messages.confirmation')),
                    deleteUploadedFileUsing: async (fileKey) => {
                        return await $wire.callSchemaComponentMethod(
                            @js($key),
                            'deleteUploadedFile',
                            { fileKey },
                        )
                    },
                    disabledSvgEditingMessage: @js(__('filament-forms::components.file_upload.editor.svg.messages.disabled')),
                    getUploadedFilesUsing: async () => {
                        return await $wire.callSchemaComponentMethod(
                            @js($key),
                            'getUploadedFiles',
                        )
                    },
                    hasCircleCropper: @js($hasCircleCropper),
                    hasImageEditor: @js($hasImageEditor),
                    imageEditorEmptyFillColor: @js($getImageEditorEmptyFillColor()),
                    imageEditorMode: @js($getImageEditorMode()),
                    imageEditorViewportHeight: @js($getImageEditorViewportHeight()),
                    imageEditorViewportWidth: @js($getImageEditorViewportWidth()),
                    imagePreviewHeight: @js($getImagePreviewHeight()),
                    isAvatar: @js($isAvatar),
                    isDeletable: @js($isDeletable()),
                    isDisabled: @js($isDisabled),
                    isDownloadable: @js($isDownloadable()),
                    isImageEditorExplicitlyEnabled: @js($isImageEditorExplicitlyEnabled),
                    isMultiple: @js($isMultiple),
                    isOpenable: @js($isOpenable()),
                    isPasteable: @js($isPasteable()),
                    isPreviewable: @js($isPreviewable()),
                    isReorderable: @js($isReorderable()),
                    isSvgEditingConfirmed: @js($isSvgEditingConfirmed()),
                    itemPanelAspectRatio: @js($getItemPanelAspectRatio()),
                    loadingIndicatorPosition: @js($getLoadingIndicatorPosition()),
                    locale: @js(app()->getLocale()),
                    maxFiles: @js($maxFiles = $getMaxFiles()),
                    maxFilesValidationMessage: @js($maxFiles ? trans_choice('validation.max.array', $maxFiles, ['attribute' => $getValidationAttribute(), 'max' => $maxFiles]) : null),
                    maxParallelUploads: @js($getMaxParallelUploads()),
                    maxSize: @js(($size = $getMaxSize()) ? "{$size}KB" : null),
                    mimeTypeMap: @js($getMimeTypeMap()),
                    minSize: @js(($size = $getMinSize()) ? "{$size}KB" : null),
                    panelAspectRatio: @js($getPanelAspectRatio()),
                    panelLayout: @js($getPanelLayout()),
                    placeholder: @js($getPlaceholder()),
                    removeUploadedFileButtonPosition: @js($getRemoveUploadedFileButtonPosition()),
                    removeUploadedFileUsing: async (fileKey) => {
                        return await $wire.callSchemaComponentMethod(
                            @js($key),
                            'removeUploadedFile',
                            { fileKey },
                        )
                    },
                    reorderUploadedFilesUsing: async (fileKeys) => {
                        return await $wire.callSchemaComponentMethod(
                            @js($key),
                            'reorderUploadedFiles',
                            { fileKeys },
                        )
                    },
                    shouldAppendFiles: @js($shouldAppendFiles()),
                    shouldAutomaticallyUpscaleImagesWhenResizing: @js($shouldAutomaticallyUpscaleImagesWhenResizing()),
                    shouldOrientImageFromExif: @js($shouldOrientImagesFromExif()),
                    shouldTransformImage: @js($automaticallyCropImagesAspectRatio || $automaticallyResizeImagesHeight || $automaticallyResizeImagesWidth),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    uploadButtonPosition: @js($getUploadButtonPosition()),
                    uploadingMessage: @js($getUploadingMessage()),
                    uploadProgressIndicatorPosition: @js($getUploadProgressIndicatorPosition()),
                    uploadUsing: (fileKey, file, success, error, progress) => {
                        $wire.upload(
                            `{{ $statePath }}.${fileKey}`,
                            file,
                            () => {
                                success(fileKey)
                            },
                            error,
                            (progressEvent) => {
                                progress(true, progressEvent.detail.progress, 100)
                            },
                        )
                    },
                })"
        wire:ignore
        wire:key="{{ $livewireKey }}.{{
            substr(md5(serialize([
                $isDisabled,
            ])), 0, 64)
        }}"
        {{
            $attributes
                ->merge([
                    'aria-labelledby' => "{$id}-label",
                    'id' => $id,
                    'role' => 'group',
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-fo-file-upload',
                    'fi-fo-file-upload-avatar' => $isAvatar,
                    ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : $alignment,
                ])
        }}
    >
        <div class="fi-fo-file-upload-input-ctn">
            <input
                x-ref="input"
                {{
                    $getExtraInputAttributeBag()
                        ->merge([
                            'aria-labelledby' => "{$id}-label",
                            'disabled' => $isDisabled,
                            'multiple' => $isMultiple,
                            'type' => 'file',
                        ], escape: false)
                }}
            />
        </div>

        <div
            x-show="error"
            x-text="error"
            x-cloak
            class="fi-fo-file-upload-error-message"
        ></div>

        @if ($hasImageEditor && (! $isDisabled))
            <div
                x-show="isEditorOpen"
                x-cloak
                x-on:click.stop=""
                x-trap.noscroll="isEditorOpen"
                x-on:keydown.escape.prevent.stop="closeEditor"
                @class([
                    'fi-fo-file-upload-editor',
                    'fi-fo-file-upload-editor-circle-cropper' => $hasCircleCropper,
                    'fi-fo-file-upload-editor-crop-only' => ! $isImageEditorExplicitlyEnabled,
                ])
            >
                <div
                    aria-hidden="true"
                    class="fi-fo-file-upload-editor-overlay"
                ></div>

                <div class="fi-fo-file-upload-editor-window">
                    <div class="fi-fo-file-upload-editor-image-ctn">
                        <img
                            x-ref="editor"
                            class="fi-fo-file-upload-editor-image"
                        />
                    </div>

                    <div class="fi-fo-file-upload-editor-control-panel">
                        @if ($isImageEditorExplicitlyEnabled)
                            <div
                                class="fi-fo-file-upload-editor-control-panel-main"
                            >
                                <div
                                    class="fi-fo-file-upload-editor-control-panel-group"
                                >
                                    @foreach ([
                                        [
                                            'label' => __('filament-forms::components.file_upload.editor.fields.x_position.label'),
                                            'ref' => 'xPositionInput',
                                            'unit' => __('filament-forms::components.file_upload.editor.fields.x_position.unit'),
                                            'alpineSaveHandler' => 'editor.setData({...editor.getData(true), x: +$el.value})',
                                        ],
                                        [
                                            'label' => __('filament-forms::components.file_upload.editor.fields.y_position.label'),
                                            'ref' => 'yPositionInput',
                                            'unit' => __('filament-forms::components.file_upload.editor.fields.y_position.unit'),
                                            'alpineSaveHandler' => 'editor.setData({...editor.getData(true), y: +$el.value})',
                                        ],
                                        [
                                            'label' => __('filament-forms::components.file_upload.editor.fields.width.label'),
                                            'ref' => 'widthInput',
                                            'unit' => __('filament-forms::components.file_upload.editor.fields.width.unit'),
                                            'alpineSaveHandler' => 'editor.setData({...editor.getData(true), width: +$el.value})',
                                        ],
                                        [
                                            'label' => __('filament-forms::components.file_upload.editor.fields.height.label'),
                                            'ref' => 'heightInput',
                                            'unit' => __('filament-forms::components.file_upload.editor.fields.height.unit'),
                                            'alpineSaveHandler' => 'editor.setData({...editor.getData(true), height: +$el.value})',
                                        ],
                                        [
                                            'label' => __('filament-forms::components.file_upload.editor.fields.rotation.label'),
                                            'ref' => 'rotationInput',
                                            'unit' => __('filament-forms::components.file_upload.editor.fields.rotation.unit'),
                                            'alpineSaveHandler' => 'editor.rotateTo(+$el.value)',
                                        ],
                                    ] as $input)
                                        <label>
                                            <x-filament::input.wrapper>
                                                <x-slot name="prefix">
                                                    {{ $input['label'] }}
                                                </x-slot>

                                                <input
                                                    x-on:keyup.enter.prevent.stop="editor && {!! $input['alpineSaveHandler'] !!}"
                                                    x-on:blur="editor && {!! $input['alpineSaveHandler'] !!}"
                                                    x-ref="{{ $input['ref'] }}"
                                                    x-on:keydown.enter.prevent
                                                    type="text"
                                                    class="fi-input"
                                                />

                                                <x-slot name="suffix">
                                                    {{ $input['unit'] }}
                                                </x-slot>
                                            </x-filament::input.wrapper>
                                        </label>
                                    @endforeach
                                </div>

                                <div
                                    class="fi-fo-file-upload-editor-control-panel-group"
                                >
                                    @foreach ($getImageEditorActions() as $groupedActions)
                                        <div class="fi-btn-group">
                                            @foreach ($groupedActions as $action)
                                                <button
                                                    aria-label="{{ $action['label'] }}"
                                                    type="button"
                                                    x-on:click.prevent.stop="{{ $action['alpineClickHandler'] }}"
                                                    x-tooltip="{ content: @js($action['label']), theme: $store.theme }"
                                                    class="fi-btn"
                                                >
                                                    {{ $action['iconHtml'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                @if (count($aspectRatios = $getImageEditorAspectRatioOptionsForJs()))
                                    <div
                                        class="fi-fo-file-upload-editor-control-panel-group"
                                    >
                                        <div
                                            class="fi-fo-file-upload-editor-control-panel-group-title"
                                        >
                                            {{ __('filament-forms::components.file_upload.editor.aspect_ratios.label') }}
                                        </div>

                                        @foreach (collect($aspectRatios)->chunk(5) as $ratiosChunk)
                                            <div class="fi-btn-group">
                                                @foreach ($ratiosChunk as $label => $ratio)
                                                    <button
                                                        type="button"
                                                        x-on:click.prevent.stop="
                                                            currentRatio = @js($label) {!! ';' !!}
                                                            editor.setAspectRatio(@js($ratio))
                                                        "
                                                        x-tooltip="{ content: @js(__('filament-forms::components.file_upload.editor.actions.set_aspect_ratio.label', ['ratio' => $label])), theme: $store.theme }"
                                                        x-bind:class="{ 'fi-active': currentRatio === @js($label) }"
                                                        class="fi-btn"
                                                    >
                                                        {{ $label }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div
                            class="fi-fo-file-upload-editor-control-panel-footer"
                        >
                            @if ($isImageEditorExplicitlyEnabled)
                                <button
                                    type="button"
                                    x-on:click.prevent="pond.imageEditEditor.oncancel"
                                    class="fi-btn"
                                >
                                    {{ __('filament-forms::components.file_upload.editor.actions.cancel.label') }}
                                </button>

                                <button
                                    type="button"
                                    x-on:click.prevent.stop="editor.reset()"
                                    {{
                                        (new \Illuminate\View\ComponentAttributeBag)
                                            ->color(\Filament\Support\View\Components\ButtonComponent::class, 'danger')
                                            ->class(['fi-btn fi-fo-file-upload-editor-control-panel-reset-action'])
                                    }}
                                >
                                    {{ __('filament-forms::components.file_upload.editor.actions.reset.label') }}
                                </button>

                                <button
                                    type="button"
                                    x-on:click.prevent="saveEditor"
                                    {{
                                        (new \Illuminate\View\ComponentAttributeBag)
                                            ->color(\Filament\Support\View\Components\ButtonComponent::class, 'success')
                                            ->class(['fi-btn'])
                                    }}
                                >
                                    {{ __('filament-forms::components.file_upload.editor.actions.save.label') }}
                                </button>
                            @else
                                <button
                                    type="button"
                                    x-on:click.prevent="saveEditor"
                                    {{
                                        (new \Illuminate\View\ComponentAttributeBag)
                                            ->color(\Filament\Support\View\Components\ButtonComponent::class, 'success')
                                            ->class(['fi-btn'])
                                    }}
                                >
                                    {{ __('filament-forms::components.file_upload.editor.actions.save.label') }}
                                </button>

                                <button
                                    type="button"
                                    x-on:click.prevent="pond.imageEditEditor.oncancel"
                                    class="fi-btn"
                                >
                                    {{ __('filament-forms::components.file_upload.editor.actions.cancel.label') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-dynamic-component>
