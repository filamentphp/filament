<?php

namespace Filament\Tests\Rector\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Cast\String_ as StringCast;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Eval_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\MagicConst\Function_ as FunctionMagicConstant;
use PhpParser\Node\Scalar\MagicConst\Method as MethodMagicConstant;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Goto_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeFinder;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ScopePestTestHelpersRector extends AbstractRector
{
    private const array CALLBACK_ARGUMENT_POSITIONS = [
        'array_filter' => 1,
        'array_find' => 1,
        'array_find_key' => 1,
        'array_map' => 0,
        'array_reduce' => 1,
        'array_walk' => 1,
        'array_walk_recursive' => 1,
        'array_all' => 1,
        'array_any' => 1,
        'call_user_func' => 0,
        'call_user_func_array' => 0,
        'iterator_apply' => 1,
        'ob_start' => 0,
        'pcntl_signal' => 1,
        'preg_replace_callback' => 1,
        'register_shutdown_function' => 0,
        'register_tick_function' => 0,
        'set_error_handler' => 0,
        'set_exception_handler' => 0,
        'spl_autoload_register' => 0,
        'uasort' => 1,
        'uksort' => 1,
        'usort' => 1,
    ];

    private const array PEST_REGISTRATION_FUNCTIONS = [
        'afterAll',
        'afterEach',
        'beforeAll',
        'beforeEach',
        'dataset',
        'describe',
        'it',
        'test',
    ];

    private const array CALLBACK_METHOD_ARGUMENT_POSITIONS = [
        'each' => 0,
        'filter' => 0,
        'map' => 0,
        'mapWithKeys' => 0,
        'reduce' => 0,
    ];

    private const array RESERVED_VARIABLE_NAMES = [
        'GLOBALS',
        '_COOKIE',
        '_ENV',
        '_FILES',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SERVER',
        '_SESSION',
        'suiteClassFile',
        'this',
    ];

    private const array UNSAFE_FUNCTIONS = [
        'array_diff_uassoc',
        'array_diff_ukey',
        'array_intersect_uassoc',
        'array_intersect_ukey',
        'array_udiff',
        'array_udiff_assoc',
        'array_udiff_uassoc',
        'array_uintersect',
        'array_uintersect_assoc',
        'array_uintersect_uassoc',
        'compact',
        'debug_backtrace',
        'debug_print_backtrace',
        'extract',
        'filter_input',
        'filter_input_array',
        'filter_var',
        'filter_var_array',
        'function_exists',
        'get_defined_functions',
        'get_defined_vars',
        'is_callable',
        'preg_replace_callback_array',
    ];

    private const array UNSAFE_CONSTRUCTORS = [
        'CallbackFilterIterator',
        'Fiber',
        'RecursiveCallbackFilterIterator',
        'ReflectionFunction',
        'ReflectionParameter',
    ];

    private const array UNSAFE_METHODS = [
        '__toString',
        'getTrace',
        'getTraceAsString',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Scope namespace-less helper functions in stand-alone Pest test files to the closures that use them.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        function helper(): string
                        {
                            return 'value';
                        }

                        it('uses the helper', function () {
                            expect(helper())->toBe('value');
                        });
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        $helper = function (): string {
                            return 'value';
                        };

                        it('uses the helper', function () use ($helper) {
                            expect($helper())->toBe('value');
                        });
                        CODE_SAMPLE,
                ),
            ],
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FileNode::class];
    }

    /** @param FileNode $node */
    public function refactor(Node $node): ?FileNode
    {
        if (! $this->shouldRefactorFile()) {
            return null;
        }

        $helperFunctions = [];

        foreach ($node->stmts as $statement) {
            if ($statement instanceof Function_) {
                $helperFunctions[$statement->name->toString()] = $statement;
            }
        }

        if ($helperFunctions === []) {
            return null;
        }

        $helperNames = array_keys($helperFunctions);

        if (! $this->containsPestRegistration($node->stmts)) {
            return null;
        }

        if ($this->hasUnsafeHelperReference($node, $helperNames, $helperFunctions)) {
            return null;
        }

        $statements = array_values(array_filter(
            $node->stmts,
            static fn (Node $statement): bool => ! $statement instanceof Function_
                || ! array_key_exists($statement->name->toString(), $helperFunctions),
        ));
        $insertionIndex = $this->findFirstUsageOrRegistrationIndex($statements, array_keys($helperFunctions));

        if ($insertionIndex === count($statements)) {
            return null;
        }

        $rawCalls = [];

        foreach ($helperFunctions as $helperName => $function) {
            $rawCalls[$helperName] = $this->findHelperCalls($function, $helperNames);
        }

        $reachableSets = $this->computeReachableSets($rawCalls);
        $assignments = [];
        $dependencies = [];

        foreach ($helperFunctions as $helperName => $function) {
            $closure = new Closure([
                'byRef' => $function->byRef,
                'params' => $function->params,
                'returnType' => $function->returnType,
                'stmts' => $function->stmts ?? [],
                'attrGroups' => $function->attrGroups,
            ], ['comments' => $function->getComments()]);

            $dependencies[$helperName] = $this->rewriteHelperCalls($closure, $helperNames, $helperName, $reachableSets);
            // Closures nested inside a helper do not inherit its `use()` variables.
            $this->wireNestedClosures($closure->getStmts(), $helperNames);
            $assignments[$helperName] = new Expression(
                new Assign(new Variable($helperName), $closure),
                ['comments' => $function->getComments()],
            );
        }

        $this->wireNestedClosures($statements, $helperNames);

        $this->rewriteHelperCallsInStatements($statements, $helperNames);

        $helperStatements = [];

        foreach ($this->sortHelperNames($helperNames, $dependencies) as $helperName) {
            $helperStatements[] = $assignments[$helperName];
        }

        $prefixStatements = [];

        if ($helperStatements !== []) {
            $registrationComments = $statements[$insertionIndex]->getComments();
            $statements[$insertionIndex]->setAttribute('comments', []);

            if ($registrationComments !== []) {
                $header = new Nop;
                $header->setAttribute('comments', $registrationComments);
                $prefixStatements[] = $header;
            }

            foreach ($helperStatements as $helperStatement) {
                $prefixStatements[] = $helperStatement;
                $prefixStatements[] = new Nop;
            }
        }

        array_splice($statements, $insertionIndex, 0, $prefixStatements);
        $node->stmts = $statements;

        return $node;
    }

    private function shouldRefactorFile(): bool
    {
        $filePath = str_replace('\\', '/', $this->getFile()->getFilePath());

        return str_contains($filePath, '/tests/')
            && ! str_ends_with($filePath, '/Pest.php')
            && ! str_ends_with($filePath, '/Helpers.php')
            && ! str_contains($filePath, '/Helpers/');
    }

    /** @param list<Node> $statements */
    private function containsPestRegistration(array $statements): bool
    {
        foreach ($statements as $statement) {
            if (! $statement instanceof Expression) {
                continue;
            }

            foreach ((new NodeFinder)->findInstanceOf([$statement], FuncCall::class) as $functionCall) {
                foreach (self::PEST_REGISTRATION_FUNCTIONS as $functionName) {
                    if ($this->isFunctionCallNamed($functionCall, $functionName)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $helperNames
     * @param  array<string, Function_>  $helperFunctions
     */
    private function hasUnsafeHelperReference(FileNode $node, array $helperNames, array $helperFunctions): bool
    {
        if (array_intersect($helperNames, self::RESERVED_VARIABLE_NAMES) !== []) {
            return true;
        }

        if ((new NodeFinder)->findFirstInstanceOf([$node], Eval_::class) instanceof Eval_
            || (new NodeFinder)->findFirstInstanceOf([$node], Include_::class) instanceof Include_
            || (new NodeFinder)->findFirstInstanceOf([$node], InterpolatedString::class) instanceof InterpolatedString
            || (new NodeFinder)->findFirstInstanceOf([$node], StringCast::class) instanceof StringCast
            || (new NodeFinder)->findFirstInstanceOf([$node], Goto_::class) instanceof Goto_) {
            return true;
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], Variable::class) as $variable) {
            if (! is_string($variable->name) || in_array($variable->name, $helperNames, true)) {
                return true;
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], FuncCall::class) as $functionCall) {
            if (! $functionCall->name instanceof Name) {
                return true;
            }

            foreach (self::UNSAFE_FUNCTIONS as $functionName) {
                if ($this->isFunctionCallNamed($functionCall, $functionName)) {
                    return true;
                }
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], New_::class) as $new) {
            if ($new->class instanceof Expr) {
                return true;
            }

            if ($new->class instanceof Name) {
                foreach (self::UNSAFE_CONSTRUCTORS as $className) {
                    if ($this->isName($new->class, $className)) {
                        return true;
                    }
                }
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], Class_::class) as $class) {
            if (! $class->extends instanceof Name) {
                continue;
            }

            foreach (self::UNSAFE_CONSTRUCTORS as $className) {
                if ($this->isName($class->extends, $className)) {
                    return true;
                }
            }
        }

        $dynamicMethodCall = (new NodeFinder)->findFirst(
            [$node],
            static fn (Node $nestedNode): bool => ($nestedNode instanceof MethodCall || $nestedNode instanceof NullsafeMethodCall || $nestedNode instanceof StaticCall)
                && ! $nestedNode->name instanceof Identifier,
        );

        if ($dynamicMethodCall instanceof Node) {
            return true;
        }

        if ($this->hasUnsafeCallback($node)) {
            return true;
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], String_::class) as $string) {
            foreach ($helperNames as $helperName) {
                if ($this->stringMatchesHelperName($string->value, $helperName)) {
                    return true;
                }
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], Concat::class) as $concat) {
            $value = $this->resolveStringExpression($concat);

            if ($value === null) {
                return true;
            }

            foreach ($helperNames as $helperName) {
                if ($this->stringMatchesHelperName($value, $helperName)) {
                    return true;
                }
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], ClassLike::class) as $classLike) {
            if ($this->findHelperCalls($classLike, $helperNames) !== []) {
                return true;
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], Function_::class) as $function) {
            if (in_array($function, $helperFunctions, true)) {
                continue;
            }

            if ($this->findHelperCalls($function, $helperNames) !== []) {
                return true;
            }
        }

        foreach ($helperFunctions as $helperFunction) {
            if ((new NodeFinder)->findFirstInstanceOf([$helperFunction], FunctionMagicConstant::class) instanceof FunctionMagicConstant
                || (new NodeFinder)->findFirstInstanceOf([$helperFunction], MethodMagicConstant::class) instanceof MethodMagicConstant) {
                return true;
            }
        }

        return false;
    }

    private function hasUnsafeCallback(FileNode $node): bool
    {
        foreach ((new NodeFinder)->findInstanceOf([$node], FuncCall::class) as $functionCall) {
            foreach (self::CALLBACK_ARGUMENT_POSITIONS as $functionName => $callbackPosition) {
                if (! $this->isFunctionCallNamed($functionCall, $functionName)) {
                    continue;
                }

                if ($this->hasUnsafeCallbackArgument($functionCall->args, $callbackPosition)) {
                    return true;
                }

                continue 2;
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], StaticCall::class) as $staticCall) {
            if (! $this->isName($staticCall->name, 'fromCallable')) {
                continue;
            }

            if (! $staticCall->class instanceof Name) {
                return true;
            }

            if (! $this->isName($staticCall->class, 'Closure')) {
                continue;
            }

            if ($this->hasUnsafeCallbackArgument($staticCall->args, 0)) {
                return true;
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], MethodCall::class) as $methodCall) {
            foreach (self::UNSAFE_METHODS as $methodName) {
                if ($this->isName($methodCall->name, $methodName)) {
                    return true;
                }
            }

            foreach (self::CALLBACK_METHOD_ARGUMENT_POSITIONS as $methodName => $callbackPosition) {
                if ($this->isName($methodCall->name, $methodName)
                    && $this->hasUnsafeCallbackArgument($methodCall->args, $callbackPosition)) {
                    return true;
                }
            }

            if ($this->isName($methodCall->name, 'fromCallable')
                && $this->hasUnsafeCallbackArgument($methodCall->args, 0)) {
                return true;
            }
        }

        foreach ((new NodeFinder)->findInstanceOf([$node], NullsafeMethodCall::class) as $methodCall) {
            foreach (self::UNSAFE_METHODS as $methodName) {
                if ($this->isName($methodCall->name, $methodName)) {
                    return true;
                }
            }

            foreach (self::CALLBACK_METHOD_ARGUMENT_POSITIONS as $methodName => $callbackPosition) {
                if ($this->isName($methodCall->name, $methodName)
                    && $this->hasUnsafeCallbackArgument($methodCall->args, $callbackPosition)) {
                    return true;
                }
            }

            if ($this->isName($methodCall->name, 'fromCallable')
                && $this->hasUnsafeCallbackArgument($methodCall->args, 0)) {
                return true;
            }
        }

        return false;
    }

    /** @param array<Node\Arg | Node\VariadicPlaceholder> $arguments */
    private function hasUnsafeCallbackArgument(array $arguments, int $callbackPosition): bool
    {
        $callback = null;

        foreach ($arguments as $index => $argument) {
            if (! $argument instanceof Node\Arg || $argument->unpack) {
                return true;
            }

            if ($argument->name?->toString() === 'callback' || ($index === $callbackPosition && $argument->name === null)) {
                $callback = $argument->value;

                break;
            }
        }

        return ! $callback instanceof Closure
            && ! $callback instanceof ArrowFunction
            && ! $callback instanceof String_
            && ! $callback instanceof Array_
            && ! ($callback instanceof FuncCall && $callback->name instanceof Name && $callback->isFirstClassCallable())
            && ! ($callback instanceof StaticCall && $callback->isFirstClassCallable())
            && ! ($callback instanceof MethodCall && $callback->isFirstClassCallable());
    }

    private function stringMatchesHelperName(string $value, string $helperName): bool
    {
        return strcasecmp(ltrim($value, '\\'), $helperName) === 0;
    }

    private function resolveStringExpression(Expr $expression): ?string
    {
        if ($expression instanceof String_) {
            return $expression->value;
        }

        if (! $expression instanceof Concat) {
            return null;
        }

        $left = $this->resolveStringExpression($expression->left);
        $right = $this->resolveStringExpression($expression->right);

        return ($left === null || $right === null) ? null : $left . $right;
    }

    /**
     * @param  array<string, list<string>>  $edges
     * @return array<string, array<string, true>>
     */
    private function computeReachableSets(array $edges): array
    {
        $reachable = [];

        foreach (array_keys($edges) as $name) {
            $reachable[$name] = $this->reachableFrom($name, $edges);
        }

        return $reachable;
    }

    /**
     * @param  array<string, list<string>>  $edges
     * @return array<string, true>
     */
    private function reachableFrom(string $start, array $edges): array
    {
        $visited = [];
        $queue = $edges[$start] ?? [];

        while ($queue !== []) {
            $name = array_pop($queue);

            if (isset($visited[$name])) {
                continue;
            }

            $visited[$name] = true;

            foreach ($edges[$name] ?? [] as $next) {
                $queue[] = $next;
            }
        }

        return $visited;
    }

    /** @param array<string, array<string, true>> $reachableSets */
    private function isSameCycle(string $first, string $second, array $reachableSets): bool
    {
        if ($first === $second) {
            return isset($reachableSets[$first][$first]);
        }

        return isset($reachableSets[$first][$second]) && isset($reachableSets[$second][$first]);
    }

    /**
     * @param  list<Node>  $statements
     * @param  list<string>  $helperNames
     */
    private function findFirstUsageOrRegistrationIndex(array $statements, array $helperNames): int
    {
        foreach ($statements as $index => $statement) {
            if ($this->findHelperCalls($statement, $helperNames) !== []) {
                return $index;
            }

            foreach ((new NodeFinder)->findInstanceOf([$statement], FuncCall::class) as $call) {
                foreach (self::PEST_REGISTRATION_FUNCTIONS as $functionName) {
                    if ($this->isFunctionCallNamed($call, $functionName)) {
                        return $index;
                    }
                }
            }
        }

        return count($statements);
    }

    /**
     * @param  list<string>  $helperNames
     * @param  array<string, array<string, true>>  $reachableSets
     * @return list<string>
     */
    private function rewriteHelperCalls(Closure | ArrowFunction $closure, array $helperNames, ?string $currentHelper = null, array $reachableSets = []): array
    {
        $dependencies = [];

        foreach ((new NodeFinder)->findInstanceOf($this->functionStatements($closure), FuncCall::class) as $call) {
            foreach ($helperNames as $helperName) {
                $isHelperCall = $this->isFunctionCallNamed($call, $helperName)
                    || ($call->name instanceof Variable && $call->name->name === $helperName);

                if (! $isHelperCall) {
                    continue;
                }

                $call->name = new Variable($helperName);

                // A cyclic dependency needs a by-reference capture since the target variable
                // is not assigned when the closure is created.
                $byReference = $currentHelper !== null && $this->isSameCycle($currentHelper, $helperName, $reachableSets);

                if ($closure instanceof Closure && ($byReference || $helperName !== $currentHelper)) {
                    $this->addClosureUse($closure, $helperName, $byReference);
                }

                if (! $byReference && $helperName !== $currentHelper) {
                    $dependencies[] = $helperName;
                }
            }
        }

        return array_values(array_unique($dependencies));
    }

    /**
     * A closure only sees its own `use()` list, so a helper call inside a closure nested
     * within another closure/helper needs its own capture, not just the outermost one.
     *
     * @param  list<Node>  $statements
     * @param  list<string>  $helperNames
     */
    private function wireNestedClosures(array $statements, array $helperNames): void
    {
        foreach ((new NodeFinder)->findInstanceOf($statements, Closure::class) as $nestedClosure) {
            $this->rewriteHelperCalls($nestedClosure, $helperNames);
        }

        foreach ((new NodeFinder)->findInstanceOf($statements, ArrowFunction::class) as $nestedArrowFunction) {
            $this->rewriteHelperCalls($nestedArrowFunction, $helperNames);
        }
    }

    /**
     * @param  list<Node>  $statements
     * @param  list<string>  $helperNames
     */
    private function rewriteHelperCallsInStatements(array $statements, array $helperNames): void
    {
        foreach ((new NodeFinder)->findInstanceOf(
            array_filter($statements, static fn (Node $statement): bool => ! $statement instanceof Function_),
            FuncCall::class,
        ) as $call) {
            foreach ($helperNames as $helperName) {
                if ($this->isFunctionCallNamed($call, $helperName)) {
                    $call->name = new Variable($helperName);
                }
            }
        }
    }

    /**
     * @param  list<string>  $helperNames
     * @return list<string>
     */
    private function findHelperCalls(Node $node, array $helperNames): array
    {
        $calls = [];

        foreach ((new NodeFinder)->findInstanceOf([$node], FuncCall::class) as $call) {
            foreach ($helperNames as $helperName) {
                if ($this->isFunctionCallNamed($call, $helperName)) {
                    $calls[] = $helperName;
                }
            }
        }

        return array_values(array_unique($calls));
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

    /** @return list<Node> */
    private function functionStatements(Closure | ArrowFunction $function): array
    {
        return $function instanceof Closure ? $function->getStmts() : [$function->expr];
    }

    /**
     * @param  list<string>  $helperNames
     * @param  array<string, list<string>>  $dependencies
     * @return list<string>
     */
    private function sortHelperNames(array $helperNames, array $dependencies): array
    {
        $sorted = [];
        $visiting = [];

        $visit = function (string $helperName) use (&$visit, &$sorted, &$visiting, $dependencies): void {
            if (in_array($helperName, $sorted, true) || isset($visiting[$helperName])) {
                return;
            }

            $visiting[$helperName] = true;

            foreach ($dependencies[$helperName] ?? [] as $dependency) {
                $visit($dependency);
            }
            unset($visiting[$helperName]);
            $sorted[] = $helperName;
        };

        foreach ($helperNames as $helperName) {
            $visit($helperName);
        }

        return $sorted;
    }

    private function addClosureUse(Closure $closure, string $variableName, bool $byReference = false): void
    {
        foreach ($closure->uses as $use) {
            if ($use->var->name === $variableName) {
                return;
            }
        }

        $closure->uses[] = new Node\ClosureUse(new Variable($variableName), $byReference);
    }
}
