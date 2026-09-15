@props([
    'applyAction',
    'form',
    'headingTag' => 'h3',
    'resetAction',
    'resetActionPosition' => null,
])

@php
    use Filament\Tables\Enums\FiltersResetActionPosition;

    $resetActionPosition ??= FiltersResetActionPosition::Header;
@endphp

<div {{ $attributes->class(['fi-ta-filters']) }}>
    <div class="fi-ta-filters-header">
        <{{ $headingTag }} class="fi-ta-filters-heading">
            {{ __('filament-tables::table.filters.heading') }}
        </{{ $headingTag }}>

        @if ($resetActionPosition === FiltersResetActionPosition::Header)
            <div>
                {{ $resetAction->link() }}
            </div>
        @endif
    </div>

    {{ $form }}

    @if ($applyAction->isVisible() || $resetActionPosition === FiltersResetActionPosition::Footer)
        <div class="fi-ta-filters-actions-ctn">
            @if ($applyAction->isVisible())
                {{ $applyAction }}
            @endif

            @if ($resetActionPosition === FiltersResetActionPosition::Footer)
                {{ $resetAction->button() }}
            @endif
        </div>
    @endif
</div>
