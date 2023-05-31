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
        $isDisabled = $isDisabled();
        $isCroppable = $isCroppable();
    @endphp

    <div
        x-ignore
        ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('file-upload', 'filament/forms') }}"
        x-data="fileUploadFormComponent({
            acceptedFileTypes: @js($getAcceptedFileTypes()),
            cropperEmptyFillColor: '{{ $getCropperEmptyFillColor() }}',
            cropperMode: {{ $getCropperMode() }},
            cropperViewportHeight: @js($getCropperViewportHeight()),
            cropperViewportWidth: @js($getCropperViewportWidth()),
            deleteUploadedFileUsing: async (fileKey) => {
                return await $wire.deleteUploadedFile(@js($statePath), fileKey)
            },
            disabled: @js($isDisabled),
            getUploadedFilesUsing: async () => {
                return await $wire.getFormUploadedFiles(@js($statePath))
            },
            imageCropAspectRatio: @js($imageCropAspectRatio),
            imagePreviewHeight: @js($getImagePreviewHeight()),
            imageResizeMode: @js($getImageResizeMode()),
            imageResizeTargetHeight: @js($imageResizeTargetHeight),
            imageResizeTargetWidth: @js($imageResizeTargetWidth),
            imageResizeUpscale: @js($getImageResizeUpscale()),
            isAvatar: @js($isAvatar),
            isCroppable: @js($isCroppable),
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
                $wire.upload(`{{ $statePath }}.${fileKey}`, file, () => {
                    success(fileKey)
                }, error, progress)
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
            style="min-height: {{ $isAvatar ? '8em' : ($getPanelLayout() === 'compact' ? '2.625em' : '4.75em') }}"
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
                            'disabled' => $isDisabled,
                            'dusk' => "filament.forms.{$statePath}",
                            'multiple' => $isMultiple(),
                            'type' => 'file',
                        ], escape: false)
                }}
            />
        </div>

        @if ($isCroppable && (! $isDisabled))
            @php
                $cropperButtonClasses = 'hover:bg-gray-600 dark:hover:bg-gray-800 hover:text-white focus:bg-gray-500 focus:ring-gray-500/50 flex-1 align-middle text-center font-medium outline-none transition-colors select-none focus:ring-2 border whitespace-no-wrap first-of-type:rounded-s-lg last-of-type:rounded-e-lg no-underline disabled:pointer-events-none disabled:opacity-70 filament-button-size-sm py-[calc(theme(spacing.2)-1px)] px-[calc(theme(spacing.[3.5])-1px)] text-sm shadow border-transparent';
            @endphp

            <div
                x-show="showCropper"
                x-cloak
                x-on:click.stop
                x-trap.noscroll="showCropper"
                class="fixed inset-0 h-screen w-screen p-2 sm:p-10 md:p-20 bg-gray-800 bg-opacity-75 z-50 isolate"
            >
                <div class="w-full h-full flex justify-center items-center isolate z-10">
                    <div class="mx-auto flex flex-col lg:flex-row overflow-hidden bg-white dark:bg-gray-800 dark:ring-gray-50/10 ring-1 ring-gray-900/10 rounded-xl">
                        <div class="flex-1 w-full lg:h-full overflow-auto p-4">
                            <div class="h-full w-full">
                                <img x-ref="cropper" src="" class="h-full w-auto"/>
                            </div>
                        </div>

                        <div class="w-full h-96 lg:h-full lg:max-w-xs overflow-auto bg-white dark:bg-gray-900/30 flex flex-col shadow-top lg:shadow-none z-[1]">
                            <div class="flex-1 overflow-hidden">
                                <div class="flex flex-col h-full overflow-y-auto pt-4">
                                    <div class="flex-1 overflow-auto px-4 pb-4">
                                        <div class="space-y-3">
                                            <div class="w-full space-y-2">
                                                <div
                                                    x-ref="preview"
                                                    class="w-[263px] h-[148px] overflow-hidden"
                                                ></div>
                                            </div>

                                            <div class="w-full">
                                                @foreach ([
                                                    [
                                                        'label' => 'X',
                                                        'name' => 'inputX',
                                                        'placeholder' => 'x',
                                                        'unit' => 'px',
                                                        'onEnter' => 'cropper.setData({...cropper.getData(true), x: +$el.value})',

                                                    ],
                                                    [
                                                        'label' => 'Y',
                                                        'name' => 'inputY',
                                                        'placeholder' => 'y',
                                                        'unit' => 'px',
                                                        'onEnter' => 'cropper.setData({...cropper.getData(true), y: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.cropper.actions.width.label'),
                                                        'name' => 'inputWidth',
                                                        'placeholder' => 'width',
                                                        'unit' => 'px',
                                                        'onEnter' => 'cropper.setData({...cropper.getData(true), width: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.cropper.actions.height.label'),
                                                        'name' => 'inputHeight',
                                                        'placeholder' => 'height',
                                                        'unit' => 'px',
                                                        'onEnter' => 'cropper.setData({...cropper.getData(true), height: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.cropper.actions.rotate.label'),
                                                        'name' => 'inputRotate',
                                                        'placeholder' => 'rotate',
                                                        'unit' => 'deg',
                                                        'onEnter' => 'cropper.rotateTo(+$el.value)',
                                                    ],
                                                ] as $input)
                                                    <div
                                                        class="relative flex items-stretch w-full input-group-sm border border-gray-300 dark:border-gray-600 rounded-md mb-1"
                                                        wire:key="@js($statePath . $input['name'])"
                                                    >
                                                        <label
                                                            for="@js($statePath . $input['name'])"
                                                            class="py-1 px-2 text-base font-normal leading-normal text-center bg-white dark:bg-gray-700 group-focus-within:text-primary-500 text-gray-400 rounded-s-md"
                                                        >
                                                            {{ $input['label'] }}
                                                        </label>

                                                        <input
                                                            x-on:keyup.enter.stop.prevent="{{ $input['onEnter'] }}"
                                                            x-on:blur="{{ $input['onEnter'] }}"
                                                            x-ref="{{ $input['name'] }}"
                                                            name="@js($statePath . $input['name'])"
                                                            placeholder="{{ $input['placeholder'] }}"
                                                            x-on:keydown.enter.prevent
                                                            type="text"
                                                            class="block appearance-none border-x-1 border-y-0 border-gray-300 dark:border-gray-600 w-full py-1 px-2 bg-white dark:bg-gray-700"
                                                        >

                                                        <span
                                                            class="py-1 px-2 text-base font-normal leading-normal text-center bg-gray-50 dark:bg-gray-700 group-focus-within:text-primary-500 text-gray-400 rounded-e-md">
                                                            {{ $input['unit'] }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @foreach ($getCropperActions() as $buttonGroups)
                                                <div class="flex items-stretch space-x-[2px] align-middle w-full">
                                                    @foreach ($buttonGroups as $button)
                                                        <button
                                                            type="button"
                                                            x-tooltip.raw="{{ $button['tooltip'] }}"
                                                            class="{{ $cropperButtonClasses }} bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-white"
                                                            x-on:click.stop.prevent="{{ $button['click'] }}"
                                                        >
                                                            {!! $button['icon'] !!}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endforeach

                                            @foreach (collect($getCropperAspectRatios())->chunk(5) as $chunk)
                                                <div class="flex items-stretch space-x-[2px] align-middle w-full">
                                                    @foreach ($chunk as $label => $ratio)
                                                        <button
                                                            type="button"
                                                            x-tooltip.raw="Set aspect ratio: {{ $label }}"
                                                            class="{{ $cropperButtonClasses }}"
                                                            x-bind:class="currentRatio === '{{ $label }}' ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-white'"
                                                            x-on:click.stop.prevent="currentRatio = '{{ $label }}'; cropper.setAspectRatio({{ $ratio }})"
                                                        >
                                                            {{ $label }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-center gap-3 py-3 px-4 border-t border-gray-300 dark:border-gray-800 dark:bg-black/10">
                                        <x-filament::button
                                            size="sm"
                                            color="secondary"
                                            x-on:click.stop.prevent="cropper.reset()"
                                        >
                                            {{ __('filament-forms::components.file_upload.cropper.actions.reset.label') }}
                                        </x-filament::button>

                                        <x-filament::button
                                            size="sm"
                                            color="warning"
                                            x-on:click.prevent="pond.imageEditEditor.oncancel"
                                        >
                                            {{ __('filament-forms::components.file_upload.cropper.actions.cancel.label') }}
                                        </x-filament::button>

                                        <x-filament::button
                                            size="sm"
                                            color="success"
                                            x-on:click.prevent="saveCropper"
                                        >
                                            {{ __('filament-forms::components.file_upload.cropper.actions.save.label') }}
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
