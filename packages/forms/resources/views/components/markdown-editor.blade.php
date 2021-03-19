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
    <script src="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/dist/mdhl.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        function markdownEditor(config) {
            return {
                overlay: null,

                preview: '',

                value: '',

                tab: 'write',

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
                }
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
        })"
        x-init="init"
        wire:ignore
    >
        <div class="space-y-2">
            <div class="flex items-stretch justify-between">
                <markdown-toolbar for="{{ $formComponent->getId() }}" class="flex items-stretch space-x-4">
                    <div class="flex items-stretch space-x-1">
                        <x-filament::button size="small" class="text-base">
                            <md-bold>𝐁</md-bold>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-italic>𝑰</md-italic>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-strikethrough>
                                <strike>𝐒</strike>
                            </md-strikethrough>
                        </x-filament::button>
                    </div>
                    <div class="flex items-stretch space-x-1">
                        <x-filament::button size="small" class="text-base">
                            <md-link class="w-full h-full">
                                <x-heroicon-o-link class="w-4" />
                            </md-link>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-image class="w-full h-full">
                                <x-heroicon-o-photograph class="w-4" />
                            </md-image>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-code class="w-full h-full">
                                <x-heroicon-o-code class="w-4" />
                            </md-code>
                        </x-filament::button>
                    </div>
                    <div class="flex items-stretch space-x-1">
                        <x-filament::button size="small" class="text-base">
                            <md-unordered-list class="w-full h-full">
                                <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2 4a1 1 0 100-2 1 1 0 000 2zm3.75-1.5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm0 5a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zM3 8a1 1 0 11-2 0 1 1 0 012 0zm-1 6a1 1 0 100-2 1 1 0 000 2z"></path></svg>
                            </md-unordered-list>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-ordered-list class="w-full h-full">
                                <svg height="16" viewBox="0 0 16 16" version="1.1" width="16" aria-hidden="true"><path fill-rule="evenodd" d="M2.003 2.5a.5.5 0 00-.723-.447l-1.003.5a.5.5 0 00.446.895l.28-.14V6H.5a.5.5 0 000 1h2.006a.5.5 0 100-1h-.503V2.5zM5 3.25a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 3.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 8.25zm0 5a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5a.75.75 0 01-.75-.75zM.924 10.32l.003-.004a.851.851 0 01.144-.153A.66.66 0 011.5 10c.195 0 .306.068.374.146a.57.57 0 01.128.376c0 .453-.269.682-.8 1.078l-.035.025C.692 11.98 0 12.495 0 13.5a.5.5 0 00.5.5h2.003a.5.5 0 000-1H1.146c.132-.197.351-.372.654-.597l.047-.035c.47-.35 1.156-.858 1.156-1.845 0-.365-.118-.744-.377-1.038-.268-.303-.658-.484-1.126-.484-.48 0-.84.202-1.068.392a1.858 1.858 0 00-.348.384l-.007.011-.002.004-.001.002-.001.001a.5.5 0 00.851.525zM.5 10.055l-.427-.26.427.26z"></path></svg>
                            </md-ordered-list>
                        </x-filament::button>
                    </div>
                </markdown-toolbar>

                                <div class="flex items-center space-x-4">
                    <button
                        class="font-mono text-sm hover:underline"
                        x-on:click.prevent="tab = 'write'"
                        x-bind:class="{ 'text-gray-400': tab !== 'write' }"
                    >
                        Write
                    </button>

                    <button
                        class="font-mono text-sm hover:underline"
                        x-on:click.prevent="tab = 'preview'"
                        x-bind:class="{ 'text-gray-400': tab !== 'preview' }"
                    >
                        Preview
                    </button>
                </div>
            </div>

            <div x-show="tab === 'write'" class="relative w-full h-full" style="min-height: 150px;">
                <textarea
                    {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
                    {!! $formComponent->isDisabled() ? 'disabled' : null !!}
                    {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
                    {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
                    {!! $formComponent->getPlaceholder() ? "placeholder=\"{$formComponent->getPlaceholder()}\"" : null !!}
                    {!! $formComponent->isRequired() ? 'required' : null !!}
                    @input="resize"
                    class="absolute bg-transparent top-0 left-0 block z-1 w-full h-full min-h-full rounded resize-none shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
                    x-ref="textarea"
                ></textarea>
                <div class="w-full h-full text-black" x-ref="overlay" x-html="overlay"></div>
            </div>
            <div class="block w-full h-full min-h-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:border-blue-300" x-show="tab === 'preview'" style="min-height: 150px;">
                <div class="prose" x-html="preview"></div>
            </div>
        </div>
    </div>
</x-forms::field-group>
