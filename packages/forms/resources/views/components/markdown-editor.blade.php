@pushonce('filament-styles:markdown-editor-component')
    <style>
        .markdown-editor .prose pre {
            color: inherit;
            background-color: inherit;
        }
    </style>
@endpushonce

@pushonce('filament-scripts:markdown-editor-component')
    <script src="http://127.0.0.1:5500/dist/oak.umd.js"></script>
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
        x-data="Oak({
            value: null,
            onUpdate(_, value) {
                this.value = value
            },
            classes: {
                textarea: 'block w-full px-2.5 rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}'
            },
            placeholder: {{ json_encode($formComponent->getPlaceholder()) }},
        })"
        x-init="init"
        class="markdown-editor"
        wire:ignore
    >
        <div
            x-spread="elements.editor"
            class="block w-full p-2 font-sans placeholder-gray-400 placeholder-opacity-100 bg-white border-gray-300 rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 max-w-none"
        ></div>
    </div>
</x-forms::field-group>
