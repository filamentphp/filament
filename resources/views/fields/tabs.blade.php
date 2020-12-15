<x-filament::tabs :label="__($label)" :tab="array_key_first($tabs)">
    <x-slot name="tablist">
        @foreach (array_keys($tabs) as $label)
            <x-filament::tab :id="$label">
                {{ __($label) }}
            </x-filament::tab>
        @endforeach
    </x-slot>
    @foreach($tabs as $label => $fields)
        <x-filament::tab-panel :id="$label">
            <x-filament::fields :fields="$fields" />
        </x-filament::tab-panel>
    @endforeach
</x-filament::tabs>