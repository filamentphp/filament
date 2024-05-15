@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Facades\FilamentView;

    $imageCropAspectRatio = $getImageCropAspectRatio();
    $imageResizeTargetHeight = $getImageResizeTargetHeight();
    $imageResizeTargetWidth = $getImageResizeTargetWidth();
    $isAvatar = $isAvatar();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $hasImageEditor = $hasImageEditor();
    $hasCircleCropper = $hasCircleCropper();

    $alignment = $getAlignment() ?? Alignment::Start;

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :label-sr-only="$isLabelHidden()"
>
    <div
        @if (FilamentView::hasSpaMode())
            ax-load="visible"
        @else
            ax-load
        @endif
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('file-upload', 'filament/forms') }}"
        x-data="fileUploadFormComponent({
                    acceptedFileTypes: @js($getAcceptedFileTypes()),
                    imageEditorEmptyFillColor: @js($getImageEditorEmptyFillColor()),
                    imageEditorMode: @js($getImageEditorMode()),
                    imageEditorViewportHeight: @js($getImageEditorViewportHeight()),
                    imageEditorViewportWidth: @js($getImageEditorViewportWidth()),
                    deleteUploadedFileUsing: async (fileKey) => {
                        return await $wire.deleteUploadedFile(@js($statePath), fileKey)
                    },
                    getUploadedFilesUsing: async () => {
                        return await $wire.getFormUploadedFiles(@js($statePath))
                    },
                    hasImageEditor: @js($hasImageEditor),
                    hasCircleCropper: @js($hasCircleCropper),
                    canEditSvgs: @js($canEditSvgs()),
                    isSvgEditingConfirmed: @js($isSvgEditingConfirmed()),
                    confirmSvgEditingMessage: @js(__('filament-forms::components.file_upload.editor.svg.messages.confirmation')),
                    disabledSvgEditingMessage: @js(__('filament-forms::components.file_upload.editor.svg.messages.disabled')),
                    imageCropAspectRatio: @js($imageCropAspectRatio),
                    imagePreviewHeight: @js($getImagePreviewHeight()),
                    imageResizeMode: @js($getImageResizeMode()),
                    imageResizeTargetHeight: @js($imageResizeTargetHeight),
                    imageResizeTargetWidth: @js($imageResizeTargetWidth),
                    imageResizeUpscale: @js($getImageResizeUpscale()),
                    isAvatar: @js($isAvatar),
                    isDeletable: @js($isDeletable()),
                    isDisabled: @js($isDisabled),
                    isDownloadable: @js($isDownloadable()),
                    isMultiple: @js($isMultiple()),
                    isOpenable: @js($isOpenable()),
                    isPreviewable: @js($isPreviewable()),
                    isReorderable: @js($isReorderable()),
                    itemPanelAspectRatio: @js($getItemPanelAspectRatio()),
                    loadingIndicatorPosition: @js($getLoadingIndicatorPosition()),
                    locale: @js(app()->getLocale()),
                    panelAspectRatio: @js($getPanelAspectRatio()),
                    panelLayout: @js($getPanelLayout()),
                    placeholder: @js($getPlaceholder()),
                    maxFiles: @js($getMaxFiles()),
                    maxSize: @js(($size = $getMaxSize()) ? "{$size}KB" : null),
                    minSize: @js(($size = $getMinSize()) ? "{$size}KB" : null),
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
        x-ignore
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-fo-file-upload flex [&_.filepond--root]:font-sans',
                    match ($alignment) {
                        Alignment::Start => 'justify-start',
                        Alignment::Center => 'justify-center',
                        Alignment::End => 'justify-end',
                        Alignment::Left => 'justify-left',
                        Alignment::Right => 'justify-right',
                        Alignment::Between, Alignment::Justify => 'justify-between',
                        default => $alignment,
                    },
                ])
        }}
    >
        <div
            @class([
                'h-full',
                'w-32' => $isAvatar,
                'w-full' => ! $isAvatar,
            ])
        >
            <input
                x-ref="input"
                {{
                    $getExtraInputAttributeBag()
                        ->merge([
                            'disabled' => $isDisabled,
                            'multiple' => $isMultiple(),
                            'type' => 'file',
                        ], escape: false)
                }}
            />
        </div>

        @if ($hasImageEditor && (! $isDisabled))
            <div
                x-show="isEditorOpen"
                x-cloak
                x-on:click.stop=""
                x-trap.noscroll="isEditorOpen"
                x-on:keydown.escape.window="closeEditor"
                @class([
                    'fixed inset-0 isolate z-50 h-[100dvh] w-screen p-2 sm:p-10 md:p-20',
                    'fi-fo-file-upload-circle-cropper' => $hasCircleCropper,
                ])
            >
                <div
                    aria-hidden="true"
                    class="fixed inset-0 h-full w-full cursor-pointer bg-black/50"
                    style="will-change: transform"
                ></div>

                <div
                    class="isolate z-10 flex h-full w-full items-center justify-center"
                >
                    <div
                        class="mx-auto flex h-full w-full flex-col overflow-hidden rounded-xl bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10 lg:flex-row"
                    >
                        <div class="w-full flex-1 overflow-auto p-4 lg:h-full">
                            <div class="h-full w-full">
                                <img x-ref="editor" class="h-full w-auto" />
                            </div>
                        </div>

                        <div
                            class="shadow-top z-[1] flex h-96 w-full flex-col overflow-auto bg-gray-50 dark:bg-gray-900/30 lg:h-full lg:max-w-xs lg:shadow-none"
                        >
                            <div class="flex-1 overflow-hidden">
                                <div
                                    class="flex h-full flex-col overflow-y-auto"
                                >
                                    <div class="flex-1 overflow-auto">
                                        <div class="space-y-6 p-4">
                                            <div class="w-full space-y-3">
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
                                                    <label
                                                        class="flex w-full items-center rounded-lg border border-gray-300 bg-gray-100 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-800"
                                                    >
                                                        <span
                                                            class="flex w-20 shrink-0 items-center justify-center self-stretch border-e border-gray-300 px-2 dark:border-gray-700"
                                                        >
                                                            {{ $input['label'] }}
                                                        </span>

                                                        <input
                                                            @class([
                                                                'block w-full border-none text-sm transition duration-75 focus-visible:border-primary-500 focus-visible:ring-1 focus-visible:ring-inset focus-visible:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus-visible:border-primary-500',
                                                            ])
                                                            x-on:keyup.enter.stop.prevent="{{ $input['alpineSaveHandler'] }}"
                                                            x-on:blur="{{ $input['alpineSaveHandler'] }}"
                                                            x-ref="{{ $input['ref'] }}"
                                                            x-on:keydown.enter.prevent
                                                            type="text"
                                                        />

                                                        <span
                                                            class="flex w-16 items-center justify-center self-stretch border-s border-gray-300 px-2 dark:border-gray-700"
                                                        >
                                                            {{ $input['unit'] }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>

                                            <div class="space-y-3">
                                                @foreach ($getImageEditorActions(iconSizeClasses: 'h-5 w-5 mx-auto') as $groupedActions)
                                                    <x-filament::button.group
                                                        class="w-full"
                                                    >
                                                        @foreach ($groupedActions as $action)
                                                            <x-filament::button
                                                                color="gray"
                                                                grouped
                                                                :icon="new \Illuminate\Support\HtmlString($action['iconHtml'])"
                                                                label-sr-only
                                                                x-on:click.stop.prevent="{{ $action['alpineClickHandler'] }}"
                                                                :x-tooltip="'{ content: ' . \Illuminate\Support\Js::from($action['label']) . ', theme: $store.theme }'"
                                                            >
                                                                {{ $action['label'] }}
                                                            </x-filament::button>
                                                        @endforeach
                                                    </x-filament::button.group>
                                                @endforeach
                                            </div>

                                            @if (count($aspectRatios = $getImageEditorAspectRatiosForJs()))
                                                <div class="space-y-3">
                                                    <div
                                                        class="text-xs text-gray-950 dark:text-white"
                                                    >
                                                        {{ __('filament-forms::components.file_upload.editor.aspect_ratios.label') }}
                                                    </div>

                                                    @foreach (collect($aspectRatios)->chunk(5) as $ratiosChunk)
                                                        <x-filament::button.group
                                                            class="w-full"
                                                        >
                                                            @foreach ($ratiosChunk as $label => $ratio)
                                                                <x-filament::button
                                                                    :x-tooltip="'{ content: ' . \Illuminate\Support\Js::from(__('filament-forms::components.file_upload.editor.actions.set_aspect_ratio.label', ['ratio' => $label])) . ', theme: $store.theme }'"
                                                                    x-on:click.stop.prevent="currentRatio = '{{ $label }}'; editor.setAspectRatio({{ $ratio }})"
                                                                    color="gray"
                                                                    x-bind:class="{'!bg-gray-50 dark:!bg-gray-700': currentRatio === '{{ $label }}'}"
                                                                    grouped
                                                                >
                                                                    {{ $label }}
                                                                </x-filament::button>
                                                            @endforeach
                                                        </x-filament::button.group>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center gap-3 px-4 py-3"
                                    >
                                        <x-filament::button
                                            color="gray"
                                            x-on:click.prevent="pond.imageEditEditor.oncancel"
                                        >
                                            {{ __('filament-forms::components.file_upload.editor.actions.cancel.label') }}
                                        </x-filament::button>

                                        <x-filament::button
                                            color="warning"
                                            x-on:click.stop.prevent="editor.reset()"
                                            class="ml-auto"
                                        >
                                            {{ __('filament-forms::components.file_upload.editor.actions.reset.label') }}
                                        </x-filament::button>

                                        <x-filament::button
                                            color="success"
                                            x-on:click.prevent="saveEditor"
                                        >
                                            {{ __('filament-forms::components.file_upload.editor.actions.save.label') }}
                                        </x-filament::button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-dynamic-component>
