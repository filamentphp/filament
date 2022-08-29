<button
    type="button"
    {{ $attributes->class([
        'filament-forms-rich-editor-component-toolbar-button h-full border-gray-300 bg-white text-gray-800 text-sm py-1 px-3 cursor-pointer font-medium border rounded-lg transition duration-200 shadow-sm hover:bg-gray-100 focus:ring-primary-200 focus:ring focus:ring-opacity-50',
        'dark:bg-gray-700 dark:border-gray-600 dark:text-white' => config('forms.dark_mode'),
    ]) }}
>
    {{ $slot }}
</button>
