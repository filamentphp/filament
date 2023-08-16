@php
    $id = $getId();
    $isDisabled = $isDisabled();
    $attributes = $getExtraAttributeBag();
@endphp

<div class="fi-ac-select-action">
    <label for="{{ $id }}" class="sr-only">
        {{ $getLabel() }}
    </label>

    <x-filament::input.select
        :disabled="$isDisabled"
        :id="$id"
        :attributes="
                \Filament\Support\prepare_inherited_attributes(
                    $attributes
                )
            "
    >
        @if (($placeholder = $getPlaceholder()) !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($getOptions() as $value => $label)
            <option value="{{ $value }}">
                {{ $label }}
            </option>
        @endforeach
    </x-filament::input.select>
</div>
