@php
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
>
    <x-slot
        name="label"
        @class([
            'sm:pt-1.5' => $hasInlineLabel,
        ])
    >
        {{ $getLabel() }}
    </x-slot>

    <x-filament::button.group class="w-max">
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
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
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                {{ $getExtraInputAttributeBag()->class(['peer pointer-events-none absolute opacity-0']) }}
            />

            <x-filament::button
                :color="$getColor($value)"
                :disabled="$shouldOptionBeDisabled"
                :for="$inputId"
                grouped
                :icon="$getIcon($value)"
                tag="label"
            >
                {{ $label }}
            </x-filament::button>
        @endforeach
    </x-filament::button.group>
</x-dynamic-component>
