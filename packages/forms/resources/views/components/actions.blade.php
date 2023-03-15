<div {{
    $attributes
        ->merge([
            'id' => $getId(),
        ], escape: false)
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-forms-actions-component flex flex-col h-full',
            match ($verticalAlignment = $getVerticalAlignment()) {
                'center' => 'justify-center',
                'end' => 'justify-end',
                'start' => 'justify-start',
                default => $verticalAlignment,
            },
        ])
}}>
    <x-filament-actions::actions
        :actions="$getChildComponentContainer()->getComponents()"
        :alignment="$getAlignment()"
        :full-width="$isFullWidth()"
    />
</div>
