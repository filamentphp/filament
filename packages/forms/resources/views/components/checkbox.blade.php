<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $statePath = $getStatePath();
    @endphp

    @capture($content)
        <input
            {{
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
                        'filament-forms-checkbox-component filament-forms-input rounded text-primary-600 shadow-sm transition duration-75 focus:ring-2 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                        'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500' => ! $errors->has($statePath),
                        'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($statePath),
                    ])
            }}
        />
    @endcapture

    @if ($isInline())
        <x-slot name="labelPrefix">
            {{ $content() }}
        </x-slot>
    @else
        {{ $content() }}
    @endif
</x-dynamic-component>
