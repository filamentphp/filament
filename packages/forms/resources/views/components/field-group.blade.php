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

<div class="flex {{ $columnSpanClass }}">
    <div class="space-y-2 w-full">
        @if ($label || $hint)
            <div class="flex items-center justify-between space-x-2">
                <div class="flex space-x-2">
                    {{ $labelPrefix }}

                    @if ($label)
                        <label for="{{ $for }}" class="text-sm leading-tight font-medium">
                            {{ $label }}

                            @if ($required)
                                <sup class="font-medium text-danger-700">*</sup>
                            @endif
                        </label>
                    @endif
                </div>

                @if ($hint)
                    <div class="text-xs leading-tight text-gray-500 font-mono">
                        {!! Str::of($hint)->markdown() !!}
                    </div>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($errorKey)
            @error($errorKey)
                <span class="block text-danger-700 text-sm leading-tight">
                    {{ $message }}
                </span>
            @enderror
        @endif

        @if ($helpMessage)
            <div class="text-xs font-normal leading-tight text-gray-500">
                {!! Str::of($helpMessage)->markdown() !!}
            </div>
        @endif
    </div>
</div>
