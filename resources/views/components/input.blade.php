@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'maxLength' => null,
    'minLength' => null,
    'name' => null,
    'nameAttribute' => 'name',
])

<input
    @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
    @if ($maxLength) maxlength="{{ $maxLength }}" @endif
    @if ($minLength) minlength="{{ $minLength }}" @endif
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300'),
    ], $extraAttributes)) }}
/>
