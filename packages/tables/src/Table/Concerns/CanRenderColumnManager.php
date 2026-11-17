<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\ColumnManagerResetActionPosition;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Support\Js;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_icon_html;

trait CanRenderColumnManager
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnManagerTriggerHtml(): string
    {
        ob_start(); ?>
        <?php if ($this->hasColumnManager()) { ?>
            <?php
                $columnManagerMaxHeight = $this->getColumnManagerMaxHeight();
            $columnManagerWidth = $this->getColumnManagerWidth();
            $columnManagerColumns = $this->getColumnManagerColumns();
            $columnManagerApplyAction = $this->getColumnManagerApplyAction();
            $columnManagerTriggerAction = $this->getColumnManagerTriggerAction();
            ?>

            <?php if (($this->getColumnManagerLayout() === ColumnManagerLayout::Modal) || $columnManagerTriggerAction->isModalSlideOver()) { ?>
                <?php
                    $columnManagerTriggerActionModalAlignment = $columnManagerTriggerAction->getModalAlignment();
                $columnManagerTriggerActionIsModalAutofocused = $columnManagerTriggerAction->isModalAutofocused();
                $columnManagerTriggerActionIsModalClickThrough = $columnManagerTriggerAction->isModalClickThrough();
                $columnManagerTriggerActionHasModalCloseButton = $columnManagerTriggerAction->hasModalCloseButton();
                $columnManagerTriggerActionIsModalClosedByClickingAway = $columnManagerTriggerAction->isModalClosedByClickingAway();
                $columnManagerTriggerActionIsModalClosedByEscaping = $columnManagerTriggerAction->isModalClosedByEscaping();
                $columnManagerTriggerActionModalDescription = $columnManagerTriggerAction->getModalDescription();
                $columnManagerTriggerActionModalEyebrow = $columnManagerTriggerAction->getModalEyebrow();
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
                ?>

                <?php $modalArguments = [
                    'alignment' => $columnManagerTriggerActionModalAlignment,
                    'autofocus' => $columnManagerTriggerActionIsModalAutofocused,
                    'clickThrough' => $columnManagerTriggerActionIsModalClickThrough,
                    'closeButton' => $columnManagerTriggerActionHasModalCloseButton,
                    'closeByClickingAway' => $columnManagerTriggerActionIsModalClosedByClickingAway,
                    'closeByEscaping' => $columnManagerTriggerActionIsModalClosedByEscaping,
                    'description' => $columnManagerTriggerActionModalDescription,
                    'eyebrow' => $columnManagerTriggerActionModalEyebrow,
                    'extraModalWindowAttributeBag' => $columnManagerTriggerActionExtraModalWindowAttributeBag,
                    'extraModalOverlayAttributeBag' => $columnManagerTriggerActionExtraModalOverlayAttributeBag,
                    'footerActions' => $columnManagerTriggerActionVisibleModalFooterActions,
                    'footerActionsAlignment' => $columnManagerTriggerActionModalFooterActionsAlignment,
                    'heading' => $columnManagerTriggerActionModalHeading,
                    'icon' => $columnManagerTriggerActionModalIcon,
                    'iconColor' => $columnManagerTriggerActionModalIconColor,
                    'slideOver' => $columnManagerTriggerActionIsModalSlideOver,
                    'slideOverPosition' => $columnManagerTriggerActionModalSlideOverPosition,
                    'stickyFooter' => $columnManagerTriggerActionIsModalFooterSticky,
                    'stickyHeader' => $columnManagerTriggerActionIsModalHeaderSticky,
                    'width' => $columnManagerWidth,
                    'attributes' => new ComponentAttributeBag([
                        'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.column-manager'),
                        'class' => 'fi-ta-col-manager-modal',
                    ]),
                ];
                ob_start(); ?>
                     <?php $modalTrigger = new ComponentSlot(trim(e($columnManagerTriggerAction))); ?>

                    <?= e($columnManagerTriggerAction->getModalContent()); ?>

                    <div
                        x-data="filamentTableColumnManager({
                                    columns: $wire.entangle('tableColumns'),
                                    isLive: <?= e($columnManagerApplyAction->isVisible() ? 'false' : 'true'); ?>,
                                })"
                        x-on:apply-table-column-manager.window="applyTableColumnManager()"
                        x-on:reset-table-column-manager.window="resetDeferredColumns()"
                        class="fi-ta-col-manager"
                    >
                        <?= $this->generateColumnManagerContentHtml(
                            hasReorderableColumns: $this->hasReorderableColumns(),
                            hasToggleableColumns: $this->hasToggleableColumns(),
                            columns: $columnManagerColumns,
                            reorderAnimationDuration: $this->getReorderAnimationDuration(),
                            attributes: new ComponentAttributeBag([]),
                        ); ?>
                    </div>

                    <?= e($columnManagerTriggerAction->getModalContentFooter()); ?>

                 <?= $this->generateModalHtml(...$modalArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $modalTrigger, livewireId: $this->getLivewire()->getId()); ?>
            <?php } else { ?>
                <?php $dropdownArguments = [
                    'menu' => false,
                    'maxHeight' => $columnManagerMaxHeight,
                    'placement' => 'bottom-end',
                    'shift' => true,
                    'flip' => false,
                    'width' => $columnManagerWidth,
                    'attributes' => new ComponentAttributeBag([
                        'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.column-manager'),
                        'class' => 'fi-ta-col-manager-dropdown',
                    ]),
                ];
                ob_start(); ?>
                     <?php $dropdownTrigger = new ComponentSlot(trim(e($columnManagerTriggerAction))); ?>

                    <?= $this->generateColumnManagerHtml(
                        applyAction: $columnManagerApplyAction,
                        hasReorderableColumns: $this->hasReorderableColumns(),
                        hasToggleableColumns: $this->hasToggleableColumns(),
                        columns: $columnManagerColumns,
                        headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                        reorderAnimationDuration: $this->getReorderAnimationDuration(),
                        resetActionPosition: $this->getColumnManagerResetActionPosition(),
                        attributes: new ComponentAttributeBag([]),
                    ); ?>
                 <?= $this->generateDropdownHtml(...$dropdownArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $dropdownTrigger); ?>
            <?php } ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, ?int> | int | null  $columns
     */
    protected function generateColumnManagerHtml(
        Action $applyAction,
        bool $hasReorderableColumns,
        bool $hasToggleableColumns,
        array | int | null $columns = null,
        string $headingTag = 'h3',
        int $reorderAnimationDuration = 300,
        ?ColumnManagerResetActionPosition $resetActionPosition = null,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.column-manager.index')) {
            return view('filament-tables::components.column-manager.index', [
                'applyAction' => $applyAction,
                'columns' => $columns,
                'hasReorderableColumns' => $hasReorderableColumns,
                'hasToggleableColumns' => $hasToggleableColumns,
                'headingTag' => $headingTag,
                'reorderAnimationDuration' => $reorderAnimationDuration,
                'resetActionPosition' => $resetActionPosition,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        $resetActionPosition ??= ColumnManagerResetActionPosition::Header;
        $isApplyActionVisible = $applyAction->isVisible();
        $resetLabel = __('filament-tables::table.column_manager.actions.reset.label');

        $html = '<div x-data="filamentTableColumnManager({ columns: $wire.entangle(\'tableColumns\'), isLive: ' . ($isApplyActionVisible ? 'false' : 'true') . ', })" class="fi-ta-col-manager">';
        $html .= '<div class="fi-ta-col-manager-header"><' . e($headingTag) . ' class="fi-ta-col-manager-heading">' . e(__('filament-tables::table.column_manager.heading')) . '</' . e($headingTag) . '>';

        if ($resetActionPosition === ColumnManagerResetActionPosition::Header) {
            $html .= '<div>' . $this->generateLinkHtml(
                attributes: new FilamentComponentAttributeBag([
                    'wire:click' => 'resetTableColumnManager',
                    'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => 'resetTableColumnManager',
                    'x-on:click' => 'resetDeferredColumns',
                ]),
                color: 'danger',
                tag: 'button',
                label: $resetLabel,
                shouldCheckForPublishedView: true,
            ) . '</div>';
        }

        $html .= '</div>' . $this->generateColumnManagerContentHtml(
            hasReorderableColumns: $hasReorderableColumns,
            hasToggleableColumns: $hasToggleableColumns,
            columns: $columns,
            reorderAnimationDuration: $reorderAnimationDuration,
        );

        if ($isApplyActionVisible || ($resetActionPosition === ColumnManagerResetActionPosition::Footer)) {
            $html .= '<div class="fi-ta-col-manager-actions-ctn">';
            $html .= $this->renderMorphMarkerHtml();

            if ($isApplyActionVisible) {
                $html .= e($applyAction);
            }

            $html .= $this->renderMorphMarkerHtml(isEnd: true);

            if ($resetActionPosition === ColumnManagerResetActionPosition::Footer) {
                $html .= $this->generateButtonHtml(
                    attributes: new FilamentComponentAttributeBag([
                        'wire:click' => 'resetTableColumnManager',
                        'x-on:click' => 'resetDeferredColumns',
                    ]),
                    color: 'danger',
                    label: $resetLabel,
                    shouldCheckForPublishedView: true,
                );
            }

            $html .= '</div>';
        }

        return $html . '</div>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, ?int> | int | null  $columns
     */
    protected function generateColumnManagerContentHtml(
        bool $hasReorderableColumns,
        bool $hasToggleableColumns,
        array | int | null $columns = null,
        int $reorderAnimationDuration = 300,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.column-manager.content')) {
            return view('filament-tables::components.column-manager.content', [
                'columns' => $columns,
                'hasReorderableColumns' => $hasReorderableColumns,
                'hasToggleableColumns' => $hasToggleableColumns,
                'reorderAnimationDuration' => $reorderAnimationDuration,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        $html = '<div ';

        if ($hasToggleableColumns) {
            $html .= 'x-id="[\'fi-ta-col-manager-group-checkbox\', \'fi-ta-col-manager-column-checkbox\']" ';
        }

        if ($hasReorderableColumns) {
            $html .= 'x-sortable x-on:end.stop="reorderColumns($event.target.sortable.toArray())" x-on:focusin.window="focusRevision++" x-on:pointerdown.window="focusRevision++" data-sortable-animation-duration="' . e($reorderAnimationDuration) . '" ';
        }

        // The original component does not forward its attribute bag to this grid.
        $html .= (new FilamentComponentAttributeBag)->grid($columns, GridDirection::Column)->class(['fi-ta-col-manager-items'])->toHtml() . '>';
        $html .= '<template x-for="column in getVisibleColumns()" x-bind:key="getColumnKey(column)"><div';

        if ($hasReorderableColumns) {
            $html .= ' x-bind:x-sortable-item="column.type === \'group\' ? \'group::\' + column.name : \'column::\' + column.name"';
        }

        $html .= '><template x-if="column.type === \'group\'"><div class="fi-ta-col-manager-group"><div class="fi-ta-col-manager-item">';
        $html .= $this->generateColumnManagerLabelHtml(isGroup: true, isGroupedColumn: false, hasToggleableColumns: $hasToggleableColumns);

        if ($hasReorderableColumns) {
            $html .= $this->generateColumnManagerReorderControlsHtml();
        }

        $html .= '</div><div';

        if ($hasReorderableColumns) {
            $html .= ' x-sortable x-on:end.stop="reorderGroupColumns($event.target.sortable.toArray(), column.name)" data-sortable-animation-duration="' . e($reorderAnimationDuration) . '"';
        }

        $html .= ' class="fi-ta-col-manager-group-items"><template x-for="groupColumn in getVisibleColumns(column.name)" x-bind:key="getColumnKey(groupColumn)"><div';

        if ($hasReorderableColumns) {
            $html .= ' x-bind:x-sortable-item="\'column::\' + groupColumn.name"';
        }

        $html .= '><div class="fi-ta-col-manager-item">';
        $html .= $this->generateColumnManagerLabelHtml(isGroup: false, isGroupedColumn: true, hasToggleableColumns: $hasToggleableColumns);

        if ($hasReorderableColumns) {
            $html .= $this->generateColumnManagerReorderControlsHtml(isGroupedColumn: true);
        }

        $html .= '</div></div></template></div></div></template><template x-if="column.type !== \'group\'"><div class="fi-ta-col-manager-item">';
        $html .= $this->generateColumnManagerLabelHtml(isGroup: false, isGroupedColumn: false, hasToggleableColumns: $hasToggleableColumns);

        if ($hasReorderableColumns) {
            $html .= $this->generateColumnManagerReorderControlsHtml();
        }

        $html .= '</div></template></div></template>';

        if ($hasReorderableColumns) {
            $html .= '<p role="status" aria-live="polite" aria-atomic="true" class="fi-ta-col-manager-status" x-text="reorderAnnouncement"></p>';
        }

        return $html . '</div>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnManagerLabelHtml(bool $isGroup, bool $isGroupedColumn, bool $hasToggleableColumns): string
    {
        $columnExpression = $isGroupedColumn ? 'groupColumn' : 'column';
        $checkboxId = $isGroup ? 'fi-ta-col-manager-group-checkbox' : 'fi-ta-col-manager-column-checkbox';
        $idExpression = '$id(\'' . $checkboxId . '\', ' . $columnExpression . '.name)';
        $html = '<label' . ($hasToggleableColumns ? ' x-bind:for="' . e($idExpression) . '"' : '') . ' class="fi-ta-col-manager-label">';

        if ($hasToggleableColumns) {
            $html .= '<input type="checkbox" class="fi-checkbox-input fi-valid" x-bind:id="' . e($idExpression) . '"';

            if ($isGroup) {
                $html .= ' x-bind:checked="(groupedColumns[column.name] || {}).checked || false" x-bind:disabled="(groupedColumns[column.name] || {}).disabled || false" x-effect="syncGroupCheckboxIndeterminate($el, column.name)" x-on:change="toggleGroup(column.name)"';
            } else {
                $getColumnExpression = 'getColumn(' . $columnExpression . '.name, ' . ($isGroupedColumn ? 'column.name' : 'null') . ')';
                $html .= ' x-bind:checked="(' . $getColumnExpression . ' || {}).isToggled || false" x-bind:disabled="(' . $getColumnExpression . ' || {}).isToggleable === false" x-on:change="toggleColumn(' . $columnExpression . '.name' . ($isGroupedColumn ? ', column.name' : '') . ')"';
            }

            $html .= ' /> ';
        }

        return $html . '<span x-html="' . $columnExpression . '.label"></span></label>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnManagerReorderControlsHtml(bool $isGroupedColumn = false): string
    {
        $columnExpression = $isGroupedColumn ? 'groupColumn' : 'column';
        $groupArgument = $isGroupedColumn ? ', column.name' : '';
        $position = Js::from(__('filament-tables::table.column_manager.' . ($isGroupedColumn ? 'position_in_group' : 'position')))->toHtml();
        $position .= ".replace(':position', getColumnPosition({$columnExpression}{$groupArgument})).replace(':count', getVisibleColumns(" . ($isGroupedColumn ? 'column.name' : '') . ').length)';

        if ($isGroupedColumn) {
            $position .= ".replace(':group', getColumnLabel(column))";
        }

        $announcement = Js::from(__('filament-tables::table.column_manager.' . ($isGroupedColumn ? 'reordered_in_group' : 'reordered')))->toHtml();
        $deferredAnnouncement = Js::from(__('filament-tables::table.column_manager.' . ($isGroupedColumn ? 'reordered_in_group_deferred' : 'reordered_deferred')))->toHtml();
        $html = '<div x-data="{}" x-id="[\'column-manager-position\']" class="fi-ta-col-manager-reorder-controls"><span x-bind:id="$id(\'column-manager-position\')" x-text="' . e($position) . '"></span>';

        foreach ($this->iterateRenderLoop(['up', 'down']) as $direction) {
            $directionExpression = Js::from($direction)->toHtml();
            $label = Js::from(__('filament-tables::table.column_manager.actions.move_' . $direction . '.label'))->toHtml() . ".replace(':column', getColumnLabel({$columnExpression}))";
            $canMove = 'canMoveColumn(' . $columnExpression . ', ' . $directionExpression . $groupArgument . ')';
            $click = 'moveColumn(' . $columnExpression . ', ' . $directionExpression . ', ' . ($isGroupedColumn ? 'column.name' : 'null') . ', $event.currentTarget, isLive ? ' . $announcement . ' : ' . $deferredAnnouncement . ',)';
            $html .= '<button type="button" tabindex="-1" x-text="' . e($label) . '" x-bind:aria-describedby="$id(\'column-manager-position\')" x-bind:disabled="! ' . e($canMove) . '" x-bind:aria-disabled="isLoading || ! ' . e($canMove) . '" x-bind:data-testid="\'column-manager-\' + ' . $columnExpression . '.name + \'-move-' . $direction . '\'" x-on:click.stop="' . e($click) . '"></button>';
        }

        return $html . '</div><button x-sortable-handle aria-hidden="true" tabindex="-1" x-on:click.stop class="fi-ta-col-manager-reorder-handle fi-icon-btn" type="button">'
            . generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE)?->toHtml() . '</button>';
    }
}
