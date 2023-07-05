@php
    $datalistOptions = $getDatalistOptions();
    $id = $getId();
    $isConcealed = $isConcealed();
    $mask = $getMask();
    $statePath = $getStatePath();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament-forms::affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-text-input-component"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        <input
            x-data="{}"
            @if (filled($mask))
                x-mask{{ $mask instanceof \Filament\Support\RawJs ? ':dynamic' : null }}="{{ $mask }}"
            @endif
            x-bind:class="
                @js($statePath) in $wire.__instance.serverMemo.errors
                    ? 'ring-danger-600 focus:ring-danger-600 dark:ring-danger-400 dark:focus:ring-danger-400'
                    : 'ring-gray-950/10 focus:ring-primary-600 dark:ring-white/20 dark:focus:ring-primary-600'
            "
            {{
                $getExtraInputAttributeBag()
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->merge([
                        'autocapitalize' => $getAutocapitalize(),
                        'autocomplete' => $getAutocomplete(),
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'id' => $id,
                        'inputmode' => $getInputMode(),
                        'list' => $datalistOptions ? "{$id}-list" : null,
                        'max' => (! $isConcealed) ? $getMaxValue() : null,
                        'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
                        'min' => (! $isConcealed) ? $getMinValue() : null,
                        'minlength' => (! $isConcealed) ? $getMinLength() : null,
                        'placeholder' => $getPlaceholder(),
                        'readonly' => $isReadOnly(),
                        'required' => $isRequired() && (! $isConcealed),
                        'step' => $getStep(),
                        'type' => blank($mask) ? $getType() : 'text',
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
                    ->class([
                        'filament-forms-input block w-full border-none bg-white px-3 py-1.5 text-base shadow-sm outline-none ring-1 transition duration-75 placeholder:text-gray-400 focus:ring-2 disabled:bg-gray-50 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:disabled:bg-gray-900 sm:text-sm sm:leading-6',
                        'rounded-s-lg' => ! ($prefixLabel || $prefixIcon),
                        'rounded-e-lg' => ! ($suffixLabel || $suffixIcon),
                    ])
            }}
        />
    </x-filament-forms::affixes>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
