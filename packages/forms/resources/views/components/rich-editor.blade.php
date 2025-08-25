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
    $tools = $getTools();
    $toolbarButtons = $getToolbarButtons();
    $floatingToolbars = $getFloatingToolbars();
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
                        activePanel: @js($getActivePanel()),
                        deleteCustomBlockButtonIconHtml: @js(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Trash, alias: \Filament\Forms\View\FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_DELETE_BUTTON)->toHtml()),
                        editCustomBlockButtonIconHtml: @js(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::PencilSquare, alias: \Filament\Forms\View\FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_EDIT_BUTTON)->toHtml()),
                        extensions: @js($getTipTapJsExtensions()),
                        key: @js($key),
                        isDisabled: @js($isDisabled),
                        isLiveDebounced: @js($isLiveDebounced()),
                        isLiveOnBlur: @js($isLiveOnBlur()),
                        liveDebounce: @js($getNormalizedLiveDebounce()),
                        livewireId: @js($this->getId()),
                        mergeTags: @js($mergeTags),
                        noMergeTagSearchResultsMessage: @js($getNoMergeTagSearchResultsMessage()),
                        placeholder: @js($getPlaceholder()),
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                        statePath: @js($statePath),
                        uploadingFileMessage: @js($getUploadingFileMessage()),
                        floatingToolbars: @js($floatingToolbars),
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
                                {{ $tools[$button] ?? throw new Exception("Toolbar button [{$button}] cannot be found.") }}
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="fi-fo-rich-editor-main">
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
                                @foreach ($mergeTags as $tag)
                                    <button
                                        draggable="true"
                                        type="button"
                                        x-on:click="insertMergeTag(@js($tag))"
                                        x-on:dragstart="$event.dataTransfer.setData('mergeTag', @js($tag))"
                                        class="fi-fo-rich-editor-merge-tag-btn"
                                    >
                                        <span
                                            data-type="mergeTag"
                                            data-id="{{ $tag }}"
                                        >
                                            {{ $tag }}
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
