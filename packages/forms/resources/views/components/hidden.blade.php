<input
    {{
        $attributes
            ->merge([
                'dusk' => "filament.forms.{$getStatePath()}",
                'id' => $getId(),
                'type' => 'hidden',
                $applyStateBindingModifiers('wire:model') => $getStatePath(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['filament-forms-hidden-component'])
    }}
/>
