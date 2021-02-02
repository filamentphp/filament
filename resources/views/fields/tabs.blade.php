<x-filament::tabs :label="__($field->label)" :tab="array_key_first($field->tabs)">
    <x-slot name="tablist">
        @foreach (array_keys($field->tabs) as $label)
            <x-filament::tab :id="$label">
                {{ __($label) }}
            </x-filament::tab>
        @endforeach
    </x-slot>

    @foreach($field->tabs as $label => $fields)
        <x-filament::tab-panel :id="$label">
            <x-filament::form :fields="$fields" />
        </x-filament::tab-panel>
    @endforeach
</x-filament::tabs>

@if (count($field->tabs) > 1 && $errors->any())
    <p role="alert" class="text-red-700 font-medium text-sm leading-tight">
        {{ __('filament::tabs.error') }}
    </p>
@endif
