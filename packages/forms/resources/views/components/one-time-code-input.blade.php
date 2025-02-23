@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributeBag()
        ->merge($getExtraInputAttributes(), escape: false)
        ->merge($getExtraAlpineAttributes(), escape: false)
        ->merge([
            'autofocus' => $isAutofocused(),
            'disabled' => $isDisabled(),
            'id' => $getId(),
            'length' => $getLength(),
            'placeholder' => $getPlaceholder(),
            'readonly' => $isReadOnly(),
            'required' => $isRequired() && (! $isConcealed()),
            $applyStateBindingModifiers('wire:model') => $getStatePath(),
        ], escape: false);
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <x-filament::input.one-time-code
        :attributes="\Filament\Support\prepare_inherited_attributes($extraAttributes)"
    />
</x-dynamic-component>
