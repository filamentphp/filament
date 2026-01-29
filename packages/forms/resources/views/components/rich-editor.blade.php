@php
    $customBlocks = $getCustomBlocks();
    $extraAttributeBag = $getExtraAttributeBag();
    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $isDisabled = $isDisabled();
    $livewireKey = $getLivewireKey();
    $key = $getKey();
    $mergeTags = $getMergeTags();
    $statePath = $getStatePath();
    $mentions = $getMentionsForJs();
    $tools = $getTools();
    $toolbarButtons = $getToolbarButtons();
    $floatingToolbars = $getFloatingToolbars();
    $linkProtocols = $getLinkProtocols();
    $fileAttachmentsMaxSize = $getFileAttachmentsMaxSize();
    $fileAttachmentsAcceptedFileTypes = $getFileAttachmentsAcceptedFileTypes();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <x-filament::input.wrapper
        :valid="! $errors->has($statePath)"
        x-cloak
        :attributes="
            \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
                ->class(['fi-fo-rich-editor'])
        "
    >
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('rich-editor', 'filament/forms') }}"
            x-data="richEditorFormComponent({
                        acceptedFileTypes: @js($fileAttachmentsAcceptedFileTypes),
                        acceptedFileTypesValidationMessage: @js($fileAttachmentsAcceptedFileTypes ? __('filament-forms::components.rich_editor.file_attachments_accepted_file_types_message', ['values' => implode(', ', $fileAttachmentsAcceptedFileTypes)]) : null),
                        activePanel: @js($getActivePanel()),
                        canAttachFiles: @js($hasFileAttachments()),
                        deleteCustomBlockButtonIconHtml: @js(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Trash, alias: \Filament\Forms\View\FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_DELETE_BUTTON)->toHtml()),
                        editCustomBlockButtonIconHtml: @js(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::PencilSquare, alias: \Filament\Forms\View\FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_EDIT_BUTTON)->toHtml()),
                        extensions: @js($getTipTapJsExtensions()),
                        floatingToolbars: @js($floatingToolbars),
                        getMentionLabelsUsing: async (mentions) => {
                            return await $wire.callSchemaComponentMethod(
                                @js($key),
                                'getMentionLabelsForJs',
                                { mentions },
                            )
                        },
                        getMentionSearchResultsUsing: async (query, char) => {
                            return await $wire.callSchemaComponentMethod(
                                @js($key),
                                'getMentionSearchResultsForJs',
                                { search: query, char },
                            )
                        },
                        hasResizableImages: @js($hasResizableImages()),
                        isDisabled: @js($isDisabled),
                        isLiveDebounced: @js($isLiveDebounced()),
                        isLiveOnBlur: @js($isLiveOnBlur()),
                        key: @js($key),
                        linkProtocols: @js($linkProtocols),
                        liveDebounce: @js($getNormalizedLiveDebounce()),
                        livewireId: @js($this->getId()),
                        maxFileSize: @js($fileAttachmentsMaxSize),
                        maxFileSizeValidationMessage: @js($fileAttachmentsMaxSize ? trans_choice('filament-forms::components.rich_editor.file_attachments_max_size_message', $fileAttachmentsMaxSize, ['max' => $fileAttachmentsMaxSize]) : null),
                        mentions: @js($mentions),
                        mergeTags: @js($mergeTags),
                        noMergeTagSearchResultsMessage: @js($getNoMergeTagSearchResultsMessage()),
                        placeholder: @js($getPlaceholder()),
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                        statePath: @js($statePath),
                        textColors: @js($getTextColorsForJs()),
                        uploadingFileMessage: @js($getUploadingFileMessage()),
                    })"
            x-bind:class="{
                'fi-fo-rich-editor-uploading-file': isUploadingFile,
            }"
            wire:ignore
            wire:key="{{ $livewireKey }}.{{
                substr(md5(serialize([
                    $isDisabled,
                ])), 0, 64)
            }}"
        >
            @if ((! $isDisabled) && filled($toolbarButtons))
                <div class="fi-fo-rich-editor-toolbar">
                    @foreach ($toolbarButtons as $button => $buttonGroup)
                        <div class="fi-fo-rich-editor-toolbar-group">
                            @foreach ($buttonGroup as $button)
                                {{ $tools[$button] ?? throw new LogicException("Toolbar button [{$button}] cannot be found.") }}
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            <div
                x-show="isUploadingFile"
                x-cloak
                class="fi-fo-rich-editor-uploading-file-message"
            >
                {{ \Filament\Support\generate_loading_indicator_html() }}

                <span>
                    {{ $getUploadingFileMessage() }}
                </span>
            </div>

            <div
                x-show="! isUploadingFile && fileValidationMessage"
                x-cloak
                class="fi-fo-rich-editor-file-validation-message"
            >
                <span
                    x-text="fileValidationMessage"
                    x-show="! isUploadingFile && fileValidationMessage"
                ></span>
            </div>

            <div
                {{ $getExtraInputAttributeBag()->class(['fi-fo-rich-editor-main']) }}
            >
                <div class="fi-fo-rich-editor-content fi-prose" x-ref="editor">
                    @foreach ($floatingToolbars as $nodeName => $buttons)
                        <div
                            x-ref="floatingToolbar::{{ $nodeName }}"
                            class="fi-fo-rich-editor-floating-toolbar fi-not-prose"
                        >
                            @foreach ($buttons as $button)
                                {{ $tools[$button] }}
                            @endforeach
                        </div>
                    @endforeach
                </div>

                @if (! $isDisabled)
                    <div
                        x-show="isPanelActive()"
                        x-cloak
                        class="fi-fo-rich-editor-panels"
                    >
                        <div
                            x-show="isPanelActive('customBlocks')"
                            x-cloak
                            class="fi-fo-rich-editor-panel"
                        >
                            <div class="fi-fo-rich-editor-panel-header">
                                <p class="fi-fo-rich-editor-panel-heading">
                                    {{ __('filament-forms::components.rich_editor.tools.custom_blocks') }}
                                </p>

                                <div
                                    class="fi-fo-rich-editor-panel-close-btn-ctn"
                                >
                                    <button
                                        type="button"
                                        x-on:click="togglePanel()"
                                        class="fi-icon-btn"
                                    >
                                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::XMark, alias: \Filament\Forms\View\FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCKS_CLOSE_BUTTON) }}
                                    </button>
                                </div>
                            </div>

                            <div class="fi-fo-rich-editor-custom-blocks-list">
                                @foreach ($customBlocks as $block)
                                    @php
                                        $blockId = $block::getId();
                                    @endphp

                                    <button
                                        draggable="true"
                                        type="button"
                                        x-data="{ isLoading: false }"
                                        x-on:click="
                                            isLoading = true

                                            $wire.mountAction(
                                                'customBlock',
                                                { editorSelection, id: @js($blockId), mode: 'insert' },
                                                { schemaComponent: @js($key) },
                                            )
                                        "
                                        x-on:dragstart="$event.dataTransfer.setData('customBlock', @js($blockId))"
                                        x-on:open-modal.window="isLoading = false"
                                        x-on:run-rich-editor-commands.window="isLoading = false"
                                        class="fi-fo-rich-editor-custom-block-btn"
                                    >
                                        {{
                                            \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                                                'x-show' => 'isLoading',
                                            ])))
                                        }}

                                        {{ $block::getLabel() }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div
                            x-show="isPanelActive('mergeTags')"
                            x-cloak
                            class="fi-fo-rich-editor-panel"
                        >
                            <div class="fi-fo-rich-editor-panel-header">
                                <p class="fi-fo-rich-editor-panel-heading">
                                    {{ __('filament-forms::components.rich_editor.tools.merge_tags') }}
                                </p>

                                <div
                                    class="fi-fo-rich-editor-panel-close-btn-ctn"
                                >
                                    <button
                                        type="button"
                                        x-on:click="togglePanel()"
                                        class="fi-icon-btn"
                                    >
                                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::XMark, alias: \Filament\Forms\View\FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_MERGE_TAGS_CLOSE_BUTTON) }}
                                    </button>
                                </div>
                            </div>

                            <div class="fi-fo-rich-editor-merge-tags-list">
                                @foreach ($mergeTags as $tagId => $tagLabel)
                                    <button
                                        draggable="true"
                                        type="button"
                                        x-on:click="insertMergeTag(@js($tagId))"
                                        x-on:dragstart="$event.dataTransfer.setData('mergeTag', @js($tagId))"
                                        class="fi-fo-rich-editor-merge-tag-btn"
                                    >
                                        <span
                                            data-type="mergeTag"
                                            data-id="{{ $tagId }}"
                                        >
                                            {{ $tagLabel }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
