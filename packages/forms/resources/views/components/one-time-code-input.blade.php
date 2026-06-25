@php
    $fieldWrapperView = $getFieldWrapperView();
    $statePath = $getStatePath();
    $length = $getLength();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-data="{
            code: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
        }"
    >
        <x-filament::input.one-time-code
            x-model="code"
            :length="$length"
            :attributes="
                \Filament\Support\prepare_inherited_attributes(
                    $getExtraAttributeBag()->merge($getExtraAlpineAttributes(), escape: false),
                )
            "
        >
            <x-slot
                name="input"
                :attributes="
                    \Filament\Support\prepare_inherited_attributes(
                        $getExtraInputAttributeBag()->merge([
                            'autofocus' => $isAutofocused(),
                            'disabled' => $isDisabled,
                            'id' => $getId(),
                            'readonly' => $isReadOnly(),
                            'required' => $isRequired() && (! $isConcealed),
                        ], escape: false),
                    )
                "
            ></x-slot>
        </x-filament::input.one-time-code>
    </div>
</x-dynamic-component>
