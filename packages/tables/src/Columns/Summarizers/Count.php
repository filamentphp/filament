<?php

namespace Filament\Tables\Columns\Summarizers;

use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\View\Components\Columns\Summarizers\CountComponent\IconComponent;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use LogicException;

use function Filament\Support\generate_icon_html;

class Count extends Summarizer
{
    protected bool $hasIcons = false;

    protected ?string $selectAlias = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    /**
     * @return int | float | array<string, array<string, int>> | null
     */
    public function summarize(Builder $query, string $attribute): int | float | array | null
    {
        if (! $this->hasIcons) {
            return $query->count();
        }

        $column = $this->getColumn();

        if (! ($column instanceof IconColumn)) {
            throw new LogicException("The [{$column->getName()}] column must be an IconColumn to show an icon count summary.");
        }

        $state = [];

        foreach ($query->clone()->distinct()->pluck($attribute) as $value) {
            $column->record($this->getQuery()->getModel()->setKeyName($attribute)->setAttribute($attribute, $value));
            $column->clearCachedState();
            $columnState = $column->getState();
            $column->clearCachedState();
            $color = json_encode($column->getColor($columnState));
            $icon = $column->getIcon($columnState);
            $iconKey = serialize($icon);

            $state[$color] ??= [];
            $state[$color][$iconKey] ??= 0;

            $state[$color][$iconKey] += $query->clone()->where($attribute, $value)->count();
        }

        return $state;
    }

    /**
     * @return array<string, string>
     */
    public function getSelectStatements(string $column): array
    {
        if ($this->hasIcons) {
            return [];
        }

        $column = $this->getQuery()->getGrammar()->wrap($column);

        return [
            $this->getSelectAlias() => "count({$column})",
        ];
    }

    public function getSelectedState(): int | float | null
    {
        if (! array_key_exists($this->selectAlias, $this->selectedState)) {
            return null;
        }

        return $this->selectedState[$this->getSelectAlias()];
    }

    public function selectAlias(?string $alias): static
    {
        $this->selectAlias = $alias;

        return $this;
    }

    public function getSelectAlias(): string
    {
        return $this->selectAlias ??= Str::random();
    }

    public function icons(bool $condition = true): static
    {
        $this->hasIcons = $condition;

        return $this;
    }

    public function getDefaultLabel(): ?string
    {
        return $this->hasIcons ? null : __('filament-tables::table.summary.summarizers.count.label');
    }

    public function hasIcons(): bool
    {
        return $this->hasIcons;
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->hasIcons()) {
            $attributes = $this->getExtraAttributeBag()
                ->class(['fi-ta-icon-count-summary']);

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($label = $this->getLabel())) { ?>
                    <span class="fi-ta-icon-count-summary-label">
                        <?= $label ?>
                    </span>
                <?php } ?>

                <?php if ($state = $this->getState()) { ?>
                    <ul>
                        <?php foreach ($state as $color => $icons) { ?>
                            <?php $color = json_decode($color); ?>

                            <?php foreach ($icons as $icon => $count) { ?>
                                <li>
                                    <span>
                                        <?= $count ?>
                                    </span>

                                    <?= generate_icon_html(
                                        unserialize($icon),
                                        attributes: (new ComponentAttributeBag)->color(IconComponent::class, $color),
                                        size: IconSize::Large,
                                    )->toHtml() ?>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        return parent::toEmbeddedHtml();
    }
}
