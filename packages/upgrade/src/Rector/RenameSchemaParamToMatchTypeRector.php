<?php

namespace Filament\Upgrade\Rector;

use Filament\Schemas\Schema;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\Naming\ParamRenamer\ParamRenamer;
use Rector\Naming\ValueObject\ParamRename;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Rector\AbstractRector;

class RenameSchemaParamToMatchTypeRector extends AbstractRector
{
    public function __construct(
        private ParamRenamer $paramRenamer,
        private BetterNodeFinder $betterNodeFinder,
    ) {}

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class, Closure::class, ArrowFunction::class];
    }

    /**
     * @param  ClassMethod|Function_|Closure|ArrowFunction  $node
     */
    public function refactor(Node $node): ?Node
    {
        $schemaParameters = array_filter(
            $node->params,
            fn (Node\Param $parameter): bool => $parameter->type && $this->isName($parameter->type, Schema::class),
        );

        if (count($schemaParameters) !== 1) {
            return null;
        }

        $parameter = reset($schemaParameters);

        if ((! $parameter->var instanceof Variable) || $parameter->isPromoted()) {
            return null;
        }

        $currentName = $this->getName($parameter);

        if (($currentName === null) || ($currentName === 'schema') || str_ends_with($currentName, 'Schema')) {
            return null;
        }

        // Include parameters, closure captures, and nested scopes in the collision check.
        if ($this->betterNodeFinder->findFirst($node, fn (Node $variable): bool => $variable instanceof Variable && $this->isName($variable, 'schema'))) {
            return null;
        }

        $this->paramRenamer->rename(new ParamRename($currentName, 'schema', $parameter->var, $node));

        return $node;
    }
}
