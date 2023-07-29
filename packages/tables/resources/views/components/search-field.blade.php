@props([
    'wireModel' => 'tableSearch',
])

<div
    x-id="['input']"
    {{ $attributes->class(['fi-ta-search-field']) }}
>
    <label x-bind:for="$id('input')" class="sr-only">
        {{ __('filament-tables::table.fields.search.label') }}
    </label>

    <x-filament-forms::affixes
        inline-prefix
        prefix-icon="heroicon-m-magnifying-glass"
        prefix-icon-alias="tables::search-field"
        :wire:target="$wireModel"
    >
        <x-filament::input
            autocomplete="off"
            inline-prefix
            :placeholder="__('filament-tables::table.fields.search.placeholder')"
            type="search"
            :wire:model.live.debounce.500ms="$wireModel"
            x-bind:for="$id('input')"
        />
    </x-filament-forms::affixes>
</div>
