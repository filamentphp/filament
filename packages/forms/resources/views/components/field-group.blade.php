@props([
    'errorKey' => null,
    'for' => null,
    'helpMessage' => null,
    'hint' => null,
    'label' => null,
    'labelPrefix' => null,
    'required' => false,
    'columnSpan' => 1,
])

@php
    $columnSpanClass = [
        '',
        'lg:col-span-1',
        'lg:col-span-2',
        'lg:col-span-3',
        'lg:col-span-4',
        'lg:col-span-5',
        'lg:col-span-6',
        'lg:col-span-7',
        'lg:col-span-8',
        'lg:col-span-9',
        'lg:col-span-10',
        'lg:col-span-11',
        'lg:col-span-12',
    ][$columnSpan];
@endphp

<div class="flex relative {{ $columnSpanClass }}">
    <div class="w-full space-y-2">
        @if ($label || $hint)
            <div class="flex items-center justify-between space-x-2">
                <div class="flex items-center space-x-3">
                    {{ $labelPrefix }}

                    @if ($label)
                        <label for="{{ $for }}" class="text-sm font-medium leading-tight">
                            {{ __($label) }}

                            @if ($required)
                                <sup class="font-medium text-danger-700">*</sup>
                            @endif
                        </label>
                    @endif
                </div>

                @if ($hint)
                    <div class="font-mono text-xs leading-tight text-gray-500">
                        {!! Str::of(__($hint))->markdown() !!}
                    </div>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($errorKey)
            @error($errorKey)
                <span class="block text-sm leading-tight text-danger-700">
                    {{ $message }}
                </span>
            @enderror
        @endif

        @if ($helpMessage)
            <div class="text-xs font-normal leading-tight text-gray-500">
                {!! Str::of(__($helpMessage))->markdown() !!}
            </div>
        @endif
    </div>
</div>
