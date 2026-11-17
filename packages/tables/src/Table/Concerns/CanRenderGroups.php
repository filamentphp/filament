<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanRenderGroups
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function areRenderGroupingSettingsVisible(): bool
    {
        return (! $this->isReordering()) && count($this->getGroups()) && (! $this->areGroupingSettingsHidden());
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function isRenderGroupsOnly(): bool
    {
        return $this->isGroupsOnly() && $this->getGrouping();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGroupingSettingsHtml(): string
    {
        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->areRenderGroupingSettingsVisible()) { ?>
            <div
                x-data="filamentTableGrouping"
                class="fi-ta-grouping-settings"
            >
                <?php $dropdownArguments = [
                    'menu' => false,
                    'placement' => 'bottom-start',
                    'shift' => true,
                    'width' => 'xs',
                    'attributes' => new ComponentAttributeBag([
                        'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.grouping'),
                        'class' => BladeCompiler::sanitizeComponentAttribute(Arr::toCssClasses(['sm:fi-hidden' => ! $this->areGroupingSettingsInDropdownOnDesktop()])),
                    ]),
                ];
            $this->setRenderKey($dropdownArguments['attributes']->get('wire:key'));
            ob_start(); ?>
                    <?php $dropdownTrigger = new ComponentSlot(trim(e($this->getGroupRecordsTriggerAction()))); ?>

                    <div class="fi-ta-grouping-settings-fields">
                        <label>
                            <span>
                                <?= e(__('filament-tables::table.grouping.fields.group.label')); ?>
                            </span>

                            <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
            ob_start(); ?>
                                <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'group', 'x-on:change' => 'resetCollapsedGroups()'])];
            ob_start(); ?>
                                    <option value="">-</option>

                                    <?php foreach ($this->iterateRenderLoop($this->getGroups()) as $groupOption) { ?>
                                        <option
                                            value="<?= e($groupOption->getId()); ?>"
                                        >
                                            <?= e($groupOption->getLabel()); ?>
                                        </option>
                                    <?php } ?>
                                <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                        </label>

                        <?php if (! $this->isGroupingDirectionSettingHidden()) { ?>
                            <label x-cloak x-show="group">
                                <span>
                                    <?= e(__('filament-tables::table.grouping.fields.direction.label')); ?>
                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                            ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                            ob_start(); ?>
                                        <option value="asc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.asc')); ?>
                                        </option>

                                        <option value="desc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.desc')); ?>
                                        </option>
                                    <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        <?php } ?>
                    </div>
                <?= $this->generateDropdownHtml(...$dropdownArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $dropdownTrigger); ?>

                <?php if (! $this->areGroupingSettingsInDropdownOnDesktop()) { ?>
                    <div class="fi-ta-grouping-settings-fields">
                        <label>
                            <?php $inputWrapperArguments = ['prefix' => __('filament-tables::table.grouping.fields.group.label'), 'attributes' => new ComponentAttributeBag([])];
                    ob_start(); ?>
                                <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'group', 'x-on:change' => 'resetCollapsedGroups()'])];
                    ob_start(); ?>
                                    <option value="">-</option>

                                    <?php foreach ($this->iterateRenderLoop($this->getGroups()) as $groupOption) { ?>
                                        <option
                                            value="<?= e($groupOption->getId()); ?>"
                                        >
                                            <?= e($groupOption->getLabel()); ?>
                                        </option>
                                    <?php } ?>
                                <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                        </label>

                        <?php if (! $this->isGroupingDirectionSettingHidden()) { ?>
                            <label x-cloak x-show="group">
                                <span class="fi-sr-only">
                                    <?= e(__('filament-tables::table.grouping.fields.direction.label')); ?>
                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                            ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                            ob_start(); ?>
                                        <option value="asc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.asc')); ?>
                                        </option>

                                        <option value="desc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.desc')); ?>
                                        </option>
                                    <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateGridGroupHeaderHtml(Model | array $record, string | Htmlable | null $recordGroupTitle, bool $isRecordGroupCollapsible, ?string $recordGroupKey): string
    {
        $livewire = $this->getLivewire();

        ob_start();

        $recordGroupLabel = $this->getGrouping()->isTitlePrefixedWithLabel() ? $this->getGrouping()->getLabel() : null;
        $recordGroupDescription = $this->getGrouping()->getDescription($record, $recordGroupTitle);
        $hasRecordGroupCheckbox = $this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1);
        $hasRecordGroupHeader = filled($recordGroupTitle)
            || filled($recordGroupLabel)
            || filled($recordGroupDescription)
            || $isRecordGroupCollapsible
            || $hasRecordGroupCheckbox;
        ?>

        <div
            <?php $groupHeaderKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $recordGroupKey])) . '.header'; ?>
            <?php $this->setRenderKey($groupHeaderKey); ?>
            wire:key="<?= e($groupHeaderKey); ?>"
            role="listitem"
            <?php if (! $hasRecordGroupHeader) { ?>
                aria-hidden="true"
            <?php } ?>
            <?php if ($isRecordGroupCollapsible) { ?>
                x-on:click="(! $event.target.closest('.fi-ta-group-checkbox')) && toggleCollapseGroup(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                <?php if (! $this->hasRenderSummary()) { ?>
                    x-bind:class="{ 'fi-collapsed': isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>) }"
                <?php } ?>
            <?php } ?>
            class="<?= Arr::toCssClasses([
                'fi-ta-group-header',
                'fi-collapsible' => $isRecordGroupCollapsible,
            ]); ?>"
        >
            <?= $this->generateMorphMarkerHtml(); ?>
            <?php if ($hasRecordGroupCheckbox) { ?>
                <?= $this->generateGroupCheckboxHtml($recordGroupTitle, $recordGroupKey); ?>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

            <div>
                <<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>
                    class="fi-ta-group-heading"
                >
                    <?php if (filled($recordGroupLabel)) { ?>
                        <?= e($recordGroupLabel); ?>:
                    <?php } ?>

                    <?= e($recordGroupTitle); ?>
                </<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>>

                <?php if (filled($recordGroupDescription)) { ?>
                    <p
                        class="fi-ta-group-description"
                    >
                        <?= e($recordGroupDescription); ?>
                    </p>
                <?php } ?>
            </div>

            <?php if ($isRecordGroupCollapsible) { ?>
                <button
                    aria-label="<?= e(filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle); ?>"
                    x-bind:aria-expanded="! isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                    type="button"
                    class="fi-icon-btn fi-size-sm"
                >
                    <?= e(\Filament\Support\generate_icon_html(Heroicon::ChevronUp, alias: TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: IconSize::Small)); ?>
                </button>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateTableGroupHeaderHtml(Model | array $record, string | Htmlable | null $recordGroupTitle, ?string $recordGroupKey): string
    {
        $livewire = $this->getLivewire();

        ob_start();

        if (! $this->isRenderGroupsOnly()) {
            $isRecordGroupCollapsible = $this->getGrouping()?->isCollapsible();
            $recordGroupLabel = $this->getGrouping()->isTitlePrefixedWithLabel() ? $this->getGrouping()->getLabel() : null;
            $recordGroupDescription = $this->getGrouping()->getDescription($record, $recordGroupTitle);
            $hasRecordGroupCheckbox = $this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1);
            $hasRecordGroupHeader = filled($recordGroupTitle)
                || filled($recordGroupLabel)
                || filled($recordGroupDescription)
                || $isRecordGroupCollapsible
                || $hasRecordGroupCheckbox;
            ?>

            <tr
                <?php $groupHeaderKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $recordGroupKey])) . '.header'; ?>
                <?php $this->setRenderKey($groupHeaderKey); ?>
                wire:key="<?= e($groupHeaderKey); ?>"
                <?php if (! $hasRecordGroupHeader) { ?>
                    aria-hidden="true"
                <?php } ?>
                class="<?= Arr::toCssClasses([
                    'fi-ta-row fi-ta-group-header-row',
                    'fi-ta-group-header-row-with-selection' => $hasRecordGroupCheckbox,
                ]); ?>"
            >
            <?php
            $groupHeaderColspan = $this->getRenderColumnsCount();

            if ($this->isRenderSelectionEnabled()) {
                $groupHeaderColspan--;

                if (
                    ($this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) &&
                    $this->hasRenderRecordActions() &&
                    ($this->getRecordActionsPosition() === RecordActionsPosition::BeforeCells)
                ) {
                    $groupHeaderColspan--;
                }
            }
            ?>

                <?= $this->generateMorphMarkerHtml(); ?>
                <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::BeforeCells) { ?>
                        <td></td>
                    <?php } ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <td
                        class="fi-ta-cell fi-ta-group-selection-cell"
                    >
                        <?php if ($this->getMaxSelectableRecords() !== 1) { ?>
                            <?= $this->generateGroupCheckboxHtml($recordGroupTitle, $recordGroupKey); ?>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                <td
                    colspan="<?= e($groupHeaderColspan); ?>"
                    class="fi-ta-group-header-cell"
                >
                    <div
                        <?php if ($isRecordGroupCollapsible) { ?>
                            x-on:click="toggleCollapseGroup(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                            x-bind:class="isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>) ? 'fi-collapsed' : null"
                        <?php } ?>
                        class="<?= Arr::toCssClasses([
                            'fi-ta-group-header',
                            'fi-collapsible' => $isRecordGroupCollapsible,
                        ]); ?>"
                    >
                        <div>
                            <<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>
                                class="fi-ta-group-heading"
                            >
                                <?php if (filled($recordGroupLabel)) { ?>
                                    <?= e($recordGroupLabel); ?>:
                                <?php } ?>

                                <?= e($recordGroupTitle); ?>
                            </<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>>

                            <?php if (filled($recordGroupDescription)) { ?>
                                <p
                                    class="fi-ta-group-description"
                                >
                                    <?= e($recordGroupDescription); ?>
                                </p>
                            <?php } ?>
                        </div>

                        <?php if ($isRecordGroupCollapsible) { ?>
                            <button
                                aria-label="<?= e(filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle); ?>"
                                x-bind:aria-expanded="! isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                                type="button"
                                class="fi-icon-btn fi-size-sm"
                            >
                                <?= e(\Filament\Support\generate_icon_html(Heroicon::ChevronUp, alias: TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: IconSize::Small)); ?>
                            </button>
                        <?php } ?>
                    </div>
                </td>

                <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                    <td
                        class="fi-ta-cell fi-ta-group-selection-cell"
                    >
                        <?php if ($this->getMaxSelectableRecords() !== 1) { ?>
                            <?= $this->generateGroupCheckboxHtml($recordGroupTitle, $recordGroupKey); ?>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php }

        return ob_get_clean();
    }
}
