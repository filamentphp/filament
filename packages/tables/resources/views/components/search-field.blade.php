@php
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'debounce' => '500ms',
    'onBlur' => false,
    'placeholder' => __('filament-tables::table.fields.search.placeholder'),
    'wireModel' => 'tableSearch',
])

@php
    $wireModelAttribute = $onBlur ? 'wire:model.blur' : "wire:model.live.debounce.{$debounce}";
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
            :attributes="
                (new ComponentAttributeBag)->merge([
                    'autocomplete' => 'off',
                    'inlinePrefix' => true,
                    'placeholder' => $placeholder,
                    'type' => 'search',
                    'wire:key' => $this->getId() . '.table.' . $wireModel . '.field.input',
                    $wireModelAttribute => $wireModel,
                    'x-bind:id' => '$id(\'input\')',
                    'x-on:keyup' => 'if ($event.key === \'Enter\') { $wire.$refresh() }',
                ])
            "
        />
    </x-filament::input.wrapper>
</div>
