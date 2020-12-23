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

@if (count($tabs) > 1 && $errors->any())
    <p role="alert" class="text-red-700 font-medium text-sm leading-tight">
        {{ __('Errors found. Please check the form and try again.') }}
    </p>
@endif