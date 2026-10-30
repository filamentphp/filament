<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Imports\ImportColumn;
use Illuminate\Support\Arr;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;

class ReplaceImportColumnGetExampleRector extends AbstractRemovedGetterRector
{
    protected function owner(): string
    {
        return ImportColumn::class;
    }

    protected function method(): string
    {
        return 'getExample';
    }

    protected function result(Expr $receiver, bool $isNullsafe = false): Expr
    {
        $examples = $isNullsafe
            ? new Coalesce(new NullsafeMethodCall($receiver, 'getExamples'), new Array_)
            : new MethodCall($receiver, 'getExamples');

        return new StaticCall(new FullyQualified(Arr::class), 'first', [new Arg($examples)]);
    }
}
