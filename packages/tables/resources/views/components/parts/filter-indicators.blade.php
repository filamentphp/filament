@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Filters\Indicator;
    use Filament\Tables\View\TablesRenderHook;

    $filterIndicators = $table->getFilterIndicators();
@endphp

@if ($filterIndicators)
    @if (filled($filterIndicatorsView = FilamentView::renderHook(TablesRenderHook::FILTER_INDICATORS, scopes: static::class, data: ['filterIndicators' => $filterIndicators])))
        {{ $filterIndicatorsView }}
    @else
        <div class="fi-ta-filter-indicators">
            <div>
                <span class="fi-ta-filter-indicators-label">
                    {{ __('filament-tables::table.filters.indicator') }}
                </span>

                <div
                    class="fi-ta-filter-indicators-badges-ctn"
                    role="list"
                >
                    @foreach ($filterIndicators as $indicator)
                        @php
                            $indicatorColor = $indicator->getColor();
                        @endphp

                        <x-filament::badge
                            :color="$indicatorColor"
                            role="listitem"
                        >
                            {{ $indicator->getLabel() }}

                            @if ($indicator->isRemovable())
                                @php
                                    $indicatorRemoveLivewireClickHandler = $indicator->getRemoveLivewireClickHandler();
                                @endphp

                                <x-slot
                                    name="deleteButton"
                                    :label="__('filament-tables::table.filters.actions.remove.label')"
                                    :wire:click="$indicatorRemoveLivewireClickHandler"
                                    wire:loading.attr="disabled"
                                    wire:target="removeTableFilter"
                                ></x-slot>
                            @endif
                        </x-filament::badge>
                    @endforeach
                </div>
            </div>

            @if (collect($filterIndicators)->contains(fn (Indicator $indicator): bool => $indicator->isRemovable()))
                {{ $table->getFiltersRemoveAllAction() }}
            @endif
        </div>
    @endif
@endif
