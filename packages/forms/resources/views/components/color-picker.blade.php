@php
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
    <div {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-color-picker-component flex items-center space-x-1 rtl:space-x-reverse group']) }}>
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
            x-data="colorPickerFormComponent({
                isAutofocused: @js($isAutofocused()),
                isDisabled: @js($isDisabled()),
                state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }}
            })"
            {{ $getExtraAlpineAttributeBag()->class(['relative flex-1']) }}
        >
            <input
                x-ref="input"
                type="text"
                dusk="filament.forms.{{ $getStatePath() }}"
                id="{{ $getId() }}"
                x-model="state"
                x-on:click="togglePanelVisibility()"
                x-on:keydown.enter.stop.prevent="togglePanelVisibility()"
                autocomplete="off"
                {!! $isDisabled() ? 'disabled' : null !!}
                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                @if (! $isConcealed())
                    {!! $isRequired() ? 'required' : null !!}
                @endif
                {{ $getExtraInputAttributeBag()->class([
                    'text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70',
                    'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                ]) }}
            />

            <span
                x-cloak
                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none rtl:right-auto rtl:left-0 rtl:pl-2"
            >
                <span
                    x-bind:style="{ 'background-color': state }"
                    class="filament-forms-color-picker-component-preview relative overflow-hidden rounded-md w-7 h-7"
                ></span>
            </span>

            <div
                x-cloak
                x-ref="panel"
                x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                wire:ignore.self
                wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.panel"
                @class([
                    'hidden absolute z-10 shadow-lg',
                    'opacity-70 pointer-events-none' => $isDisabled(),
                ])
            >
                <{{ match($getFormat()) {
                    'hsl' => 'hsl-string',
                    'rgb' => 'rgb-string',
                    'rgba' => 'rgba-string',
                    default => 'hex',
                } }}-color-picker />
            </div>
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
</x-dynamic-component>
