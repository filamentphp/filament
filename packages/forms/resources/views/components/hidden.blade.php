<input
    id="{{ $getId() }}"
    {!! $isRequired() ? 'required' : null !!}
    type="hidden"
    {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
    {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-hidden-component']) }}
    dusk="filament.forms.{{ $getStatePath() }}"
/>
