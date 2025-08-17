<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Illuminate\Support\Collection;

class TableEntry extends Entry implements HasEmbeddedView
{
    /**
     * @var array<int,string>|Closure|null
     */
    protected array | Closure | null $columnLabels = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('filament-infolists::components.entries.key_value.placeholder'));
    }

    /**
     * @param array<int,string>|Closure|null $columns
     */
    public function columnLabels(array | Closure | null $columns): static
    {
        $this->columnLabels = $columns;

        return $this;
    }

    /**
     * @return array<int,string>
     */
    public function getColumnLabels(): array
    {
        return $this->evaluate($this->columnLabels) ?? [];
    }

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-key-value',
            ]);

        ob_start(); ?>

        <table <?= $attributes->toHtml() ?>>
            <thead>
                <tr>
                    <?php foreach ($this->getColumnLabels() as $column) { ?>
                        <th scope="col">
                            <?= e($column) ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach (($state ?? []) as $row) { ?>
                    <tr>
                        <?php foreach ($row as $value) { ?>
                            <th scope="row">
                                <?= e($value) ?>
                            </th>
                        <?php } ?>
                    </tr>
                <?php } ?>

                <?php if (empty($state)) { ?>
                    <tr>
                        <td colspan="<?= count($this->getColumnLabels()) ?: 1 ?>" class="fi-in-placeholder">
                            <?= e($this->getPlaceholder()) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
