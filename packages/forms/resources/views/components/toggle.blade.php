@php
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();
    $statePath = $getStatePath();

    $attributes = (new ComponentAttributeBag)
        ->merge([
            'aria-checked' => 'false',
            'autofocus' => $isAutofocused(),
            'disabled' => $isDisabled(),
            'id' => $getId(),
            'offColor' => $getOffColor() ?? 'gray',
            'offIcon' => $getOffIcon(),
            'onColor' => $getOnColor() ?? 'primary',
            'onIcon' => $getOnIcon(),
            'state' => '$wire.' . $applyStateBindingModifiers('$entangle(\'' . $statePath . '\')'),
            'wire:loading.attr' => 'disabled',
            'wire:target' => $statePath,
        ], escape: false)
        ->merge($getExtraAttributes(), escape: false)
        ->merge($getExtraAlpineAttributes(), escape: false)
        ->class(['fi-fo-toggle']);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    @if ($isInline())
        <x-slot name="labelPrefix">
            <x-filament::toggle
                :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
            />
        </x-slot>
    @else
        <x-filament::toggle
            :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
        />
    @endif
</x-dynamic-component>
