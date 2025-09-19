<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Schemas\Components\Concerns\HasContainerGridLayout;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

class RepeatableEntry extends Entry implements HasEmbeddedView
{
    use CanBeContained;
    use HasContainerGridLayout;

    /**
     * @var array<TableColumn> | Closure | null
     */
    protected array | Closure | null $tableColumns = null;

    /**
     * Configure table columns for display
     *
     * @param  array<TableColumn> | Closure | null  $columns
     */
    public function table(array | Closure | null $columns): static
    {
        $this->tableColumns = $columns;

        return $this;
    }

    /**
     * Get configured table columns
     *
     * @return ?array<TableColumn>
     */
    public function getTableColumns(): ?array
    {
        return $this->evaluate($this->tableColumns);
    }

    /**
     * Determine if component should render as table
     */
    public function isTable(): bool
    {
        return filled($this->getTableColumns());
    }

    /**
     * @return array<Schema>
     */
    public function getItems(): array
    {
        $containers = [];

        foreach ($this->getState() ?? [] as $itemKey => $itemData) {
            $container = $this
                ->getChildSchema()
                ->getClone()
                ->statePath($itemKey)
                ->inlineLabel(false);

            if ($itemData instanceof Model) {
                $container->record($itemData);
            } elseif (is_array($itemData) || is_object($itemData)) {
                $container->constantState($itemData);
            }

            $containers[$itemKey] = $container;
        }

        return $containers;
    }

    /**
     * @return array<Schema>
     */
    public function getDefaultChildSchemas(): array
    {
        return $this->getItems();
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->isTable()) {
            return $this->renderAsTable();
        }

        return $this->renderAsGrid();
    }

    protected function renderAsTable(): string
    {
        $items = $this->getItems();
        $tableColumns = $this->getTableColumns();

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-repeatable',
                'fi-in-repeatable-table',
                'fi-contained' => $this->isContained(),
            ]);

        if (empty($items)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder) && filled($tableColumns)) { ?>
                    <table class="fi-in-repeatable-table-element">
                        <thead
                            <?php if (empty(array_filter($tableColumns, fn ($column): bool => ! $column->isHeaderLabelHidden()))) { ?>
                                class="fi-sr-only"
                            <?php } ?>
                        >
                            <tr>
                                <?php foreach ($tableColumns as $column) { ?>
                                    <th
                                        <?php if ($column->getAlignment()) { ?>
                                            <?php
                                            $alignmentClass = match ($column->getAlignment()) {
                                                Alignment::Left => 'fi-align-left',
                                                Alignment::Center => 'fi-align-center',
                                                Alignment::Right => 'fi-align-right',
                                                default => null,
                                            }
                                            ?>
                                            class="<?= $alignmentClass ?>"
                                        <?php } ?>
                                        <?php if ($column->getWidth()) { ?>
                                            style="width: <?= $column->getWidth() ?>"
                                        <?php } ?>
                                    >
                                        <?php if (! $column->isHeaderLabelHidden()) { ?>
                                            <?= e($column->getLabel()) ?>
                                        <?php } else { ?>
                                            <span class="fi-sr-only">
                                                <?= e($column->getLabel()) ?>
                                            </span>
                                        <?php } ?>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="<?= count($tableColumns) ?>" class="fi-in-placeholder">
                                    <?= e($placeholder) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php } elseif (filled($placeholder)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <table class="fi-in-repeatable-table-element">
                <thead
                    <?php if (empty(array_filter($tableColumns, fn ($column): bool => ! $column->isHeaderLabelHidden()))) { ?>
                        class="fi-sr-only"
                    <?php } ?>
                >
                    <tr>
                        <?php foreach ($tableColumns as $column) { ?>
                            <th
                                <?php if ($column->getAlignment()) { ?>
                                    <?php
                                    $alignmentClass = match ($column->getAlignment()) {
                                        Alignment::Left => 'fi-align-left',
                                        Alignment::Center => 'fi-align-center',
                                        Alignment::Right => 'fi-align-right',
                                        default => null,
                                    }
                                    ?>
                                    class="<?= $alignmentClass ?>"
                                <?php } ?>
                                <?php if ($column->getWidth()) { ?>
                                    style="width: <?= $column->getWidth() ?>"
                                <?php } ?>
                            >
                                <?php if (! $column->isHeaderLabelHidden()) { ?>
                                    <?= e($column->getLabel()) ?>
                                <?php } else { ?>
                                    <span class="fi-sr-only">
                                        <?= e($column->getLabel()) ?>
                                    </span>
                                <?php } ?>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                        <tr>
                            <?php
                            $components = $item->getComponents();
                        $componentIndex = 0;
                        foreach ($tableColumns as $column) {
                            if (isset($components[$componentIndex])) {
                                $component = $components[$componentIndex];
                                $componentIndex++;
                            } else {
                                $component = null;
                            }
                            ?>
                                <td>
                                    <?php if ($component) { ?>
                                        <?= $component->toHtml() ?>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }

    protected function renderAsGrid(): string
    {
        $items = $this->getItems();

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-repeatable',
                'fi-contained' => $this->isContained(),
            ]);

        if (empty($items)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder !== null)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        $attributes = $attributes->grid($this->getGridColumns());

        ob_start(); ?>

        <ul <?= $attributes->toHtml() ?>>
            <?php foreach ($items as $item) { ?>
                <li class="fi-in-repeatable-item">
                    <?= $item->toHtml() ?>
                </li>
            <?php } ?>
        </ul>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
