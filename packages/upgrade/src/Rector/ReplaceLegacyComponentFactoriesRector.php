<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use ReflectionMethod;
use ReflectionNamedType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceLegacyComponentFactoriesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace direct legacy component factories with explicit configuration', [
            new CodeSample(
                "\\Filament\\Tables\\Columns\\TagsColumn::make('tags')->limit(length: 2)",
                "\\Filament\\Tables\\Columns\\TextColumn::make('tags')->badge()->limitList(limit: 2)",
            ),
            new CodeSample(
                "\\Filament\\Forms\\Components\\MultiSelect::make('tags')",
                "\\Filament\\Forms\\Components\\Select::make('tags')->multiple()",
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Namespace_::class, FileNode::class, ClassMethod::class, Function_::class, Closure::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param  Namespace_ | FileNode | ClassMethod | Function_ | Closure | MethodCall | NullsafeMethodCall | StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (($node instanceof Namespace_) || ($node instanceof FileNode) || ($node instanceof FunctionLike)) {
            $this->markStoredCalls($node->stmts ?? []);

            return null;
        }

        $factory = $node;
        $calls = [];

        while (($factory instanceof MethodCall) || ($factory instanceof NullsafeMethodCall)) {
            $calls[] = $factory;
            $factory = $factory->var;
        }

        if ((! $factory instanceof StaticCall) && ($node instanceof MethodCall)) {
            return $this->refactorStoredCall($node);
        }

        if (
            (! $factory instanceof StaticCall) ||
            (! $factory->class instanceof Name) ||
            (! $factory->name instanceof Identifier) ||
            (! $this->isName($factory->name, 'make')) ||
            $factory->isFirstClassCallable() ||
            $factory->getAttribute('legacy_component_manual_migration')
        ) {
            return null;
        }

        $legacyClass = strtolower((string) $this->getName($factory->class));
        $replacement = $this->getReplacement($legacyClass);

        if (! $replacement) {
            return null;
        }

        $arguments = $factory->args;

        foreach ($calls as $call) {
            array_push($arguments, ...$call->args);
        }

        if ((! $this->canMigrateArguments($arguments, $legacyClass)) || (! $this->canMigrateCalls($calls, $legacyClass, $replacement[0]))) {
            // Rector visits the outer chain first. Do not migrate its inner
            // factory later if the complete chain needs manual review.
            $factory->setAttribute('legacy_component_manual_migration', true);

            return null;
        }

        foreach ($calls as $call) {
            $this->migrateCall($call, $legacyClass);
        }

        $factory->class = new FullyQualified($replacement[0]);
        $configuredFactory = $replacement[1] ? new MethodCall($factory, $replacement[1]) : $factory;

        if ($calls === []) {
            return $configuredFactory;
        }

        $calls[array_key_last($calls)]->var = $configuredFactory;

        // These legacy getters always returned true. Keep argument evaluation,
        // but do not accidentally activate an ineffective disabling condition.
        if (in_array($legacyClass, ['filament\\forms\\components\\multiselect', 'filament\\tables\\columns\\badgecolumn', 'filament\\tables\\columns\\booleancolumn', 'filament\\tables\\filters\\multiselectfilter'], true)) {
            foreach ($calls as $index => $call) {
                if ((! $this->isName($call->name, $replacement[1])) || $call->isFirstClassCallable()) {
                    continue;
                }

                $configuredCall = new MethodCall($call, $replacement[1]);

                if ($index === 0) {
                    $node = $configuredCall;
                } else {
                    $calls[$index - 1]->var = $configuredCall;
                }
            }
        }

        return $node;
    }

    /**
     * @param  array<Node\Stmt>  $statements
     */
    protected function markStoredCalls(array $statements): void
    {
        $classes = [];

        foreach ($statements as $statement) {
            // Only follow straight-line local assignments, not branches, loops,
            // references, or calls inside another function's scope.
            if ((! $statement instanceof Expression) && (! $statement instanceof Return_)) {
                $classes = [];

                continue;
            }

            $expression = $statement->expr;
            $assignment = $expression instanceof Assign ? $expression : null;
            $nestedAssignments = [];
            $calls = [];

            $this->traverseNodesWithCallable($statement, static function (Node $candidate) use (&$nestedAssignments, &$calls): ?int {
                if ($candidate instanceof FunctionLike) {
                    return NodeTraverser::DONT_TRAVERSE_CHILDREN;
                }

                if (($candidate instanceof Assign) || ($candidate instanceof AssignRef)) {
                    $nestedAssignments[] = $candidate;
                }

                if ($candidate instanceof MethodCall) {
                    $calls[] = $candidate;
                }

                return null;
            });

            if (($nestedAssignments !== []) && (($nestedAssignments !== [$assignment]) || (! $assignment?->var instanceof Variable) || (! is_string($assignment->var->name)))) {
                $classes = [];

                continue;
            }

            foreach ($calls as $call) {
                $receiver = $call->var;
                $receiverCalls = [];

                while ($receiver instanceof MethodCall) {
                    $receiverCalls[] = $receiver;
                    $receiver = $receiver->var;
                }

                if ((! $receiver instanceof Variable) || (! is_string($receiver->name)) || (! isset($classes[$receiver->name]))) {
                    continue;
                }

                $legacyClass = $classes[$receiver->name];

                foreach ($receiverCalls as $receiverCall) {
                    if (! $this->preservesReceiver($receiverCall, $legacyClass)) {
                        continue 2;
                    }
                }

                $call->setAttribute('legacy_component_class', $legacyClass);
            }

            if ((! $assignment) || (! $assignment->var instanceof Variable) || (! is_string($assignment->var->name))) {
                continue;
            }

            $factory = $assignment->expr;
            $calls = [];

            while ($factory instanceof MethodCall) {
                $calls[] = $factory;
                $factory = $factory->var;
            }

            $legacyClass = (($factory instanceof StaticCall) && ($factory->class instanceof Name) && $this->isName($factory->name, 'make') && (! $factory->isFirstClassCallable()))
                ? strtolower((string) $this->getName($factory->class))
                : null;

            unset($classes[$assignment->var->name]);

            $replacement = $legacyClass ? $this->getReplacement($legacyClass) : null;

            if (! $replacement) {
                continue;
            }

            foreach ($calls as $call) {
                if (! $this->preservesReceiver($call, $legacyClass)) {
                    continue 2;
                }
            }

            $classes[$assignment->var->name] = $legacyClass;
        }
    }

    protected function preservesReceiver(MethodCall $call, string $legacyClass): bool
    {
        if ((! $call->name instanceof Identifier) || $call->isFirstClassCallable()) {
            return false;
        }

        $replacement = $this->getReplacement($legacyClass);
        $method = (($legacyClass === 'filament\\forms\\components\\placeholder') && $this->isName($call->name, 'content')) ? 'state' : $call->name->name;

        if ((! $replacement) || (! method_exists($replacement[0], $method))) {
            return false;
        }

        $returnType = (new ReflectionMethod($replacement[0], $method))->getReturnType();

        return ($returnType instanceof ReflectionNamedType) && ($returnType->getName() === 'static');
    }

    protected function refactorStoredCall(MethodCall $call): ?Node
    {
        if ((! $call->name instanceof Identifier) || $call->isFirstClassCallable() || $call->getAttribute('legacy_component_migrated')) {
            return null;
        }

        $legacyClass = $call->getAttribute('legacy_component_class');

        if (! is_string($legacyClass)) {
            return null;
        }

        $replacement = $this->getReplacement($legacyClass);

        if ((! $replacement) || (! $this->canMigrateCalls([$call], $legacyClass, $replacement[0]))) {
            return null;
        }

        if (
            (($legacyClass === 'filament\\forms\\components\\placeholder') && $this->isNames($call->name, ['content', 'getContent'])) ||
            (($legacyClass === 'filament\\tables\\columns\\tagscolumn') && $this->isName($call->name, 'limit'))
        ) {
            $this->migrateCall($call, $legacyClass);

            return $call;
        }

        if (
            in_array($legacyClass, ['filament\\forms\\components\\multiselect', 'filament\\tables\\columns\\badgecolumn', 'filament\\tables\\columns\\booleancolumn', 'filament\\tables\\filters\\multiselectfilter'], true) &&
            $this->isName($call->name, $replacement[1])
        ) {
            $call->setAttribute('legacy_component_migrated', true);
            $configuredCall = new MethodCall($call, $replacement[1]);
            $configuredCall->setAttribute('legacy_component_migrated', true);

            return $configuredCall;
        }

        return null;
    }

    /**
     * @return array{string, ?string} | null
     */
    protected function getReplacement(string $legacyClass): ?array
    {
        return match ($legacyClass) {
            'filament\\tables\\columns\\badgecolumn', 'filament\\tables\\columns\\tagscolumn' => ['Filament\\Tables\\Columns\\TextColumn', 'badge'],
            'filament\\tables\\columns\\booleancolumn' => ['Filament\\Tables\\Columns\\IconColumn', 'boolean'],
            'filament\\forms\\components\\multiselect' => ['Filament\\Forms\\Components\\Select', 'multiple'],
            'filament\\forms\\components\\relationshiprepeater' => ['Filament\\Forms\\Components\\Repeater', 'relationship'],
            'filament\\forms\\components\\placeholder' => ['Filament\\Infolists\\Components\\TextEntry', null],
            'filament\\tables\\filters\\multiselectfilter' => ['Filament\\Tables\\Filters\\SelectFilter', 'multiple'],
            default => null,
        };
    }

    /**
     * @param  array<Node>  $arguments
     */
    protected function canMigrateArguments(array $arguments, string $legacyClass): bool
    {
        $methodsRequiringReview = match ($legacyClass) {
            'filament\\forms\\components\\multiselect', 'filament\\tables\\filters\\multiselectfilter' => ['multiple'],
            'filament\\forms\\components\\placeholder' => ['content', 'getContent'],
            'filament\\tables\\columns\\badgecolumn' => ['badge'],
            'filament\\tables\\columns\\booleancolumn' => ['boolean'],
            'filament\\tables\\columns\\tagscolumn' => ['limit'],
            default => [],
        };

        // Callbacks may reconfigure forced behavior or use removed aliases.
        if ($methodsRequiringReview && (new NodeFinder)->findFirst(
            $arguments,
            fn (Node $candidate): bool => (($candidate instanceof MethodCall) || ($candidate instanceof NullsafeMethodCall)) &&
                ((! $candidate->name instanceof Identifier) || $this->isNames($candidate->name, $methodsRequiringReview))
        )) {
            return false;
        }

        // A callback typed to a removed class cannot accept the replacement.
        // Nested factories in a repeater schema are not type declarations.
        $nodeFinder = new NodeFinder;

        return ! $nodeFinder->findFirst($arguments, function (Node $candidate) use ($nodeFinder): bool {
            $type = match (true) {
                $candidate instanceof Param => $candidate->type,
                $candidate instanceof FunctionLike => $candidate->getReturnType(),
                default => null,
            };

            return $type && $nodeFinder->findFirst([$type], fn (Node $typeNode): bool => ($typeNode instanceof Name) && $this->getReplacement(strtolower((string) $this->getName($typeNode))) !== null);
        });
    }

    /**
     * @param  array<MethodCall | NullsafeMethodCall>  $calls
     */
    protected function canMigrateCalls(array $calls, string $legacyClass, string $replacementClass): bool
    {
        // Keep the existing filter factory migration's permissive chain handling.
        if ($legacyClass === 'filament\\tables\\filters\\multiselectfilter') {
            return true;
        }

        foreach ($calls as $index => $call) {
            if (
                ($call instanceof NullsafeMethodCall) ||
                (! $call->name instanceof Identifier) ||
                $call->isFirstClassCallable()
            ) {
                return false;
            }

            if ($legacyClass === 'filament\\forms\\components\\placeholder') {
                if ($this->isName($call->name, 'getContent')) {
                    if (($index !== 0) || ($call->args !== [])) {
                        return false;
                    }

                    continue;
                }

                if ($this->isName($call->name, 'content')) {
                    if (
                        (count($call->args) !== 1) || $call->args[0]->unpack ||
                        ($call->args[0]->name && (! $this->isName($call->args[0]->name, 'content')))
                    ) {
                        return false;
                    }

                    continue;
                }
            }

            // Use the real public API rather than a short list of setters that
            // accidentally excludes normal chains such as `validationMessages()`.
            if (! method_exists($replacementClass, $call->name->name)) {
                return false;
            }

            $method = new ReflectionMethod($replacementClass, $call->name->name);
            $returnType = $method->getReturnType();

            if ((! $method->isPublic()) || (($index !== 0) && (! $this->isNames($call->name, ['tap', 'when', 'unless'])) && ((! $returnType instanceof ReflectionNamedType) || ($returnType->getName() !== 'static')))) {
                return false;
            }

            if (($legacyClass === 'filament\\tables\\columns\\tagscolumn') && $this->isName($call->name, 'limit') && (! $this->canRenameLimit($call))) {
                return false;
            }
        }

        return true;
    }

    protected function migrateCall(MethodCall | NullsafeMethodCall $call, string $legacyClass): void
    {
        if ($legacyClass === 'filament\\forms\\components\\placeholder') {
            if ($this->isName($call->name, 'content')) {
                $call->name = new Identifier('state');

                foreach ($call->args as $argument) {
                    if ($argument->name) {
                        $argument->name = new Identifier('state');
                    }
                }
            } elseif ($this->isName($call->name, 'getContent')) {
                $call->name = new Identifier('getState');
            }

            return;
        }

        if (($legacyClass !== 'filament\\tables\\columns\\tagscolumn') || (! $this->isName($call->name, 'limit'))) {
            return;
        }

        $call->name = new Identifier('limitList');

        foreach ($call->args as $argument) {
            if ($argument->name) {
                $argument->name = new Identifier('limit');
            }
        }
    }

    protected function canRenameLimit(MethodCall $call): bool
    {
        // PHP evaluates even the ignored `end` expression. Leave these and
        // unpacked calls untouched rather than dropping meaningful effects.
        return (count($call->args) <= 1) &&
            (($call->args === []) || (
                (! $call->args[0]->unpack) &&
                ((! $call->args[0]->name) || $this->isName($call->args[0]->name, 'length'))
            ));
    }
}
