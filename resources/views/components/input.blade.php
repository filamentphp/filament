@props([
    'type' => 'text',
    'modelDirective' => 'wire:model',
    'name' => null,
    'errorKey' => null,
    'extraAttributes' => [],
    'required' => false,
    'minLength' => null,
    'maxLength' => null,
])

<input
    type="{{ $type }}"
    @if ($name) {{ $modelDirective }}="{{ $name }}" @endif
    @if ($required) required @endif
    @if ($minLength) minlength="{{ $minLength }}" @endif
    @if ($maxLength) maxlength="{{ $maxLength }}" @endif
    {{ $attributes->merge(array_merge([
        'class' => 'block w-full rounded shadow-sm border-gray-400 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 '.($errors->has($errorKey) ? 'border-red-600 motion-safe:animate-shake' : 'border-gray-300')
    ], $extraAttributes)) }}
/>
