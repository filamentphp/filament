@unless ($column->url)
    <span>{{ $column->getValue($record) }}</span>
@else
    <a
        href="{{ $column->getUrl($record) }}"
        class="text-secondary-500 underline hover:text-secondary-700 transition-colors duration-200"
    >
        {{ $column->getValue($record) }}
    </a>
@endunless
