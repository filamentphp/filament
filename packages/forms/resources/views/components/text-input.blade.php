@php
    use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
    use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;

    $fieldWrapperView = $getFieldWrapperView();
    $datalistOptions = $getDatalistOptions();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $extraAttributeBag = $getExtraAttributeBag();
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $isPasswordRevealable = $isPasswordRevealable();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $mask = $getMask();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixIconColor = $getPrefixIconColor();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixIconColor = $getSuffixIconColor();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();

    if ($isPasswordRevealable) {
        $xData = '{ isPasswordRevealed: false }';
    } elseif (count($extraAlpineAttributes) || filled($mask)) {
        $xData = '{}';
    } else {
        $xData = null;
    }

    if ($isPasswordRevealable) {
        $type = null;
    } elseif (filled($mask)) {
        $type = 'text';
    } else {
        $type = $getType();
    }

    $inputAttributes = $getExtraInputAttributeBag()
        ->merge($extraAlpineAttributes, escape: false)
        ->merge([
            'autocapitalize' => $getAutocapitalize(),
            'autocomplete' => $getAutocomplete(),
            'autofocus' => $isAutofocused(),
            'disabled' => $isDisabled,
            'id' => $id,
            'inlinePrefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
            'inlineSuffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
            'inputmode' => $getInputMode(),
            'list' => $datalistOptions ? $id . '-list' : null,
            'max' => (! $isConcealed) ? $getMaxValue() : null,
            'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
            'min' => (! $isConcealed) ? $getMinValue() : null,
            'minlength' => (! $isConcealed) ? $getMinLength() : null,
            'placeholder' => $getPlaceholder(),
            'readonly' => $isReadOnly(),
            'required' => $isRequired() && (! $isConcealed),
            'step' => $getStep(),
            'type' => $type,
            $applyStateBindingModifiers('wire:model') => $statePath,
            'x-bind:type' => $isPasswordRevealable ? 'isPasswordRevealed ? \'text\' : \'password\'' : null,
            'x-mask' . ($mask instanceof \Filament\Support\RawJs ? ':dynamic' : '') => filled($mask) ? $mask : null,
        ], escape: false)
        ->class([
            'fi-revealable' => $isPasswordRevealable,
        ]);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
    class="fi-fo-text-input-wrp"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$prefixIconColor"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$suffixIconColor"
        :valid="! $errors->has($statePath)"
        :x-data="$xData"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
                ->class(['fi-fo-text-input'])
        "
    >
    <div data-input-root class="fi-fo-text-input flex items-center">
        <input
            {{
                $inputAttributes->class([
                    'fi-input',
                    'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                    'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                ])
            }}
        />
        <button
            type="button"
            x-data="{ shown: false }"
            x-on:click="shown = !shown; $dispatch('filament-forms::password-visibility-toggled', { shown })"
            x-bind:aria-pressed="shown.toString()"
            x-bind:aria-label="shown ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
            x-bind:title="shown ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
            class="fi-fo-text-input-toggle inline-flex items-center justify-center"
        >
            <template x-if="!shown">
                <x-filament::icon icon="heroicon-m-eye" class="fi-fo-text-input-icon" />
            </template>
            <template x-if="shown">
                <x-filament::icon icon="heroicon-m-eye-slash" class="fi-fo-text-input-icon" />
            </template>
            <span x-init="
                const input = $el.closest('[data-input-root]')?.querySelector('input');
                if (input) {
                    $watch('shown', value => { input.type = value ? 'text' : 'password' })
                }
            " class="sr-only"></span>
        </button>
        </div>
    </x-filament::input.wrapper>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}"></option>
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
