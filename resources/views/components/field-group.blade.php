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
            <div class="flex space-x-2">
                {{ $labelPrefix }}

                @if ($label)
                    <x-filament::label :for="$for">
                        {{ $label }}

                        @if ($required)
                            <sup class="font-medium text-red-700">*</sup>
                        @endif
                    </x-filament::label>
                @endif
            </div>

            @if ($hint)
                <x-filament::hint>
                    @markdown($hint)
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
            @markdown($help)
        </x-filament::help>
    @endif
</div>
