@php
    $datalistOptions = $getDatalistOptions();
    $hasMask = $hasMask();
    $id = $getId();
    $isConcealed = $isConcealed();
    $statePath = $getStatePath();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-text-input-component"
        :attributes="$getExtraAttributeBag()"
    >
        <input
            @if ($hasMask)
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('text-input', 'filament/forms') }}"
                x-data="textInputFormComponent({
                    getMaskOptionsUsing: (IMask) => ({{ $getJsonMaskConfiguration() }}),
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')', lazilyEntangledModifiers: ['defer']) }},
                })"
                wire:ignore
                @if ($isDebounced()) x-on:input.debounce.{{ $getDebounce() }}="$wire.$refresh" @endif
                @if ($isLazy()) x-on:blur="$wire.$refresh" @endif
                {{ $getExtraAlpineAttributeBag() }}
            @else
                x-data="{}"
            @endif
            x-bind:class="{
                'border-gray-300 dark:border-gray-600': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                'border-danger-600 ring-danger-600': (@js($statePath) in $wire.__instance.serverMemo.errors),
            }"
            {{
                $getExtraInputAttributeBag()
                    ->merge([
                        'autocapitalize' => $getAutocapitalize(),
                        'autocomplete' => $getAutocomplete(),
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'dusk' => "filament.forms.{$statePath}",
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
                        'type' => $hasMask ? 'text' : $getType(),
                        $applyStateBindingModifiers('wire:model') => (! $hasMask) ? $statePath : null,
                    ], escape: false)
                    ->class([
                        'block w-full transition duration-75 shadow-sm outline-none sm:text-sm focus:border-primary-500 focus:relative focus:z-[1] focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500',
                        'rounded-l-lg' => ! ($prefixLabel || $prefixIcon),
                        'rounded-r-lg' => ! ($suffixLabel || $suffixIcon),
                    ])
            }}
        />
    </x-filament::input.affixes>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
