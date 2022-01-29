@props([
    'details' => [],
    'title',
    'url',
])

<li {{ $attributes->class(['filament-global-search-result']) }}>
    <a href="{{ $url }}" class="relative block px-6 py-4 focus:bg-gray-500/5 hover:bg-gray-500/5 focus:ring-1 focus:ring-gray-300">
        <p class="font-medium">{{ $title }}</p>

        <p class="text-sm space-x-2 font-medium text-gray-500">
            @foreach ($details as $label => $value)
                <span>
                    <span class="font-medium text-gray-700">{{ $label }}:</span> <span>{{ $value }}</span>
                </span>
            @endforeach
        </p>
    </a>
</li>
