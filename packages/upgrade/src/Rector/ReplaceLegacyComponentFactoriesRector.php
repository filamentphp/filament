<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
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

        if (str_starts_with($legacyClass, 'filament\\forms\\components\\') && (! $this->canMigrateFormFactory($factory))) {
            return null;
        }

        $arguments = $factory->args;

        foreach ($calls as $call) {
            array_push($arguments, ...$call->args);
        }

        if ((! $this->canMigrateArguments($arguments, $legacyClass)) || (! $this->canMigrateCalls($calls, $legacyClass))) {
            // Rector visits the outer chain first. Do not migrate its
            // inner factory later if the complete chain needs review.
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

        return $node;
    }

    protected function canMigrateFormFactory(StaticCall $factory): bool
    {
        $nodeFinder = new NodeFinder;

        return ! $nodeFinder->findFirst($this->getFile()->getNewStmts(), function (Node $candidate) use ($factory, $nodeFinder): bool {
            // An escaped instance may receive later configuration or alias calls.
            if (($candidate instanceof Assign) || ($candidate instanceof AssignRef)) {
                return (bool) $nodeFinder->findFirst([$candidate->expr], static fn (Node $expression): bool => $expression === $factory);
            }

            // Configuration in other files still needs application-wide review.
            return ($candidate instanceof StaticCall) && ($candidate->name instanceof Identifier) && $this->isName($candidate->name, 'configureUsing');
        });
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
            default => [],
        };

        // Callbacks can change forced multiplicity or use removed content aliases.
        if ((new NodeFinder)->findFirst(
            $arguments,
            fn (Node $candidate): bool => (($candidate instanceof MethodCall) || ($candidate instanceof NullsafeMethodCall)) &&
            ($candidate->name instanceof Identifier) && $this->isNames($candidate->name, $methodsRequiringReview)
        )) {
            return false;
        }

        // A callback typed to a removed class cannot accept the replacement.
        return ! (new NodeFinder)->findFirst($arguments, fn (Node $candidate): bool => ($candidate instanceof Name) && in_array(strtolower((string) $this->getName($candidate)), [
            'filament\\tables\\columns\\badgecolumn',
            'filament\\tables\\columns\\booleancolumn',
            'filament\\tables\\columns\\tagscolumn',
            'filament\\forms\\components\\multiselect',
            'filament\\forms\\components\\relationshiprepeater',
            'filament\\forms\\components\\placeholder',
        ], true));
    }

    /**
     * @param  array<MethodCall | NullsafeMethodCall>  $calls
     */
    protected function canMigrateCalls(array $calls, string $legacyClass): bool
    {
        if (str_starts_with($legacyClass, 'filament\\forms\\components\\')) {
            return $this->canMigrateFormCalls($calls, $legacyClass);
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

    /**
     * @param  array<MethodCall | NullsafeMethodCall>  $calls
     */
    protected function canMigrateFormCalls(array $calls, string $legacyClass): bool
    {
        $allowedMethods = [
            'label', 'hiddenlabel', 'helpertext', 'hint', 'hintcolor', 'hinticon',
            'visible', 'hidden', 'columnspan', 'columnspanfull', 'default', 'state',
        ];
        $allowedMethods = [...$allowedMethods, ...match ($legacyClass) {
            'filament\\forms\\components\\multiselect' => ['options', 'searchable', 'preload', 'native', 'placeholder', 'required', 'disabled', 'relationship'],
            'filament\\forms\\components\\relationshiprepeater' => ['schema', 'columns', 'defaultitems', 'minitems', 'maxitems', 'addable', 'deletable', 'reorderable', 'ordercolumn', 'itemlabel', 'collapsed', 'dehydrated'],
            'filament\\forms\\components\\placeholder' => ['content', 'getcontent', 'color', 'icon', 'html', 'placeholder', 'formatstateusing'],
            default => [],
        }];

        foreach ($calls as $index => $call) {
            if (
                ($call instanceof NullsafeMethodCall) ||
                (! $call->name instanceof Identifier) ||
                $call->isFirstClassCallable() ||
                (! in_array(strtolower($call->name->name), $allowedMethods, true))
            ) {
                return false;
            }

            if ($legacyClass !== 'filament\\forms\\components\\placeholder') {
                continue;
            }

            // A getter ends the component chain, and forwarding wrappers did
            // not forward extra arguments. Unpacking may contain named keys.
            if ($this->isName($call->name, 'getContent') && (($index !== 0) || ($call->args !== []))) {
                return false;
            }

            if ($this->isName($call->name, 'content') && (
                (count($call->args) !== 1) || $call->args[0]->unpack ||
                ($call->args[0]->name && (! $this->isName($call->args[0]->name, 'content')))
            )) {
                return false;
            }

        }

        return true;
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
