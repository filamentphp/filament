<?php

namespace Filament\Upgrade\Rector;

use Filament\Schemas\Components\Component;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;

class RemoveObsoleteConcealmentRector extends AbstractRemovedGetterRector
{
    protected function owner(): string
    {
        return Component::class;
    }

    protected function method(): string
    {
        return 'isConcealed';
    }

    protected function result(Expr $receiver, bool $isNullsafe = false): Expr
    {
        $result = new ConstFetch(new Name('false'));

        if ($isNullsafe) {
            return new Ternary(new Identical($receiver, new ConstFetch(new Name('null'))), new ConstFetch(new Name('null')), $result);
        }

        return $receiver instanceof Variable
            ? $result
            : new ArrayDimFetch(new Array_([new ArrayItem($receiver), new ArrayItem($result)]), new Int_(1));
    }
}
