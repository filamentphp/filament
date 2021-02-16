@php
    $primaryClasses = $column->primary ? 'font-medium' : null;
@endphp

@unless ($column->url)
    <span class="{{ $primaryClasses }}">{{ $column->getValue($record) }}</span>
@else
    <a
        href="{{ $column->getUrl($record) }}"
        class="{{ $primaryClasses }} hover:underline hover:text-secondary-700 transition-colors duration-200"
    >
        {{ $column->getValue($record) }}
    </a>
@endunless
