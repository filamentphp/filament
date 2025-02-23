@php
    use Filament\Support\Facades\FilamentView;

    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $key = $getKey();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        @if (FilamentView::hasSpaMode())
            {{-- format-ignore-start --}}x-load="visible || event (x-modal-opened)"{{-- format-ignore-end --}}
        @else
            x-load
        @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('rich-editor', 'filament/forms') }}"
        x-data="richEditorFormComponent({
                    key: @js($key),
                    livewireId: @js($this->getId()),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                    statePath: @js($statePath),
                    uploadingFileMessage: @js($getUploadingFileMessage()),
                })"
        x-cloak
        x-bind:class="{
            'fi-fo-rich-editor-uploading-file': isUploadingFile,
        }"
        {{ $getExtraAttributeBag()->class(['fi-fo-rich-editor']) }}
    >
        <x-filament::input.wrapper :valid="! $errors->has($statePath)">
            @if (filled($getToolbarButtons()))
                <div class="fi-fo-rich-editor-toolbar">
                    @if ($hasToolbarButton(['bold', 'italic', 'underline', 'strike', 'link']))
                        <div class="fi-fo-rich-editor-toolbar-group">
                            @if ($hasToolbarButton('bold'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="bold"
                                    x-on:click="getEditor().chain().focus().toggleBold().run()"
                                    :title="__('filament-forms::components.rich_editor.toolbar_buttons.bold')"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bold, alias: 'forms:components.rich-editor.toolbar.bold') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('italic'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="italic"
                                    x-on:click="getEditor().chain().focus().toggleItalic().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.italic') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Italic, alias: 'forms:components.rich-editor.toolbar.italic') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('underline'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="underline"
                                    x-on:click="getEditor().chain().focus().toggleUnderline().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.underline') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Underline, alias: 'forms:components.rich-editor.toolbar.underline') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('strike'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="strike"
                                    x-on:click="getEditor().chain().focus().toggleStrike().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.strike') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Strikethrough, alias: 'forms:components.rich-editor.toolbar.strikethrough') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('subscript'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="subscript"
                                    x-on:click="getEditor().chain().focus().toggleSubscript().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.subscript') }}"
                                    tabindex="-1"
                                >
                                    {{
                                        \Filament\Support\generate_icon_html(new \Illuminate\Support\HtmlString(<<<'HTML'
                                        <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                        data-slot="icon"
                                        >
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 7l8 10m-8 0l8 -10" />
                                        <path d="M21 20h-4l3.5 -4a1.73 1.73 0 0 0 -3.5 -2" />
                                        </svg>
                                        HTML), alias: 'forms:components.rich-editor.toolbar.subscript')
                                    }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('superscript'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="superscript"
                                    x-on:click="getEditor().chain().focus().toggleSuperscript().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.superscript') }}"
                                    tabindex="-1"
                                >
                                    {{
                                        \Filament\Support\generate_icon_html(new \Illuminate\Support\HtmlString(<<<'HTML'
                                        <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                        data-slot="icon"
                                        >
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 7l8 10m-8 0l8 -10" />
                                        <path d="M21 11h-4l3.5 -4a1.73 1.73 0 0 0 -3.5 -2" />
                                        </svg>
                                        HTML), alias: 'forms:components.rich-editor.toolbar.superscript')
                                    }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('link'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="link"
                                    :x-on:click="$getAction('link')->getAlpineClickHandler()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.link') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Link, alias: 'forms:components.rich-editor.toolbar.link') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </div>
                    @endif

                    @if ($hasToolbarButton(['h1', 'h2', 'h3']))
                        <div class="fi-fo-rich-editor-toolbar-group">
                            @if ($hasToolbarButton('h1'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="heading"
                                    :active-options="['level' => 1]"
                                    x-on:click="getEditor().chain().focus().toggleHeading({ level: 1 }).run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h1') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::H1, alias: 'forms:components.rich-editor.toolbar.h1') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('h2'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="heading"
                                    :active-options="['level' => 2]"
                                    x-on:click="getEditor().chain().focus().toggleHeading({ level: 2 }).run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h2') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::H2, alias: 'forms:components.rich-editor.toolbar.h2') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('h3'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="heading"
                                    :active-options="['level' => 3]"
                                    x-on:click="getEditor().chain().focus().toggleHeading({ level: 3 }).run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h3') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::H3, alias: 'forms:components.rich-editor.toolbar.h3') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </div>
                    @endif

                    @if ($hasToolbarButton(['blockquote', 'codeBlock', 'bulletList', 'orderedList']))
                        <div class="fi-fo-rich-editor-toolbar-group">
                            @if ($hasToolbarButton('blockquote'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="blockquote"
                                    x-on:click="getEditor().chain().focus().toggleBlockquote().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.blockquote') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChatBubbleBottomCenterText, alias: 'forms:components.rich-editor.toolbar.blockquote') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('codeBlock'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="codeBlock"
                                    x-on:click="getEditor().chain().focus().toggleCodeBlock().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.code_block') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::CodeBracket, alias: 'forms:components.rich-editor.toolbar.code-block') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('bulletList'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="bulletList"
                                    x-on:click="getEditor().chain().focus().toggleBulletList().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.bullet_list') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ListBullet, alias: 'forms:components.rich-editor.toolbar.bullet-list') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('orderedList'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="orderedList"
                                    x-on:click="getEditor().chain().focus().toggleOrderedList().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.ordered_list') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::NumberedList, alias: 'forms:components.rich-editor.toolbar.ordered-list') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </div>
                    @endif

                    @if ($hasToolbarButton('attachFiles'))
                        <div class="fi-fo-rich-editor-toolbar-group">
                            <x-filament-forms::rich-editor.toolbar.button
                                type="image"
                                :x-on:click="$getAction('attachFiles')->getAlpineClickHandler()"
                                title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.attach_files') }}"
                                tabindex="-1"
                            >
                                {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Photo, alias: 'forms:components.rich-editor.toolbar.attach-files') }}
                            </x-filament-forms::rich-editor.toolbar.button>
                        </div>
                    @endif

                    @if ($hasToolbarButton(['undo', 'redo']))
                        <div class="fi-fo-rich-editor-toolbar-group">
                            @if ($hasToolbarButton('undo'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    x-on:click="getEditor().chain().focus().undo().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.undo') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ArrowUturnLeft, alias: 'forms:components.rich-editor.toolbar.undo') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('redo'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    x-on:click="getEditor().chain().focus().redo().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.redo') }}"
                                    tabindex="-1"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ArrowUturnRight, alias: 'forms:components.rich-editor.toolbar.redo') }}
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <div
                class="fi-fo-rich-editor-content"
                x-ref="editor"
                wire:ignore
            ></div>
        </x-filament::input.wrapper>
    </div>
</x-dynamic-component>
