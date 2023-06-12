<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @capture($content)
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
                        'filament-forms-checkbox-component filament-forms-input rounded text-primary-600 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70',
                        'dark:bg-gray-700 dark:checked:bg-primary-500' => config('forms.dark_mode'),
                        'border-gray-300' => ! $errors->has($getStatePath()),
                        'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                        'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                        'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode'),
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
