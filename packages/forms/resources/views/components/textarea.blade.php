@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributeBag = $getExtraAttributeBag();
    $hasInlineLabel = $hasInlineLabel();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $rows = $getRows();
    $shouldAutosize = $shouldAutosize();
    $statePath = $getStatePath();

    $initialHeight = (($rows ?? 2) * 1.5) + 0.75;
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
    class="fi-fo-textarea-wrp"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
                ->class([
                    'fi-fo-textarea',
                    'fi-autosizable' => $shouldAutosize,
                ])
        "
    >
        <div wire:ignore.self style="height: '{{ $initialHeight . 'rem' }}'">
            <textarea
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('textarea', 'filament/forms') }}"
                x-data="textareaFormComponent({
                            initialHeight: @js($initialHeight),
                            shouldAutosize: @js($shouldAutosize),
                            state: $wire.$entangle('{{ $statePath }}'),
                        })"
                @if ($shouldAutosize)
                    x-intersect.once="resize()"
                    x-on:resize.window="resize()"
                @endif
                x-model="state"
                @if ($isGrammarlyDisabled())
                    data-gramm="false"
                    data-gramm_editor="false"
                    data-enable-grammarly="false"
                @endif
                {{ $getExtraAlpineAttributeBag() }}
                {{
                    $getExtraInputAttributeBag()
                        ->merge([
                            'autocomplete' => $getAutocomplete(),
                            'autofocus' => $isAutofocused(),
                            'cols' => $getCols(),
                            'disabled' => $isDisabled,
                            'id' => $getId(),
                            'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
                            'minlength' => (! $isConcealed) ? $getMinLength() : null,
                            'placeholder' => $getPlaceholder(),
                            'readonly' => $isReadOnly(),
                            'required' => $isRequired() && (! $isConcealed),
                            'rows' => $rows,
                            $applyStateBindingModifiers('wire:model') => $statePath,
                        ], escape: false)
                }}
            ></textarea>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
