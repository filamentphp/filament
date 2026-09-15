@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Support\Enums\IconSize;

    $headingTag = $table->getSecondLevelHeadingTag();
@endphp

@if ($table->hasEmptyState())
    @if ($emptyState = $table->getEmptyState())
        {{ $emptyState }}
    @else
        <div class="fi-ta-empty-state" role="status">
            <div class="fi-ta-empty-state-content">
                <div class="fi-ta-empty-state-icon-bg">
                    {{ \Filament\Support\generate_icon_html($table->getEmptyStateIcon(), size: IconSize::Large) }}
                </div>

                <{{ $headingTag }}
                    class="fi-ta-empty-state-heading"
                >
                    {{ $table->getEmptyStateHeading() }}
                </{{ $headingTag }}>

                @if (filled($emptyStateDescription = $table->getEmptyStateDescription()))
                    <p class="fi-ta-empty-state-description">
                        {{ $emptyStateDescription }}
                    </p>
                @endif

                @if ($emptyStateActions = array_filter(
                         $table->getEmptyStateActions(),
                         fn (Action | ActionGroup $action): bool => $action->isVisible(),
                     ))
                    <div
                        class="fi-ta-actions fi-align-center fi-wrapped"
                    >
                        @foreach ($emptyStateActions as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
