<label {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
    <input
        type="{{ $type }}"
        class="form-{{ $type }} dark:bg-gray-900 dark:border-gray-700 h-4 w-4 transition duration-150 ease-in-out 
            @error($name) 
                text-red-500 
            @else 
                text-blue-500 
            @enderror
        "
        @if ($value)
            value="{{ $value }}"
        @endif
        @if ($model)
            wire:model.lazy="{{ $model }}"
        @endif
        @if ($disabled) 
            disabled
        @endif
    >
    <span class="ml-2 text-sm leading-5">{{ __($label) }}</span>
</label>
