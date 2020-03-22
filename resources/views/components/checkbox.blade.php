<label class="inline-flex items-center">
    <input type="{{ $type }}" name={{ $name }} {{ $checked ? 'checked' : '' }} {{ $attributes->merge(['class' => 'form-checkbox h-4 w-4 text-blue-500 transition duration-150 ease-in-out']) }} />
    <span class="ml-2 text-sm leading-5 text-gray-700">{{ __($label) }}</span>
</label>