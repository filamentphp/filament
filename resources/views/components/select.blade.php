@props([
    'modelDirective' => 'wire:model',
    'model' => null,
    'errorKey' => null,
    'extraAttributes' => [],
])

<select
    @if ($model) {{ $modelDirective }}="{{ $model }}" @endif
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 '.($errors->has($errorKey) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300')
    ], $extraAttributes)) }}
>
    {{ $slot }}
</select>
