<x-filament::tabs :label="__($field->label)" :default-tab-id="Str::of(array_key_first($field->tabs))->kebab()">
    <x-slot name="tabList">
        @foreach (array_keys($field->tabs) as $label)
            <x-filament::tab :id="Str::of($label)->kebab()">
                {{ __($label) }}
            </x-filament::tab>
        @endforeach
    </x-slot>

    @foreach($field->tabs as $label => $fields)
        <x-filament::tab-panel :id="Str::of($label)->kebab()">
            <x-filament::form embedded :fields="$fields" />
        </x-filament::tab-panel>
    @endforeach
</x-filament::tabs>
