@php
    $disabled = $isDisabled();
    $id = $getId();
@endphp

<div class="filament-actions-select-action">
    <label for="{{ $id }}" class="sr-only">
        {{ $getLabel() }}
    </label>

    <x-filament-forms::affixes :disabled="$disabled">
        <x-filament::input.select
            :disabled="$disabled"
            :id="$id"
            :wire:model="$getName()"
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
    </x-filament-forms::affixes>
</div>
