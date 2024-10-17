<?php

namespace Filament\Tables\Columns\Summarizers;

use Closure;
use Illuminate\Database\Query\Builder;

class Values extends Summarizer
{
    protected bool | Closure $isBulleted = true;

    /**
     * @return array<string, int>
     */
    public function summarize(Builder $query, string $attribute): array
    {
        return $query->clone()->distinct()->pluck($attribute)->all();
    }

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }

    public function toEmbeddedHtml(): string
    {
        $attributes = $this->getExtraAttributeBag()
            ->class(['fi-ta-values-summary']);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php if (filled($label = $this->getLabel())) { ?>
                <span class="fi-ta-values-summary-label">
                    <?= $label ?>
                </span>
            <?php } ?>

            <?php if ($state = $this->getState()) { ?>
                <ul <?= $this->isBulleted() ? 'class="fi-bulleted"' : '' ?>>
                    <?php foreach ($state as $stateItem) { ?>
                        <li>
                            <?= $this->formatState($stateItem) ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
