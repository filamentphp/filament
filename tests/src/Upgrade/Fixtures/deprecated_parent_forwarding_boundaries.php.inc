<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedParentForwardingBoundaries;

use Closure;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\Support\Arrayable;

class DualOverride extends IconColumn
{
    public int $canonicalCalls = 0;

    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }

    public function icons(array | Closure $customIcons): static
    {
        $this->canonicalCalls++;

        return clone $this;
    }
}

class InheritedDualOverride extends DualOverride {}

class AncestorIcon extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }
}

class IntermediateIcon extends AncestorIcon {}

trait CustomIcons
{
    public int $canonicalCalls = 0;

    public function icons(array | Closure $customIcons): static
    {
        $this->canonicalCalls++;

        return clone $this;
    }
}

class TraitDescendant extends IntermediateIcon
{
    use CustomIcons;
}

class DualAction extends Action
{
    public int $canonicalCalls = 0;

    public function form(array | Closure | null $customSchema): static
    {
        return parent::form($customSchema);
    }

    public function schema(array | Closure | null $customSchema): static
    {
        $this->canonicalCalls++;

        return clone $this;
    }
}

class AncestorAction extends Action
{
    public function form(array | Closure | null $customSchema): static
    {
        return parent::form(form: $customSchema);
    }
}

class DescendantAction extends AncestorAction
{
    public int $canonicalCalls = 0;

    public function schema(array | Closure | null $customSchema): static
    {
        $this->canonicalCalls++;

        return clone $this;
    }
}

class CustomParent extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return $this;
    }
}

class CustomChild extends CustomParent
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }
}

class UnsupportedDispatch extends IconColumn
{
    public function configureExamples(): void
    {
        self::options([]);
        static::options([]);
        $callback = parent::options(...);
        parent::options(...['options' => []]);
    }

    public static function configureStatically(): void
    {
        parent::options([]);
    }
}
