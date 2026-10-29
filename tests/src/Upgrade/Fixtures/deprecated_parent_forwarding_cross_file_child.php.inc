<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedParentForwardingCrossFile;

use Closure;

class IntermediateIcon extends ParentIcon {}

trait CustomIcons
{
    public function icons(array | Closure $customIcons): static
    {
        return clone $this;
    }
}

class DescendantIcon extends IntermediateIcon
{
    use CustomIcons;
}

class IntermediateAction extends ParentAction {}

class DescendantAction extends IntermediateAction
{
    public function schema(array | Closure | null $customSchema): static
    {
        return clone $this;
    }
}
