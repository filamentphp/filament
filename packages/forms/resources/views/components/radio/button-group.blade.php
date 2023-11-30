@php
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::button.group class="w-max">
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <input
                @disabled($shouldOptionBeDisabled)
                hidden
                id="{{ $inputId }}"
                name="{{ $id }}"
                type="radio"
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
