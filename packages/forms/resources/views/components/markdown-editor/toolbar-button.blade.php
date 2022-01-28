<button
    x-bind:disabled="tab === 'preview'"
    type="button"
    {{ $attributes->class(['border-gray-300 bg-white text-base text-gray-800 text-xs py-1 px-3 cursor-pointer font-medium border rounded transition duration-200 shadow-sm hover:bg-gray-100 focus:ring-primary-200 focus:ring focus:ring-opacity-50', 'filament-forms-markdown-editor-component-toolbar-button']) }}
>
    {{ $slot }}
</button>
