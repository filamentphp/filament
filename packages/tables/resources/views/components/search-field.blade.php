@php
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'debounce' => '500ms',
    'hasSearchActionIcon' => true,
    'hasSearchActionLabel' => true,
    'onAction' => false,
    'onBlur' => false,
    'placeholder' => __('filament-tables::table.fields.search.placeholder'),
    'searchActionLabel' => __('filament-tables::table.fields.search.action.label'),
    'wireModel' => 'tableSearch',
])

@php
    if ($onAction) {
        $wireModelAttribute = 'wire:model';
    } elseif ($onBlur) {
        $wireModelAttribute = 'wire:model.live.blur';
    } else {
        $wireModelAttribute = "wire:model.live.debounce.{$debounce}";
    }
@endphp

<div
    x-id="['input']"
    @class([
        'fi-ta-search-field',
        'fi-ta-search-field-with-action' => $onAction,
    ])
>
    <label x-bind:for="$id('input')" class="fi-sr-only">
        {{ __('filament-tables::table.fields.search.label') }}
    </label>

    <x-filament::input.wrapper
        :inline-prefix="! $onAction"
        :prefix-icon="$onAction ? null : \Filament\Support\Icons\Heroicon::MagnifyingGlass"
        :prefix-icon-alias="$onAction ? null : \Filament\Tables\View\TablesIconAlias::SEARCH_FIELD"
        :wire:target="$onAction ? null : $wireModel"
    >
        <x-filament::input
            :attributes="
                (new ComponentAttributeBag)->merge([
                    'autocomplete' => 'off',
                    'inlinePrefix' => ! $onAction,
                    'maxlength' => 1000,
                    'placeholder' => $placeholder,
                    'type' => 'search',
                    'wire:key' => $this->getId() . '.table.' . $wireModel . '.field.input',
                    $wireModelAttribute => $wireModel,
                    'x-bind:id' => '$id(\'input\')',
                    'x-on:keydown.enter' => $onAction ? '$wire.searchTable()' : null,
                    'x-on:search' => $onAction ? '$wire.searchTable()' : '$wire.$refresh()',
                ], escape: false)
            "
        />
    </x-filament::input.wrapper>

    @if ($onAction)
        @if ($hasSearchActionLabel)
            <x-filament::button
                outlined
                :icon="$hasSearchActionIcon ? \Filament\Support\Icons\Heroicon::MagnifyingGlass : null"
                size="sm"
                wire:click="searchTable"
            >
                {{ $searchActionLabel }}
            </x-filament::button>
        @else
            <x-filament::icon-button
                :icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass"
                :label="__('filament-tables::table.fields.search.action.label')"
                size="sm"
                wire:click="searchTable"
            />
        @endif
    @endif
</div>
