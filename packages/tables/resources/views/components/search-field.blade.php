@props([
    'placeholder' => __('filament-tables::table.fields.search.placeholder'),
    'wireModel' => 'tableSearch',
    'debounce' => '500ms'
])

@php
    $modelAttribute = 'wire:model.live.debounce.' . $debounce;
@endphp

<div
    x-id="['input']"
    {{ $attributes->class(['fi-ta-search-field']) }}
>
    <label x-bind:for="$id('input')" class="sr-only">
        {{ __('filament-tables::table.fields.search.label') }}
    </label>

    <x-filament::input.wrapper
        inline-prefix
        prefix-icon="heroicon-m-magnifying-glass"
        prefix-icon-alias="tables::search-field"
        :wire:target="$wireModel"
    >
        <x-filament::input
            :attributes="$attributes->merge([
                'autocomplete' => 'off',
                'inline-prefix' => true,
                'placeholder' => $placeholder,
                'type' => 'search',
                $modelAttribute => $wireModel,
                'x-bind:id' => '$id(\'input\')',
                'wire:key' => $this->getId() . '.table.' . $wireModel . '.field.input',
            ])"
        />
    </x-filament::input.wrapper>
</div>
