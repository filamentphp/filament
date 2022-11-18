<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $isInline = $isInline();
        $statePath = $getStatePath();
    @endphp

    @if ($isInline)
        <x-slot name="labelPrefix">
    @endif
            <input {{
                $attributes
                    ->merge([
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'dusk' => "filament.forms.{$statePath}",
                        'id' => $getId(),
                        'required' => $isRequired() && (! $isConcealed()),
                        'type' => 'checkbox',
                        'wire:loading.attr' => 'disabled',
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraInputAttributes(), escape: false)
                    ->class([
                        'filament-forms-checkbox-component text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                        'border-gray-300 dark:border-gray-600' => ! $errors->has($statePath),
                        'border-danger-300 ring-danger-500 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                    ])
            }} />
    @if ($isInline)
        </x-slot>
    @endif
</x-dynamic-component>
