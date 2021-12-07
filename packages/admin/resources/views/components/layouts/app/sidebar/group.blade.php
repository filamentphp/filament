@props([
    'label' => null,
])

<li>
    @if ($label)
        <p class="font-bold uppercase text-gray-600 text-xs tracking-wider">
            {{ $label }}
        </p>
    @endif

    <ul @class([
        'text-sm space-y-1 -mx-3',
        'mt-2' => $label,
    ])>
        {{ $slot }}
    </ul>
</li>
