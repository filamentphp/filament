@pushonce('filament-styles:markdown-editor-component')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/mdhl.css">

    <style>
        [x-data^="markdownEditor"] > textarea {
            color: transparent;
            caret-color: inherit;
        }
        /*
        [x-data^="markdownEditor"] > textarea::selection {
            color: inherit;
            -webkit-text-fill-color: inherit;
        } */
    </style>
@endpushonce

@pushonce('filament-scripts:markdown-editor-component')
    <script type="module" src="https://cdn.jsdelivr.net/npm/@github/markdown-toolbar-element@1.4.0/dist/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/dist/mdhl.umd.js"></script>

    <script>
        function markdownEditor(config) {
            return {
                overlay: null,
                init: function () {
                    this.$refs.overlay.style.padding = window.getComputedStyle(this.$refs.textarea).padding

                    this.overlay = mdhl.highlight(this.$refs.textarea.value)
                },
                resize: function () {
                    this.$el.style.height = this.$refs.textarea.style.height

                    this.overlay = mdhl.highlight(this.$refs.textarea.value)
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
    <div class="space-y-2">
        <markdown-toolbar for="{{ $formComponent->getId() }}" class="flex items-stretch space-x-1">
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

            <x-filament::button size="small" class="text-base">
                <md-link>
                    <x-heroicon-o-link class="w-3" />
                </md-link>
            </x-filament::button>
        </markdown-toolbar>
        <div
            x-data="markdownEditor({
            })"
            class="relative w-full h-full"
            style="min-height: 200px"
            x-init="init"
            wire:ignore
        >
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
    </div>
</x-forms::field-group>
