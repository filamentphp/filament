@props([
    'length' => 6,
])

@php
    $inputAttributes = $input?->attributes ?? new \Illuminate\View\ComponentAttributeBag;
    $isDisabled = filter_var($inputAttributes->get('disabled'), FILTER_VALIDATE_BOOLEAN);
    $isReadOnly = filter_var($inputAttributes->get('readonly'), FILTER_VALIDATE_BOOLEAN);
@endphp

<div
    x-data="filamentOneTimeCodeInput"
    x-modelable="state"
    role="group"
    {{ $attributes->class(['fi-one-time-code-input-ctn']) }}
>
    @foreach (range(0, $length - 1) as $index)
        @if ($index === 0)
            <input
                autocomplete="one-time-code"
                inputmode="numeric"
                type="text"
                {{ $inputAttributes->class(['fi-one-time-code-input-digit']) }}
            />
        @else
            <input
                aria-label="{{ __('filament::components/input/one-time-code.aria_label', ['position' => $index + 1, 'count' => $length]) }}"
                autocomplete="off"
                inputmode="numeric"
                type="text"
                @disabled($isDisabled)
                @readonly($isReadOnly)
                class="fi-one-time-code-input-digit"
            />
        @endif
    @endforeach
</div>
