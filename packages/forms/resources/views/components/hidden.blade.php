<input
    {{
        $attributes
            ->merge([
                'dusk' => "filament.forms.{$getStatePath()}",
                'id' => $getId(),
                'type' => 'hidden',
                $applyStateBindingModifiers('wire:model') => $getStatePath(),
            ], escape: true)
            ->merge($getExtraAttributes(), escape: true)
            ->class(['filament-forms-hidden-component'])
    }}
/>
