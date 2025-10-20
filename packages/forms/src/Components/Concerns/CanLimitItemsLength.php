<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;

trait CanLimitItemsLength
{
    protected int | Closure | null $maxItems = null;

    protected int | Closure | null $minItems = null;

    public function maxItems(int | Closure | null $count): static
    {
        $this->maxItems = $count;

        $this->rule('array', static function (Component $component): bool {
            /** @var static $component */
            $count = $component->getMaxItems();

            return $count !== null;
        });
        $this->rule(static function (Component $component): string {
            /** @var static $component */
            $count = $component->getMaxItems();

            return "max:{$count}";
        }, static function (Component $component): bool {
            /** @var static $component */
            $count = $component->getMaxItems();

            return $count !== null;
        });

        return $this;
    }

    public function minItems(int | Closure | null $count): static
    {
        $this->minItems = $count;

        $this->rule('array', static function (Component $component): bool {
            /** @var static $component */
            $count = $component->getMinItems();

            return $count !== null;
        });
        $this->rule(static function (Component $component): string {
            /** @var static $component */
            $count = $component->getMinItems();

            return "min:{$count}";
        }, static function (Component $component): bool {
            /** @var static $component */
            $count = $component->getMinItems();

            return $count !== null;
        });

        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    public function getMinItems(): ?int
    {
        return $this->evaluate($this->minItems);
    }

    public function getItemsCount(): int
    {
        $state = $this->getRawState();

        return is_array($state) ? count($state) : 0;
    }
}
