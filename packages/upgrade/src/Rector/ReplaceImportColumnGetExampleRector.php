<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Imports\ImportColumn;
use Illuminate\Support\Arr;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceImportColumnGetExampleRector extends AbstractRemovedGetterRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace the removed import example getter, preserving custom dispatch and evaluation', [new CodeSample('$column->getExample()', '\\Illuminate\\Support\\Arr::first($column->getExamples())')]);
    }

    protected function owner(): string
    {
        return ImportColumn::class;
    }

    protected function method(): string
    {
        return 'getExample';
    }

    protected function result(Expr $receiver): Expr
    {
        return new StaticCall(new FullyQualified(Arr::class), 'first', [new Arg(new MethodCall($receiver, 'getExamples'))]);
    }
}
