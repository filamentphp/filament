<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceLegacyColumnFactoriesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace direct legacy column factories with explicit presentation configuration', [
            new CodeSample(
                "\\Filament\\Tables\\Columns\\TagsColumn::make('tags')->limit(length: 2)",
                "\\Filament\\Tables\\Columns\\TextColumn::make('tags')->badge()->limitList(limit: 2)",
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
            $factory->getAttribute('legacy_column_manual_migration')
        ) {
            return null;
        }

        $legacyClass = strtolower((string) $this->getName($factory->class));
        $replacement = match ($legacyClass) {
            'filament\\tables\\columns\\badgecolumn', 'filament\\tables\\columns\\tagscolumn' => ['Filament\\Tables\\Columns\\TextColumn', 'badge'],
            'filament\\tables\\columns\\booleancolumn' => ['Filament\\Tables\\Columns\\IconColumn', 'boolean'],
            default => null,
        };

        if (! $replacement) {
            return null;
        }

        $arguments = $factory->args;

        foreach ($calls as $call) {
            array_push($arguments, ...$call->args);
        }

        // A callback typed to a removed class cannot accept the replacement.
        if ((new NodeFinder)->findFirst($arguments, fn (Node $candidate): bool => ($candidate instanceof Name) && in_array(strtolower((string) $this->getName($candidate)), [
            'filament\\tables\\columns\\badgecolumn',
            'filament\\tables\\columns\\booleancolumn',
            'filament\\tables\\columns\\tagscolumn',
        ], true))) {
            $factory->setAttribute('legacy_column_manual_migration', true);

            return null;
        }

        foreach ($calls as $call) {
            // Only known setters are safe: callbacks like `tap()` can disable
            // forced presentation, and custom methods can change the receiver.
            if (
                ($call instanceof NullsafeMethodCall) ||
                (! $call->name instanceof Identifier) ||
                $call->isFirstClassCallable() ||
                (! in_array(strtolower($call->name->name), [
                    'label', 'color', 'icon', 'sortable', 'searchable', 'placeholder',
                    'default', 'state', 'formatstateusing', 'limitlist', 'wrap',
                    'listwithlinebreaks', 'bulleted', 'separator', 'expandablelimitedlist',
                    'limit', 'badge', 'trueicon', 'falseicon', 'truecolor', 'falsecolor',
                ], true)) ||
                ($this->isName($call->name, 'badge') && ($legacyClass !== 'filament\\tables\\columns\\tagscolumn')) ||
                ($this->isName($call->name, 'limit') && (! $this->canRenameLimit($call, $legacyClass)))
            ) {
                // Rector visits the outer chain first. Do not migrate its
                // inner factory later if the complete chain needs review.
                $factory->setAttribute('legacy_column_manual_migration', true);

                return null;
            }
        }

        foreach ($calls as $call) {
            if (($legacyClass !== 'filament\\tables\\columns\\tagscolumn') || (! $this->isName($call->name, 'limit'))) {
                continue;
            }

            $call->name = new Identifier('limitList');

            foreach ($call->args as $argument) {
                if ($argument->name) {
                    $argument->name = new Identifier('limit');
                }
            }
        }

        $factory->class = new FullyQualified($replacement[0]);
        $configuredFactory = new MethodCall($factory, $replacement[1]);

        if ($calls === []) {
            return $configuredFactory;
        }

        $calls[array_key_last($calls)]->var = $configuredFactory;

        return $node;
    }

    protected function canRenameLimit(MethodCall $call, string $legacyClass): bool
    {
        if ($legacyClass !== 'filament\\tables\\columns\\tagscolumn') {
            return true;
        }

        // Even an ignored `end` expression is evaluated by PHP. Leave all
        // two-argument and unpacked calls for manual migration.
        return (count($call->args) <= 1) &&
            (($call->args === []) || (
                (! $call->args[0]->unpack) &&
                ((! $call->args[0]->name) || $this->isName($call->args[0]->name, 'length'))
            ));
    }
}
