@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\View\TablesRenderHook;

    $hasDefaultItems = $part->hasDefaultItems();

    // Custom items are rendered first, so the toolbar can hide itself when none of them rendered anything.
    $itemsHtml = $hasDefaultItems ? null : $part->renderItems();
    $hasHeaderToolbar = $hasDefaultItems ? $table->hasHeaderToolbar() : $part->hasNonBulkItems();
    $hasNonBulkToolbarAction = $hasDefaultItems && $table->hasNonBulkToolbarAction();
    $toolbarActionsCount = ($hasDefaultItems || $part->hasToolbarActionsItem()) ? count($table->getVisibleToolbarActions()) : 0;
    $visibilityMode = $hasDefaultItems
        ? $table->getHeaderToolbarVisibilityMode()
        : ($hasHeaderToolbar ? 'visible' : ($toolbarActionsCount ? 'selection' : 'hidden'));
@endphp

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_BEFORE, scopes: static::class) }}

<div
    @if (! $hasHeaderToolbar) x-cloak @endif
    x-show="@js($hasHeaderToolbar) || @js($hasNonBulkToolbarAction) || (getSelectedRecordsCount() && @js($toolbarActionsCount))"
    wire:key="{{ $this->getId() }}.table.header-toolbar.{{ $visibilityMode }}"
    class="fi-ta-header-toolbar"
>
    {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_START, scopes: static::class) }}

    {{-- Raw PHP instead of `@if`, so Livewire does not inject `<!--[if BLOCK]-->` markers the original toolbar did not have. --}}

    <?php if ($hasDefaultItems) { ?>

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

    {!! $itemsHtml !!}

    <?php } ?>

    {{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_END) }}
</div>

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_AFTER) }}
