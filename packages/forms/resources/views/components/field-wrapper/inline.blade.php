@props([
    'id',
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => false,
    'labelSuffix' => null,
    'helperText' => null,
    'hint' => null,
    'hintIcon' => null,
    'required' => false,
    'statePath',
])

<div {{ $attributes->class(['filament-forms-field-wrapper']) }}>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div class="grid gap-2 sm:grid-cols-3 sm:gap-4 sm:items-start">
        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $hint)
            <div class="flex items-center justify-between gap-2 sm:gap-1 sm:items-start sm:flex-col sm:pt-2 rtl:space-x-reverse">
                @if ($label && (! $labelSrOnly))
                    <x-forms::field-wrapper.label
                        :for="$id"
                        :error="$errors->has($statePath)"
                        :prefix="$labelPrefix"
                        :required="$required"
                        :suffix="$labelSuffix"
                    >
                        {{ $label }}
                    </x-forms::field-wrapper.label>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                @if ($hint || $hintIcon)
                    <x-forms::field-wrapper.hint :icon="$hintIcon">
                        {!! filled($hint) ? \Illuminate\Support\Str::markdown($hint) : null !!}
                    </x-forms::field-wrapper.hint>
                @endif
            </div>
        @endif

        <div class="space-y-2 sm:space-y-1 sm:col-span-2">
            {{ $slot }}

            @if ($errors->has($statePath))
                <x-forms::field-wrapper.error-message>
                    {{ $errors->first($statePath) }}
                </x-forms::field-wrapper.error-message>
            @endif

            @if ($helperText)
                <x-forms::field-wrapper.helper-text>
                    {!! \Illuminate\Support\Str::markdown($helperText) !!}
                </x-forms::field-wrapper.helper-text>
            @endif
        </div>
    </div>
</div>
