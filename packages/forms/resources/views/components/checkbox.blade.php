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
                        'id' => $getId(),
                        'required' => $isRequired() && (! $isConcealed()),
                        'type' => 'checkbox',
                        'wire:loading.attr' => 'disabled',
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraInputAttributes(), escape: false)
                    ->class([
                        'filament-forms-checkbox-component rounded border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0 disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-current disabled:checked:text-gray-400 dark:bg-gray-900 dark:disabled:bg-gray-950 dark:disabled:checked:bg-gray-600',
                        'text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:ring-white/20 dark:checked:bg-primary-600 dark:focus:ring-primary-600 dark:checked:focus:ring-primary-500/50' => ! $errors->has($statePath),
                        'text-danger-600 ring-danger-600 focus:ring-danger-600 checked:focus:ring-danger-500/50 dark:ring-danger-400 dark:checked:bg-danger-600 dark:focus:ring-danger-600 dark:checked:focus:ring-danger-500/50' => $errors->has($statePath),
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
