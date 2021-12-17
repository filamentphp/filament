<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
<div class="space-y-2">
    @foreach ($getOptions() as $value => $label)
        <div class="relative flex items-start">
            <div class="flex items-center h-5">
                <input
                    name="{{ $getId() }}"
                    id="{{ $getId() }}-{{ $value }}"
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
            </div>

            <div class="ml-3 rtl:mr-3 text-sm">
                <label for="{{ $getId() }}-{{ $value }}" @class([
                    'font-medium',
                    'text-gray-700' => ! $errors->has($getStatePath()),
                    'text-danger-600' => $errors->has($getStatePath()),
                ])>
                    {{ $label }}
                </label>

                @if ($hasDescription($value))
                    <p class="text-gray-500">
                        {{ $getDescription($value) }}
                    </p>
                @endif
            </div>
        </div>
    @endforeach
</div>
</x-forms::field-wrapper>
