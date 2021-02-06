@props([
    'errorKey' => null,
    'extraAttributes' => [],
    'maxLength' => null,
    'minLength' => null,
    'modelDirective' => 'wire:model',
    'name' => null,
])

<input
    @if ($name) {{ $modelDirective }}="{{ $name }}" @endif
    @if ($maxLength) maxlength="{{ $maxLength }}" @endif
    @if ($minLength) minlength="{{ $minLength }}" @endif
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm border-gray-400 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ' . ($errors->has($errorKey) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300')
    ], $extraAttributes)) }}
/>
