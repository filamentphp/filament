@props([
    'button' => false,
    'class' => 'w-full flex text-sm leading-tight text-left whitespace-nowrap rounded py-1.5 px-4 transition-colors duration-200 text-white hover:bg-primary-700',
])

@if ($button)
    <button {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@else
    <a {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@endif
