<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\NodeFinder;
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
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall | StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        $factory = $node;
        $calls = [];

        while (($factory instanceof MethodCall) || ($factory instanceof NullsafeMethodCall)) {
            $calls[] = $factory;
            $factory = $factory->var;
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
        if (in_array($legacyClass, ['filament\\forms\\components\\multiselect', 'filament\\tables\\columns\\badgecolumn', 'filament\\tables\\columns\\booleancolumn'], true)) {
            foreach ($calls as $index => $call) {
                if (! $this->isName($call->name, $replacement[1])) {
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
            default => null,
        };
    }

    /**
     * @param  array<Node>  $arguments
     */
    protected function canMigrateArguments(array $arguments, string $legacyClass): bool
    {
        $methodsRequiringReview = match ($legacyClass) {
            'filament\\forms\\components\\multiselect' => ['multiple'],
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
