@props([
    'id',
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => false,
    'helperText' => null,
    'hint' => null,
    'required' => false,
    'statePath',
])

<div {{ $attributes }}>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div class="space-y-2">
        @if (($label && ! $labelSrOnly) || $hint)
            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                @if ($label && ! $labelSrOnly)
                    <label
                        for="{{ $id }}"
                        class="inline-flex items-center space-x-3"
                    >
                        {{ $labelPrefix }}

                        <span @class([
                            'text-sm font-medium leading-4',
                            'text-gray-700' => ! $errors->has($statePath),
                            'text-danger-700' => $errors->has($statePath),
                        ])>
                            {{ $label }}

                            @if ($required)
                                <sup class="font-medium text-danger-700">*</sup>
                            @endif
                        </span>
                    </label>
                @endif

                @if ($hint)
                    <div class="font-mono text-xs leading-tight text-gray-500">
                        {!! Str::of($hint)->markdown() !!}
                    </div>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($errors->has($statePath))
            <p class="text-sm text-danger-600">
                {{ $errors->first($statePath) }}
            </p>
        @endif

        @if ($helperText)
            <div class="text-sm text-gray-600">
                {!! Str::of($helperText)->markdown() !!}
            </div>
        @endif
    </div>
</div>
