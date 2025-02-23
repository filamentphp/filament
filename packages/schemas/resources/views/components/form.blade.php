<form
    {{
        $attributes
            ->merge([
                'id' => $getId(),
                'wire:submit' => $getLivewireSubmitHandler(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-form',
                'fi-dense' => $isDense(),
            ])
    }}
>
    {{ $getChildSchema($schemaComponent::HEADER_SCHEMA_KEY) }}

    {{ $getChildSchema() }}

    {{ $getChildSchema($schemaComponent::FOOTER_SCHEMA_KEY) }}
</form>
