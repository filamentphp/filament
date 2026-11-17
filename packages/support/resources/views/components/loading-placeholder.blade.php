@props([
    'loadingLabel' => null,
])

@php
    use Filament\Support\Enums\IconSize;
@endphp

<span aria-hidden="true" {{ $attributes->class(['fi-loading-placeholder']) }}>
    {{ \Filament\Support\generate_loading_indicator_html(size: IconSize::Small) }}

    <span>{{ $loadingLabel ?? __('filament::loading.label') }}</span>
</span>
