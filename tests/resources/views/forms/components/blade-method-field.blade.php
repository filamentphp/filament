<x-dynamic-component
    :component="isset($field) ? $getFieldWrapperView() : $getEntryWrapperView()"
    :field="$field ?? null"
    :entry="$entry ?? null"
>
    <div x-data="{ report: '' }" data-blade-method="{{ $getName() }}">
        <x-filament::button
            color="gray"
            x-on:click="report = await $replaceText({ text: 'Named café' })"
        >
            Named Blade call
        </x-filament::button>
        <x-filament::button
            color="gray"
            x-on:click="report = await $callSchemaComponentMethod('replaceText', { text: 'General café' })"
        >
            General Blade call
        </x-filament::button>
        <output x-text="report"></output>
    </div>
</x-dynamic-component>
