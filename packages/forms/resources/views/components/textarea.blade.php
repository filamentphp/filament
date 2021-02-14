@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'maxLength' => null,
    'minLength' => null,
    'name' => null,
    'nameAttribute' => 'name',
])

<textarea
    @if ($minLength) minlength="{{ $minLength }}" @endif
    @if ($maxLength) maxlength="{{ $maxLength }}" @endif
    @if ($name) {{ $nameAttribute }}="{{ $name }}" @endif
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300'),
    ], $extraAttributes)) }}
>
    {{ $slot }}
</textarea>
