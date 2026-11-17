@props([
    'columnSpan' => [],
    'columnStart' => [],
    'height' => null,
    'loadingPlaceholder' => null,
    'loadingLabel' => null,
])

@php
    use Filament\Support\View\ComponentAttributeBag;
@endphp

<div
    role="status"
    aria-busy="true"
    {{
        ($attributes ?? new ComponentAttributeBag)
            ->gridColumn($columnSpan, $columnStart)
            ->class(['fi-section fi-loading-section'])
            ->style(['height: ' . e($height ?? '8rem')])
    }}
>
    <span class="fi-sr-only">
        {{ $loadingLabel ?? __('filament::loading.label') }}
    </span>

    <div aria-hidden="true" class="fi-loading-placeholder-content">
        @if ($loadingPlaceholder !== null)
            {{ $loadingPlaceholder }}
        @else
            <x-filament::loading-placeholder :loading-label="$loadingLabel" />
        @endif
    </div>
</div>
