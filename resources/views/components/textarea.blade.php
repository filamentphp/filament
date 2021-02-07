@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'maxLength' => null,
    'minLength' => null,
    'nameAttribute' => 'name',
    'name' => null,
])

<textarea
    @if ($minLength) minlength="{{ $minLength }}" @endif
    @if ($maxLength) maxlength="{{ $maxLength }}" @endif
    @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm border-gray-400 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300'),
    ], $extraAttributes)) }}
>
    {{ $slot }}
</textarea>
