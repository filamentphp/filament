@php
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
    $isLabelHidden = $isLabelHidden();
    $label = $getLabel();
@endphp

<x-filament::fieldset
    :label="$label"
    :label-hidden="$isLabelHidden"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge([
                'id' => $id,
            ], escape: false)
            ->merge($extraAttributes, escape: false)
            ->class(['fi-sc-fieldset'])
    "
>
    {{ $getChildSchema() }}
</x-filament::fieldset>
