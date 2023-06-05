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

            <div
                x-show="showCropper"
                x-cloak
                x-on:click.stop
                x-trap.noscroll="showCropper"
                x-on:keydown.escape.window="cancelCropper"
                class="fixed inset-0 h-screen w-screen p-2 sm:p-10 md:p-20 z-50 isolate"
            >
                <div
                    x-on:click="cancelCropper"
                    aria-hidden="true"
                    class="filament-modal-close-overlay fixed inset-0 w-full h-full bg-black/50 cursor-pointer"
                    style="will-change: transform;"
                ></div>
                <div class="w-full h-full flex justify-center items-center isolate z-10">
                    <div class="mx-auto h-full w-full flex flex-col lg:flex-row overflow-hidden bg-white dark:bg-gray-800 dark:ring-gray-50/10 ring-1 ring-gray-900/10 rounded-xl">
                        <div class="flex-1 w-full lg:h-full overflow-auto p-4">
                            <div class="h-full w-full">
                                <img x-ref="cropper" src="" class="h-full w-auto"/>
                            </div>
                        </div>

                        <div class="w-full h-96 lg:h-full lg:max-w-xs overflow-auto bg-gray-50 dark:bg-gray-900/30 flex flex-col shadow-top lg:shadow-none z-[1]">
                            <div class="flex-1 overflow-hidden">
                                <div class="flex flex-col h-full overflow-y-auto">
                                    <div class="flex-1 overflow-auto">
                                        <div class="space-y-3 p-4">

                                            <div class="w-full space-y-2">
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
                                                    <label class="flex items-center w-full text-sm border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800">
                                                        <span class="w-20 flex-shrink-0 self-stretch flex items-center justify-center px-2 border-e border-gray-300 dark:border-gray-700">{{ $input['label'] }}</span>
                                                        <input
                                                            @class([
                                                                "text-sm block w-full transition duration-75 border-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500"
                                                            ])
                                                            x-on:keyup.enter.stop.prevent="{{ $input['onEnter'] }}"
                                                            x-on:blur="{{ $input['onEnter'] }}"
                                                            x-ref="{{ $input['name'] }}"
                                                            name="@js($statePath . $input['name'])"
                                                            placeholder="{{ $input['placeholder'] }}"
                                                            x-on:keydown.enter.prevent
                                                            type="text"
                                                        />
                                                        <span class="w-16 self-stretch flex items-center justify-center px-2 border-s border-gray-300 dark:border-gray-700">{{ $input['unit'] }}</span>
                                                    </label>

                                                @endforeach
                                            </div>

                                            @foreach ($getCropperActions() as $buttonGroups)
                                                <div class="flex items-center w-full isolate rounded-lg shadow">
                                                    @foreach ($buttonGroups as $button)
                                                        <x-filament::button
                                                            type="button"
                                                            x-tooltip.raw="{{ $button['tooltip'] }}"
                                                            x-on:click.stop.prevent="{{ $button['click'] }}"
                                                            color="gray"
                                                            @class([
                                                                '!rounded-none !shadow-none flex-1 focus:z-10 active:z-10',
                                                                '!rounded-l-lg' => $loop->first,
                                                                '!rounded-r-lg' => $loop->last,
                                                                '-ml-px' => ! $loop->first,
                                                            ])
                                                        >
                                                            {!! $button['icon'] !!}
                                                        </x-filament::button>
                                                    @endforeach
                                                </div>
                                            @endforeach

                                            @foreach (collect($getCropperAspectRatios())->chunk(5) as $chunk)
                                                <div class="flex items-center w-full isolate rounded-lg shadow">
                                                    @foreach ($chunk as $label => $ratio)
                                                        <x-filament::button
                                                            type="button"
                                                            x-tooltip.raw="Set aspect ratio: {{ $label }}"
                                                            x-on:click.stop.prevent="currentRatio = '{{ $label }}'; cropper.setAspectRatio({{ $ratio }})"
                                                            color="gray"
                                                            x-bind:class="{'!bg-gray-50 dark:!bg-gray-700': currentRatio === '{{ $label }}'}"
                                                            @class([
                                                                '!rounded-none !shadow-none flex-1 focus:z-10 active:z-10',
                                                                '!rounded-l-lg !rounded-r-none' => $loop->first,
                                                                '!rounded-r-lg !rounded-l-none' => $loop->last,
                                                                '-ml-px' => ! $loop->first,
                                                            ])
                                                        >
                                                            {{ $label }}
                                                        </x-filament::button>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 py-3 px-4 border-t border-gray-200 dark:border-gray-800 bg-gray-200 dark:bg-black/10">
                                        <x-filament::button
                                            size="sm"
                                            color="warning"
                                            x-on:click.stop.prevent="cropper.reset()"
                                        >
                                            {{ __('filament-forms::components.file_upload.cropper.actions.reset.label') }}
                                        </x-filament::button>

                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            x-on:click.prevent="pond.imageEditEditor.oncancel"
                                        >
                                            {{ __('filament-forms::components.file_upload.cropper.actions.cancel.label') }}
                                        </x-filament::button>

                                        <x-filament::button
                                            size="sm"
                                            color="success"
                                            x-on:click.prevent="saveCropper"
                                            class="ml-auto"
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
