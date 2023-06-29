@php
    $statePath = $getStatePath();
@endphp

<input
    {{
        $attributes
            ->merge([
                'id' => $getId(),
                'type' => 'hidden',
                $applyStateBindingModifiers('wire:model') => $statePath,
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['filament-forms-hidden-component'])
    }}
/>
