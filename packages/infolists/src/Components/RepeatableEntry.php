<?php

namespace Filament\Infolists\Components;

use Filament\Schemas\Components\Concerns\HasContainerGridLayout;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeContained;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

class RepeatableEntry extends Entry implements HasEmbeddedView
{
    use CanBeContained;
    use HasContainerGridLayout;

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
