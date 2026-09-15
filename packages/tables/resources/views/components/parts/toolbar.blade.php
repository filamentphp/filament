@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\View\TablesRenderHook;

    $hasHeaderToolbar = $table->hasHeaderToolbar();
@endphp

<div
    @if (! $hasHeaderToolbar) x-cloak @endif
    x-show="@js($hasHeaderToolbar) || @js($table->hasNonBulkToolbarAction()) || (getSelectedRecordsCount() && @js(count($table->getVisibleToolbarActions())))"
    wire:key="{{ $this->getId() }}.table.header-toolbar.{{ $table->getHeaderToolbarVisibilityMode() }}"
    class="fi-ta-header-toolbar"
>
    {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_START, scopes: static::class) }}

    {{-- Raw PHP instead of `@if`, so Livewire does not inject `<!--[if BLOCK]-->` markers the original toolbar did not have. --}}
    <?php if ($part->hasDefaultItems()) { ?>
        @php
            [$reorderTrigger, $toolbarActions, $groupingSettings, $search, $filtersTrigger, $columnManager] = $part->getChildSchema()->getComponents();
        @endphp

        <div class="fi-ta-actions fi-align-start fi-wrapped">
            {{ $reorderTrigger }}

            {{ $toolbarActions }}

            {{ $groupingSettings }}
        </div>

        @if ($table->isSearchable() || $table->hasFiltersTrigger() || $table->hasColumnManager())
            <div>
                {{ $search }}

                @if ($table->hasFiltersTrigger() || $table->hasColumnManager())
                    {{ $filtersTrigger }}

                    {{ $columnManager }}
                @endif
            </div>
        @endif
    <?php } else { ?>
        <div class="fi-ta-actions fi-align-start fi-wrapped">
            {!! $table->renderLayout($part->getChildSchema()) !!}
        </div>
    <?php } ?>

    {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_END) }}
</div>
