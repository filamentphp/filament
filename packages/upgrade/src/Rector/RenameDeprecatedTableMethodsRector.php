<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedTableMethodsRector extends AbstractRector
{
    /**
     * @var array<string, string>
     */
    private const METHOD_RENAMES = [
        'actions' => 'recordActions',
        'pushActions' => 'pushRecordActions',
        'actionsColumnLabel' => 'recordActionsColumnLabel',
        'actionsAlignment' => 'recordActionsAlignment',
        'actionsPosition' => 'recordActionsPosition',
        'getActions' => 'getRecordActions',
        'getActionsPosition' => 'getRecordActionsPosition',
        'getActionsAlignment' => 'getRecordActionsAlignment',
        'getActionsColumnLabel' => 'getRecordActionsColumnLabel',
        'bulkActions' => 'toolbarActions',
        'pushBulkActions' => 'pushToolbarActions',
        'getBulkActions' => 'getToolbarActions',
        'toggleColumnsTriggerAction' => 'columnManagerTriggerAction',
        'columnToggleFormColumns' => 'columnManagerColumns',
        'columnToggleFormMaxHeight' => 'columnManagerMaxHeight',
        'columnToggleFormWidth' => 'columnManagerWidth',
        'getColumnToggleFormColumns' => 'getColumnManagerColumns',
        'getColumnToggleFormMaxHeight' => 'getColumnManagerMaxHeight',
        'getColumnToggleFormWidth' => 'getColumnManagerWidth',
        'getToggleColumnsTriggerAction' => 'getColumnManagerTriggerAction',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Filament table method calls', [
            new CodeSample(
                '$table->actions(actions: []);',
                '$table->recordActions(actions: []);',
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = strtolower($node->name->toString());
        $methodRenames = array_change_key_case(self::METHOD_RENAMES);

        if (! isset($methodRenames[$methodName])) {
            return null;
        }

        $receiverType = TypeCombinator::removeNull($this->getType($node->var));

        if (! (new ObjectType('Filament\\Tables\\Table'))->isSuperTypeOf($receiverType)->yes()) {
            return null;
        }

        // Custom overrides need manual migration, not a call redirected past their behavior.
        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            foreach ([$methodName, $methodRenames[$methodName]] as $candidateMethodName) {
                if (
                    $classReflection->hasNativeMethod($candidateMethodName) &&
                    ($classReflection->getNativeMethod($candidateMethodName)->getDeclaringClass()->getName() !== 'Filament\\Tables\\Table')
                ) {
                    return null;
                }
            }
        }

        $node->name = new Identifier($methodRenames[$methodName]);

        return $node;
    }
}
