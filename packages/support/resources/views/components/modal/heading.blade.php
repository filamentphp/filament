@props([
    'level' => 2,
])

@php
    $level = max(1, (int) $level);
    $headingTag = ($level > 6) ? 'p' : "h{$level}";
@endphp

<{{ $headingTag }}
    {{ $attributes->class(['fi-modal-heading']) }}
>
    {{ $slot }}
</{{ $headingTag }}>
