@php
    use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;

    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge([
                    'id' => $id,
                ], escape: false)
                ->merge($extraAttributes, escape: false)
        }}
    >
        @livewire(TableSelectLivewireComponent::class, [
            'isDisabled' => $isDisabled(),
            'maxSelectableRecords' => $getMaxItems(),
            'model' => $getModel(),
            'record' => $getRecord(),
            'relationshipName' => $getRelationshipName(),
            'shouldIgnoreRelatedRecords' => $shouldIgnoreRelatedRecords(),
            'tableConfiguration' => base64_encode($getTableConfiguration()),
            'tableArguments' => $getTableArguments(),
            $applyStateBindingModifiers('wire:model') => $getStatePath(),
        ], key($getLivewireKey()))
    </div>
</x-dynamic-component>
