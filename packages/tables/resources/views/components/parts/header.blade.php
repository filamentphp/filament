@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Actions\HeaderActionsPosition;
    use Filament\Tables\View\TablesRenderHook;

    $header = $table->getHeader();
    $heading = $table->getHeading();
    $description = $table->getDescription();
    $headerActions = $table->getVisibleHeaderActions();
    $headerActionsPosition = $table->getHeaderActionsPosition();
    $headingTag = $table->getHeadingTag();
    $isReordering = $table->isReordering();
@endphp

{{ FilamentView::renderHook(TablesRenderHook::HEADER_BEFORE, scopes: static::class) }}

@if ($header)
    {{ $header }}
@elseif ($heading || $description || ($headerActions && (! $isReordering)))
    <div
        @class([
            'fi-ta-header',
            'fi-ta-header-adaptive-actions-position' => $headerActions && (! $isReordering) && ($headerActionsPosition === HeaderActionsPosition::Adaptive),
        ])
    >
        @if ($heading || $description)
            <div>
                @if ($heading)
                    <{{ $headingTag }} class="fi-ta-header-heading">
                        {{ $heading }}
                    </{{ $headingTag }}>
                @endif

                @if ($description)
                    <p class="fi-ta-header-description">
                        {{ $description }}
                    </p>
                @endif
            </div>
        @endif

        @if ((! $isReordering) && $headerActions)
            <div class="fi-ta-actions fi-align-start fi-wrapped">
                @foreach ($headerActions as $action)
                    {{ $action }}
                @endforeach
            </div>
        @endif
    </div>
@endif

{{ FilamentView::renderHook(TablesRenderHook::HEADER_AFTER, scopes: static::class) }}
