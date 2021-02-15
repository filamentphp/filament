@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'maxLength' => null,
    'minLength' => null,
    'name' => null,
    'nameAttribute' => 'name',
])

<input
    {!! $name ? "{$nameAttribute}=\"{$name}\"" : null !!}
    {!! $maxLength ? "maxlength=\"{$maxLength}\"" : null !!}
    {!! $minLength ? "minlength=\"{$minLength}\"" : null !!}
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300'),
    ], $extraAttributes)) }}
/>
