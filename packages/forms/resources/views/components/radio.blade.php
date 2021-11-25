<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
<div class="space-y-4">
    @foreach ($getOptions() as $value => $label)
        <div class="flex items-center">
            <input
                name="{{ $getId() }}"
                id="{{ $value }}"
                type="radio"
                value="{{ $value }}"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                {{ $attributes->merge($getExtraAttributes())->class([
                    'focus:ring-primary-500 h-4 w-4 text-primary-600',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                ]) }}
                {!! $isOptionDisabled($value, $label) ? 'disabled' : null !!}
            />
            <label for="{{ $value }}" @class([
                'block ml-3 text-sm font-medium',
                'text-gray-700' => ! $errors->has($getStatePath()),
                'text-danger-600' => $errors->has($getStatePath()),
            ])>

                {{ $label }}

                @if($hasDescription($value))
                    <p id="{{ $value }}-description" class="text-gray-500">
                        {{ $getDescription($value) }}
                    </p>
                @endif
            </label>
        </div>
    @endforeach
</div>
</x-forms::field-wrapper>
