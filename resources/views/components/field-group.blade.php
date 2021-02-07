@props([
    'errorKey' => null,
    'for' => null,
    'help' => null,
    'hint' => null,
    'label' => null,
    'labelPrefix' => null,
    'required' => false,
])

<div class="space-y-2">
    @if ($label || $hint)
        <div class="flex items-center justify-between space-x-2">
            @if ($label)
                <x-filament::label :for="$for">
                    {{ $labelPrefix }}

                    {{ __($label) }}

                    @if ($required)
                        <sup class="font-medium text-red-700">*</sup>
                    @endif
                </x-filament::label>
            @endif

            @if ($hint)
                <x-filament::hint>
                    @markdown(__($hint))
                </x-filament::hint>
            @endif
        </div>
    @endif

    {{ $slot }}

    @if ($errorKey)
        <x-filament::error :field="$errorKey" />
    @endif

    @if ($help)
        <x-filament::help>
            @markdown(__($help))
        </x-filament::help>
    @endif
</div>
