<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($isInline())
        <x-slot name="labelPrefix">
    @endif
            <input
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                wire:loading.attr="disabled"
                id="{{ $getId() }}"
                type="checkbox"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                dusk="filament.forms.{{ $getStatePath() }}"
                @if (! $isConcealed())
                    {!! $isRequired() ? 'required' : null !!}
                @endif
                {{
                    $attributes
                        ->merge($getExtraAttributes())
                        ->merge($getExtraInputAttributeBag()->getAttributes())
                        ->class([
                            'filament-forms-checkbox-component text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500',
                            'border-gray-300 dark:border-gray-600' => ! $errors->has($getStatePath()),
                            'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                            'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
                        ])
                }}
            />
    @if ($isInline())
        </x-slot>
    @endif
</x-dynamic-component>
