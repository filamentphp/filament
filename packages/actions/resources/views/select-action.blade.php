@php
    $id = $getId();
    $isDisabled = $isDisabled();
@endphp

<div class="fi-ac-select-action">
    <label for="{{ $id }}" class="sr-only">
        {{ $getLabel() }}
    </label>

    <x-filament::input.wrapper :disabled="$isDisabled">
        <x-filament::input.select
            :disabled="$isDisabled"
            :id="$id"
            :wire:model.live="$getName()"
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
    </x-filament::input.wrapper>
</div>
