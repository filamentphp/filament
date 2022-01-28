<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="markdownEditorFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            tab: '{{ $isDisabled() ? 'preview' : 'edit' }}',
        })"
        x-cloak
        wire:ignore
        {{ $attributes->merge($getExtraAttributes())->class('filament-forms-markdown-editor-component') }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        <div class="space-y-2">
            @unless ($isDisabled())
                <div class="flex justify-between space-x-4 overflow-x-auto items-stretch h-8">
                    <markdown-toolbar
                        for="{{ $getId() }}"
                        x-bind:class="{ 'pointer-events-none opacity-75': tab === 'preview' }"
                        class="flex items-stretch space-x-4 rtl:space-x-reverse focus:outline-none"
                    >
                        @if ($hasToolbarButton(['bold', 'italic', 'strike']))
                            <div class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('bold'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.bold') }}"
                                    >
                                        <md-bold>
                                            <svg class="w-4" viewBox="0 0 16 16" version="1.1" aria-hidden="true"><path fill-rule="evenodd" d="M4 2a1 1 0 00-1 1v10a1 1 0 001 1h5.5a3.5 3.5 0 001.852-6.47A3.5 3.5 0 008.5 2H4zm4.5 5a1.5 1.5 0 100-3H5v3h3.5zM5 9v3h4.5a1.5 1.5 0 000-3H5z"></path></svg>
                                        </md-bold>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('italic'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.italic') }}"
                                    >
                                        <md-italic>
                                            <svg class="w-4" height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M6 2.75A.75.75 0 016.75 2h6.5a.75.75 0 010 1.5h-2.505l-3.858 9H9.25a.75.75 0 010 1.5h-6.5a.75.75 0 010-1.5h2.505l3.858-9H6.75A.75.75 0 016 2.75z"></path></svg>
                                        </md-italic>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('strike'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.strike') }}"
                                    >
                                        <md-strikethrough>
                                            <svg class="w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.581 3.25c-2.036 0-2.778 1.082-2.778 1.786 0 .055.002.107.006.157a.75.75 0 01-1.496.114 3.56 3.56 0 01-.01-.271c0-1.832 1.75-3.286 4.278-3.286 1.418 0 2.721.58 3.514 1.093a.75.75 0 11-.814 1.26c-.64-.414-1.662-.853-2.7-.853zm3.474 5.25h3.195a.75.75 0 000-1.5H1.75a.75.75 0 000 1.5h6.018c.835.187 1.503.464 1.951.81.439.34.647.725.647 1.197 0 .428-.159.895-.594 1.267-.444.38-1.254.726-2.676.726-1.373 0-2.38-.493-2.86-.956a.75.75 0 00-1.042 1.079C3.992 13.393 5.39 14 7.096 14c1.652 0 2.852-.403 3.65-1.085a3.134 3.134 0 001.12-2.408 2.85 2.85 0 00-.811-2.007z"></path></svg>
                                        </md-strikethrough>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton(['link', 'attachFiles', 'codeBlock']))
                            <div class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('link'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.link') }}"
                                    >
                                        <md-link class="w-full h-full">
                                            <x-heroicon-o-link class="w-4" />
                                        </md-link>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('attachFiles'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.attach_files') }}"
                                    >
                                        <md-image class="w-full h-full" x-ref="imageTrigger">
                                            <x-heroicon-o-photograph class="w-4" />
                                        </md-image>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('codeBlock'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.code_block') }}"
                                    >
                                        <md-code class="w-full h-full">
                                            <x-heroicon-o-code class="w-4" />
                                        </md-code>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton(['bulletList', 'orderedList']))
                            <div class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('bulletList'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.bullet_list') }}"
                                    >
                                        <md-unordered-list class="w-full h-full">
                                            <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2 4a1 1 0 100-2 1 1 0 000 2zm3.75-1.5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zM3 8a1 1 0 11-2 0 1 1 0 012 0zm-1 6a1 1 0 100-2 1 1 0 000 2z"></path></svg>
                                        </md-unordered-list>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('orderedList'))
                                    <x-forms::markdown-editor.toolbar-button
                                        title="{{ __('forms::components.markdown_editor.toolbar_buttons.ordered_list') }}"
                                    >
                                        <md-ordered-list class="w-full h-full">
                                            <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2.003 2.5a.5.5 0 00-.723-.447l-1.003.5a.5.5 0 00.446.895l.28-.14V6H.5a.5.5 0 000 1h2.006a.5.5 0 100-1h-.503V2.5zM5 3.25a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 3.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 8.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5a.75.75 0 01-.75-.75zM.924 10.32l.003-.004a.851.851 0 01.144-.153A.66.66 0 011.5 10c.195 0 .306.068.374.146a.57.57 0 01.128.376c0 .453-.269.682-.8 1.078l-.035.025C.692 11.98 0 12.495 0 13.5a.5.5 0 00.5.5h2.003a.5.5 0 000-1H1.146c.132-.197.351-.372.654-.597l.047-.035c.47-.35 1.156-.858 1.156-1.845 0-.365-.118-.744-.377-1.038-.268-.303-.658-.484-1.126-.484-.48 0-.84.202-1.068.392a1.858 1.858 0 00-.348.384l-.007.011-.002.004-.001.002-.001.001a.5.5 0 00.851.525zM.5 10.055l-.427-.26.427.26z"></path></svg>
                                        </md-ordered-list>
                                    </x-forms::markdown-editor.toolbar-button>
                                @endif
                            </div>
                        @endif
                    </markdown-toolbar>

                    @if ($hasToolbarButton(['edit', 'preview']) && ! $isDisabled())
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            @if ($hasToolbarButton('edit'))
                                <button
                                    x-on:click.prevent="tab = 'edit'"
                                    x-bind:class="{ 'text-gray-400': tab !== 'edit' }"
                                    class="text-sm hover:underline"
                                >
                                    {{ __('forms::components.markdown_editor.toolbar_buttons.edit') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('preview'))
                                <button
                                    x-on:click.prevent="tab = 'preview'"
                                    x-bind:class="{ 'text-gray-400': tab !== 'preview' }"
                                    class="text-sm hover:underline"
                                >
                                    {{ __('forms::components.markdown_editor.toolbar_buttons.preview') }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endunless

            <div x-show="tab === 'edit'" class="relative w-full h-full" style="min-height: 150px;">
                <file-attachment directory>
                    <textarea
                        {!! $isAutofocused() ? 'autofocus' : null !!}
                        id="{{ $getId() }}"
                        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                        {!! $isRequired() ? 'required' : null !!}
                        x-model="state"
                        x-on:keyup.enter="checkForAutoInsertion"
                        x-on:file-attachment-accepted.window="
                            attachment = $event.detail?.attachments?.[0]

                            if (! attachment || ! attachment.file) return

                            $wire.upload(`componentFileAttachments.{{ $getStatePath() }}`, attachment.file, () => {
                                $wire.getComponentFileAttachmentUrl('{{ $getStatePath() }}').then((url) => {
                                    if (! url) {
                                        return
                                    }

                                    $refs.imageTrigger.click()

                                    const urlStart = $refs.textarea.selectionStart + 2
                                    const urlEnd = urlStart + 3

                                    state = [
                                        $refs.textarea.value.substring(0, urlStart),
                                        url,
                                        $refs.textarea.value.substring(urlEnd)
                                    ].join('')

                                    $refs.textarea.selectionStart = urlStart - 2
                                    $refs.textarea.selectionEnd = urlStart - 2

                                    render()
                                })
                            })
                        "
                        x-ref="textarea"
                        style="caret-color: black; color: transparent"
                        @class([
                            'tracking-normal whitespace-pre-wrap overflow-y-hidden font-mono block absolute bg-transparent top-0 text-sm left-0 block z-1 w-full h-full min-h-full resize-none transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-600',
                            'border-gray-300' => ! $errors->has($getStatePath()),
                            'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                        ])
                    ></textarea>
                </file-attachment>

                <div
                    x-ref="overlay"
                    x-html="overlay"
                    style="min-height: 150px;"
                    class="w-full h-full rounded-lg px-3 py-2 border border-transparent font-mono tracking-normal bg-white text-sm text-gray-900 break-words whitespace-pre-wrap"
                ></div>
            </div>

            <div class="block w-full h-full min-h-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-primary-300" x-show="tab === 'preview'" style="min-height: 150px;">
                <div class="prose" x-html="preview"></div>
            </div>
        </div>
    </div>
</x-forms::field-wrapper>
