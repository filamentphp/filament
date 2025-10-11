@php
    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
    $areButtonLabelsHidden = $areButtonLabelsHidden();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
    $extraInputAttributeBag = $getExtraInputAttributeBag()->class(['fi-fo-toggle-buttons-input']);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    tabindex="-1"
    class="fi-fo-toggle-buttons-wrp"
>
    <div
        {{ $getExtraAttributeBag()->class(['fi-fo-toggle-buttons fi-btn-group']) }}
    >
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
                $color = $getColor($value);
                $icon = $getIcon($value);
            @endphp

            <input
                @disabled($shouldOptionBeDisabled)
                id="{{ $inputId }}"
                @if (! $isMultiple)
                    name="{{ $id }}"
                @endif
                type="{{ $isMultiple ? 'checkbox' : 'radio' }}"
                value="{{ $value }}"
                wire:loading.attr="disabled"
                {{ $wireModelAttribute }}="{{ $statePath }}"
                {{ $extraInputAttributeBag }}
            />

            <x-filament::button
                :color="$color"
                :disabled="$shouldOptionBeDisabled"
                :for="$inputId"
                grouped
                :icon="$icon"
                :label-sr-only="$areButtonLabelsHidden"
                tag="label"
            >
                {{ $label }}
            </x-filament::button>
        @endforeach
    </div>
</x-dynamic-component>
