<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @foreach ($getOptions() as $optionValue => $optionLabel)
        <label class="flex items-center space-x-3">
            <input
                {!! $isDisabled() ? 'disabled' : null !!}
                type="checkbox"
                value="{{ $optionValue }}"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                {{ $attributes->merge($getExtraAttributes())->class([
                    'text-primary-600 duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                ]) }}
            />

            <span class="text-sm font-medium text-gray-700">
                {{ $optionLabel }}
            </span>
        </label>
    @endforeach
</x-forms::field-wrapper>
