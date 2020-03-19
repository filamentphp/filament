<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if ($label || $hint)
        <div class="flex justify-between mb-1">
            @if ($label)
                <label @if ($id) for="{{ $id }}" @endif class="block text-sm font-medium leading-5 text-gray-700">
                    {{ __($label) }}
                    @if ($required)
                        <sup class="text-red-600">*</sup>
                        <span class="sr-only">(required)</span>
                    @endif
                </label>
            @endif
            @if ($hint)
                <span class="text-xs leading-5 text-gray-500">{{ $hint }}</span>
            @endif
        </div>
    @endif 
    {{ $slot }}
    @if ($info)
        <p class="mt-1 text-xs leading-5 text-gray-500">{{ $info }}</p>
    @endif
</div>