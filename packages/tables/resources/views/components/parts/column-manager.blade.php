@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Enums\ColumnManagerLayout;
    use Filament\Tables\View\TablesRenderHook;

    $columnManagerTriggerAction = $table->getColumnManagerTriggerAction();
    $hasReorderableColumns = $table->hasReorderableColumns();
    $hasToggleableColumns = $table->hasToggleableColumns();
@endphp

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE, scopes: static::class) }}

@if ($table->hasColumnManager())
    @php
        $columnManagerMaxHeight = $table->getColumnManagerMaxHeight();
        $columnManagerWidth = $table->getColumnManagerWidth();
        $columnManagerColumns = $table->getColumnManagerColumns();
    @endphp

    @if (($table->getColumnManagerLayout() === ColumnManagerLayout::Modal) || $columnManagerTriggerAction->isModalSlideOver())
        @php
            $columnManagerTriggerActionModalAlignment = $columnManagerTriggerAction->getModalAlignment();
            $columnManagerTriggerActionIsModalAutofocused = $columnManagerTriggerAction->isModalAutofocused();
            $columnManagerTriggerActionHasModalCloseButton = $columnManagerTriggerAction->hasModalCloseButton();
            $columnManagerTriggerActionIsModalClosedByClickingAway = $columnManagerTriggerAction->isModalClosedByClickingAway();
            $columnManagerTriggerActionIsModalClosedByEscaping = $columnManagerTriggerAction->isModalClosedByEscaping();
            $columnManagerTriggerActionModalDescription = $columnManagerTriggerAction->getModalDescription();
            $columnManagerTriggerActionExtraModalWindowAttributeBag = $columnManagerTriggerAction->getExtraModalWindowAttributeBag();
            $columnManagerTriggerActionExtraModalOverlayAttributeBag = $columnManagerTriggerAction->getExtraModalOverlayAttributeBag();
            $columnManagerTriggerActionVisibleModalFooterActions = $columnManagerTriggerAction->getVisibleModalFooterActions();
            $columnManagerTriggerActionModalFooterActionsAlignment = $columnManagerTriggerAction->getModalFooterActionsAlignment();
            $columnManagerTriggerActionModalHeading = $columnManagerTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.column_manager.heading');
            $columnManagerTriggerActionModalIcon = $columnManagerTriggerAction->getModalIcon();
            $columnManagerTriggerActionModalIconColor = $columnManagerTriggerAction->getModalIconColor();
            $columnManagerTriggerActionIsModalSlideOver = $columnManagerTriggerAction->isModalSlideOver();
            $columnManagerTriggerActionModalSlideOverPosition = $columnManagerTriggerAction->getModalSlideOverPosition();
            $columnManagerTriggerActionIsModalFooterSticky = $columnManagerTriggerAction->isModalFooterSticky();
            $columnManagerTriggerActionIsModalHeaderSticky = $columnManagerTriggerAction->isModalHeaderSticky();
        @endphp

        <x-filament::modal
            :alignment="$columnManagerTriggerActionModalAlignment"
            :autofocus="$columnManagerTriggerActionIsModalAutofocused"
            :close-button="$columnManagerTriggerActionHasModalCloseButton"
            :close-by-clicking-away="$columnManagerTriggerActionIsModalClosedByClickingAway"
            :close-by-escaping="$columnManagerTriggerActionIsModalClosedByEscaping"
            :description="$columnManagerTriggerActionModalDescription"
            :extra-modal-window-attribute-bag="$columnManagerTriggerActionExtraModalWindowAttributeBag"
            :extra-modal-overlay-attribute-bag="$columnManagerTriggerActionExtraModalOverlayAttributeBag"
            :footer-actions="$columnManagerTriggerActionVisibleModalFooterActions"
            :footer-actions-alignment="$columnManagerTriggerActionModalFooterActionsAlignment"
            :heading="$columnManagerTriggerActionModalHeading"
            :icon="$columnManagerTriggerActionModalIcon"
            :icon-color="$columnManagerTriggerActionModalIconColor"
            :slide-over="$columnManagerTriggerActionIsModalSlideOver"
            :slide-over-position="$columnManagerTriggerActionModalSlideOverPosition"
            :sticky-footer="$columnManagerTriggerActionIsModalFooterSticky"
            :sticky-header="$columnManagerTriggerActionIsModalHeaderSticky"
            :width="$columnManagerWidth"
            :wire:key="$this->getId() . '.table.column-manager'"
            class="fi-ta-col-manager-modal"
        >
            <x-slot name="trigger">
                {{ $columnManagerTriggerAction }}
            </x-slot>

            {{ $columnManagerTriggerAction->getModalContent() }}

            <div
                x-data="filamentTableColumnManager({
                            columns: $wire.entangle('tableColumns'),
                            isLive: {{ $table->getColumnManagerApplyAction()->isVisible() ? 'false' : 'true' }},
                        })"
                x-on:apply-table-column-manager.window="applyTableColumnManager()"
                x-on:reset-table-column-manager.window="resetDeferredColumns()"
                class="fi-ta-col-manager"
            >
                <x-filament-tables::column-manager.content
                    :columns="$columnManagerColumns"
                    :has-reorderable-columns="$hasReorderableColumns"
                    :has-toggleable-columns="$hasToggleableColumns"
                    :reorder-animation-duration="$table->getReorderAnimationDuration()"
                />
            </div>

            {{ $columnManagerTriggerAction->getModalContentFooter() }}
        </x-filament::modal>
    @else
        <x-filament::dropdown
            :max-height="$columnManagerMaxHeight"
            placement="bottom-end"
            shift
            :flip="false"
            :width="$columnManagerWidth"
            :wire:key="$this->getId() . '.table.column-manager'"
            class="fi-ta-col-manager-dropdown"
        >
            <x-slot name="trigger">
                {{ $columnManagerTriggerAction }}
            </x-slot>

            <x-filament-tables::column-manager
                :apply-action="$table->getColumnManagerApplyAction()"
                :columns="$columnManagerColumns"
                :reset-action-position="$table->getColumnManagerResetActionPosition()"
                :has-reorderable-columns="$hasReorderableColumns"
                :has-toggleable-columns="$hasToggleableColumns"
                :heading-tag="$table->getSecondLevelHeadingTag()"
                :reorder-animation-duration="$table->getReorderAnimationDuration()"
            />
        </x-filament::dropdown>
    @endif
@endif

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER, scopes: static::class) }}
