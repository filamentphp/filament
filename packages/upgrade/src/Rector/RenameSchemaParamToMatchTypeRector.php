<?php

namespace Filament\Upgrade\Rector;

use Filament\Schemas\Schema;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\Naming\ExpectedNameResolver\MatchParamTypeExpectedNameResolver;
use Rector\Naming\Guard\BreakingVariableRenameGuard;
use Rector\Naming\Naming\ExpectedNameResolver;
use Rector\Naming\ParamRenamer\ParamRenamer;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\ValueObject\ParamRename;
use Rector\Naming\ValueObjectFactory\ParamRenameFactory;
use Rector\Rector\AbstractRector;
use Rector\ValueObject\MethodName;

/**
 * @see RenameParamToMatchTypeRector
 */
class RenameSchemaParamToMatchTypeRector extends AbstractRector
{
    /**
     * @readonly
     */
    private BreakingVariableRenameGuard $breakingVariableRenameGuard;

    /**
     * @readonly
     */
    private ExpectedNameResolver $expectedNameResolver;

    /**
     * @readonly
     */
    private MatchParamTypeExpectedNameResolver $matchParamTypeExpectedNameResolver;

    /**
     * @readonly
     */
    private ParamRenameFactory $paramRenameFactory;

    /**
     * @readonly
     */
    private ParamRenamer $paramRenamer;

    private bool $hasChanged = \false;

    public function __construct(BreakingVariableRenameGuard $breakingVariableRenameGuard, ExpectedNameResolver $expectedNameResolver, MatchParamTypeExpectedNameResolver $matchParamTypeExpectedNameResolver, ParamRenameFactory $paramRenameFactory, ParamRenamer $paramRenamer)
    {
        $this->breakingVariableRenameGuard = $breakingVariableRenameGuard;
        $this->expectedNameResolver = $expectedNameResolver;
        $this->matchParamTypeExpectedNameResolver = $matchParamTypeExpectedNameResolver;
        $this->paramRenameFactory = $paramRenameFactory;
        $this->paramRenamer = $paramRenamer;
    }

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
        $this->hasChanged = \false;
        foreach ($node->params as $param) {
            if ((! $param->type) || (! $this->isName($param->type, Schema::class))) {
                continue;
            }
            $expectedName = $this->expectedNameResolver->resolveForParamIfNotYet($param);
            if ($expectedName === null) {
                continue;
            }
            if ($this->shouldSkipParam($param, $expectedName, $node)) {
                continue;
            }
            $expectedName = $this->matchParamTypeExpectedNameResolver->resolve($param);
            if ($expectedName === null) {
                continue;
            }
            $paramRename = $this->paramRenameFactory->createFromResolvedExpectedName($node, $param, $expectedName);
            if (! $paramRename instanceof ParamRename) {
                continue;
            }
            $this->paramRenamer->rename($paramRename);
            $this->hasChanged = \true;
        }
        if (! $this->hasChanged) {
            return null;
        }

        return $node;
    }

    /**
     * @param  ClassMethod|Function_|Closure|ArrowFunction  $classMethod
     */
    private function shouldSkipParam(Param $param, string $expectedName, $classMethod): bool
    {
        /** @var string $paramName */
        $paramName = $this->getName($param);
        if ($this->breakingVariableRenameGuard->shouldSkipParam($paramName, $expectedName, $classMethod, $param)) {
            return \true;
        }
        if (! $classMethod instanceof ClassMethod) {
            return \false;
        }
        // promoted property
        if (! $this->isName($classMethod, MethodName::CONSTRUCT)) {
            return \false;
        }

        return $param->isPromoted();
    }
}
