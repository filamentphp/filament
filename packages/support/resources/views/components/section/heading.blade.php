{{-- @deprecated Use the `heading` slot of `<x-filament::section>` instead. --}}

@props([
    'level' => 2,
])

@php
    $level = max(1, (int) $level);
    $headingTag = ($level > 6) ? 'p' : "h{$level}";
@endphp

<{{ $headingTag }}
    {{ $attributes->class(['fi-section-header-heading']) }}
>
    {{ $slot }}
</{{ $headingTag }}>
