<?php

namespace Filament\Tests\Rector\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Eval_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeFinder;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class MovePestTestClassesToEndRector extends AbstractRector
{
    private const array PEST_REGISTRATION_FUNCTIONS = [
        'afterAll',
        'afterEach',
        'beforeAll',
        'beforeEach',
        'dataset',
        'describe',
        'it',
        'test',
        'uses',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Move class-like declarations after Pest tests in the same namespace.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        class TestComponent
                        {
                        }

                        it('renders the component', function () {
                            // ...
                        });
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        it('renders the component', function () {
                            // ...
                        });

                        class TestComponent
                        {
                        }
                        CODE_SAMPLE,
                ),
            ],
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FileNode::class, Namespace_::class];
    }

    /** @param FileNode | Namespace_ $node */
    public function refactor(Node $node): FileNode | Namespace_ | null
    {
        if (! str_contains(str_replace('\\', '/', $this->getFile()->getFilePath()), '/tests/')) {
            return null;
        }

        $classLikeStatements = [];
        $otherStatements = [];
        $hasPestTest = false;
        $hasClassLikeBeforePestTest = false;

        foreach ($node->stmts as $statement) {
            if ($statement instanceof ClassLike) {
                $classLikeStatements[] = $statement;

                continue;
            }

            $otherStatements[] = $statement;

            if (! $this->containsPestTestRegistration($statement)) {
                continue;
            }

            $hasPestTest = true;
            $hasClassLikeBeforePestTest = $hasClassLikeBeforePestTest || ($classLikeStatements !== []);
        }

        if ((! $hasPestTest) || (! $hasClassLikeBeforePestTest)) {
            return null;
        }

        if (! $this->canMoveClassLikeStatements($otherStatements, $classLikeStatements)) {
            return null;
        }

        $node->stmts = $otherStatements;

        foreach ($classLikeStatements as $classLikeStatement) {
            $node->stmts[] = new Nop;
            $node->stmts[] = $classLikeStatement;
        }

        return $node;
    }

    /**
     * @param  list<Node>  $statements
     * @param  list<ClassLike>  $classLikeStatements
     */
    private function canMoveClassLikeStatements(array $statements, array $classLikeStatements): bool
    {
        $classLikeNames = [];

        foreach ($classLikeStatements as $classLikeStatement) {
            if ($classLikeStatement->name === null) {
                continue;
            }

            $classLikeNames[] = strtolower($classLikeStatement->namespacedName->toString());
        }

        foreach ($statements as $statement) {
            if ($statement instanceof Nop
                || $statement instanceof Use_
                || $statement instanceof GroupUse
                || $statement instanceof Function_) {
                continue;
            }

            if ($statement instanceof Declare_) {
                if ($statement->stmts === null) {
                    continue;
                }

                return false;
            }

            if (! $statement instanceof Expression) {
                return false;
            }

            if ($statement->expr instanceof Assign
                && $statement->expr->var instanceof Variable
                && is_string($statement->expr->var->name)
                && ($statement->expr->expr instanceof Closure || $statement->expr->expr instanceof ArrowFunction)) {
                continue;
            }

            if (! $this->containsPestTestRegistration($statement)) {
                return false;
            }

            if ($this->containsUnsafeEagerCall($statement)) {
                return false;
            }

            if ($this->containsEagerClassLikeReference($statement, $classLikeNames)) {
                return false;
            }
        }

        return true;
    }

    private function containsUnsafeEagerCall(Node $node): bool
    {
        if ($node instanceof Closure || $node instanceof ArrowFunction || $node instanceof Function_ || $node instanceof ClassLike) {
            return false;
        }

        if ($node instanceof FuncCall
            && ($this->isFunctionCallNamed($node, 'describe') || $this->isFunctionCallNamed($node, 'uses') || ! $this->isPestRegistrationCall($node))) {
            return true;
        }

        if ($node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
            return true;
        }

        if ($node instanceof StaticCall || $node instanceof New_ || $node instanceof Eval_ || $node instanceof Include_) {
            return true;
        }

        if (($node instanceof ClassConstFetch || $node instanceof StaticPropertyFetch || $node instanceof Instanceof_)
            && ! $node->class instanceof Name) {
            return true;
        }

        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->{$subNodeName};

            if ($subNode instanceof Node && $this->containsUnsafeEagerCall($subNode)) {
                return true;
            }

            if (! is_array($subNode)) {
                continue;
            }

            foreach ($subNode as $nestedNode) {
                if ($nestedNode instanceof Node && $this->containsUnsafeEagerCall($nestedNode)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @param list<string> $classLikeNames */
    private function containsEagerClassLikeReference(Node $node, array $classLikeNames): bool
    {
        if ($node instanceof Closure || $node instanceof ArrowFunction || $node instanceof Function_ || $node instanceof ClassLike) {
            return false;
        }

        if (($node instanceof New_
                || $node instanceof StaticCall
                || $node instanceof StaticPropertyFetch
                || $node instanceof ClassConstFetch
                || $node instanceof Instanceof_)
            && $node->class instanceof Name) {
            $resolvedName = $node->class->getAttribute('resolvedName');
            $referenceName = strtolower(($resolvedName instanceof Name ? $resolvedName : $node->class)->toString());

            if (in_array($referenceName, $classLikeNames, true)) {
                return true;
            }
        }

        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->{$subNodeName};

            if ($subNode instanceof Node && $this->containsEagerClassLikeReference($subNode, $classLikeNames)) {
                return true;
            }

            if (! is_array($subNode)) {
                continue;
            }

            foreach ($subNode as $nestedNode) {
                if ($nestedNode instanceof Node && $this->containsEagerClassLikeReference($nestedNode, $classLikeNames)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function containsPestTestRegistration(Node $statement): bool
    {
        foreach ((new NodeFinder)->findInstanceOf([$statement], FuncCall::class) as $functionCall) {
            if ($this->isPestRegistrationCall($functionCall)) {
                return true;
            }
        }

        return false;
    }

    private function isPestRegistrationCall(FuncCall $functionCall): bool
    {
        foreach (self::PEST_REGISTRATION_FUNCTIONS as $functionName) {
            if ($this->isFunctionCallNamed($functionCall, $functionName)) {
                return true;
            }
        }

        return false;
    }

    private function isFunctionCallNamed(FuncCall $functionCall, string $functionName): bool
    {
        if ($functionCall->name instanceof Name) {
            $resolvedName = $functionCall->name->getAttribute('resolvedName');

            if ($resolvedName instanceof Name) {
                return strcasecmp($resolvedName->toString(), $functionName) === 0;
            }
        }

        return $this->isName($functionCall, $functionName);
    }
}
