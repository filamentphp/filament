<?php

namespace Filament\Infolists\Components;

use ArrayAccess;
use Closure;
use Exception;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasContainerGridLayout;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Enums\Alignment;
use Generator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Traversable;

class RepeatableEntry extends Entry implements HasEmbeddedView
{
    use CanBeContained;
    use HasContainerGridLayout;

    /**
     * @var array<TableColumn> | Closure | null
     */
    protected array | Closure | null $tableColumns = null;

    protected mixed $cachedItemsState = null;

    protected ?Generator $cachedItemsStateGenerator = null;

    /**
     * @var array<array-key, mixed> | null
     */
    protected ?array $cachedItemsStateGeneratorSnapshot = null;

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
        return $this->getCachedDefaultChildSchemas();
    }

    /**
     * @return array<Schema>
     */
    public function getDefaultChildSchemas(): array
    {
        $state = $this->normalizeItemsState($this->getState() ?? []);

        $this->cachedItemsState = $state;

        $containers = [];

        foreach ($state as $itemKey => $itemData) {
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

    protected function areCachedDefaultChildSchemasFresh(): bool
    {
        return $this->cachedItemsState === $this->normalizeItemsState($this->getState() ?? []);
    }

    protected function isCachedDefaultChildSchemaFresh(string | int $key): bool
    {
        [$hasItem, $itemState] = $this->getItemState($this->getState() ?? [], $key);

        return $hasItem
            && is_array($this->cachedItemsState)
            && array_key_exists($key, $this->cachedItemsState)
            && ($this->cachedItemsState[$key] === $itemState);
    }

    /**
     * @return array{bool, mixed}
     */
    protected function getItemState(mixed $state, string | int $key): array
    {
        if ($state instanceof Generator) {
            $state = $this->normalizeItemsState($state);
        }

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        if (is_array($state)) {
            return array_key_exists($key, $state)
                ? [true, $state[$key]]
                : [false, null];
        }

        if ($state instanceof ArrayAccess) {
            return $state->offsetExists($key)
                ? [true, $state->offsetGet($key)]
                : [false, null];
        }

        if (is_object($state) && property_exists($state, (string) $key)) {
            return [true, $state->{$key}];
        }

        if ($state instanceof Traversable) {
            foreach ($state as $itemKey => $itemState) {
                if ($itemKey === $key) {
                    return [true, $itemState];
                }
            }
        }

        return [false, null];
    }

    protected function normalizeItemsState(mixed $state): mixed
    {
        if ($state instanceof Generator) {
            if ($state === $this->cachedItemsStateGenerator) {
                return $this->cachedItemsStateGeneratorSnapshot;
            }

            $snapshot = iterator_to_array($state);
            $this->cachedItemsStateGenerator = $state;
            $this->cachedItemsStateGeneratorSnapshot = $snapshot;

            return $snapshot;
        }

        if ($state instanceof Collection) {
            return $state->all();
        }

        if ($state instanceof Traversable) {
            return iterator_to_array($state);
        }

        if (is_object($state)) {
            return get_object_vars($state);
        }

        return $state;
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->isTable()) {
            return $this->toEmbeddedTableHtml();
        }

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
                            allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder)) { ?>
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

    protected function toEmbeddedTableHtml(): string
    {
        $items = $this->getItems();
        $tableColumns = $this->getTableColumns();

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-table-repeatable',
            ]);

        if (empty($items)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                            allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <table>
                <thead>
                    <tr>
                        <?php foreach ($tableColumns as $column) { ?>
                            <th
                                scope="col"
                                class="<?= Arr::toCssClasses([
                                    'fi-wrapped' => $column->canHeaderWrap(),
                                    (($columnAlignment = $column->getAlignment()) instanceof Alignment) ? ('fi-align-' . $columnAlignment->value) : e($columnAlignment),
                                ]) ?>"
                                <?php if (filled($columnWidth = $column->getWidth())) { ?>
                                    style="width: <?= e($columnWidth) ?>"
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
                            <?php $counter = 0 ?>

                            <?php foreach ($item->getComponents(withHidden: true) as $component) { ?>
                                <?php throw_unless(
                                    $component instanceof Component,
                                    new Exception('Table repeatable entries must only contain schema components, but [' . $component::class . '] was used.'),
                                ) ?>

                                <?php if (count($tableColumns) > $counter) { ?>
                                    <?php $counter++ ?>

                                    <?php if ($component->isVisible()) { ?>
                                        <td
                                            wire:key="<?= e($component->getLivewireKey()) ?>"
                                            x-data="filamentSchemaComponent({
                                                ...<?= Js::from($component->getAlpineScopeConfiguration()) ?>,
                                                $wire,
                                            })"
                                        >
                                            <?= $component->toHtml() ?>
                                        </td>
                                    <?php } else { ?>
                                        <td class="fi-hidden"></td>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
