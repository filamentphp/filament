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
            x-bind:class="{
                'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500':
                    ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400':
                    @js($statePath) in $wire.__instance.serverMemo.errors,
            }"
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
                        'filament-forms-input block w-full shadow-sm outline-none transition duration-75 placeholder:text-gray-500 focus:relative focus:z-[1] focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white sm:text-sm',
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
