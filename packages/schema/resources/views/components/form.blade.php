<form
    {{
        $attributes
            ->merge([
                'id' => $getId(),
                'wire:submit' => $getLivewireSubmitHandler(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-fo-form flex-col gap-6'])
    }}
>
    {{ $getDecorations($schemaComponent::HEADER_DECORATIONS) }}

    {{ $getChildComponentContainer() }}

    {{ $getDecorations($schemaComponent::FOOTER_DECORATIONS) }}
</form>
