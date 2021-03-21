@pushonce('filament-styles:markdown-editor-component')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/mdhl.css">

    <style>
        [x-data^="markdownEditor"] textarea {
            color: transparent;
            caret-color: black;
        }
    </style>
@endpushonce

@pushonce('filament-scripts:markdown-editor-component')
    <script type="module" src="https://cdn.jsdelivr.net/npm/@github/markdown-toolbar-element@1.4.0/dist/index.min.js"></script>
    <script type="module" src="https://unpkg.com/@github/file-attachment-element@1.x/dist/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/dist/mdhl.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        function markdownEditor(config) {
            return {
                attachmentDirectory: config.attachmentDirectory,

                attachmentDisk: config.attachmentDisk,

                attachmentUploadUrl: config.attachmentUploadUrl,

                overlay: null,

                preview: '',

                tab: config.tab,

                value: '',

                checkForAutoInsertion($event) {
                    const lines = this.$refs.textarea.value.split("\n")

                    const currentLine = this.$refs.textarea.value.substring(
                        0, this.$refs.textarea.value.selectionStart
                    ).split("\n").length

                    const previousLine = lines[currentLine - 2]

                    if (! previousLine.match(/^(\*\s|-\s)|^(\d)+\./)) {
                        return;
                    }

                    if (previousLine.match(/^(\*\s)/)) {
                        lines[currentLine - 1] = '* '
                    } else if (previousLine.match(/^(-\s)/)) {
                        lines[currentLine - 1] = '- '
                    } else {
                        const number = previousLine.match(/^(\d)+/)

                        lines[currentLine - 1] = `${parseInt(number) + 1}. `
                    }

                    this.$refs.textarea.value = lines.join("\n")

                    this.resize()
                },

                init: function () {
                    this.value = this.$refs.textarea.value

                    this.$refs.overlay.style.padding = window.getComputedStyle(this.$refs.textarea).padding

                    this.overlay = mdhl.highlight(this.$refs.textarea.value)

                    this.$watch('tab', () => {
                        if (this.tab !== 'preview') return

                        this.preview = marked(this.value)
                    })
                },

                resize: function () {
                    this.$el.style.height = this.$refs.textarea.style.height

                    this.overlay = mdhl.highlight(this.value = this.$refs.textarea.value)
                },

                uploadAttachments: function ($event) {
                    const attachment = $event.detail?.attachments?.[0]

                    if (! attachment || ! attachment.file) return

                    let formData = new FormData()

                    formData.append('directory', this.attachmentDirectory)
                    formData.append('disk', this.attachmentDisk)
                    formData.append('file', attachment.file)

                    fetch(this.attachmentUploadUrl, {
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        method: 'POST',
                    })
                    .then(response => response.text())
                    .then(url => {
                        this.$refs.imageTrigger.click()

                        const urlStart = this.$refs.textarea.selectionStart + 2
                        const urlEnd = urlStart + 3

                        this.$refs.textarea.value = [
                            this.$refs.textarea.value.substring(0, urlStart),
                            url,
                            this.$refs.textarea.value.substring(urlEnd)
                        ].join('')

                        this.$refs.textarea.selectionStart = urlStart - 2
                        this.$refs.textarea.selectionEnd = urlStart - 2

                        this.resize()
                    })
                },
            }
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <div
        x-data="markdownEditor({
            attachmentDirectory: {{ json_encode($formComponent->getAttachmentDirectory()) }},
            attachmentDisk: {{ json_encode($formComponent->getAttachmentDiskName()) }},
            attachmentUploadUrl: {{ json_encode($formComponent->getAttachmentUploadUrl()) }},
            tab: '{{ $formComponent->isDisabled() ? 'preview' : 'write' }}',
        })"
        x-init="init"
        wire:ignore
    >
        <div class="space-y-2">
            @unless ($formComponent->isDisabled())
                <div class="flex items-stretch justify-between h-8">
                    <markdown-toolbar for="{{ $formComponent->getId() }}" class="flex items-stretch space-x-4">
                        @if ($formComponent->hasToolbarButton(['bold', 'italic', 'strike']))
                            <div class="flex items-stretch space-x-1">
                                @if ($formComponent->hasToolbarButton('bold'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.bold') }}">
                                        <md-bold>
                                            <svg class="w-4" viewBox="0 0 16 16" version="1.1" aria-hidden="true"><path fill-rule="evenodd" d="M4 2a1 1 0 00-1 1v10a1 1 0 001 1h5.5a3.5 3.5 0 001.852-6.47A3.5 3.5 0 008.5 2H4zm4.5 5a1.5 1.5 0 100-3H5v3h3.5zM5 9v3h4.5a1.5 1.5 0 000-3H5z"></path></svg>
                                        </md-bold>
                                    </x-filament::button>
                                @endif

                                @if ($formComponent->hasToolbarButton('italic'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.italic') }}">
                                        <md-italic>
                                            <svg class="w-4" height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M6 2.75A.75.75 0 016.75 2h6.5a.75.75 0 010 1.5h-2.505l-3.858 9H9.25a.75.75 0 010 1.5h-6.5a.75.75 0 010-1.5h2.505l3.858-9H6.75A.75.75 0 016 2.75z"></path></svg>
                                        </md-italic>
                                    </x-filament::button>
                                @endif

                                @if ($formComponent->hasToolbarButton('strike'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.strike') }}">
                                        <md-strikethrough>
                                            <svg class="w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.581 3.25c-2.036 0-2.778 1.082-2.778 1.786 0 .055.002.107.006.157a.75.75 0 01-1.496.114 3.56 3.56 0 01-.01-.271c0-1.832 1.75-3.286 4.278-3.286 1.418 0 2.721.58 3.514 1.093a.75.75 0 11-.814 1.26c-.64-.414-1.662-.853-2.7-.853zm3.474 5.25h3.195a.75.75 0 000-1.5H1.75a.75.75 0 000 1.5h6.018c.835.187 1.503.464 1.951.81.439.34.647.725.647 1.197 0 .428-.159.895-.594 1.267-.444.38-1.254.726-2.676.726-1.373 0-2.38-.493-2.86-.956a.75.75 0 00-1.042 1.079C3.992 13.393 5.39 14 7.096 14c1.652 0 2.852-.403 3.65-1.085a3.134 3.134 0 001.12-2.408 2.85 2.85 0 00-.811-2.007z"></path></svg>
                                        </md-strikethrough>
                                    </x-filament::button>
                                @endif
                            </div>
                        @endif

                        @if ($formComponent->hasToolbarButton(['link', 'attachFiles', 'code']))
                            <div class="flex items-stretch space-x-1">
                                @if ($formComponent->hasToolbarButton('link'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.link') }}">
                                        <md-link class="w-full h-full">
                                            <x-heroicon-o-link class="w-4" />
                                        </md-link>
                                    </x-filament::button>
                                @endif

                                @if ($formComponent->hasToolbarButton('attachFiles'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.attachFiles') }}">
                                        <md-image class="w-full h-full" x-ref="imageTrigger">
                                            <x-heroicon-o-photograph class="w-4" />
                                        </md-image>
                                    </x-filament::button>
                                @endif

                                @if ($formComponent->hasToolbarButton('code'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.code') }}">
                                        <md-code class="w-full h-full">
                                            <x-heroicon-o-code class="w-4" />
                                        </md-code>
                                    </x-filament::button>
                                @endif
                            </div>
                        @endif

                        @if ($formComponent->hasToolbarButton(['bullet', 'number']))
                            <div class="flex items-stretch space-x-1">
                                @if ($formComponent->hasToolbarButton('bullet'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.bullet') }}">
                                        <md-unordered-list class="w-full h-full">
                                            <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2 4a1 1 0 100-2 1 1 0 000 2zm3.75-1.5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zM3 8a1 1 0 11-2 0 1 1 0 012 0zm-1 6a1 1 0 100-2 1 1 0 000 2z"></path></svg>
                                        </md-unordered-list>
                                    </x-filament::button>
                                @endif

                                @if ($formComponent->hasToolbarButton('number'))
                                    <x-filament::button size="small" class="text-base" title="{{ __('forms::fields.markdownEditor.toolbarButtons.number') }}">
                                        <md-ordered-list class="w-full h-full">
                                            <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2.003 2.5a.5.5 0 00-.723-.447l-1.003.5a.5.5 0 00.446.895l.28-.14V6H.5a.5.5 0 000 1h2.006a.5.5 0 100-1h-.503V2.5zM5 3.25a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 3.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 8.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5a.75.75 0 01-.75-.75zM.924 10.32l.003-.004a.851.851 0 01.144-.153A.66.66 0 011.5 10c.195 0 .306.068.374.146a.57.57 0 01.128.376c0 .453-.269.682-.8 1.078l-.035.025C.692 11.98 0 12.495 0 13.5a.5.5 0 00.5.5h2.003a.5.5 0 000-1H1.146c.132-.197.351-.372.654-.597l.047-.035c.47-.35 1.156-.858 1.156-1.845 0-.365-.118-.744-.377-1.038-.268-.303-.658-.484-1.126-.484-.48 0-.84.202-1.068.392a1.858 1.858 0 00-.348.384l-.007.011-.002.004-.001.002-.001.001a.5.5 0 00.851.525zM.5 10.055l-.427-.26.427.26z"></path></svg>
                                        </md-ordered-list>
                                    </x-filament::button>
                                @endif
                            </div>
                        @endif
                    </markdown-toolbar>

                    @if ($formComponent->hasToolbarButton(['write', 'preview']) && ! $formComponent->isDisabled())
                        <div class="flex items-center space-x-4">
                            @if ($formComponent->hasToolbarButton('write'))
                                <button
                                    class="font-mono text-sm hover:underline"
                                    x-on:click.prevent="tab = 'write'"
                                    x-bind:class="{ 'text-gray-400': tab !== 'write' }"
                                >
                                    {{ __('forms::fields.markdownEditor.toolbarButtons.write') }}
                                </button>
                            @endif

                            @if ($formComponent->hasToolbarButton('preview'))
                                <button
                                    class="font-mono text-sm hover:underline"
                                    x-on:click.prevent="tab = 'preview'"
                                    x-bind:class="{ 'text-gray-400': tab !== 'preview' }"
                                >
                                    {{ __('forms::fields.markdownEditor.toolbarButtons.preview') }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endunless

            <div x-show="tab === 'write'" class="relative w-full h-full" style="min-height: 150px;">
                <file-attachment directory>
                    <textarea
                        {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
                        {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
                        {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
                        {!! $formComponent->getPlaceholder() ? "placeholder=\"{$formComponent->getPlaceholder()}\"" : null !!}
                        {!! $formComponent->isRequired() ? 'required' : null !!}
                        x-on:input="resize"
                        x-on:keyup.enter="checkForAutoInsertion"
                        x-on:file-attachment-accepted.window="uploadAttachments"
                        x-ref="textarea"
                        class="absolute bg-transparent top-0 left-0 block z-1 w-full h-full min-h-full rounded resize-none shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
                    ></textarea>
                </file-attachment>

                <div class="w-full h-full text-black break-words" x-ref="overlay" x-html="overlay"></div>
            </div>

            <div class="block w-full h-full min-h-full px-6 py-4 border border-gray-300 rounded shadow-sm focus:border-blue-300" x-show="tab === 'preview'" style="min-height: 150px;">
                <div class="prose" x-html="preview"></div>
            </div>
        </div>
    </div>
</x-forms::field-group>
