@php
    $datalistOptions = $getDatalistOptions();

    $affixLabelClasses = [
        'whitespace-nowrap group-focus-within:text-primary-500',
        'text-gray-400' => ! $errors->has($getStatePath()),
        'text-danger-400' => $errors->has($getStatePath()),
    ];
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['flex items-center space-x-2 rtl:space-x-reverse group filament-forms-text-input-component']) }}>
        @if (($prefixAction = $getPrefixAction()) && (! $prefixAction->isHidden()))
            {{ $prefixAction }}
        @endif

        @if ($icon = $getPrefixIcon())
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if ($label = $getPrefixLabel())
            <span @class($affixLabelClasses)>
                {{ $label }}
            </span>
        @endif

        <div
            @if ($hasLoadingIndicator()) x-data="{ showValidity: false }" @endif
            @class([
                'flex-1',
                'relative' => $hasLoadingIndicator(),
            ])
        >
            <input
                @if ($hasLoadingIndicator()) x-on:change="showValidity = true" @endif
                @unless ($hasMask())
                    {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                    type="{{ $getType() }}"
                @else
                    x-data="textInputFormComponent({
                        {{ $hasMask() ? "getMaskOptionsUsing: (IMask) => ({$getJsonMaskConfiguration()})," : null }}
                        state: $wire.{{
                            $isLazy() ?
                                'entangle(\'' . $getStatePath() . '\').defer' :
                                $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')')
                        }},
                    })"
                    type="text"
                    wire:ignore
                    @if ($isLazy()) x-on:blur="$wire.$refresh;" @endif
                    {{ $getExtraAlpineAttributeBag() }}
                @endunless
                dusk="filament.forms.{{ $getStatePath() }}"
                {!! ($autocapitalize = $getAutocapitalize()) ? "autocapitalize=\"{$autocapitalize}\"" : null !!}
                {!! ($autocomplete = $getAutocomplete()) ? "autocomplete=\"{$autocomplete}\"" : null !!}
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                id="{{ $getId() }}"
                {!! ($inputMode = $getInputMode()) ? "inputmode=\"{$inputMode}\"" : null !!}
                {!! $datalistOptions ? "list=\"{$getId()}-list\"" : null !!}
                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                {!! ($interval = $getStep()) ? "step=\"{$interval}\"" : null !!}
                @if (! $isConcealed())
                    {!! filled($length = $getMaxLength()) ? "maxlength=\"{$length}\"" : null !!}
                    {!! filled($value = $getMaxValue()) ? "max=\"{$value}\"" : null !!}
                    {!! filled($length = $getMinLength()) ? "minlength=\"{$length}\"" : null !!}
                    {!! filled($value = $getMinValue()) ? "min=\"{$value}\"" : null !!}
                    {!! $isRequired() ? 'required' : null !!}
                @endif
                {{ $getExtraInputAttributeBag()->class([
                    'block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70',
                    'dark:bg-gray-700 dark:text-white dark:focus:border-primary-600' => config('forms.dark_mode'),
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                    'pr-10' => $hasLoadingIndicator(),
                ]) }}
            />

            @if ($hasLoadingIndicator())
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                    x-cloak
                    x-show="showValidity"
                >
                    @if ($errors->has($getStatePath()))
                        <x-heroicon-s-exclamation-circle
                            class="text-danger-500 h-5 w-5"
                            wire:loading.remove
                            wire:target="{{ $getStatePath() }}"
                        />
                    @endif

                    @if (!$errors->has($getStatePath()))
                        <x-heroicon-s-check-circle
                            class="text-success-500 h-5 w-5"
                            wire:loading.remove
                            wire:target="{{ $getStatePath() }}"
                        />
                    @endif

                    <svg
                        class="text-primary-500 h-5 w-5 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                        wire:loading
                        wire:target="{{ $getStatePath() }}"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke-width="4"
                            stroke="currentColor"
                        ></circle>
                        <path
                            class="opacity-75"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            fill="currentColor"
                        ></path>
                    </svg>
                </div>
            @endif
        </div>

        @if ($label = $getSuffixLabel())
            <span @class($affixLabelClasses)>
                {{ $label }}
            </span>
        @endif

        @if ($icon = $getSuffixIcon())
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if (($suffixAction = $getSuffixAction()) && (! $suffixAction->isHidden()))
            {{ $suffixAction }}
        @endif
    </div>

    @if ($datalistOptions)
        <datalist id="{{ $getId() }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
