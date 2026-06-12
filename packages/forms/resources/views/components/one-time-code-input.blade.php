@php
    $fieldWrapperView = $getFieldWrapperView();
    $placeholder = $getPlaceholder();
    $extraAttributes = $getExtraAttributeBag()
        ->merge($getExtraAlpineAttributes(), escape: false);
    $extraInputAttributes = $getExtraInputAttributeBag()
        ->merge([
            'autocomplete' => false,
            'autofocus' => $isAutofocused(),
            'disabled' => $isDisabled(),
            'id' => $getId(),
            'length' => $getLength(),
            'placeholder' => filled($placeholder) ? e($placeholder) : null,
            'readonly' => $isReadOnly(),
            'required' => $isRequired() && (! $isConcealed()),
            $applyStateBindingModifiers('wire:model') => $getStatePath(),
        ], escape: false);
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <x-filament::input.one-time-code
        :attributes="\Filament\Support\prepare_inherited_attributes($extraAttributes)"
    >
        <x-slot
            name="input"
            :attributes="\Filament\Support\prepare_inherited_attributes($extraInputAttributes)"
        ></x-slot>
    </x-filament::input.one-time-code>
</x-dynamic-component>
