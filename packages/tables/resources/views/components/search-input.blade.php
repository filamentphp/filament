@props([
    'wireModel' => 'tableSearch',
])

@php
    $id = 'filament-tables-search-input-' . str()->random();
@endphp

<div {{ $attributes->class(['filament-tables-search-input']) }}>
    <label for="{{ $id }}" class="sr-only">
        {{ __('filament-tables::table.fields.search.label') }}
    </label>

    <x-filament-forms::affixes
        prefix-icon="heroicon-m-magnifying-glass"
        prefix-icon-alias="tables::search-input"
    >
        <x-filament::input
            autocomplete="off"
            :id="$id"
            :placeholder="__('filament-tables::table.fields.search.placeholder')"
            :wire:model.live.debounce.500ms="$wireModel"
        />
    </x-filament-forms::affixes>
</div>
