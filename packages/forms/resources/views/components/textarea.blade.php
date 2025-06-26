@php
    use Filament\Support\Facades\FilamentView;

    $hasInlineLabel = $hasInlineLabel();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $rows = $getRows();
    $shouldAutosize = $shouldAutosize();
    $statePath = $getStatePath();

    $initialHeight = (($rows ?? 2) * 1.5) + 0.75;
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
>
    <x-slot
        name="label"
        @class([
            'sm:pt-1.5' => $hasInlineLabel,
        ])
    >
        {{ $getLabel() }}
    </x-slot>

    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['fi-fo-textarea overflow-hidden'])
        "
    >
        <div wire:ignore.self style="height: '{{ $initialHeight . 'rem' }}'">
            <textarea
                @if (FilamentView::hasSpaMode())
                    {{-- format-ignore-start --}}x-load="visible || event (ax-modal-opened)"{{-- format-ignore-end --}}
                @else
                    x-load
                @endif
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
                        ->class([
                            'block h-full w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6',
                            'resize-none' => $shouldAutosize,
                        ])
                }}
            ></textarea>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
